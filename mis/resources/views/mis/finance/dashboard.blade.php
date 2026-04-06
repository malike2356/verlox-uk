@extends('layouts.mis')
@section('title', 'Finance Dashboard')
@section('heading', 'Finance - ' . $year)

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
@php
    $fmt = fn($pence) => '£'.number_format($pence / 100, 2);
    $pct = fn($a, $b) => $b > 0 ? round($a / $b * 100) : 0;
@endphp

@include('mis.partials.zoho-accounting-strip', [
    'automation' => $zohoLinked ? ['invoice' => $zohoInvoiceAuto, 'expense' => $zohoExpenseAuto] : null,
])

{{-- ── KPI row ──────────────────────────────────────────────────────── --}}
<div class="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">

    @php
        $kpis = [
            ['Revenue collected',  $fmt($revenueYtd),  'fa-arrow-down-to-bracket', 'text-emerald-500', 'bg-emerald-500/10'],
            ['Total invoiced',     $fmt($invoicedYtd), 'fa-file-invoice-dollar',   'text-sky-400',     'bg-sky-400/10'],
            ['Outstanding',        $fmt($outstanding), 'fa-hourglass-half',        'text-amber-400',   'bg-amber-400/10'],
            ['Total expenses',     $fmt($expensesYtd), 'fa-arrow-up-from-bracket', 'text-rose-400',    'bg-rose-400/10'],
            ['Net profit',         $fmt($netProfit),   'fa-scale-balanced',        $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400', $netProfit >= 0 ? 'bg-emerald-400/10' : 'bg-rose-400/10'],
            ['Corp. tax est. (19%)','£'.number_format($taxEstimate / 100, 2), 'fa-landmark', 'text-purple-400', 'bg-purple-400/10'],
        ];
    @endphp

    @foreach($kpis as [$label, $value, $icon, $iconColor, $iconBg])
        <div class="rounded-2xl border border-gray-200/80 bg-white/60 p-4 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
            <div class="mb-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $iconBg }}">
                <i class="fa-solid {{ $icon }} text-sm {{ $iconColor }}"></i>
            </div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ $label }}</p>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $value }}</p>
        </div>
    @endforeach
</div>

{{-- ── Revenue streams & cross-module context ───────────────────────── --}}
<div class="mb-8 grid gap-4 lg:grid-cols-2">
    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __('Paid revenue by source') }} ({{ $year }})</h2>
        <p class="mb-4 text-xs text-gray-500 dark:text-slate-500">{{ __('All paid invoices roll into the figures below. Catalogue = website offering checkout; quotations = accepted quotes invoiced in MIS; ad hoc = invoices without catalogue or quotation link.') }}</p>
        <ul class="space-y-2 text-sm">
            <li class="flex justify-between gap-2 border-b border-gray-100 pb-2 dark:border-slate-800">
                <span class="text-gray-600 dark:text-slate-400">{{ __('Website catalogue / Stripe checkout') }}</span>
                <span class="font-semibold tabular-nums text-gray-900 dark:text-white">{{ $fmt($revenueFromCatalogue) }}</span>
            </li>
            <li class="flex justify-between gap-2 border-b border-gray-100 pb-2 dark:border-slate-800">
                <span class="text-gray-600 dark:text-slate-400">{{ __('Quotations → contracts → invoices') }}</span>
                <span class="font-semibold tabular-nums text-gray-900 dark:text-white">{{ $fmt($revenueFromQuotations) }}</span>
            </li>
            <li class="flex justify-between gap-2">
                <span class="text-gray-600 dark:text-slate-400">{{ __('Ad hoc invoices') }}</span>
                <span class="font-semibold tabular-nums text-gray-900 dark:text-white">{{ $fmt($revenueAdHoc) }}</span>
            </li>
        </ul>
    </section>
    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __('Operations (non-invoice)') }}</h2>
        <p class="mb-4 text-xs text-gray-500 dark:text-slate-500">{{ __('Bookings and VA retainers are tracked in their modules. Bill recurring VA work through invoices so it appears in revenue and Zoho.') }}</p>
        <ul class="space-y-3 text-sm">
            <li class="flex justify-between gap-2">
                <span class="text-gray-600 dark:text-slate-400">{{ __('Calendar bookings this year') }}</span>
                <a href="{{ route('mis.bookings.index') }}" class="font-semibold text-verlox-accent hover:underline tabular-nums">{{ $bookingsYtd }}</a>
            </li>
            <li class="flex justify-between gap-2">
                <span class="text-gray-600 dark:text-slate-400">{{ __('Active VA engagements') }}</span>
                <a href="{{ route('mis.va.dashboard') }}" class="font-semibold text-verlox-accent hover:underline tabular-nums">{{ $vaActiveCount }}</a>
            </li>
            <li class="flex justify-between gap-2">
                <span class="text-gray-600 dark:text-slate-400">{{ __('VA contracted MRR (list rates)') }}</span>
                <span class="font-semibold tabular-nums text-gray-900 dark:text-white">{{ $fmt($vaMrrPence) }}</span>
            </li>
        </ul>
    </section>
</div>

{{-- ── Charts row ───────────────────────────────────────────────────── --}}
<div class="mb-8 grid gap-6 lg:grid-cols-3">

    {{-- Monthly P&L bar chart --}}
    <div class="lg:col-span-2 rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
            Monthly P&amp;L - {{ $year }}
        </h2>
        <canvas id="plChart" height="110"></canvas>
    </div>

    {{-- Expense by category donut --}}
    <div class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Expenses by category</h2>
        @if($expenseByCategory->isEmpty())
            <p class="text-xs text-gray-400 dark:text-slate-500 mt-8 text-center">No expenses yet this year.</p>
        @else
            <canvas id="expCatChart" height="160"></canvas>
            <ul class="mt-4 space-y-1.5">
                @foreach($expenseByCategory as $row)
                    <li class="flex items-center justify-between text-xs">
                        <span class="text-gray-700 dark:text-slate-300">{{ \App\Models\Expense::CATEGORIES[$row->category] ?? $row->category }}</span>
                        <span class="font-medium tabular-nums text-gray-900 dark:text-white">£{{ number_format($row->total / 100, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- ── Recent activity ─────────────────────────────────────────────── --}}
<div class="grid gap-6 lg:grid-cols-2">

    {{-- Recent invoices --}}
    <section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent invoices</h2>
            <a href="{{ route('mis.invoices.index') }}" class="text-xs text-verlox-accent hover:underline">View all</a>
        </div>
        @if($recentInvoices->isEmpty())
            <p class="px-5 py-4 text-xs text-gray-400 dark:text-slate-500">No invoices yet.</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach($recentInvoices as $inv)
                    <li class="flex items-center justify-between px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('mis.invoices.show', $inv) }}"
                               class="block truncate text-sm font-medium text-gray-900 hover:text-verlox-accent dark:text-white">
                                {{ $inv->number }}
                            </a>
                            <span class="text-xs text-gray-500 dark:text-slate-400">
                                {{ $inv->client?->name ?? '-' }}
                                · {{ $inv->issued_at ? \Carbon\Carbon::parse($inv->issued_at)->format('d M') : '' }}
                            </span>
                        </div>
                        <div class="ml-4 flex shrink-0 items-center gap-3">
                            <span class="tabular-nums text-sm font-semibold text-gray-900 dark:text-white">
                                £{{ number_format($inv->total_pence / 100, 2) }}
                            </span>
                            @php
                                $cls = match($inv->status) {
                                    'paid'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'sent'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    default => 'bg-gray-100 text-gray-500 dark:bg-slate-800 dark:text-slate-400',
                                };
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $cls }}">
                                {{ ucfirst($inv->status) }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Recent expenses --}}
    <section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent expenses</h2>
            <a href="{{ route('mis.finance.expenses.index') }}" class="text-xs text-verlox-accent hover:underline">View all</a>
        </div>
        @if($recentExpenses->isEmpty())
            <p class="px-5 py-4 text-xs text-gray-400 dark:text-slate-500">No expenses recorded yet.</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach($recentExpenses as $exp)
                    <li class="flex items-center justify-between px-5 py-3">
                        <div class="min-w-0">
                            <span class="block truncate text-sm font-medium text-gray-900 dark:text-white">
                                {{ $exp->description }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-slate-400">
                                {{ $exp->category_label }}
                                @if($exp->vendor) · {{ $exp->vendor }}@endif
                                · {{ $exp->date->format('d M') }}
                            </span>
                        </div>
                        <div class="ml-4 shrink-0 text-right">
                            <span class="text-sm font-semibold text-rose-500 dark:text-rose-400 tabular-nums">
                                −£{{ number_format($exp->amount_pence / 100, 2) }}
                            </span>
                            @if($exp->zoho_expense_id)
                                <span class="ml-2 text-[10px] text-emerald-500" title="Synced to Zoho">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const tickColor = isDark ? '#94a3b8' : '#6b7280';

    // ── Monthly P&L chart ──────────────────────────────────────────
    const monthly = @json($monthly);
    new Chart(document.getElementById('plChart'), {
        type: 'bar',
        data: {
            labels: monthly.map(m => m.label),
            datasets: [
                {
                    label: 'Income',
                    data: monthly.map(m => m.income),
                    backgroundColor: 'rgba(16,185,129,0.75)',
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'Expenses',
                    data: monthly.map(m => m.expenses),
                    backgroundColor: 'rgba(239,68,68,0.65)',
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'Net',
                    data: monthly.map(m => m.income - m.expenses),
                    type: 'line',
                    borderColor: 'rgba(99,102,241,0.9)',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(99,102,241,0.9)',
                    tension: 0.3,
                    order: 1,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index' },
            plugins: {
                legend: { labels: { color: tickColor, boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' £' + ctx.raw.toLocaleString('en-GB', { minimumFractionDigits: 2 }),
                    },
                },
            },
            scales: {
                x: { ticks: { color: tickColor, font: { size: 11 } }, grid: { color: gridColor } },
                y: {
                    ticks: {
                        color: tickColor, font: { size: 11 },
                        callback: v => '£' + v.toLocaleString('en-GB'),
                    },
                    grid: { color: gridColor },
                },
            },
        },
    });

    // ── Expense by category donut ─────────────────────────────────
    @if($expenseByCategory->isNotEmpty())
    const catData = @json($expenseByCategory->values());
    const catLabels = catData.map(r => @json(\App\Models\Expense::CATEGORIES)[r.category] ?? r.category);
    const palette = ['#6366f1','#f59e0b','#10b981','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#64748b'];
    new Chart(document.getElementById('expCatChart'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catData.map(r => (r.total / 100).toFixed(2)),
                backgroundColor: palette.slice(0, catData.length),
                borderWidth: 0,
                hoverOffset: 4,
            }],
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' £' + parseFloat(ctx.raw).toLocaleString('en-GB', { minimumFractionDigits: 2 }),
                    },
                },
            },
        },
    });
    @endif
});
</script>
@endpush
@endsection
