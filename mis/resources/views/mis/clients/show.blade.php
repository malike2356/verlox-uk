@extends('layouts.mis')

@section('title', $client->contact_name)
@section('heading', $client->contact_name)

@section('content')
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('mis.clients.edit', $client) }}" class="rounded-lg border border-gray-300 dark:border-slate-700 px-3 py-2 text-sm">Edit</a>
        @if($canDeleteClient ?? false)
            <form method="post" action="{{ route('mis.clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Delete this client? This cannot be undone.');">@csrf @method('delete')
                <button type="submit" class="rounded-lg border border-red-500/50 text-red-400 px-3 py-2 text-sm">Delete client</button>
            </form>
        @endif
        <a href="{{ route('mis.conversations.create', ['client_id' => $client->id]) }}" class="rounded-lg bg-indigo-100 dark:bg-[#C9A84C]/20 text-indigo-600 dark:text-[#E7D59C] px-3 py-2 text-sm border border-indigo-200 dark:border-[#C9A84C]/30">New message</a>
        <a href="{{ route('mis.documents.create', ['client_id' => $client->id]) }}" class="rounded-lg border border-gray-300 dark:border-slate-700 px-3 py-2 text-sm">Upload document</a>
    </div>

    <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">{{ $client->email }} @if($client->phone) &middot; {{ $client->phone }} @endif
        @if($client->lead_id && $client->lead)
            <span class="text-gray-400">&middot;</span> <a href="{{ route('mis.leads.show', $client->lead) }}" class="text-verlox-accent">Originating lead</a>
        @endif
    </p>

    <nav class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 dark:border-slate-800 pb-3" id="client-hub-tabs" aria-label="Client sections">
        <button type="button" data-hub="profile" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Profile</button>
        <button type="button" data-hub="quotes" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Quotations</button>
        <button type="button" data-hub="contracts" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Contracts</button>
        <button type="button" data-hub="invoices" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Invoices</button>
        <button type="button" data-hub="bookings" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Bookings</button>
        <button type="button" data-hub="va" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">VA</button>
        <button type="button" data-hub="docs" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Documents</button>
        <button type="button" data-hub="messages" class="hub-tab rounded-lg border border-transparent px-3 py-1.5 text-sm text-gray-600 dark:text-slate-400 hover:border-gray-300 dark:hover:border-slate-600">Messages</button>
    </nav>

    <div id="hub-profile" class="hub-panel space-y-4">
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 p-4 text-sm space-y-2">
            @if($client->company_name)<p><span class="text-gray-500 dark:text-slate-500">Company:</span> {{ $client->company_name }}</p>@endif
            @if($client->address)<p class="whitespace-pre-wrap text-gray-700 dark:text-slate-300">{{ $client->address }}</p>@endif
            @if($client->notes)<p class="whitespace-pre-wrap text-gray-700 dark:text-slate-300">{{ $client->notes }}</p>@endif
            @if(!$client->company_name && !$client->address && !$client->notes)
                <p class="text-gray-500 dark:text-slate-500">No extra profile notes.</p>
            @endif
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 p-4 text-sm">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-slate-500 uppercase tracking-wide mb-2">YTD money (this client)</h3>
            <p class="text-gray-900 dark:text-white">Invoiced: <span class="font-mono">£{{ number_format($invoicedYtdPence / 100, 2) }}</span></p>
            <p class="text-gray-900 dark:text-white">Collected: <span class="font-mono">£{{ number_format($collectedYtdPence / 100, 2) }}</span></p>
        </div>
    </div>

    <div id="hub-quotes" class="hub-panel hidden rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Quotations</h2>
        <ul class="text-sm space-y-1">
            @forelse ($client->quotations as $q)
                <li><a href="{{ route('mis.quotations.show', $q) }}" class="text-verlox-accent">{{ $q->number }}</a> <span class="text-gray-500 dark:text-slate-500">{{ $q->status }}</span></li>
            @empty
                <li class="text-gray-500 dark:text-slate-500">None</li>
            @endforelse
        </ul>
    </div>

    <div id="hub-contracts" class="hub-panel hidden rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Contracts</h2>
        <ul class="text-sm space-y-1">
            @forelse ($client->contracts as $c)
                <li><a href="{{ route('mis.contracts.show', $c) }}" class="text-verlox-accent">{{ $c->number }}</a> <span class="text-gray-500 dark:text-slate-500">{{ $c->status }}</span></li>
            @empty
                <li class="text-gray-500 dark:text-slate-500">None</li>
            @endforelse
        </ul>
    </div>

    <div id="hub-invoices" class="hub-panel hidden rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
        <div class="mb-3">@include('mis.partials.zoho-accounting-strip', ['compact' => true])</div>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Invoices</h2>
        <ul class="text-sm space-y-1">
            @forelse ($client->invoices as $inv)
                <li><a href="{{ route('mis.invoices.show', $inv) }}" class="text-verlox-accent">{{ $inv->number }}</a> <span class="text-gray-500 dark:text-slate-500">{{ $inv->status }}</span></li>
            @empty
                <li class="text-gray-500 dark:text-slate-500">None</li>
            @endforelse
        </ul>
    </div>

    <div id="hub-bookings" class="hub-panel hidden rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Bookings (same email)</h2>
        <ul class="text-sm space-y-2">
            @forelse ($bookings as $b)
                <li class="flex flex-wrap justify-between gap-2">
                    <a href="{{ route('mis.bookings.show', $b) }}" class="text-verlox-accent">{{ $b->starts_at->timezone($b->timezone)->format('D j M Y H:i') }}</a>
                    <span class="text-xs text-gray-500 dark:text-slate-500">{{ $b->status }}</span>
                </li>
            @empty
                <li class="text-gray-500 dark:text-slate-500">No bookings for this email.</li>
            @endforelse
        </ul>
    </div>

    <div id="hub-va" class="hub-panel hidden space-y-4">
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 p-4 text-sm">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">VA snapshot</h2>
            <p class="text-gray-700 dark:text-slate-300">Approved hours this month (all VA accounts): <span class="font-mono font-semibold">{{ number_format($vaApprovedHoursMonth, 2) }}</span> h</p>
            <p class="text-xs text-gray-500 dark:text-slate-500 mt-2">Compare to retainer / invoice lines for margin; detailed P&amp;L stays in finance.</p>
        </div>
        @if(count($vaBurnDown))
            <div class="rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Retainer burn-down (this month)</h3>
                <ul class="space-y-4 text-sm">
                    @foreach ($vaBurnDown as $row)
                        @php
                            $cap = $row['cap'];
                            $used = $row['used'];
                            $pct = $cap > 0 ? min(100, round(100 * $used / $cap)) : null;
                        @endphp
                        <li class="border-b border-gray-100 dark:border-slate-800 pb-3 last:border-0">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $row['engagement']->assistant?->full_name ?? 'Engagement #'.$row['engagement']->id }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-500">{{ $row['account']->company_name ?? $row['account']->contact_name }}</p>
                            <p class="mt-1 font-mono text-gray-800 dark:text-slate-200">{{ number_format($used, 2) }} / {{ $cap > 0 ? number_format($cap, 2).' h' : '-' }} @if($cap > 0) ({{ number_format($pct, 0) }}%) @endif</p>
                            @if($cap > 0)
                                <div class="mt-1 h-2 rounded-full bg-gray-200 dark:bg-slate-800 overflow-hidden">
                                    <div class="h-full rounded-full bg-verlox-accent" style="width: {{ $pct }}%"></div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">VA client accounts</h3>
            <ul class="text-sm space-y-1">
                @forelse ($client->vaClientAccounts as $acc)
                    <li>
                        <a href="{{ route('mis.va.client-accounts.show', $acc) }}" class="text-verlox-accent">{{ $acc->company_name ?: $acc->contact_name }}</a>
                        <span class="text-gray-500 dark:text-slate-500 text-xs"> · {{ $acc->status }}</span>
                    </li>
                @empty
                    <li class="text-gray-500 dark:text-slate-500">No VA accounts linked.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div id="hub-docs" class="hub-panel hidden rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Documents</h2>
        <ul class="text-sm space-y-1">
            @forelse ($client->documents as $d)
                <li><span class="text-gray-900 dark:text-white">{{ $d->title }}</span> <span class="text-xs text-gray-500">#{{ $d->id }}</span></li>
            @empty
                <li class="text-gray-500 dark:text-slate-500">None</li>
            @endforelse
        </ul>
        <a href="{{ route('mis.documents.create', ['client_id' => $client->id]) }}" class="text-xs text-verlox-accent mt-2 inline-block">Upload</a>
    </div>

    <div id="hub-messages" class="hub-panel hidden rounded-2xl border border-gray-200 dark:border-slate-800 p-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Conversations</h2>
        <ul class="text-sm space-y-1">
            @forelse ($client->conversations as $c)
                <li><a href="{{ route('mis.conversations.show', $c) }}" class="text-verlox-accent">{{ $c->subject }}</a></li>
            @empty
                <li class="text-gray-500 dark:text-slate-500">None</li>
            @endforelse
        </ul>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var panels = document.querySelectorAll('.hub-panel');
        var tabs = document.querySelectorAll('.hub-tab');
        function showHub(id) {
            panels.forEach(function (p) {
                p.classList.toggle('hidden', p.id !== 'hub-' + id);
            });
            tabs.forEach(function (t) {
                var on = t.getAttribute('data-hub') === id;
                t.classList.toggle('bg-gray-200', on);
                t.classList.toggle('dark:bg-slate-800', on);
                t.classList.toggle('text-gray-900', on);
                t.classList.toggle('dark:text-white', on);
                t.classList.toggle('border-gray-300', on);
                t.classList.toggle('dark:border-slate-600', on);
            });
        }
        var hash = (location.hash || '#profile').replace(/^#/, '') || 'profile';
        var valid = ['profile','quotes','contracts','invoices','bookings','va','docs','messages'];
        if (valid.indexOf(hash) === -1) hash = 'profile';
        showHub(hash);
        tabs.forEach(function (t) {
            t.addEventListener('click', function () {
                var id = t.getAttribute('data-hub');
                showHub(id);
                history.replaceState(null, '', '#' + id);
            });
        });
        window.addEventListener('hashchange', function () {
            var h = (location.hash || '#profile').replace(/^#/, '') || 'profile';
            if (valid.indexOf(h) !== -1) showHub(h);
        });
    });
    </script>
    @endpush
@endsection
