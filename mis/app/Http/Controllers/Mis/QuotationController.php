<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Services\AuditLogger;
use App\Services\ContractRenderer;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::query()->with('client')->orderByDesc('id')->paginate(20);

        return view('mis.quotations.index', compact('quotations'));
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

        return view('mis.quotations.create', compact('clients', 'leadsForLink'));
    }

    public function store(Request $request, DocumentNumberService $numbers): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'valid_until' => ['nullable', 'date'],
            'terms' => ['nullable', 'string', 'max:20000'],
        ]);

        $client = Client::query()->findOrFail($data['client_id']);
        $leadId = $data['lead_id'] ?? null;
        if ($leadId !== null) {
            $lead = Lead::query()->findOrFail($leadId);
            if (! $lead->client || $lead->client->id !== $client->id) {
                return back()->withErrors(['lead_id' => 'That lead is not linked to the selected client.'])->withInput();
            }
        }

        $q = Quotation::create([
            'number' => $numbers->nextQuotationNumber(),
            'client_id' => $data['client_id'],
            'lead_id' => $leadId,
            'status' => 'draft',
            'valid_until' => $data['valid_until'] ?? null,
            'currency' => 'GBP',
            'terms' => $data['terms'] ?? null,
        ]);

        return redirect()->route('mis.quotations.show', $q)->with('status', 'Quotation created.');
    }

    public function edit(Quotation $quotation): View
    {
        if ($quotation->status === 'accepted') {
            abort(403, 'Accepted quotations cannot be edited here.');
        }

        $quotation->load('client');
        $leadsForLink = Lead::query()
            ->with('client')
            ->whereHas('client', fn ($q) => $q->where('id', $quotation->client_id))
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('mis.quotations.edit', compact('quotation', 'leadsForLink'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        if ($quotation->status === 'accepted') {
            return back()->with('error', 'Accepted quotations cannot be edited.');
        }

        $data = $request->validate([
            'valid_until' => ['nullable', 'date'],
            'terms' => ['nullable', 'string', 'max:20000'],
            'lead_id' => ['nullable', 'exists:leads,id'],
        ]);

        $leadId = $data['lead_id'] ?? null;
        if ($leadId !== null) {
            $lead = Lead::query()->findOrFail($leadId);
            if (! $lead->client || $lead->client->id !== $quotation->client_id) {
                return back()->withErrors(['lead_id' => 'That lead is not linked to this quotation’s client.'])->withInput();
            }
        }

        $quotation->update([
            'valid_until' => $data['valid_until'] ?? null,
            'terms' => $data['terms'] ?? null,
            'lead_id' => $leadId,
        ]);

        return redirect()->route('mis.quotations.show', $quotation)->with('status', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status !== 'draft') {
            return redirect()->route('mis.quotations.show', $quotation)
                ->with('error', 'Only draft quotations can be deleted.');
        }
        if (Contract::query()->where('quotation_id', $quotation->id)->exists()) {
            return redirect()->route('mis.quotations.show', $quotation)
                ->with('error', 'This quotation has a linked contract; cannot delete.');
        }

        $quotation->lines()->delete();
        $quotation->delete();

        return redirect()->route('mis.quotations.index')->with('status', 'Quotation deleted.');
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load(['client', 'lines', 'lead']);

        return view('mis.quotations.show', compact('quotation'));
    }

    public function addLine(Request $request, Quotation $quotation): RedirectResponse
    {
        if ($quotation->status !== 'draft') {
            return back()->with('error', 'Lines can only be changed while the quotation is a draft.');
        }

        $data = $request->validate([
            'description' => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price_pence' => ['required', 'integer', 'min:0'],
        ]);
        $lineTotal = (int) round($data['quantity'] * $data['unit_price_pence']);
        QuotationLine::create([
            'quotation_id' => $quotation->id,
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'unit_price_pence' => $data['unit_price_pence'],
            'line_total_pence' => $lineTotal,
        ]);
        $quotation->recalculateTotals();

        return back()->with('status', 'Line added.');
    }

    public function destroyLine(Quotation $quotation, QuotationLine $line): RedirectResponse
    {
        if ($quotation->status !== 'draft') {
            return back()->with('error', 'Lines can only be changed while the quotation is a draft.');
        }

        if ($line->quotation_id !== $quotation->id) {
            abort(404);
        }
        $line->delete();
        $quotation->recalculateTotals();

        return back()->with('status', 'Line removed.');
    }

    public function updateStatus(Request $request, Quotation $quotation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,sent,accepted,declined,expired'],
        ]);
        $old = $quotation->only(['status']);
        $quotation->update(['status' => $data['status']]);
        AuditLogger::record($quotation, 'status_changed', $old, $quotation->only(['status']), $request);

        return back()->with('status', 'Status updated.');
    }

    public function accept(
        Quotation $quotation,
        ContractRenderer $renderer,
        DocumentNumberService $numbers
    ): RedirectResponse {
        if (Contract::query()->where('quotation_id', $quotation->id)->exists()) {
            return back()->withErrors(['contract' => 'A contract already exists for this quotation.']);
        }

        $template = ContractTemplate::query()->where('is_default', true)->first();
        if (! $template) {
            return back()->withErrors(['template' => 'Set a default contract template first.']);
        }

        $quotation->load('client');
        $body = $renderer->renderFromTemplate($template, $quotation->client, $quotation);

        Contract::create([
            'number' => $numbers->nextContractNumber(),
            'contract_template_id' => $template->id,
            'quotation_id' => $quotation->id,
            'client_id' => $quotation->client_id,
            'status' => 'sent',
            'body_snapshot' => $body,
            'effective_from' => now(),
        ]);

        $quotation->update(['status' => 'accepted']);

        return redirect()->route('mis.contracts.index')->with('status', 'Contract generated from quotation.');
    }
}
