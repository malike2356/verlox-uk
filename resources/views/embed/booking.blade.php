<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    @include('partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($fav = $settings->faviconPublicUrl())
        <link rel="icon" href="{{ $fav }}">
    @endif
    <title>{{ ($rescheduleBooking ?? null) ? 'Reschedule' : 'Book' }} | {{ $settings->company_name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'DM Sans', system-ui, sans-serif; margin: 0; padding: 12px; min-height: 100vh; }

        /* ── Widget shell ────────────────────────────────────────────── */
        .bk {
            max-width: 700px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--bk-border);
            background: var(--bk-card);
            --bk-accent:    #6366f1;
            --bk-accent-dk: #4f46e5;
            --bk-card:      #ffffff;
            --bk-page:      #f4f5fb;
            --bk-border:    rgba(15,23,42,.09);
            --bk-ink:       #0f172a;
            --bk-ink2:      #4b5563;
            --bk-ink3:      #9ca3af;
            --bk-avail-bg:  #f1f5f9;
            --bk-avail-hov: #e0e7ff;
            --bk-avail-col: #1e293b;
            --bk-dim:       #d1d5db;
            --bk-day-surface: #f8fafc;
            --bk-day-border:  rgba(15, 23, 42, 0.08);
        }
        html[data-theme="dark"] .bk {
            --bk-card:      #0f223c;
            --bk-page:      #0b1829;
            --bk-border:    rgba(148, 163, 184, 0.14);
            --bk-ink:       #e2e8f0;
            --bk-ink2:      #94a3b8;
            --bk-ink3:      #64748b;
            /* Verlox gold accent in dark (matches MIS marketing) */
            --bk-accent:    #c9a84c;
            --bk-accent-dk: #a88b3d;
            --bk-day-surface: rgba(255, 255, 255, 0.035);
            --bk-day-border:  rgba(255, 255, 255, 0.08);
            --bk-avail-bg:    rgba(148, 163, 184, 0.12);
            --bk-avail-hov:   rgba(201, 168, 76, 0.22);
            --bk-avail-col:   #f1f5f9;
            --bk-dim:         #475569;
        }

        /* ── Header ──────────────────────────────────────────────────── */
        .bk-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 24px 16px;
            border-bottom: 1px solid var(--bk-border);
        }
        .bk-brand { display: flex; align-items: center; gap: 12px; }
        .bk-avatar {
            width: 42px; height: 42px; border-radius: 10px;
            background: var(--bk-accent); color: #fff;
            font-size: 17px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .bk-company { font-size: 14px; font-weight: 600; color: var(--bk-ink); line-height: 1.3; }
        .bk-meta { font-size: 12px; color: var(--bk-ink3); margin-top: 1px; display: flex; align-items: center; gap: 6px; }
        .bk-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: var(--bk-ink3); }

        /* ── Body: two-pane grid ─────────────────────────────────────── */
        .bk-body {
            display: grid;
            grid-template-columns: 1fr;
        }
        .bk-body--split {
            grid-template-columns: 1.1fr 1fr;
        }
        @media (max-width: 540px) {
            .bk-body--split { grid-template-columns: 1fr; }
        }

        /* ── Calendar pane ───────────────────────────────────────────── */
        .bk-cal { padding: 22px 24px 20px; }
        .bk-body--split .bk-cal { border-right: 1px solid var(--bk-border); }

        .bk-eyebrow {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: var(--bk-ink3); margin-bottom: 16px;
        }
        .bk-month-nav {
            display: grid;
            grid-template-columns: 36px 1fr 36px;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
        }
        .bk-month-title {
            font-size: 15px; font-weight: 600; color: var(--bk-ink);
            text-align: center;
            min-width: 0;
        }
        .bk-nav-btn {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid transparent;
            background: transparent; cursor: pointer; color: var(--bk-ink2);
            font-size: 15px; display: flex; align-items: center; justify-content: center;
            transition: background .14s, border-color .14s, color .14s;
        }
        .bk-nav-btn:hover {
            background: var(--bk-day-surface);
            border-color: var(--bk-border);
            color: var(--bk-ink);
        }
        .bk-nav-btn:focus { outline: none; }
        .bk-nav-btn:focus-visible {
            outline: 2px solid var(--bk-accent);
            outline-offset: 2px;
            border-color: transparent;
        }

        /*
         * One grid for headers + dates so columns line up (separate grids with only .bk-days { gap }
         * used to shift every header off the day columns).
         */
        .bk-cal-matrix {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            column-gap: 5px;
            row-gap: 6px;
            max-width: 380px;
            margin: 0 auto;
        }
        .bk-wdays,
        .bk-days {
            display: contents;
        }
        .bk-wday {
            font-size: 11px;
            font-weight: 600;
            color: var(--bk-ink3);
            padding: 0 0 2px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bk-day {
            aspect-ratio: 1;
            max-height: 44px;
            border-radius: 8px;
            border: 1px solid var(--bk-day-border);
            font-size: 13px; font-weight: 500; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            position: relative;
            transition: background .14s, color .14s, border-color .14s;
            background: var(--bk-day-surface);
            color: var(--bk-ink);
            -webkit-font-smoothing: antialiased;
        }
        .bk-day:disabled { cursor: default; }
        .bk-day--empty {
            visibility: hidden;
            border: none;
            background: transparent;
            pointer-events: none;
        }
        /* Unavailable: same “tile” as the grid, muted (no empty holes on weekends) */
        .bk-day--dim {
            background: var(--bk-day-surface) !important;
            color: var(--bk-ink3) !important;
            border-color: var(--bk-day-border) !important;
            opacity: 0.72;
        }
        .bk-day--avail {
            background: var(--bk-avail-bg);
            color: var(--bk-avail-col);
            border-color: rgba(148, 163, 184, 0.22);
        }
        html[data-theme="dark"] .bk .bk-day--avail {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .bk-day--avail:hover:not(:disabled) {
            background: var(--bk-avail-hov);
            color: var(--bk-accent);
            border-color: var(--bk-accent);
        }
        .bk-day--sel {
            background: var(--bk-accent) !important;
            color: #0b1829 !important;
            border-color: var(--bk-accent) !important;
            box-shadow: 0 4px 18px rgba(99, 102, 241, 0.28);
            opacity: 1 !important;
        }
        html[data-theme="dark"] .bk .bk-day--sel {
            box-shadow: 0 4px 20px rgba(201, 168, 76, 0.35);
        }
        html[data-theme="light"] .bk .bk-day--sel {
            color: #fff !important;
        }
        .bk-day:focus { outline: none; }
        .bk-day:focus-visible:not(:disabled) {
            outline: 2px solid var(--bk-accent);
            outline-offset: 1px;
        }
        .bk-day--today::after {
            content: ''; position: absolute; bottom: 5px; left: 50%;
            transform: translateX(-50%); width: 4px; height: 4px;
            border-radius: 50%; background: currentColor; opacity: .55;
        }
        .bk-day--sel.bk-day--today::after { background: rgba(255,255,255,.8); opacity: 1; }

        /* Timezone row */
        .bk-tz {
            margin-top: 18px; display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: var(--bk-ink3);
        }
        .bk-tz-icon { font-size: 13px; }
        .bk-tz select {
            font-size: 11px; border: none; background: transparent;
            color: var(--bk-ink2); cursor: pointer; padding: 0 0 0 2px;
            font-family: inherit; -webkit-appearance: none; appearance: none;
        }
        .bk-tz select:focus { outline: none; }

        /* ── Slots pane ──────────────────────────────────────────────── */
        .bk-slots { padding: 22px 20px 20px; display: flex; flex-direction: column; }
        .bk-slots-head {
            margin-bottom: 14px; padding-bottom: 14px;
            border-bottom: 1px solid var(--bk-border);
        }
        .bk-slots-date { font-size: 14px; font-weight: 600; color: var(--bk-ink); }
        .bk-slots-sub { font-size: 11px; color: var(--bk-ink3); margin-top: 2px; }
        .bk-slots-list {
            flex: 1; overflow-y: auto; display: flex; flex-direction: column;
            gap: 7px; max-height: 320px; padding-right: 2px;
        }
        .bk-slots-list::-webkit-scrollbar { width: 4px; }
        .bk-slots-list::-webkit-scrollbar-track { background: transparent; }
        .bk-slots-list::-webkit-scrollbar-thumb { background: var(--bk-dim); border-radius: 4px; }
        .bk-slot {
            width: 100%; padding: 9px 14px; border-radius: 8px;
            font-size: 13px; font-weight: 500; cursor: pointer;
            border: 1.5px solid var(--bk-border); background: transparent;
            color: var(--bk-ink); transition: border-color .14s, background .14s, color .14s;
            display: flex; align-items: center; justify-content: space-between;
            font-family: inherit;
        }
        .bk-slot:hover {
            border-color: var(--bk-accent);
            color: var(--bk-accent);
            background: rgba(99, 102, 241, 0.06);
        }
        html[data-theme="dark"] .bk .bk-slot:hover {
            background: rgba(201, 168, 76, 0.1);
        }
        .bk-slot-arr { font-size: 12px; opacity: .5; }
        .bk-slot--sel {
            border-color: var(--bk-accent) !important;
            background: rgba(99, 102, 241, 0.1) !important;
            color: var(--bk-accent) !important;
        }
        html[data-theme="dark"] .bk .bk-slot--sel {
            background: rgba(201, 168, 76, 0.14) !important;
        }
        .bk-empty { font-size: 13px; color: var(--bk-ink3); padding: 8px 0; }
        .bk-loading {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--bk-ink3);
        }
        .bk-spinner {
            width: 14px; height: 14px; border-radius: 50%;
            border: 2px solid var(--bk-dim); border-top-color: var(--bk-accent);
            animation: spin .7s linear infinite; flex-shrink: 0;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Form pane ───────────────────────────────────────────────── */
        .bk-form { padding: 22px 20px 20px; display: flex; flex-direction: column; gap: 14px; }
        .bk-back {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 500; color: var(--bk-accent);
            background: none; border: none; cursor: pointer; padding: 0; font-family: inherit;
        }
        .bk-summary {
            padding: 12px 14px; border-radius: 10px;
            background: rgba(99,102,241,.07); line-height: 1.5;
        }
        .bk-summary-date { font-size: 13px; font-weight: 600; color: var(--bk-ink); }
        .bk-summary-time { font-size: 12px; color: var(--bk-ink2); margin-top: 1px; }
        .bk-input-wrap { display: flex; flex-direction: column; gap: 5px; }
        .bk-label { font-size: 11px; font-weight: 600; color: var(--bk-ink2); }
        .bk-input {
            padding: 9px 12px; border-radius: 8px; font-size: 13px;
            border: 1.5px solid var(--bk-border); background: transparent;
            color: var(--bk-ink); transition: border-color .14s; font-family: inherit;
            width: 100%;
        }
        .bk-input:focus { outline: none; border-color: var(--bk-accent); }
        .bk-input::placeholder { color: var(--bk-ink3); }
        .bk-btn {
            padding: 11px 16px; border-radius: 9px; font-size: 13px;
            font-weight: 600; border: none; cursor: pointer; font-family: inherit;
            transition: opacity .15s; background: var(--bk-accent); color: #fff;
        }
        .bk-btn:hover { opacity: .88; }
        .bk-btn:disabled { opacity: .45; cursor: not-allowed; }
        .bk-err { font-size: 12px; color: #dc2626; }

        /* ── Confirmation ────────────────────────────────────────────── */
        .bk-done { padding: 36px 28px; text-align: center; }
        .bk-done-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: #d1fae5; color: #059669; font-size: 22px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .bk-done-title { font-size: 18px; font-weight: 700; color: var(--bk-ink); margin-bottom: 6px; }
        .bk-done-detail { font-size: 13px; color: var(--bk-ink2); line-height: 1.65; }
        .bk-done-actions { margin-top: 22px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .bk-done-btn {
            font-size: 12px; font-weight: 500; padding: 8px 18px;
            border-radius: 8px; text-decoration: none; cursor: pointer; font-family: inherit;
            transition: opacity .15s; border: 1.5px solid var(--bk-border);
            background: transparent; color: var(--bk-ink);
        }
        .bk-done-btn:hover { opacity: .7; }
        .bk-done-btn--primary { background: var(--bk-accent); color: #fff; border-color: var(--bk-accent); }

        /* ── Reschedule info strip ───────────────────────────────────── */
        .bk-reschedule-strip {
            font-size: 12px; color: var(--bk-ink2); background: rgba(99,102,241,.06);
            border-radius: 8px; padding: 10px 14px; line-height: 1.5;
        }
    </style>
</head>
<body class="bg-verlox-page">
@php
    $rb  = $rescheduleBooking ?? null;
    $rt  = $rescheduleToken  ?? null;
    $cfg = [
        'rescheduleBookingId' => $rb?->id,
        'rescheduleToken'     => $rt,
        'contactName'         => $rb?->contact_name  ?? '',
        'contactEmail'        => $rb?->contact_email ?? '',
        'reschedulePostUrl'   => ($rb && $rt) ? route('public.booking.reschedule', [$rb, $rt]) : null,
        'slotMinutes'         => (int) $settings->booking_slot_minutes,
        'calendarUrl'         => route('public.booking.calendar'),
        'slotsUrl'            => route('public.booking.slots'),
        'bookUrl'             => route('public.bookings.store'),
    ];
@endphp

<div class="bk" x-data="bookingWidget({{ \Illuminate\Support\Js::from($cfg) }})" x-init="init()">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="bk-head">
        <div class="bk-brand">
            <div class="bk-avatar">{{ substr($settings->company_name, 0, 1) }}</div>
            <div>
                <div class="bk-company">{{ $settings->company_name }}</div>
                <div class="bk-meta">
                    <span>{{ $settings->booking_slot_minutes }} min</span>
                    <span class="bk-meta-dot"></span>
                    <span>Video call</span>
                </div>
            </div>
        </div>
        <button type="button" class="theme-toggle !py-1.5 !px-2.5" data-theme-toggle aria-label="Toggle colour mode">
            <span class="theme-toggle__icon" aria-hidden="true"></span>
            <span data-theme-label class="theme-toggle__text text-[10px]">Dark</span>
        </button>
    </div>

    {{-- ── Confirmation screen ─────────────────────────────────────────── --}}
    <template x-if="step === 'done'">
        <div class="bk-done">
            <div class="bk-done-icon">✓</div>
            <div class="bk-done-title" x-text="rescheduleBookingId ? 'Rescheduled!' : 'You\'re booked!'"></div>
            <div class="bk-done-detail" x-html="confirmSummary"></div>
            <div class="bk-done-actions">
                <a class="bk-done-btn bk-done-btn--primary" :href="confirmIcsUrl" x-show="confirmIcsUrl">
                    ↓ Add to calendar
                </a>
                <a class="bk-done-btn" :href="confirmManageUrl" x-show="confirmManageUrl">
                    Manage booking
                </a>
            </div>
        </div>
    </template>

    {{-- ── Calendar + Slots/Form ────────────────────────────────────────── --}}
    <template x-if="step !== 'done'">
        <div class="bk-body" :class="{ 'bk-body--split': date && step !== 'calendar' }">

            {{-- Calendar pane --}}
            <div class="bk-cal">
                <div class="bk-eyebrow" x-text="rescheduleBookingId ? 'Reschedule · select new date' : 'Select a date'"></div>

                <div class="bk-month-nav">
                    <button type="button" class="bk-nav-btn" @click="prevMonth()" aria-label="Previous month">&#8592;</button>
                    <span class="bk-month-title" x-text="monthTitle"></span>
                    <button type="button" class="bk-nav-btn" @click="nextMonth()" aria-label="Next month">&#8594;</button>
                </div>

                <div class="bk-cal-matrix">
                    <div class="bk-wdays">
                        <span class="bk-wday">Mo</span>
                        <span class="bk-wday">Tu</span>
                        <span class="bk-wday">We</span>
                        <span class="bk-wday">Th</span>
                        <span class="bk-wday">Fr</span>
                        <span class="bk-wday">Sa</span>
                        <span class="bk-wday">Su</span>
                    </div>
                    <div class="bk-days">
                        <template x-for="cell in monthCells" :key="cell.key">
                            <button type="button"
                                class="bk-day"
                                :class="{
                                    'bk-day--empty': !cell.day,
                                    'bk-day--avail': cell.day && cell.open,
                                    'bk-day--dim':   cell.day && !cell.open,
                                    'bk-day--sel':   cell.day && date === cell.iso,
                                    'bk-day--today': cell.today
                                }"
                                :disabled="!cell.day || !cell.open"
                                @click="cell.day && cell.open && pickDate(cell.iso)"
                                x-text="cell.day || ''"
                                :aria-label="cell.day ? (cell.open ? cell.iso : cell.iso + ' unavailable') : ''">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Timezone --}}
                <div class="bk-tz">
                    <span class="bk-tz-icon">🌐</span>
                    <select x-model="timezone" @change="onTzChange()" aria-label="Timezone">
                        <option value="Europe/London">London</option>
                        <option value="Europe/Paris">Paris</option>
                        <option value="Europe/Berlin">Berlin</option>
                        <option value="America/New_York">New York</option>
                        <option value="America/Los_Angeles">Los Angeles</option>
                        <option value="Asia/Dubai">Dubai</option>
                        <option value="Asia/Singapore">Singapore</option>
                        <option value="Australia/Sydney">Sydney</option>
                        <option value="UTC">UTC</option>
                    </select>
                </div>
            </div>

            {{-- Slots pane --}}
            <template x-if="date && step === 'slots'">
                <div class="bk-slots">
                    <div class="bk-slots-head">
                        <div class="bk-slots-date" x-text="selectedDateLabel"></div>
                        <div class="bk-slots-sub">Pick a time</div>
                    </div>
                    <div class="bk-slots-list">
                        <template x-if="loading">
                            <div class="bk-loading"><div class="bk-spinner"></div> Loading…</div>
                        </template>
                        <template x-if="!loading && slots.length === 0">
                            <div class="bk-empty">No availability this day.</div>
                        </template>
                        <template x-for="s in slots" :key="s.start">
                            <button type="button" class="bk-slot" @click="pickSlot(s)">
                                <span x-text="s.label"></span>
                                <span class="bk-slot-arr">→</span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Contact form pane --}}
            <template x-if="date && step === 'form'">
                <div class="bk-form">
                    <button type="button" class="bk-back" @click="step = 'slots'; selected = null">
                        ← Back
                    </button>
                    <div class="bk-summary">
                        <div class="bk-summary-date" x-text="selectedDateLabel"></div>
                        <div class="bk-summary-time" x-text="selected ? selected.label : ''"></div>
                    </div>
                    <template x-if="rescheduleBookingId">
                        <div class="bk-reschedule-strip">
                            Rescheduling for <strong x-text="name"></strong>
                            <span x-show="email"> (<span x-text="email"></span>)</span>
                        </div>
                    </template>
                    <template x-if="!rescheduleBookingId">
                        <div class="bk-input-wrap">
                            <label class="bk-label" for="bk-name">Your name</label>
                            <input id="bk-name" class="bk-input" type="text" x-model="name" placeholder="Jane Smith" required autocomplete="name">
                        </div>
                    </template>
                    <template x-if="!rescheduleBookingId">
                        <div class="bk-input-wrap">
                            <label class="bk-label" for="bk-email">Email address</label>
                            <input id="bk-email" class="bk-input" type="email" x-model="email" placeholder="jane@company.com" required autocomplete="email">
                        </div>
                    </template>
                    <button type="button" class="bk-btn" @click="submit()" :disabled="submitting">
                        <span x-text="submitting ? 'Confirming…' : (rescheduleBookingId ? 'Confirm new time' : 'Confirm booking')"></span>
                    </button>
                    <div class="bk-err" x-show="error" x-text="error"></div>
                </div>
            </template>

        </div>
    </template>

</div>

<script>
function bookingWidget(cfg) {
    return {
        // Config
        rescheduleBookingId: cfg.rescheduleBookingId || null,
        rescheduleToken:     cfg.rescheduleToken     || null,
        reschedulePostUrl:   cfg.reschedulePostUrl   || null,
        slotMinutes:         cfg.slotMinutes         || 30,
        calendarUrl:         cfg.calendarUrl,
        slotsUrl:            cfg.slotsUrl,
        bookUrl:             cfg.bookUrl,
        csrf:                document.querySelector('meta[name=csrf-token]').content,

        // State
        step:      'calendar', // calendar | slots | form | done
        name:      cfg.contactName  || '',
        email:     cfg.contactEmail || '',
        timezone:  'Europe/London',
        viewYear:  new Date().getFullYear(),
        viewMonth: new Date().getMonth() + 1,
        date:      null,
        todayIso:  new Date().toISOString().slice(0, 10),
        monthCells: [],
        dayOpen:   {},
        slots:     [],
        loading:   false,
        selected:  null,
        submitting: false,
        error:     '',

        // Confirmation
        confirmSummary:  '',
        confirmIcsUrl:   '',
        confirmManageUrl: '',

        get monthTitle() {
            return new Date(this.viewYear, this.viewMonth - 1, 1)
                .toLocaleString('en-GB', { month: 'long', year: 'numeric' });
        },
        get selectedDateLabel() {
            if (!this.date) return '';
            const d = new Date(this.date + 'T12:00:00');
            return d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' });
        },

        init() {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const allowed = ['Europe/London','Europe/Paris','Europe/Berlin','America/New_York',
                             'America/Los_Angeles','Asia/Dubai','Asia/Singapore','Australia/Sydney','UTC'];
            this.timezone = allowed.includes(tz) ? tz : 'Europe/London';
            this.buildMonthGrid();
            this.loadCalendar();
        },

        buildMonthGrid() {
            const first    = new Date(this.viewYear, this.viewMonth - 1, 1);
            const last     = new Date(this.viewYear, this.viewMonth, 0);
            const startPad = (first.getDay() + 6) % 7; // Mon = 0
            const cells    = [];
            let k = 0;
            for (let i = 0; i < startPad; i++) {
                cells.push({ key: 'p' + i, day: null, iso: '', open: false, today: false });
            }
            for (let d = 1; d <= last.getDate(); d++) {
                const iso = this.viewYear + '-'
                    + String(this.viewMonth).padStart(2, '0') + '-'
                    + String(d).padStart(2, '0');
                cells.push({
                    key:   'd' + d,
                    day:   d,
                    iso,
                    open:  !!this.dayOpen[String(d)],
                    today: iso === this.todayIso,
                });
                k++;
            }
            while (cells.length % 7 !== 0) {
                cells.push({ key: 't' + k++, day: null, iso: '', open: false, today: false });
            }
            this.monthCells = cells;
        },

        async loadCalendar() {
            const u = new URL(this.calendarUrl, window.location.origin);
            u.searchParams.set('year',     this.viewYear);
            u.searchParams.set('month',    this.viewMonth);
            u.searchParams.set('timezone', this.timezone);
            if (this.rescheduleBookingId && this.rescheduleToken) {
                u.searchParams.set('reschedule_booking_id', this.rescheduleBookingId);
                u.searchParams.set('manage_token',          this.rescheduleToken);
            }
            const r = await fetch(u);
            const j = await r.json();
            this.dayOpen = j.days || {};
            this.buildMonthGrid();
        },

        prevMonth() {
            if (this.viewMonth === 1) { this.viewMonth = 12; this.viewYear--; }
            else { this.viewMonth--; }
            this.buildMonthGrid();
            this.loadCalendar();
        },
        nextMonth() {
            if (this.viewMonth === 12) { this.viewMonth = 1; this.viewYear++; }
            else { this.viewMonth++; }
            this.buildMonthGrid();
            this.loadCalendar();
        },

        pickDate(iso) {
            this.date     = iso;
            this.selected = null;
            this.step     = 'slots';
            this.loadSlots();
        },

        async loadSlots() {
            this.loading = true;
            this.slots   = [];
            this.error   = '';
            const u = new URL(this.slotsUrl, window.location.origin);
            u.searchParams.set('date',     this.date);
            u.searchParams.set('timezone', this.timezone);
            if (this.rescheduleBookingId && this.rescheduleToken) {
                u.searchParams.set('reschedule_booking_id', this.rescheduleBookingId);
                u.searchParams.set('manage_token',          this.rescheduleToken);
            }
            const r = await fetch(u);
            const j = await r.json();
            this.slots   = j.slots || [];
            this.loading = false;
        },

        pickSlot(s) {
            this.selected = s;
            this.step     = 'form';
            this.error    = '';
        },

        onTzChange() {
            this.selected = null;
            this.step     = 'calendar';
            this.date     = null;
            this.buildMonthGrid();
            this.loadCalendar();
        },

        async submit() {
            if (!this.rescheduleBookingId && (!this.name.trim() || !this.email.trim())) {
                this.error = 'Please fill in your name and email.';
                return;
            }
            this.submitting = true;
            this.error      = '';

            if (this.rescheduleBookingId && this.reschedulePostUrl) {
                const r = await fetch(this.reschedulePostUrl, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    body:    JSON.stringify({ start: this.selected.start, end: this.selected.end, timezone: this.timezone }),
                });
                const j = await r.json().catch(() => ({}));
                this.submitting = false;
                if (!r.ok) { this.error = j.message || Object.values(j.errors || {}).flat().join(' ') || 'Could not reschedule.'; return; }
                this.confirmSummary  = `<strong>${this.selectedDateLabel}</strong><br>${this.selected.label}`;
                this.confirmManageUrl = j.manage_url || '';
                this.step = 'done';
                return;
            }

            const r = await fetch(this.bookUrl, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                body:    JSON.stringify({
                    start:         this.selected.start,
                    end:           this.selected.end,
                    contact_name:  this.name.trim(),
                    contact_email: this.email.trim(),
                    create_lead:   true,
                    timezone:      this.timezone,
                }),
            });
            const j = await r.json().catch(() => ({}));
            this.submitting = false;
            if (!r.ok) { this.error = j.message || Object.values(j.errors || {}).flat().join(' ') || 'Could not book.'; return; }
            this.confirmSummary   = `A confirmation has been sent to <strong>${this.email}</strong>.<br><br><strong>${this.selectedDateLabel}</strong><br>${this.selected.label}`;
            this.confirmIcsUrl    = j.ics_download   || '';
            this.confirmManageUrl = j.manage_url     || '';
            this.step = 'done';
        },
    };
}
</script>
</body>
</html>
