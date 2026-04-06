import '../css/marketing-book.css';
import Alpine from 'alpinejs';

Alpine.data('bookingWidget', () => {
    const misBase = (document.body?.dataset?.misBase || '').replace(/\/$/, '');
    return {
        misBase,
        step: 'calendar',
        eventTypes: [],
        eventType: null,
        questions: [],
        answers: {},
        name: '',
        email: '',
        timezone: 'Europe/London',
        viewYear: new Date().getFullYear(),
        viewMonth: new Date().getMonth() + 1,
        date: null,
        calLoading: false,
        monthCells: [],
        dayOpen: {},
        slots: [],
        loading: false,
        selected: null,
        submitting: false,
        error: '',
        confirmSummary: '',
        confirmIcsUrl: '',
        confirmManageUrl: '',

        get monthTitle() {
            return new Date(this.viewYear, this.viewMonth - 1, 1).toLocaleString('en-GB', {
                month: 'long',
                year: 'numeric',
            });
        },
        get selectedDateLabel() {
            if (!this.date) return '';
            return new Date(`${this.date}T12:00:00`).toLocaleDateString('en-GB', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
            });
        },
        get isViewingCurrentMonth() {
            const n = new Date();
            return this.viewYear === n.getFullYear() && this.viewMonth === n.getMonth() + 1;
        },
        localDateIso(d) {
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        },
        cellAriaLabel(cell) {
            if (!cell.day) return '';
            const label = new Date(`${cell.iso}T12:00:00`).toLocaleDateString('en-GB', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
            });
            return cell.open ? `Select ${label}` : `No slots, ${label}`;
        },
        goToThisMonth() {
            const n = new Date();
            this.viewYear = n.getFullYear();
            this.viewMonth = n.getMonth() + 1;
            this.date = null;
            this.selected = null;
            this.step = 'calendar';
            this.loadCalendar();
        },
        async init() {
            this.buildMonthGrid();
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const ok = [
                'Europe/London',
                'Europe/Paris',
                'Europe/Berlin',
                'America/New_York',
                'America/Los_Angeles',
                'Asia/Dubai',
                'Asia/Singapore',
                'Australia/Sydney',
                'UTC',
            ];
            this.timezone = ok.includes(tz) ? tz : 'Europe/London';
            if (this.misBase) {
                const r = await fetch(`${this.misBase}/api/public/event-types`).catch(() => null);
                if (r?.ok) {
                    const j = await r.json().catch(() => ({}));
                    this.eventTypes = j.event_types || [];
                }
            }
            if (this.eventTypes.length === 1) {
                await this.selectType(this.eventTypes[0]);
            } else if (this.eventTypes.length > 1) {
                this.step = 'type';
            } else {
                this.loadCalendar();
            }
        },
        async selectType(type) {
            this.eventType = type;
            this.questions = [];
            this.answers = {};
            this.dayOpen = {};
            this.buildMonthGrid();
            if (this.misBase) {
                const u = new URL(`${this.misBase}/api/public/booking-questions`);
                u.searchParams.set('event_type_id', type.id);
                const r = await fetch(u).catch(() => null);
                if (r?.ok) {
                    const j = await r.json().catch(() => ({}));
                    this.questions = j.questions || [];
                }
            }
            this.step = 'calendar';
            this.loadCalendar();
        },
        buildMonthGrid() {
            const first = new Date(this.viewYear, this.viewMonth - 1, 1);
            const last = new Date(this.viewYear, this.viewMonth, 0);
            const startPad = (first.getDay() + 6) % 7;
            const cells = [];
            let k = 0;
            const todayIso = this.localDateIso(new Date());
            for (let i = 0; i < startPad; i++) {
                cells.push({ key: `p${i}`, day: null, iso: '', open: false, today: false, weekend: false });
            }
            for (let d = 1; d <= last.getDate(); d++) {
                const iso = `${this.viewYear}-${String(this.viewMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const cellDate = new Date(this.viewYear, this.viewMonth - 1, d);
                const wd = cellDate.getDay();
                const weekend = wd === 0 || wd === 6;
                cells.push({
                    key: `d${d}`,
                    day: d,
                    iso,
                    open: !!this.dayOpen[String(d)],
                    today: iso === todayIso,
                    weekend,
                });
                k++;
            }
            while (cells.length % 7 !== 0) {
                cells.push({ key: `t${k++}`, day: null, iso: '', open: false, today: false, weekend: false });
            }
            this.monthCells = cells;
        },
        async loadCalendar() {
            if (!this.misBase) {
                this.dayOpen = {};
                this.buildMonthGrid();
                return;
            }
            this.calLoading = true;
            this.dayOpen = {};
            this.buildMonthGrid();
            try {
                const u = new URL(`${this.misBase}/api/public/booking-calendar`);
                u.searchParams.set('year', this.viewYear);
                u.searchParams.set('month', this.viewMonth);
                u.searchParams.set('timezone', this.timezone);
                if (this.eventType) u.searchParams.set('event_type_id', this.eventType.id);
                const r = await fetch(u).catch(() => null);
                if (r?.ok) {
                    const j = await r.json().catch(() => ({}));
                    this.dayOpen = j.days || {};
                } else {
                    this.dayOpen = {};
                }
                this.buildMonthGrid();
            } finally {
                this.calLoading = false;
            }
        },
        prevMonth() {
            if (this.viewMonth === 1) {
                this.viewMonth = 12;
                this.viewYear--;
            } else {
                this.viewMonth--;
            }
            this.loadCalendar();
        },
        nextMonth() {
            if (this.viewMonth === 12) {
                this.viewMonth = 1;
                this.viewYear++;
            } else {
                this.viewMonth++;
            }
            this.loadCalendar();
        },
        pickDate(iso) {
            this.date = iso;
            this.selected = null;
            this.step = 'slots';
            this.loadSlots();
        },
        async loadSlots() {
            if (!this.misBase) return;
            this.loading = true;
            this.slots = [];
            this.error = '';
            const u = new URL(`${this.misBase}/api/public/booking-slots`);
            u.searchParams.set('date', this.date);
            u.searchParams.set('timezone', this.timezone);
            if (this.eventType) u.searchParams.set('event_type_id', this.eventType.id);
            const r = await fetch(u).catch(() => null);
            if (!r) {
                this.loading = false;
                return;
            }
            const j = await r.json().catch(() => ({}));
            this.slots = j.slots || [];
            this.loading = false;
        },
        pickSlot(s) {
            this.selected = s;
            this.step = 'form';
            this.error = '';
        },
        onTzChange() {
            this.selected = null;
            this.step = 'calendar';
            this.date = null;
            this.loadCalendar();
        },
        async submit() {
            if (!this.name.trim() || !this.email.trim()) {
                this.error = 'Please fill in your name and email.';
                return;
            }
            for (const q of this.questions) {
                if (q.is_required && !this.answers[q.id]) {
                    this.error = 'Please answer all required questions (*).';
                    return;
                }
            }
            this.submitting = true;
            this.error = '';
            const payload = {
                start: this.selected.start,
                end: this.selected.end,
                contact_name: this.name.trim(),
                contact_email: this.email.trim(),
                create_lead: true,
                timezone: this.timezone,
            };
            if (this.eventType) payload.event_type_id = this.eventType.id;
            if (Object.keys(this.answers).length) payload.answers = this.answers;
            const r = await fetch(`${this.misBase}/api/public/bookings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(payload),
            }).catch(() => null);
            this.submitting = false;
            if (!r?.ok) {
                const j = r ? await r.json().catch(() => ({})) : {};
                this.error =
                    j.message ||
                    Object.values(j.errors || {})
                        .flat()
                        .join(' ') ||
                    'Could not book. Please try again.';
                return;
            }
            const j = await r.json().catch(() => ({}));
            const typeLine = this.eventType ? `<br>${this.eventType.name}` : '';
            this.confirmSummary = `A confirmation has been sent to <strong>${this.email}</strong>.<br><br><strong>${this.selectedDateLabel}</strong><br>${this.selected.label}${typeLine}`;
            this.confirmIcsUrl = j.ics_download || '';
            this.confirmManageUrl = j.manage_url || '';
            this.step = 'done';
        },
        reset() {
            this.step = this.eventTypes.length > 1 ? 'type' : 'calendar';
            this.date = null;
            this.selected = null;
            this.name = '';
            this.email = '';
            this.slots = [];
            this.answers = {};
            this.confirmSummary = '';
            this.confirmIcsUrl = '';
            this.confirmManageUrl = '';
            if (this.eventTypes.length > 1) {
                this.eventType = null;
                this.questions = [];
            } else if (this.step === 'calendar') {
                this.loadCalendar();
            }
        },
    };
});

Alpine.start();
