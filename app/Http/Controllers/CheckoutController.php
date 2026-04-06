<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Offering;
use App\Services\AccountingSyncService;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function show(Offering $offering): View
    {
        if ($offering->type !== 'purchase' || ! $offering->price_pence) {
            abort(404);
        }

        return view('checkout.show', ['offering' => $offering]);
    }

    public function start(Request $request, Offering $offering, DocumentNumberService $numbers, AccountingSyncService $accounting): RedirectResponse
    {
        if ($offering->type !== 'purchase' || ! $offering->price_pence) {
            abort(404);
        }

        $data = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $settings = CompanySetting::current();
        $secret = $settings->stripe_secret_key ?: config('services.stripe.secret');
        if (! $secret) {
            return back()->withErrors(['payment' => 'Stripe is not configured in settings.'])->withInput();
        }

        Stripe::setApiKey($secret);

        $client = Client::query()->firstOrCreate(
            ['email' => $data['email']],
            [
                'contact_name' => $data['contact_name'],
                'company_name' => $data['company_name'] ?? null,
            ]
        );
        $client->update([
            'contact_name' => $data['contact_name'],
            'company_name' => $data['company_name'] ?? $client->company_name,
        ]);

        $subtotal = (int) $offering->price_pence;
        $tax = (int) round($subtotal * 0.2);
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'number' => $numbers->nextInvoiceNumber(),
            'client_id' => $client->id,
            'offering_id' => $offering->id,
            'status' => 'draft',
            'currency' => $offering->currency,
            'issued_at' => now(),
            'due_at' => now()->addDays(14),
            'subtotal_pence' => $subtotal,
            'tax_pence' => $tax,
            'total_pence' => $total,
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => $offering->name,
            'quantity' => 1,
            'unit_price_pence' => $subtotal,
            'line_total_pence' => $subtotal,
        ]);

        $session = Session::create([
            'mode' => 'payment',
            'customer_email' => $data['email'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($offering->currency),
                    'product_data' => ['name' => $offering->name],
                    'unit_amount' => $total,
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('marketing.home'),
            'metadata' => [
                'invoice_id' => (string) $invoice->id,
                'offering_id' => (string) $offering->id,
            ],
        ]);

        $invoice->update([
            'stripe_checkout_session_id' => $session->id,
            'status' => 'sent',
        ]);

        $accounting->syncInvoiceToZoho($invoice->fresh(['client', 'lines', 'offering', 'quotation']));

        return redirect()->away($session->url);
    }

    public function success(Request $request): View
    {
        return view('checkout.success', [
            'sessionId' => $request->query('session_id'),
        ]);
    }
}
