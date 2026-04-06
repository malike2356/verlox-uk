@extends('layouts.mis')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
@endpush

@section('content')
    <p class="mb-4 text-xs text-gray-500 dark:text-slate-500">
        <a href="{{ route('mis.network.index') }}" class="font-medium text-verlox-accent hover:underline">{{ __('MIS network map') }}</a>
        <span class="text-gray-400 dark:text-slate-600">-</span>
        {{ __('How modules connect from marketing to accounting') }}
    </p>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-gray-100/90 dark:bg-slate-900/50 p-4">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase tracking-wide">Leads</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $leadCount }}</p>
            <a href="{{ route('mis.leads.index') }}" class="text-xs text-verlox-accent mt-2 inline-block">Open</a>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-gray-100/90 dark:bg-slate-900/50 p-4">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase tracking-wide">Clients</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $clientCount }}</p>
            <a href="{{ route('mis.clients.index') }}" class="text-xs text-verlox-accent mt-2 inline-block">Open</a>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-gray-100/90 dark:bg-slate-900/50 p-4">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase tracking-wide">Open quotes</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $openQuotes }}</p>
            <a href="{{ route('mis.quotations.index') }}" class="text-xs text-verlox-accent mt-2 inline-block">Open</a>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-gray-100/90 dark:bg-slate-900/50 p-4">
            <p class="text-xs text-gray-500 dark:text-slate-500 uppercase tracking-wide">Invoices (open)</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $unpaidInvoices }}</p>
            <a href="{{ route('mis.invoices.index') }}" class="text-xs text-verlox-accent mt-2 inline-block">Open</a>
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 mt-4 text-xs">
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/30 p-3">
            <p class="text-gray-500 dark:text-slate-500 uppercase tracking-wide">Pipeline value</p>
            <p class="mt-1 text-lg font-semibold font-mono text-gray-900 dark:text-white">£{{ number_format($pipelineValuePence / 100, 0) }}</p>
            <p class="text-gray-500 dark:text-slate-500 mt-0.5">Open leads, deal field</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/30 p-3">
            <p class="text-gray-500 dark:text-slate-500 uppercase tracking-wide">Lead conversion</p>
            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $conversionRate }}%</p>
            <p class="text-gray-500 dark:text-slate-500 mt-0.5">Converted vs active pipeline</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/30 p-3">
            <p class="text-gray-500 dark:text-slate-500 uppercase tracking-wide">YTD invoiced</p>
            <p class="mt-1 text-lg font-semibold font-mono text-gray-900 dark:text-white">£{{ number_format($invoicedYtdPence / 100, 0) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/30 p-3">
            <p class="text-gray-500 dark:text-slate-500 uppercase tracking-wide">YTD collected</p>
            <p class="mt-1 text-lg font-semibold font-mono text-emerald-700 dark:text-emerald-300">£{{ number_format($collectedYtdPence / 100, 0) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/30 p-3">
            <p class="text-gray-500 dark:text-slate-500 uppercase tracking-wide">VA hours (month)</p>
            <p class="mt-1 text-lg font-semibold font-mono text-gray-900 dark:text-white">{{ number_format($vaHoursMonth, 1) }}</p>
            <p class="text-gray-500 dark:text-slate-500 mt-0.5">Approved logs</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/30 p-3">
            <p class="text-gray-500 dark:text-slate-500 uppercase tracking-wide">Quotes won</p>
            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $wonQuotes }}</p>
            <p class="text-gray-500 dark:text-slate-500 mt-0.5">Accepted (all time)</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3 mt-6">
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">New leads (30 days)</h2>
            <div class="h-56"><canvas id="chartLeads"></canvas></div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Pipeline</h2>
            <div class="h-56"><canvas id="chartPipeline"></canvas></div>
            <a href="{{ route('mis.pipeline.index') }}" class="text-xs text-verlox-accent mt-2 inline-block">Open board</a>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2 mt-6">
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Paid invoice total (6 months)</h2>
            <div class="h-52"><canvas id="chartRevenue"></canvas></div>
            <p class="text-xs text-gray-500 dark:text-slate-500 mt-2">Totals in GBP from invoices marked paid.</p>
            <p class="mt-2 text-xs">
                <a href="{{ route('mis.finance.dashboard') }}" class="text-verlox-accent hover:underline">{{ __('Finance dashboard') }}</a>
                <span class="text-gray-400 dark:text-slate-600">·</span>
                <a href="{{ route('mis.zoho.index') }}" class="text-verlox-accent hover:underline">{{ __('Zoho Books sync') }}</a>
                @if(auth()->user()->is_admin)
                    <span class="text-gray-400 dark:text-slate-600">·</span>
                    <a href="{{ route('mis.settings.edit') }}#zoho-books" class="text-verlox-accent hover:underline">{{ __('Zoho settings') }}</a>
                @endif
            </p>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Activity</h2>
            <ul class="text-sm space-y-2 text-gray-700 dark:text-slate-300">
                <li><span class="font-medium text-gray-900 dark:text-white">{{ $newLeadsWeek }}</span> new leads in the last 7 days</li>
                <li><span class="font-medium text-gray-900 dark:text-white">{{ $bookingsLast30 }}</span> bookings created in the last 30 days</li>
                <li><span class="font-medium text-gray-900 dark:text-white">{{ $wonQuotes }}</span> quotations accepted (all time)</li>
            </ul>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Zoho sync health</h2>
                @if($lastZohoSync)
                    <p class="text-xs text-gray-500 dark:text-slate-500 mt-1">Last log: {{ $lastZohoSync->created_at->format('Y-m-d H:i') }} · {{ $lastZohoSync->status }} · {{ $lastZohoSync->entity_type ?? '-' }}</p>
                @else
                    <p class="text-xs text-gray-500 dark:text-slate-500 mt-1">No sync log entries yet.</p>
                @endif
            </div>
            <div class="text-right">
                @if($zohoFailureCount24h > 0)
                    <span class="inline-flex rounded-lg bg-red-100 dark:bg-red-950/50 text-red-800 dark:text-red-200 px-2 py-1 text-xs font-medium">{{ $zohoFailureCount24h }} error(s) in 24h</span>
                @else
                    <span class="text-xs text-emerald-600 dark:text-emerald-400">No errors in last 24h</span>
                @endif
                <a href="{{ route('mis.zoho.index') }}" class="block text-xs text-verlox-accent mt-1">Full sync log</a>
            </div>
        </div>
        @if($zohoRecentFailures->isNotEmpty())
            <ul class="text-xs space-y-1 border-t border-gray-200 dark:border-slate-800 pt-3 text-red-800 dark:text-red-200">
                @foreach ($zohoRecentFailures as $fail)
                    <li>{{ $fail->created_at->format('Y-m-d H:i') }} - {{ $fail->entity_type }}: {{ \Illuminate\Support\Str::limit($fail->message, 120) }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Upcoming bookings</h2>
            <a href="{{ route('mis.bookings.index') }}" class="text-xs text-verlox-accent">All</a>
        </div>
        @forelse ($upcomingBookings as $b)
            <div class="flex flex-wrap items-center justify-between gap-2 py-2 border-t border-gray-200 dark:border-slate-800 first:border-0 first:pt-0">
                <div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $b->contact_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-500">{{ $b->contact_email }}</p>
                </div>
                <p class="text-xs font-mono text-gray-600 dark:text-slate-400">{{ $b->starts_at->timezone($b->timezone)->format('D j M H:i') }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-slate-500">No upcoming bookings.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const tick = isDark ? '#94a3b8' : '#64748b';
    const grid = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(0,0,0,0.06)';

    const leadLabels = @json($leadTrendLabels);
    const leadData = @json($leadTrendData);
    const pipeLabels = @json($pipelineLabels);
    const pipeData = @json($pipelineData);
    const pipeColors = @json($pipelineColors);
    const revLabels = @json($revenueLabels);
    const revData = @json($revenueData);

    if (typeof Chart !== 'undefined') {
        new Chart(document.getElementById('chartLeads'), {
            type: 'line',
            data: {
                labels: leadLabels,
                datasets: [{
                    label: 'Leads',
                    data: leadData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.12)',
                    fill: true,
                    tension: 0.25,
                    pointRadius: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: tick, maxTicksLimit: 8 }, grid: { color: grid } },
                    y: { beginAtZero: true, ticks: { color: tick, stepSize: 1 }, grid: { color: grid } },
                },
            },
        });

        new Chart(document.getElementById('chartPipeline'), {
            type: 'doughnut',
            data: {
                labels: pipeLabels,
                datasets: [{
                    data: pipeData,
                    backgroundColor: pipeColors.length ? pipeColors : ['#64748b'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: tick, boxWidth: 10 } } },
            },
        });

        new Chart(document.getElementById('chartRevenue'), {
            type: 'bar',
            data: {
                labels: revLabels,
                datasets: [{
                    label: 'GBP',
                    data: revData,
                    backgroundColor: isDark ? 'rgba(201,168,76,0.55)' : 'rgba(79,70,229,0.55)',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: tick }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { color: tick }, grid: { color: grid } },
                },
            },
        });
    }
});
</script>
@endpush
