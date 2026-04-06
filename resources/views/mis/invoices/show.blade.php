@extends('layouts.mis')

@section('title', $invoice->number)
@section('heading', $invoice->number)

@section('content')
    @php
        $zohoConfigured = app(\App\Services\ZohoBooksClient::class)->isConfigured();
        $invLogo = \App\Models\CompanySetting::current()->invoiceLogoPublicUrl();
    @endphp
    @if($invLogo)
        <div class="mb-4 flex justify-end">
            <img src="{{ $invLogo }}" alt="" class="max-h-14 max-w-[220px] object-contain object-right rounded border border-gray-200 dark:border-slate-700 bg-white/5 p-2">
        </div>
    @endif
    <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">
        Client: <a href="{{ route('mis.clients.show', $invoice->client) }}" class="text-verlox-accent">{{ $invoice->client->contact_name }}</a>
        @if($invoice->offering)
            <span class="mx-1 text-gray-400">·</span>
            {{ __('Catalogue') }}:
            @if(auth()->user()->isMisFinanceOnly())
                <span class="text-gray-700 dark:text-slate-300">{{ $invoice->offering->name }}</span>
            @else
                <a href="{{ route('mis.offerings.edit', $invoice->offering) }}" class="text-verlox-accent">{{ $invoice->offering->name }}</a>
            @endif
        @elseif($invoice->quotation)
            <span class="mx-1 text-gray-400">·</span>
            {{ __('Quotation') }} {{ $invoice->quotation->number }}
        @else
            <span class="mx-1 text-gray-400">·</span>
            {{ __('Ad hoc invoice') }}
        @endif
    </p>
    <div class="mb-4">@include('mis.partials.zoho-accounting-strip', ['compact' => true])</div>
    <div class="mb-4 flex flex-wrap items-center gap-3">
        @if($invoice->status !== 'paid')
            <form method="post" action="{{ route('mis.invoices.stripe-checkout', $invoice) }}" class="inline">@csrf
                <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">Stripe checkout link</button>
            </form>
        @endif
        @if($zohoConfigured)
            <form method="post" action="{{ route('mis.invoices.sync-zoho', $invoice) }}" class="inline">@csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 dark:border-slate-600 dark:text-slate-300" title="{{ $invoice->zoho_invoice_id ? 'Re-sync to Zoho Books' : 'Push to Zoho Books' }}">
                    <i class="fa-solid fa-cloud-arrow-up {{ $invoice->zoho_invoice_id ? 'text-emerald-500' : 'text-gray-400 dark:text-slate-500' }}"></i>
                    {{ $invoice->zoho_invoice_id ? 'Synced to Zoho' : 'Sync to Zoho' }}
                </button>
            </form>
        @endif
    </div>
    <div class="rounded-2xl border border-gray-200 dark:border-slate-800 overflow-hidden mb-4">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs text-gray-500 dark:text-slate-500">
            <tr><th class="px-3 py-2">Description</th><th class="px-3 py-2">Qty</th><th class="px-3 py-2 text-right">Line</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($invoice->lines as $line)
                <tr>
                    <td class="px-3 py-2">{{ $line->description }}</td>
                    <td class="px-3 py-2">{{ $line->quantity }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($line->line_total_pence / 100, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-right text-sm font-mono text-gray-700 dark:text-slate-300 space-y-1 mb-6">
        <p>Subtotal: {{ number_format($invoice->subtotal_pence / 100, 2) }}</p>
        <p>VAT: {{ number_format($invoice->tax_pence / 100, 2) }}</p>
        <p class="text-gray-900 dark:text-white font-semibold">Total: {{ number_format($invoice->total_pence / 100, 2) }} {{ $invoice->currency }}</p>
        <p>Paid: {{ number_format($invoice->paid_pence / 100, 2) }}</p>
        <p>Balance: {{ number_format($invoice->balanceOutstandingPence() / 100, 2) }}</p>
        <p>Status: <span class="font-semibold">{{ $invoice->status }}</span></p>
        @if($invoice->sent_at)<p class="text-xs text-gray-500 dark:text-slate-500">Sent {{ $invoice->sent_at->format('Y-m-d H:i') }}</p>@endif
        @if($invoice->last_reminder_at)<p class="text-xs text-gray-500 dark:text-slate-500">Last reminder {{ $invoice->last_reminder_at->format('Y-m-d') }}</p>@endif
        @if($invoice->next_reminder_at)<p class="text-xs text-gray-500 dark:text-slate-500">Next reminder {{ $invoice->next_reminder_at->format('Y-m-d') }}</p>@endif
        @if($invoice->written_off_at)<p class="text-xs text-amber-700 dark:text-amber-300">Written off {{ $invoice->written_off_at->format('Y-m-d') }}</p>@endif
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/40 p-4 space-y-4 text-sm max-w-md">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Invoice lifecycle</h3>
        <form method="post" action="{{ route('mis.invoices.status', $invoice) }}" class="space-y-2">
            @csrf @method('patch')
            <label class="text-xs text-gray-500 dark:text-slate-500">Set status</label>
            <select name="status" class="w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach (\App\Models\Invoice::STATUSES as $st)
                    <option value="{{ $st }}" @selected($invoice->status === $st)>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-gray-200 dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white">Update status</button>
        </form>
        @if($invoice->status !== 'paid' && $invoice->status !== 'written_off')
            <form method="post" action="{{ route('mis.invoices.reminder', $invoice) }}">@csrf
                <button type="submit" class="rounded-lg border border-gray-300 dark:border-slate-600 px-4 py-2 text-sm">Log reminder (schedule +7 days)</button>
            </form>
        @endif
        @if($invoice->status === 'draft' && $invoice->paid_pence === 0 && $invoice->payments->isEmpty())
            <form method="post" action="{{ route('mis.invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this draft invoice?');">@csrf @method('delete')
                <button type="submit" class="rounded-lg border border-red-500/50 text-red-400 px-4 py-2 text-sm">Delete draft invoice</button>
            </form>
        @endif
    </div>
@endsection
