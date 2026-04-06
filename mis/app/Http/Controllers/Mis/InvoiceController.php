<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Lead;
use App\Models\Offering;
use App\Models\Quotation;
use App\Services\AccountingSyncService;
use App\Services\AuditLogger;
use App\Services\DocumentNumberService;
use App\Services\ZohoBooksClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::query()->with('client')->orderByDesc('id')->paginate(20);

        return view('mis.invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        $clients = Client::query()->orderBy('contact_name')->limit(500)->get();
        $leadsForLink = Lead::query()
            ->with('client')
            ->whereHas('client')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $offerings = Offering::query()->orderBy('name')->limit(500)->get();

        return view('mis.invoices.create', compact('clients', 'leadsForLink', 'offerings'));
    }

    public function store(Request $request, DocumentNumberService $numbers, AccountingSyncService $accounting): RedirectResponse
    {
        $request->merge([
            'lead_id' => $request->filled('lead_id') ? $request->input('lead_id') : null,
            'offering_id' => $request->filled('offering_id') ? $request->input('offering_id') : null,
        ]);

        $rawLines = $request->input('lines', []);
        $lines = collect($rawLines)->filter(fn ($l) => filled(trim((string) ($l['description'] ?? ''))));
        $request->merge(['lines' => $lines->values()->all()]);

        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'offering_id' => ['nullable', 'exists:offerings,id'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'currency' => ['required', 'string', 'size:3'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string', 'max:500'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $client = Client::query()->findOrFail($data['client_id']);
        $leadId = $data['lead_id'] ?? null;
        if ($leadId !== null) {
            $lead = Lead::query()->findOrFail($leadId);
            if (! $lead->client || $lead->client->id !== $client->id) {
                return back()->withErrors(['lead_id' => 'That lead is not linked to the selected client.'])->withInput();
            }
        }

        $issued = $data['issued_at'] ?? now()->toDateString();
        $due = $data['due_at'] ?? now()->addDays(14)->toDateString();
        $currency = strtoupper($data['currency']);

        $invoice = DB::transaction(function () use ($data, $numbers, $issued, $due, $currency, $leadId) {
            $inv = Invoice::create([
                'number' => $numbers->nextInvoiceNumber(),
                'client_id' => $data['client_id'],
                'lead_id' => $leadId,
                'offering_id' => $data['offering_id'] ?? null,
                'quotation_id' => null,
                'contract_id' => null,
                'status' => 'draft',
                'currency' => $currency,
                'issued_at' => $issued,
                'due_at' => $due,
                'subtotal_pence' => 0,
                'tax_pence' => 0,
                'total_pence' => 0,
                'paid_pence' => 0,
            ]);

            $order = 0;
            foreach ($data['lines'] as $line) {
                $unitPence = (int) round(((float) $line['unit_price']) * 100);
                $qty = (float) $line['quantity'];
                $lineTotal = (int) round($qty * $unitPence);
                InvoiceLine::create([
                    'invoice_id' => $inv->id,
                    'description' => $line['description'],
                    'quantity' => $qty,
                    'unit_price_pence' => $unitPence,
                    'line_total_pence' => $lineTotal,
                    'sort_order' => $order++,
                ]);
            }

            $inv->recalculateTotals();

            return $inv->fresh(['client', 'lines']);
        });

        $accounting->syncInvoiceToZoho($invoice);

        return redirect()->route('mis.invoices.show', $invoice)->with('status', 'Draft invoice created.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['client', 'lines', 'payments', 'offering', 'quotation', 'lead']);

        return view('mis.invoices.show', compact('invoice'));
    }

    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Invoice::STATUSES)],
        ]);
        $old = $invoice->only(['status', 'written_off_at']);
        $updates = ['status' => $data['status']];
        if ($data['status'] === 'written_off') {
            $updates['written_off_at'] = now();
        } else {
            $updates['written_off_at'] = null;
        }
        $invoice->update($updates);
        AuditLogger::record($invoice, 'status_changed', $old, $invoice->only(['status', 'written_off_at']), $request);

        return back()->with('status', 'Invoice status updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status !== 'draft' || $invoice->paid_pence > 0 || $invoice->payments()->exists()) {
            return redirect()->route('mis.invoices.show', $invoice)
                ->with('error', 'Only draft invoices with no payments can be deleted.');
        }

        $invoice->lines()->delete();
        $invoice->delete();

        return redirect()->route('mis.invoices.index')->with('status', 'Draft invoice deleted.');
    }

    public function recordReminder(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update([
            'last_reminder_at' => now(),
            'next_reminder_at' => now()->addDays(7),
        ]);

        return back()->with('status', 'Reminder logged; next follow-up in 7 days.');
    }

    public function fromQuotation(Quotation $quotation, DocumentNumberService $numbers, AccountingSyncService $accounting): RedirectResponse
    {
        $quotation->load(['lines', 'client']);
        if ($quotation->status !== 'accepted') {
            return back()->withErrors(['quote' => 'Accept the quotation before invoicing.']);
        }

        $invoice = Invoice::create([
            'number' => $numbers->nextInvoiceNumber(),
            'client_id' => $quotation->client_id,
            'quotation_id' => $quotation->id,
            'lead_id' => $quotation->lead_id,
            'status' => 'draft',
            'currency' => $quotation->currency,
            'issued_at' => now(),
            'due_at' => now()->addDays(14),
            'subtotal_pence' => $quotation->subtotal_pence,
            'tax_pence' => $quotation->tax_pence,
            'total_pence' => $quotation->total_pence,
        ]);

        $order = 0;
        foreach ($quotation->lines as $line) {
            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit_price_pence' => $line->unit_price_pence,
                'line_total_pence' => $line->line_total_pence,
                'sort_order' => $order++,
            ]);
        }

        $accounting->syncInvoiceToZoho($invoice->fresh(['client', 'lines', 'offering', 'quotation']));

        return redirect()->route('mis.invoices.show', $invoice)->with('status', 'Invoice created from quotation.');
    }

    public function stripeCheckout(Invoice $invoice, AccountingSyncService $accounting): RedirectResponse
    {
        if ($invoice->status === 'paid') {
            return back()->withErrors(['paid' => 'Invoice already paid.']);
        }

        $settings = CompanySetting::current();
        $secret = $settings->stripe_secret_key ?: config('services.stripe.secret');
        if (! $secret) {
            return back()->withErrors(['stripe' => 'Stripe is not configured.']);
        }

        Stripe::setApiKey($secret);

        $session = Session::create([
            'mode' => 'payment',
            'customer_email' => $invoice->client->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($invoice->currency),
                    'product_data' => ['name' => 'Invoice '.$invoice->number],
                    'unit_amount' => $invoice->total_pence,
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('mis.invoices.show', $invoice),
            'metadata' => [
                'invoice_id' => (string) $invoice->id,
            ],
        ]);

        $invoice->update([
            'stripe_checkout_session_id' => $session->id,
            'status' => 'sent',
            'sent_at' => $invoice->sent_at ?? now(),
        ]);

        $accounting->syncInvoiceToZoho($invoice->fresh(['client', 'lines', 'offering', 'quotation']));

        return redirect()->away($session->url);
    }

    public function syncZoho(Invoice $invoice, ZohoBooksClient $client): RedirectResponse
    {
        if (! $client->isConfigured()) {
            return back()->withErrors(['zoho' => 'Zoho Books is not configured in Company settings.']);
        }

        $result = $client->pushInvoice($invoice);

        return $result['ok']
            ? back()->with('status', $result['message'])
            : back()->withErrors(['zoho' => $result['message']]);
    }
}
