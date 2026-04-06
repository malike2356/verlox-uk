@extends('layouts.mis')

@section('title', $account->company_name)
@section('heading', $account->company_name)

@section('content')
    <div class="mb-6 flex flex-wrap gap-3 text-sm">
        <a href="{{ route('mis.va.client-accounts.edit', $account) }}" class="text-verlox-accent">Edit account</a>
        @if($account->misClient)
            <span class="text-gray-500 dark:text-slate-500">|</span>
            <a href="{{ route('mis.clients.show', $account->misClient) }}" class="text-verlox-accent">MIS client record</a>
        @endif
        <span class="text-gray-500 dark:text-slate-500">|</span>
        <a href="{{ route('mis.va.client-accounts.engagements.create', $account) }}" class="font-medium text-verlox-accent">+ New engagement</a>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 text-sm">
        <div><span class="text-gray-500 dark:text-slate-500">Status</span><p class="font-medium text-gray-900 dark:text-white">{{ $account->status }}</p></div>
        <div><span class="text-gray-500 dark:text-slate-500">Tier</span><p class="font-medium text-gray-900 dark:text-white">{{ $account->tier }}</p></div>
        <div><span class="text-gray-500 dark:text-slate-500">Monthly rate</span><p class="font-mono text-gray-900 dark:text-white">£{{ number_format($account->monthly_rate_gbp, 2) }}</p></div>
        <div><span class="text-gray-500 dark:text-slate-500">Hours / mo</span><p class="font-medium text-gray-900 dark:text-white">{{ $account->hours_included }}</p></div>
    </div>

    <div class="mb-8 rounded-2xl border border-gray-200 dark:border-slate-800 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold dark:border-slate-800 dark:bg-slate-900">Engagements</div>
        <table class="min-w-full text-sm">
            <thead class="text-left text-xs text-gray-500 dark:text-slate-500">
            <tr><th class="px-3 py-2">Assistant</th><th class="px-3 py-2">Hours/mo</th><th class="px-3 py-2">Status</th><th class="px-3 py-2 text-right">Client £/mo</th><th class="px-3 py-2 text-right">Est. VA cost</th><th class="px-3 py-2 text-right">Est. margin</th><th class="px-3 py-2"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse ($account->engagements as $e)
                @php
                    $cost = (float) $e->estimatedMonthlyVaCost();
                    $margin = (float) $e->estimatedGrossProfit();
                @endphp
                <tr>
                    <td class="px-3 py-2">{{ $e->assistant->full_name }}</td>
                    <td class="px-3 py-2">{{ $e->hours_per_month }}</td>
                    <td class="px-3 py-2">{{ $e->status }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($e->client_rate_monthly_gbp, 2) }}</td>
                    <td class="px-3 py-2 text-right font-mono text-gray-600 dark:text-slate-400">{{ number_format($cost, 2) }}</td>
                    <td class="px-3 py-2 text-right font-mono {{ $margin >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600' }}">{{ number_format($margin, 2) }}</td>
                    <td class="px-3 py-2"><a href="{{ route('mis.va.engagements.edit', $e) }}" class="text-verlox-accent text-xs">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-3 py-4 text-gray-500 dark:text-slate-500">No engagements - assign a VA.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Communication log</h3>
            <form method="post" action="{{ route('mis.va.client-accounts.communications.store', $account) }}" class="mb-4 space-y-2 text-sm">
                @csrf
                <select name="type" class="w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-2 py-1.5">
                    <option value="note">Note</option>
                    <option value="email">Email</option>
                    <option value="call">Call</option>
                    <option value="meeting">Meeting</option>
                    <option value="slack">Slack</option>
                </select>
                <input name="summary" placeholder="Summary" required class="w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-2 py-1.5">
                <textarea name="details" placeholder="Details (optional)" rows="2" class="w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-2 py-1.5 text-xs"></textarea>
                <button type="submit" class="rounded-lg bg-verlox-accent px-3 py-1.5 text-xs font-semibold text-on-verlox-accent">Add entry</button>
            </form>
            <ul class="space-y-2 text-xs text-gray-600 dark:text-slate-400">
                @foreach ($account->communicationLogs as $log)
                    <li class="border-b border-gray-100 pb-2 dark:border-slate-800">
                        <span class="font-medium text-gray-800 dark:text-slate-200">{{ $log->type }}</span>
                        · {{ $log->summary }}
                        <span class="text-gray-400">({{ $log->created_by }}, {{ $log->created_at->format('Y-m-d') }})</span>
                        @if($log->details)<p class="mt-1 text-gray-500">{{ $log->details }}</p>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
