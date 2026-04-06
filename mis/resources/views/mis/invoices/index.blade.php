@extends('layouts.mis')

@section('title', 'Invoices')
@section('heading', 'Invoices')

@section('content')
    @php $zohoConfigured = app(\App\Services\ZohoBooksClient::class)->isConfigured(); @endphp
    @include('mis.partials.zoho-accounting-strip')
    <div class="mb-4 flex flex-wrap items-center justify-end gap-2">
        <a href="{{ route('mis.invoices.create') }}" class="inline-flex items-center rounded-lg bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New invoice</a>
    </div>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Number</th>
                <th class="px-3 py-2">Client</th>
                <th class="px-3 py-2">Status</th>
                @if($zohoConfigured)
                    <th class="px-3 py-2 text-center">Zoho</th>
                @endif
                <th class="px-3 py-2 text-right">Total</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($invoices as $inv)
                <tr>
                    <td class="px-3 py-2"><a href="{{ route('mis.invoices.show', $inv) }}" class="text-verlox-accent">{{ $inv->number }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $inv->client->contact_name }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $inv->status }}</td>
                    @if($zohoConfigured)
                        <td class="px-3 py-2 text-center text-lg" title="{{ $inv->zoho_invoice_id ? 'In Zoho Books' : 'Not synced' }}">
                            @if($inv->zoho_invoice_id)
                                <span class="text-emerald-500" aria-label="Synced">&#10003;</span>
                            @else
                                <span class="text-gray-300 dark:text-slate-600" aria-label="Not synced">&mdash;</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($inv->total_pence / 100, 2) }} {{ $inv->currency }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
@endsection
