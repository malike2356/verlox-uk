@extends('layouts.mis')

@section('title', 'Quotations')
@section('heading', 'Quotations')

@section('content')
    @include('mis.partials.zoho-accounting-strip')
    <a href="{{ route('mis.quotations.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New quotation</a>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Number</th>
                <th class="px-3 py-2">Client</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2 text-right">Total</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($quotations as $q)
                <tr>
                    <td class="px-3 py-2"><a href="{{ route('mis.quotations.show', $q) }}" class="text-verlox-accent">{{ $q->number }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $q->client->contact_name }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $q->status }}</td>
                    <td class="px-3 py-2 text-right font-mono text-gray-700 dark:text-slate-300">{{ number_format($q->total_pence / 100, 2) }} {{ $q->currency }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $quotations->links() }}</div>
@endsection
