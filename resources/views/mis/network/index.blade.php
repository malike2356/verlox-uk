@extends('layouts.mis')

@section('title', __('MIS network'))
@section('heading', __('MIS network map'))

@section('content')
    <p class="mb-2 max-w-3xl text-sm text-gray-600 dark:text-slate-400">
        {{ __('Every module below is a node; arrows show how data typically flows from first touch through sales, delivery, and accounting. This is your operational “nervous system” - not a machine-learning model.') }}
    </p>
    <p class="mb-6 text-xs text-gray-500 dark:text-slate-500">
        {{ __('Tip: open any node to work in that area. Finance and Zoho are linked from every accounting screen.') }}
    </p>

    @php
        $node = function (string $route, string $icon, string $label, ?string $countLabel = null) use ($stats) {
            return [
                'route' => $route,
                'icon' => $icon,
                'label' => $label,
                'count' => $countLabel,
            ];
        };
    @endphp

    <div class="space-y-10">
        {{-- Layer: public / marketing inputs --}}
        <section>
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Surface & intake') }}</h2>
            <div class="flex flex-wrap items-stretch justify-center gap-3 sm:justify-start">
                @foreach ([
                    $node('mis.content-blocks.index', 'fa-pen-to-square', __('Site content')),
                    $node('mis.offerings.index', 'fa-box-open', __('Offerings / catalogue')),
                    $node('mis.bookings.index', 'fa-calendar-days', __('Bookings'), (string) $stats['bookings_ytd'].' '.__('this year')),
                ] as $n)
                    <a href="{{ route($n['route']) }}" class="mis-net-node group w-[140px] shrink-0 rounded-2xl border border-gray-200/90 bg-white/80 p-3 text-center shadow-sm transition hover:border-[#C9A84C]/60 hover:shadow-md dark:border-slate-700 dark:bg-slate-900/50 dark:hover:border-[#C9A84C]/50">
                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#C9A84C]/15 group-hover:text-[#8B6914] dark:bg-slate-800 dark:text-slate-300 dark:group-hover:text-[#E7D59C]">
                            <i class="fa-solid {{ $n['icon'] }} text-lg" aria-hidden="true"></i>
                        </span>
                        <span class="block text-xs font-semibold text-gray-900 dark:text-white">{{ $n['label'] }}</span>
                        @if (! empty($n['count']))
                            <span class="mt-1 block text-[10px] text-gray-500 dark:text-slate-500">{{ $n['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <p class="mt-3 text-center text-lg text-[#C9A84C]/80 dark:text-[#C9A84C]/60 sm:text-left" aria-hidden="true">↓</p>
        </section>

        {{-- CRM core --}}
        <section>
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('CRM & pipeline') }}</h2>
            <div class="flex flex-wrap items-stretch justify-center gap-3 sm:justify-start">
                @foreach ([
                    $node('mis.leads.index', 'fa-user-plus', __('Leads'), (string) $stats['leads']),
                    $node('mis.clients.index', 'fa-building', __('Clients'), (string) $stats['clients']),
                    $node('mis.pipeline.index', 'fa-diagram-project', __('Pipeline board')),
                    $node('mis.conversations.index', 'fa-comments', __('Messages')),
                ] as $n)
                    <a href="{{ route($n['route']) }}" class="mis-net-node group w-[140px] shrink-0 rounded-2xl border border-gray-200/90 bg-white/80 p-3 text-center shadow-sm transition hover:border-[#C9A84C]/60 hover:shadow-md dark:border-slate-700 dark:bg-slate-900/50 dark:hover:border-[#C9A84C]/50">
                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#C9A84C]/15 group-hover:text-[#8B6914] dark:bg-slate-800 dark:text-slate-300 dark:group-hover:text-[#E7D59C]">
                            <i class="fa-solid {{ $n['icon'] }} text-lg" aria-hidden="true"></i>
                        </span>
                        <span class="block text-xs font-semibold text-gray-900 dark:text-white">{{ $n['label'] }}</span>
                        @if (! empty($n['count']))
                            <span class="mt-1 block text-[10px] tabular-nums text-gray-500 dark:text-slate-500">{{ $n['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <p class="mt-3 text-center text-lg text-[#C9A84C]/80 dark:text-[#C9A84C]/60 sm:text-left" aria-hidden="true">↓</p>
        </section>

        {{-- Commercial documents --}}
        <section>
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Commercial') }}</h2>
            <div class="flex flex-wrap items-stretch justify-center gap-3 sm:justify-start">
                @foreach ([
                    $node('mis.quotations.index', 'fa-file-lines', __('Quotations'), (string) $stats['quotations']),
                    $node('mis.contracts.index', 'fa-file-contract', __('Contracts')),
                    $node('mis.contract-templates.index', 'fa-file-code', __('Contract templates')),
                ] as $n)
                    <a href="{{ route($n['route']) }}" class="mis-net-node group w-[140px] shrink-0 rounded-2xl border border-gray-200/90 bg-white/80 p-3 text-center shadow-sm transition hover:border-[#C9A84C]/60 hover:shadow-md dark:border-slate-700 dark:bg-slate-900/50 dark:hover:border-[#C9A84C]/50">
                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#C9A84C]/15 group-hover:text-[#8B6914] dark:bg-slate-800 dark:text-slate-300 dark:group-hover:text-[#E7D59C]">
                            <i class="fa-solid {{ $n['icon'] }} text-lg" aria-hidden="true"></i>
                        </span>
                        <span class="block text-xs font-semibold text-gray-900 dark:text-white">{{ $n['label'] }}</span>
                        @if (! empty($n['count']))
                            <span class="mt-1 block text-[10px] tabular-nums text-gray-500 dark:text-slate-500">{{ $n['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <p class="mt-3 text-center text-lg text-[#C9A84C]/80 dark:text-[#C9A84C]/60 sm:text-left" aria-hidden="true">↓</p>
        </section>

        {{-- Finance hub --}}
        <section>
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Finance & accounting hub') }}</h2>
            <div class="flex flex-wrap items-stretch justify-center gap-3 sm:justify-start">
                @foreach ([
                    $node('mis.finance.dashboard', 'fa-chart-line', __('Finance dashboard')),
                    $node('mis.invoices.index', 'fa-file-invoice', __('Invoices'), (string) $stats['invoices']),
                    $node('mis.finance.expenses.index', 'fa-receipt', __('Expenses'), (string) $stats['expenses']),
                    $node('mis.zoho.index', 'fa-cloud', __('Zoho Books')),
                ] as $n)
                    <a href="{{ route($n['route']) }}" class="mis-net-node group w-[140px] shrink-0 rounded-2xl border border-sky-200/80 bg-sky-50/50 p-3 text-center shadow-sm transition hover:border-[#C9A84C]/60 hover:shadow-md dark:border-sky-900/40 dark:bg-sky-950/20 dark:hover:border-[#C9A84C]/50">
                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 group-hover:bg-[#C9A84C]/15 group-hover:text-[#8B6914] dark:bg-sky-900/50 dark:text-sky-200 dark:group-hover:text-[#E7D59C]">
                            <i class="fa-solid {{ $n['icon'] }} text-lg" aria-hidden="true"></i>
                        </span>
                        <span class="block text-xs font-semibold text-gray-900 dark:text-white">{{ $n['label'] }}</span>
                        @if (! empty($n['count']))
                            <span class="mt-1 block text-[10px] tabular-nums text-gray-500 dark:text-slate-500">{{ $n['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

        {{-- VA branch (parallel) --}}
        <section>
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('VA division (parallel track)') }}</h2>
            <p class="mb-3 max-w-2xl text-xs text-gray-500 dark:text-slate-500">{{ __('Engagements and time logs tie to VA clients; raise MIS invoices for billable revenue so the finance hub and Zoho stay in sync.') }}</p>
            <div class="flex flex-wrap items-stretch justify-center gap-3 sm:justify-start">
                @foreach ([
                    $node('mis.va.dashboard', 'fa-gauge-high', __('VA dashboard')),
                    $node('mis.va.client-accounts.index', 'fa-handshake', __('VA clients')),
                    $node('mis.va.assistants.index', 'fa-user-tie', __('Assistants')),
                    $node('mis.va.time-logs.index', 'fa-clock', __('Time logs')),
                ] as $n)
                    <a href="{{ route($n['route']) }}" class="mis-net-node group w-[140px] shrink-0 rounded-2xl border border-violet-200/80 bg-violet-50/40 p-3 text-center shadow-sm transition hover:border-[#C9A84C]/60 hover:shadow-md dark:border-violet-900/40 dark:bg-violet-950/20 dark:hover:border-[#C9A84C]/50">
                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 group-hover:bg-[#C9A84C]/15 group-hover:text-[#8B6914] dark:bg-violet-900/40 dark:text-violet-200 dark:group-hover:text-[#E7D59C]">
                            <i class="fa-solid {{ $n['icon'] }} text-lg" aria-hidden="true"></i>
                        </span>
                        <span class="block text-xs font-semibold text-gray-900 dark:text-white">{{ $n['label'] }}</span>
                        @if ($n['route'] === 'mis.va.dashboard' && $stats['va_active'] > 0)
                            <span class="mt-1 block text-[10px] text-gray-500 dark:text-slate-500">{{ $stats['va_active'] }} {{ __('active engagements') }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-10">
        @include('mis.partials.zoho-accounting-strip')
    </div>
@endsection
