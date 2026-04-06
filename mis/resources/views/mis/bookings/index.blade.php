@extends('layouts.mis')

@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')
    <div class="mis-bookings-view-tabs flex flex-wrap gap-2 mb-4 text-sm">
        <a href="{{ route('mis.bookings.index', ['view' => 'list']) }}" class="mis-bookings-view-tabs__item rounded-lg px-3 py-1 border {{ $view === 'list' ? 'mis-bookings-view-tabs__item--active border-indigo-500 text-verlox-accent dark:border-[#C9A84C]' : 'border-gray-300 dark:border-slate-700 text-gray-600 dark:text-slate-400' }}">List</a>
        <a href="{{ route('mis.bookings.index', ['view' => 'calendar']) }}" class="mis-bookings-view-tabs__item rounded-lg px-3 py-1 border {{ $view === 'calendar' ? 'mis-bookings-view-tabs__item--active border-indigo-500 text-verlox-accent dark:border-[#C9A84C]' : 'border-gray-300 dark:border-slate-700 text-gray-600 dark:text-slate-400' }}">Calendar</a>
        <a href="{{ route('mis.bookings.availability') }}" class="mis-bookings-view-tabs__item rounded-lg px-3 py-1 border border-gray-300 dark:border-slate-700 text-gray-600 dark:text-slate-400">Availability rules</a>
    </div>

    @if($view === 'calendar')
        {{-- Calendar chrome is forced dark via #cal-scoped CSS (see script); keep light fallbacks if JS disabled. --}}
        <div id="cal" class="mis-bookings-calendar overflow-hidden rounded-2xl border border-gray-200 bg-white p-2 min-h-[480px] dark:border-0 dark:bg-[#0B1829] text-slate-200"></div>
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            (function () {
                var el = document.getElementById('cal');
                if (!el || typeof FullCalendar === 'undefined') return;

                var styleId = 'mis-bookings-fc-dark-overrides';

                function misBookingsCalendarDarkCss() {
                    /* Scope to #cal. FC v6 has no wrapper with class "fc"; toolbar is direct child of #cal. */
                    return [
                        '/* MIS bookings calendar - true dark chrome (toolbar, frame, grid). */',
                        '#cal.mis-bookings-calendar {',
                        '  --fc-page-bg-color: #0b1829 !important;',
                        '  --fc-neutral-bg-color: #0f223c !important;',
                        '  --fc-neutral-text-color: #e2e8f0 !important;',
                        '  --fc-border-color: rgba(71, 85, 105, 0.75) !important;',
                        '  --fc-button-bg-color: #1e293b;',
                        '  --fc-button-border-color: #475569;',
                        '  --fc-button-text-color: #f1f5f9;',
                        '  --fc-button-hover-bg-color: #334155;',
                        '  --fc-button-hover-border-color: #64748b;',
                        '  --fc-button-active-bg-color: #0f172a;',
                        '  --fc-button-active-border-color: #475569;',
                        '  --fc-today-bg-color: rgba(201, 168, 76, 0.35);',
                        '  background-color: #0b1829 !important;',
                        '  background: #0b1829 !important;',
                        '  border: 0 !important;',
                        '  outline: none !important;',
                        '  box-shadow: inset 0 0 0 1px rgb(51, 65, 85) !important;',
                        '  color-scheme: dark;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-view-harness,',
                        '#cal.mis-bookings-calendar .fc-view {',
                        '  border: none !important;',
                        '  box-shadow: none !important;',
                        '  background-color: #0b1829 !important;',
                        '  color: #e2e8f0 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-theme-standard {',
                        '  --fc-page-bg-color: #0b1829 !important;',
                        '  --fc-neutral-bg-color: #0f223c !important;',
                        '  --fc-neutral-text-color: #e2e8f0 !important;',
                        '  --fc-border-color: rgba(71, 85, 105, 0.75) !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-toolbar,',
                        '#cal.mis-bookings-calendar .fc-toolbar.fc-header-toolbar,',
                        '#cal.mis-bookings-calendar .fc-header-toolbar,',
                        '#cal.mis-bookings-calendar .fc-toolbar-ltr,',
                        '#cal.mis-bookings-calendar .fc-toolbar-rtl {',
                        '  background-color: #0f223c !important;',
                        '  background: #0f223c !important;',
                        '  background-image: none !important;',
                        '  border: 1px solid rgba(71, 85, 105, 0.85) !important;',
                        '  border-radius: 0.5rem;',
                        '  padding: 0.35rem 0.5rem;',
                        '  margin-bottom: 0.75rem;',
                        '  color: #e2e8f0 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-toolbar-chunk {',
                        '  background: transparent !important;',
                        '  background-color: transparent !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-toolbar-title,',
                        '#cal.mis-bookings-calendar h2.fc-toolbar-title {',
                        '  color: #f8fafc !important;',
                        '  -webkit-text-fill-color: #f8fafc !important;',
                        '  opacity: 1 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-view-harness,',
                        '#cal.mis-bookings-calendar .fc-view-harness-active,',
                        '#cal.mis-bookings-calendar .fc-scroller-harness,',
                        '#cal.mis-bookings-calendar .fc-scroller {',
                        '  background: #0b1829 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-scrollgrid-section-header td,',
                        '#cal.mis-bookings-calendar .fc-scrollgrid-section-header th {',
                        '  background: #0f223c !important;',
                        '  border-color: rgba(71, 85, 105, 0.65) !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-scrollgrid,',
                        '#cal.mis-bookings-calendar table,',
                        '#cal.mis-bookings-calendar td,',
                        '#cal.mis-bookings-calendar th {',
                        '  border-color: rgba(71, 85, 105, 0.65) !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-scrollgrid-section-sticky > * {',
                        '  background: #0f223c !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-scrollgrid,',
                        '#cal.mis-bookings-calendar .fc-scrollgrid table {',
                        '  background: #0b1829 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-scrollgrid-section > * {',
                        '  background: #0b1829 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-theme-standard td,',
                        '#cal.mis-bookings-calendar .fc-theme-standard th {',
                        '  border-color: rgba(71, 85, 105, 0.65) !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-col-header-cell {',
                        '  background: #0f223c !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-col-header-cell-cushion,',
                        '#cal.mis-bookings-calendar a.fc-col-header-cell-cushion {',
                        '  color: #e2e8f0 !important;',
                        '  font-weight: 600;',
                        '  text-decoration: none !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-scrollgrid-sync-inner {',
                        '  background: #0f223c !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-daygrid-day {',
                        '  background: #0b1829 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-daygrid-day-frame {',
                        '  background: transparent !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-daygrid-day-number,',
                        '#cal.mis-bookings-calendar a.fc-daygrid-day-number {',
                        '  color: #f1f5f9 !important;',
                        '  text-decoration: none !important;',
                        '  font-weight: 600;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-day-other .fc-daygrid-day-number,',
                        '#cal.mis-bookings-calendar .fc-day-other a.fc-daygrid-day-number {',
                        '  color: #94a3b8 !important;',
                        '  opacity: 1 !important;',
                        '  text-decoration: none !important;',
                        '  font-weight: 500;',
                        '}',
                        '/* Today highlight: ::before avoids <td> inset shadow paint bugs. */',
                        '#cal.mis-bookings-calendar td.fc-day-today.fc-daygrid-day {',
                        '  position: relative !important;',
                        '  background: rgba(201, 168, 76, 0.28) !important;',
                        '}',
                        '#cal.mis-bookings-calendar td.fc-day-today.fc-daygrid-day::before {',
                        '  content: "";',
                        '  position: absolute;',
                        '  inset: 0;',
                        '  box-shadow: inset 0 0 0 2px #c9a84c;',
                        '  pointer-events: none;',
                        '  z-index: 0;',
                        '}',
                        '#cal.mis-bookings-calendar td.fc-day-today .fc-daygrid-day-frame {',
                        '  position: relative;',
                        '  z-index: 1;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-day-today .fc-daygrid-day-number,',
                        '#cal.mis-bookings-calendar .fc-day-today a.fc-daygrid-day-number {',
                        '  color: #fefce8 !important;',
                        '  font-weight: 700;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-button {',
                        '  color: #f1f5f9 !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-button-primary {',
                        '  background-color: #1e293b !important;',
                        '  border-color: #475569 !important;',
                        '  color: #f8fafc !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-button-primary:not(:disabled):active,',
                        '#cal.mis-bookings-calendar .fc-button-primary:not(:disabled):hover {',
                        '  background-color: #334155 !important;',
                        '  border-color: #64748b !important;',
                        '  color: #fff !important;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-h-event {',
                        '  border-color: #6366f1;',
                        '}',
                        '#cal.mis-bookings-calendar .fc-event-title {',
                        '  color: #f8fafc;',
                        '}'
                    ].join('\n');
                }

                function syncMisBookingsCalendarDarkStyles() {
                    var root = document.documentElement;
                    var dark = root.classList.contains('dark') || root.getAttribute('data-theme') === 'dark';
                    var node = document.getElementById(styleId);
                    var calEl = document.getElementById('cal');
                    if (!dark) {
                        if (node) node.remove();
                        if (calEl) {
                            calEl.style.removeProperty('background-color');
                            calEl.style.removeProperty('background');
                            calEl.style.removeProperty('border');
                            calEl.style.removeProperty('box-shadow');
                        }
                        return;
                    }
                    var css = misBookingsCalendarDarkCss();
                    if (node) {
                        node.textContent = css;
                    } else {
                        node = document.createElement('style');
                        node.id = styleId;
                        node.textContent = css;
                        document.head.appendChild(node);
                    }
                    /* Re-append so this sheet stays after anything FullCalendar adds later (Firefox). */
                    document.head.appendChild(node);
                    /* Inline fallback beats late-loaded Bootstrap / Tailwind mismatches on #cal. */
                    if (calEl) {
                        calEl.style.setProperty('background-color', '#0b1829', 'important');
                        calEl.style.setProperty('border', 'none', 'important');
                        calEl.style.setProperty('box-shadow', 'inset 0 0 0 1px rgb(51, 65, 85)', 'important');
                    }
                }

                function scheduleMisBookingsCalendarDarkSync() {
                    syncMisBookingsCalendarDarkStyles();
                    [16, 64, 200, 500, 1200].forEach(function (ms) {
                        setTimeout(syncMisBookingsCalendarDarkStyles, ms);
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    var calendar = new FullCalendar.Calendar(el, {
                        initialView: 'dayGridMonth',
                        height: 'auto',
                        events: @json(route('mis.bookings.calendar')),
                        eventClick: function (info) {
                            if (info.event.url) { window.location = info.event.url; info.jsEvent.preventDefault(); }
                        }
                    });
                    calendar.render();
                    requestAnimationFrame(function () {
                        scheduleMisBookingsCalendarDarkSync();
                    });

                    var mo = new MutationObserver(function () {
                        syncMisBookingsCalendarDarkStyles();
                    });
                    mo.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });

                    /* Keep override sheet last in <head> when FullCalendar injects more <style> nodes (Firefox). */
                    var headMo = new MutationObserver(function (mutations) {
                        var ours = document.getElementById(styleId);
                        if (!ours) return;
                        var bump = false;
                        mutations.forEach(function (m) {
                            for (var i = 0; i < m.addedNodes.length; i++) {
                                var n = m.addedNodes[i];
                                if (n.nodeType !== 1) continue;
                                if (n.id === styleId) continue;
                                if (n.nodeName === 'STYLE' || n.nodeName === 'LINK') bump = true;
                            }
                        });
                        if (bump) document.head.appendChild(ours);
                    });
                    headMo.observe(document.head, { childList: true });
                });
            })();
        </script>
        @endpush
    @else
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
                <tr>
                    <th class="px-3 py-2">When</th>
                    <th class="px-3 py-2">Contact</th>
                    <th class="px-3 py-2">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                @foreach ($bookings as $b)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-900/60">
                        <td class="px-3 py-2 font-mono text-gray-700 dark:text-slate-300"><a href="{{ route('mis.bookings.show', $b) }}" class="text-verlox-accent">{{ $b->starts_at->format('Y-m-d H:i') }}</a></td>
                        <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $b->contact_name }}</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $b->status }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $bookings->links() }}</div>
    @endif
@endsection
