@extends('layouts.mis')

@section('title', 'VA division')
@section('heading', 'VA division')

@section('content')
    @include('mis.partials.zoho-accounting-strip')
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('mis.va.client-accounts.create') }}" class="rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New VA client</a>
        <a href="{{ route('mis.va.assistants.create') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm dark:border-slate-700">New assistant</a>
        <a href="{{ route('mis.va.time-logs.create') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm dark:border-slate-700">Log time</a>
        <a href="{{ route('mis.va.time-logs.index', ['pending' => 1]) }}" class="rounded-xl border border-amber-200 px-4 py-2 text-sm text-amber-800 dark:border-amber-800 dark:text-amber-200">Pending approvals @if($pendingTimeLogs > 0)({{ $pendingTimeLogs }})@endif</a>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white/80 p-4 dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-xs uppercase text-gray-500 dark:text-slate-500">Active engagements</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $activeEngagements }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white/80 p-4 dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-xs uppercase text-gray-500 dark:text-slate-500">Active VA clients</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $activeVaClients }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white/80 p-4 dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-xs uppercase text-gray-500 dark:text-slate-500">Assistants (active)</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $assistantsActive }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white/80 p-4 dark:border-slate-800 dark:bg-slate-900/40">
            <p class="text-xs uppercase text-gray-500 dark:text-slate-500">Approved hours (this month)</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($hoursThisMonth, 1) }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-slate-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-slate-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent engagements</h2>
            <p class="text-xs text-gray-500 dark:text-slate-500">Client ↔ VA assignments (draft &amp; active)</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left text-xs text-gray-500 dark:bg-slate-900 dark:text-slate-500">
                <tr>
                    <th class="px-3 py-2">Client</th>
                    <th class="px-3 py-2">Assistant</th>
                    <th class="px-3 py-2">Tier</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2 text-right">Client £/mo</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @forelse ($recentEngagements as $e)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('mis.va.client-accounts.show', $e->clientAccount) }}" class="text-verlox-accent">{{ $e->clientAccount->company_name }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $e->assistant->full_name }}</td>
                        <td class="px-3 py-2">{{ $e->tier }}</td>
                        <td class="px-3 py-2">{{ $e->status }}</td>
                        <td class="px-3 py-2 text-right font-mono">{{ number_format($e->client_rate_monthly_gbp, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500 dark:text-slate-500">No engagements yet. Create a VA client account and add an engagement.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
