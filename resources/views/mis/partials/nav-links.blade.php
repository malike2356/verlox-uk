@php
    use Illuminate\Support\Str;

    /** @param array<int, mixed> $row [route, label, icon?, query?] */
    $linkActive = function (array $row): bool {
        $route = $row[0];
        $query = isset($row[3]) && is_array($row[3]) ? $row[3] : null;

        if ($query !== null) {
            if (! request()->routeIs($route)) {
                return false;
            }
            foreach ($query as $k => $v) {
                if ($k === 'view' && $v === 'list') {
                    if (! in_array(request()->query('view'), [null, '', 'list'], true)) {
                        return false;
                    }
                } elseif ((string) request()->query($k) !== (string) $v) {
                    return false;
                }
            }

            return true;
        }

        return match (true) {
            $route === 'mis.dashboard' => request()->routeIs('mis.dashboard'),
            $route === 'mis.help.index' => request()->routeIs('mis.help.index'),
            $route === 'mis.network.index' => request()->routeIs('mis.network.index'),
            $route === 'mis.pipeline.index' => request()->routeIs('mis.pipeline.index'),
            default => request()->routeIs($route) || request()->routeIs(Str::beforeLast($route, '.').'.*'),
        };
    };

    $vaOnly = auth()->check() && auth()->user()->isMisVaOnly();
    $isAdmin = auth()->check() && auth()->user()->is_admin;

    if ($vaOnly) {
        $groups = [
            'Overview' => [
                'icon' => 'fa-house',
                'items' => [
                    ['mis.dashboard', 'Dashboard', 'fa-gauge'],
                ],
            ],
            'VA division' => [
                'icon' => 'fa-headset',
                'items' => [
                    ['mis.va.dashboard', 'VA dashboard', 'fa-gauge-high'],
                    ['mis.va.client-accounts.index', 'VA clients', 'fa-handshake'],
                    ['mis.va.assistants.index', 'Assistants', 'fa-user-tie'],
                    ['mis.va.time-logs.index', 'Time logs', 'fa-clock'],
                ],
            ],
        ];
    } else {
        $crmItems = [
            ['mis.leads.index', 'Leads', 'fa-user-plus'],
            ['mis.clients.index', 'Clients', 'fa-building'],
            ['mis.pipeline.index', 'Pipeline', 'fa-diagram-project'],
        ];
        if ($isAdmin) {
            $crmItems[] = ['mis.pipeline.stages.index', 'Pipeline stages', 'fa-layer-group'];
        }
        $crmItems[] = ['mis.conversations.index', 'Messages', 'fa-comments'];
        $crmItems[] = ['mis.documents.index', 'Documents', 'fa-folder-open'];

        $financeItems = [
            ['mis.finance.dashboard', 'Finance dashboard', 'fa-chart-line'],
            ['mis.finance.receivables', 'Receivables (AR)', 'fa-coins'],
            ['mis.invoices.index', 'Invoices', 'fa-file-invoice'],
            ['mis.finance.expenses.index', 'Expenses', 'fa-receipt'],
            ['mis.quotations.index', 'Quotations', 'fa-file-lines'],
            ['mis.contracts.index', 'Contracts', 'fa-file-contract'],
        ];
        if ($isAdmin) {
            $financeItems[] = ['mis.contract-templates.index', 'Contract templates', 'fa-file-code'];
        }
        $financeItems[] = ['mis.zoho.index', 'Zoho Books', 'fa-cloud'];
        $financeItems[] = ['mis.exports.leads', 'Export leads (CSV)', 'fa-file-csv'];
        $financeItems[] = ['mis.exports.invoices', 'Export invoices (CSV)', 'fa-file-csv'];
        $financeItems[] = ['mis.exports.expenses', 'Export expenses (CSV)', 'fa-file-csv'];

        $bookingItems = [
            ['mis.bookings.index', 'All bookings', 'fa-list', ['view' => 'list']],
            ['mis.bookings.index', 'Calendar', 'fa-calendar-alt', ['view' => 'calendar']],
        ];
        if ($isAdmin) {
            $bookingItems[] = ['mis.bookings.availability', 'Availability rules', 'fa-clock'];
            $bookingItems[] = ['mis.event-types.index', 'Event types', 'fa-list-check'];
        }

        $groups = [
            'Overview' => [
                'icon' => 'fa-house',
                'items' => [
                    ['mis.dashboard', 'Dashboard', 'fa-gauge'],
                ],
            ],
            'CRM & pipeline' => [
                'icon' => 'fa-users',
                'items' => $crmItems,
            ],
            'Finance & contracts' => [
                'icon' => 'fa-file-invoice-dollar',
                'items' => $financeItems,
            ],
            'VA division' => [
                'icon' => 'fa-headset',
                'items' => [
                    ['mis.va.dashboard', 'VA dashboard', 'fa-gauge-high'],
                    ['mis.va.client-accounts.index', 'VA clients', 'fa-handshake'],
                    ['mis.va.assistants.index', 'Assistants', 'fa-user-tie'],
                    ['mis.va.time-logs.index', 'Time logs', 'fa-clock'],
                ],
            ],
            'Bookings' => [
                'icon' => 'fa-calendar-days',
                'items' => $bookingItems,
            ],
        ];

        if ($isAdmin) {
            $groups['Website & catalogue'] = [
                'icon' => 'fa-globe',
                'items' => [
                    ['mis.offerings.index', 'Offerings', 'fa-box-open'],
                    ['mis.offering-types.index', 'Offering types', 'fa-layer-group'],
                    ['mis.pricing-plans.index', 'Pricing plans', 'fa-tags'],
                    ['mis.content-blocks.index', 'Site content', 'fa-pen-to-square'],
                    ['mis.legal-documents.index', 'Legal documents', 'fa-scale-balanced'],
                ],
            ];
            $groups['Users & system'] = [
                'icon' => 'fa-screwdriver-wrench',
                'items' => [
                    ['mis.users.index', 'Users', 'fa-user-gear'],
                    ['mis.settings.edit', 'Company settings', 'fa-gear'],
                ],
            ];
        }

        $groups = array_map(function (array $meta): array {
            $meta['items'] = array_values(array_filter($meta['items']));

            return $meta;
        }, $groups);
    }
@endphp
@foreach ($groups as $groupTitle => $meta)
    @php
        $items = $meta['items'];
        $gIcon = $meta['icon'];
        $firstRoute = $items[0][0];
        $groupPrefix = Str::beforeLast($firstRoute, '.');
        $groupOpen = collect($items)->contains(fn ($row) => $linkActive($row))
            || request()->routeIs($groupPrefix.'.*');
        $single = count($items) === 1;
        $route = $items[0][0];
        $label = $items[0][1];
        $iIcon = $items[0][2] ?? 'fa-circle';
    @endphp

    @if ($single)
        <div class="mis-sidebar-section">
            <a href="{{ route($route) }}"
               class="mis-menu-link {{ $linkActive($items[0]) ? 'is-active' : '' }}">
                <i class="fas {{ $iIcon }}" aria-hidden="true"></i>
                {{ $label }}
            </a>
        </div>
    @else
        <details class="mis-sidebar-section mis-sidebar-details" @if ($groupOpen) open @endif>
            <summary>
                <span class="flex min-w-0 items-center gap-2">
                    <i class="fas {{ $gIcon }} w-5 shrink-0 text-center text-[0.85em]" aria-hidden="true"></i>
                    <span class="truncate">{{ $groupTitle }}</span>
                </span>
                <i class="fas fa-chevron-down mis-menu-chevron" aria-hidden="true"></i>
            </summary>
            <div class="mis-sub-menu py-1">
                @foreach ($items as $row)
                    @php
                        [$r, $lbl] = $row;
                        $icon = $row[2] ?? 'fa-angle-right';
                        $query = isset($row[3]) && is_array($row[3]) ? $row[3] : [];
                        $href = $query === [] ? route($r) : route($r, $query);
                    @endphp
                    <a href="{{ $href }}"
                       class="mis-menu-link mis-menu-link--sub {{ $linkActive($row) ? 'is-active' : '' }}">
                        <i class="fas {{ $icon }}" aria-hidden="true"></i>
                        {{ $lbl }}
                    </a>
                @endforeach
            </div>
        </details>
    @endif
@endforeach

<div class="mis-sidebar-section mis-sidebar-section--footer-links mt-4 border-t border-white/10 pt-3">
    <a href="{{ route('mis.help.index') }}"
       class="mis-menu-link {{ request()->routeIs('mis.help.index') ? 'is-active' : '' }}">
        <i class="fas fa-circle-question" aria-hidden="true"></i>
        Help & documentation
    </a>
    <a href="{{ route('mis.network.index') }}"
       class="mis-menu-link {{ request()->routeIs('mis.network.index') ? 'is-active' : '' }}">
        <i class="fas fa-diagram-project" aria-hidden="true"></i>
        MIS network map
    </a>
</div>
