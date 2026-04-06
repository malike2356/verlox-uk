@extends('layouts.mis')

@section('title', 'Accounts receivable')
@section('heading', 'Accounts receivable')

@section('content')
    <p class="mb-4 text-sm text-gray-600 dark:text-slate-400">
        Open invoices (sent, partial, overdue) excluding written off.
        <a href="{{ route('mis.invoices.index') }}" class="text-verlox-accent hover:underline">All invoices</a>
    </p>

    @php
        $totalOutstanding = $invoices->sum(fn ($inv) => $inv->balanceOutstandingPence());
        $byBucket = ['current' => 0, 'd30' => 0, 'd60' => 0, 'd90' => 0];
        foreach ($invoices as $inv) {
            $bal = $inv->balanceOutstandingPence();
            if ($bal <= 0) {
                continue;
            }
            $due = $inv->due_at;
            if (! $due || $due->isFuture()) {
                $byBucket['current'] += $bal;
            } else {
                $days = (int) $due->diffInDays(now());
                if ($days <= 30) {
                    $byBucket['d30'] += $bal;
                } elseif ($days <= 60) {
                    $byBucket['d60'] += $bal;
                } else {
                    $byBucket['d90'] += $bal;
                }
            }
        }
    @endphp

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 mb-6 text-sm">
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/40 p-3">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase">Total outstanding</p>
            <p class="mt-1 text-lg font-semibold font-mono text-gray-900 dark:text-white">£{{ number_format($totalOutstanding / 100, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/40 p-3">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase">Not yet due</p>
            <p class="mt-1 font-mono text-gray-900 dark:text-white">£{{ number_format($byBucket['current'] / 100, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/40 p-3">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase">1–30 days</p>
            <p class="mt-1 font-mono text-amber-700 dark:text-amber-300">£{{ number_format($byBucket['d30'] / 100, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/40 p-3">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase">31–60 days</p>
            <p class="mt-1 font-mono text-orange-700 dark:text-orange-300">£{{ number_format($byBucket['d60'] / 100, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/40 p-3">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase">61+ days</p>
            <p class="mt-1 font-mono text-red-700 dark:text-red-300">£{{ number_format($byBucket['d90'] / 100, 2) }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-slate-800 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Invoice</th>
                <th class="px-3 py-2">Client</th>
                <th class="px-3 py-2">Due</th>
                <th class="px-3 py-2 text-right">Total</th>
                <th class="px-3 py-2 text-right">Paid</th>
                <th class="px-3 py-2 text-right">Balance</th>
                <th class="px-3 py-2">Age</th>
                <th class="px-3 py-2">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse ($invoices as $inv)
                @php
                    $bal = $inv->balanceOutstandingPence();
                    $due = $inv->due_at;
                    $ageLabel = '-';
                    if ($due) {
                        if ($due->isFuture()) {
                            $ageLabel = 'Due in '.now()->diffInDays($due).'d';
                        } else {
                            $ageLabel = (int) $due->diffInDays(now()).'d overdue';
                        }
                    }
                @endphp
                <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40">
                    <td class="px-3 py-2">
                        <a href="{{ route('mis.invoices.show', $inv) }}" class="text-verlox-accent font-medium">{{ $inv->number }}</a>
                    </td>
                    <td class="px-3 py-2">
                        <a href="{{ route('mis.clients.show', $inv->client) }}" class="text-verlox-accent">{{ $inv->client->contact_name }}</a>
                    </td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $due ? $due->format('Y-m-d') : '-' }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($inv->total_pence / 100, 2) }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($inv->paid_pence / 100, 2) }}</td>
                    <td class="px-3 py-2 text-right font-mono font-semibold">{{ number_format($bal / 100, 2) }}</td>
                    <td class="px-3 py-2 text-xs">{{ $ageLabel }}</td>
                    <td class="px-3 py-2">{{ $inv->status }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500 dark:text-slate-500">No open receivables.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
