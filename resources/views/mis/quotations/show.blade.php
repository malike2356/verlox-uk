@extends('layouts.mis')

@section('title', $quotation->number)
@section('heading', $quotation->number)

@section('content')
    <div class="mb-4">@include('mis.partials.zoho-accounting-strip', ['compact' => true])</div>
    <div class="flex flex-wrap gap-2 mb-4 text-sm">
        @if($quotation->status !== 'accepted')
            <a href="{{ route('mis.quotations.edit', $quotation) }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-3 py-1 text-gray-900 dark:text-slate-200">Edit details</a>
        @endif
        @if($quotation->status === 'draft')
            <form method="post" action="{{ route('mis.quotations.destroy', $quotation) }}" class="inline" onsubmit="return confirm('Delete this draft quotation?');">@csrf @method('delete')
                <button type="submit" class="rounded-lg border border-red-500/50 text-red-400 px-3 py-1">Delete draft</button>
            </form>
        @endif
        <form method="post" action="{{ route('mis.quotations.status', $quotation) }}">@csrf @method('patch')
            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-2 py-1 text-gray-900 dark:text-white">
                @foreach (['draft','sent','accepted','declined','expired'] as $s)
                    <option value="{{ $s }}" @selected($quotation->status === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </form>
        @if($quotation->status === 'accepted')
            <form method="post" action="{{ route('mis.invoices.from-quotation', $quotation) }}">@csrf
                <button type="submit" class="rounded-lg bg-emerald-500/20 text-emerald-300 px-3 py-1 border border-emerald-500/40">Create invoice</button>
            </form>
        @endif
        @if($quotation->status !== 'accepted')
            <form method="post" action="{{ route('mis.quotations.accept', $quotation) }}" onsubmit="return confirm('Generate contract and mark accepted?');">@csrf
                <button type="submit" class="rounded-lg bg-indigo-100 dark:bg-[#C9A84C]/20 text-indigo-600 dark:text-[#E7D59C] px-3 py-1 border border-indigo-200 dark:border-[#C9A84C]/40">Accept and generate contract</button>
            </form>
        @endif
    </div>

    <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">
        Client: <a href="{{ route('mis.clients.show', $quotation->client) }}" class="text-verlox-accent">{{ $quotation->client->contact_name }}</a>
        @if($quotation->lead_id && $quotation->lead)
            <span class="text-gray-400">·</span> Lead: <a href="{{ route('mis.leads.show', $quotation->lead) }}" class="text-verlox-accent">{{ $quotation->lead->contact_name }}</a>
        @endif
    </p>

    <div class="rounded-2xl border border-gray-200 dark:border-slate-800 overflow-hidden mb-6">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs text-gray-500 dark:text-slate-500">
            <tr><th class="px-3 py-2">Description</th><th class="px-3 py-2">Qty</th><th class="px-3 py-2 text-right">Unit</th><th class="px-3 py-2 text-right">Line</th><th></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($quotation->lines as $line)
                <tr>
                    <td class="px-3 py-2">{{ $line->description }}</td>
                    <td class="px-3 py-2">{{ $line->quantity }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($line->unit_price_pence / 100, 2) }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($line->line_total_pence / 100, 2) }}</td>
                    <td class="px-3 py-2">
                        @if($quotation->status === 'draft')
                            <form method="post" action="{{ route('mis.quotations.lines.destroy', [$quotation, $line]) }}">@csrf @method('delete')
                                <button type="submit" class="text-red-400 text-xs">Remove</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($quotation->status === 'draft')
        <form method="post" action="{{ route('mis.quotations.lines.store', $quotation) }}" class="grid sm:grid-cols-4 gap-2 text-sm mb-6">
            @csrf
            <input name="description" placeholder="Description" required class="rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white sm:col-span-2">
            <input name="quantity" type="number" step="0.01" value="1" class="rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            <input name="unit_price_pence" type="number" placeholder="Pence" required class="rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            <button class="rounded-lg bg-gray-200 text-gray-900 dark:bg-slate-800 dark:text-white font-semibold px-3 py-2 sm:col-span-4">Add line</button>
        </form>
    @endif

    <div class="text-right text-sm space-y-1 font-mono text-gray-700 dark:text-slate-300">
        <p>Subtotal: {{ number_format($quotation->subtotal_pence / 100, 2) }} {{ $quotation->currency }}</p>
        <p>VAT (20%): {{ number_format($quotation->tax_pence / 100, 2) }} {{ $quotation->currency }}</p>
        <p class="text-gray-900 dark:text-white font-semibold">Total: {{ number_format($quotation->total_pence / 100, 2) }} {{ $quotation->currency }}</p>
    </div>
@endsection
