@extends('layouts.marketing-site')

@section('title', 'Book a Call | '.$settings->company_name)

@push('vite')
    @vite(['resources/js/marketing-home.js', 'resources/js/marketing-book.js'])
@endpush

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
@endpush

@section('content')
    @include('marketing.partials.topbar')

    <main id="main">
        @include('marketing.partials.pricing-plans-section', [
            'sectionClass' => 'section--alt',
            'sectionId' => 'book-pricing',
            'eyebrow' => 'Plans',
            'title' => 'Packages & session types',
            'subtitle' => 'Published rates and retainers from the MIS. When you are ready, pick a time below.',
        ])

        <section class="book-page">
            <div class="container book-page__layout">

                <div class="book-page__intro reveal">
                    <p class="book-page__eyebrow">Schedule</p>
                    <h1 class="book-page__title">Book a call with {{ $settings->company_name }}</h1>
                    <p class="book-page__sub">30 minutes, no sales script. Tell us what you're building. We'll tell you honestly
                        whether we're the right fit.</p>
                    <div class="book-page__meta">
                        <span class="book-page__chip"><i class="fa-regular fa-clock"></i> 30 minutes</span>
                        <span class="book-page__chip"><i class="fa-solid fa-video"></i> Video call</span>
                        <span class="book-page__chip"><i class="fa-solid fa-check"></i> Free</span>
                    </div>
                </div>

                <div class="bkw reveal" id="bookingWidget" x-data="bookingWidget()" x-init="init()">

                    <template x-if="step === 'type'">
                        <div class="bkw-types-wrap">
                            <p class="bkw-eyebrow">What would you like to book?</p>
                            <div class="bkw-types-grid">
                                <template x-for="t in eventTypes" :key="t.id">
                                    <button type="button" class="bkw-type-card" @click="selectType(t)">
                                        <span class="bkw-type-dot" :style="'background:' + t.color"></span>
                                        <span class="bkw-type-name" x-text="t.name"></span>
                                        <span class="bkw-type-dur">
                                            <i class="fa-regular fa-clock"></i>
                                            <span x-text="t.duration_minutes + ' min'"></span>
                                        </span>
                                        <span class="bkw-type-price" x-show="t.price_label" x-text="t.price_label"></span>
                                        <span class="bkw-type-desc" x-show="t.description" x-text="t.description"></span>
                                        <span class="bkw-type-arr">Select <i class="fa-solid fa-arrow-right"></i></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="step === 'done'">
                        <div class="bkw-done">
                            <div class="bkw-done-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="bkw-done-title">You're booked!</div>
                            <div class="bkw-done-detail" x-html="confirmSummary"></div>
                            <div class="bkw-done-actions">
                                <a class="bkw-done-btn bkw-done-btn--primary" :href="confirmIcsUrl" x-show="confirmIcsUrl">↓ Add to
                                    calendar</a>
                                <a class="bkw-done-btn" :href="confirmManageUrl" x-show="confirmManageUrl">Manage booking</a>
                                <button type="button" class="bkw-done-btn" @click="reset()">Book another</button>
                            </div>
                        </div>
                    </template>

                    <template x-if="step !== 'done'">
                        <div class="bkw-body" :class="{ 'bkw-body--split': date && step !== 'calendar' }">

                            <div class="bkw-cal">
                                <template x-if="eventTypes.length > 1 && eventType">
                                    <button type="button" class="bkw-change-type" @click="step = 'type'">
                                        <span class="bkw-type-dot" :style="'background:' + eventType.color"></span>
                                        <span x-text="eventType.name"></span>
                                        <i class="fa-solid fa-pen" style="font-size:10px;opacity:.6"></i>
                                    </button>
                                </template>
                                <p class="bkw-eyebrow">Select a date</p>

                                <div class="bkw-month-nav">
                                    <div class="bkw-month-nav__main">
                                        <button type="button" class="bkw-nav-btn" @click="prevMonth()" :disabled="calLoading"
                                            aria-label="Previous month"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                                        <span class="bkw-month-title" x-text="monthTitle" id="bkw-cal-month-label"></span>
                                        <button type="button" class="bkw-nav-btn" @click="nextMonth()" :disabled="calLoading"
                                            aria-label="Next month"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                                    </div>
                                    <button type="button" class="bkw-today-btn" @click="goToThisMonth()"
                                        :disabled="calLoading || isViewingCurrentMonth" x-show="!isViewingCurrentMonth">Today</button>
                                </div>

                                <div class="bkw-wdays" aria-hidden="true">
                                    <span class="bkw-wday">Mon</span><span class="bkw-wday">Tue</span>
                                    <span class="bkw-wday">Wed</span><span class="bkw-wday">Thu</span>
                                    <span class="bkw-wday">Fri</span><span class="bkw-wday">Sat</span>
                                    <span class="bkw-wday">Sun</span>
                                </div>

                                <div class="bkw-days-wrap" role="grid" aria-labelledby="bkw-cal-month-label"
                                    :aria-busy="calLoading ? 'true' : 'false'">
                                    <div class="bkw-cal-loading" x-show="calLoading" x-cloak>
                                        <div class="bkw-spinner" aria-hidden="true"></div>
                                        <span>Loading availability…</span>
                                    </div>
                                    <div class="bkw-days">
                                        <template x-for="cell in monthCells" :key="cell.key">
                                            <button type="button" class="bkw-day" role="gridcell" :class="{
                                                'bkw-day--empty': !cell.day,
                                                'bkw-day--avail': cell.day && cell.open,
                                                'bkw-day--dim': cell.day && !cell.open,
                                                'bkw-day--sel': cell.day && date === cell.iso,
                                                'bkw-day--today': cell.today,
                                                'bkw-day--weekend': cell.weekend
                                            }" :disabled="!cell.day || !cell.open"
                                                :aria-selected="cell.day && date === cell.iso ? 'true' : 'false'"
                                                :aria-label="cell.day ? cellAriaLabel(cell) : null"
                                                @click="cell.day && cell.open && pickDate(cell.iso)" x-text="cell.day || ''">
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <p class="bkw-cal-legend">Highlighted days have open slots. Dot under today.</p>

                                <div class="bkw-tz">
                                    <i class="fa-solid fa-globe"></i>
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

                            <template x-if="date && step === 'slots'">
                                <div class="bkw-slots">
                                    <div class="bkw-slots-head">
                                        <div class="bkw-slots-date" x-text="selectedDateLabel"></div>
                                        <div class="bkw-slots-sub">Pick a time</div>
                                    </div>
                                    <div class="bkw-slots-list">
                                        <template x-if="loading">
                                            <div class="bkw-loading">
                                                <div class="bkw-spinner"></div> Loading…
                                            </div>
                                        </template>
                                        <template x-if="!loading && slots.length === 0">
                                            <div class="bkw-empty">No availability on this day.</div>
                                        </template>
                                        <template x-for="s in slots" :key="s.start">
                                            <button type="button" class="bkw-slot" @click="pickSlot(s)">
                                                <span x-text="s.label"></span>
                                                <span class="bkw-slot-arr">→</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="date && step === 'form'">
                                <div class="bkw-form">
                                    <button type="button" class="bkw-back" @click="step = 'slots'; selected = null">← Back</button>
                                    <div class="bkw-summary">
                                        <div class="bkw-summary-date" x-text="selectedDateLabel"></div>
                                        <div class="bkw-summary-time">
                                            <span x-text="selected ? selected.label : ''"></span>
                                            <template x-if="eventType">
                                                <span style="margin-left:8px;opacity:.6" x-text="'· ' + eventType.name"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="bkw-label" for="bkw-name">Your name</label>
                                        <input id="bkw-name" class="bkw-input" type="text" x-model="name" placeholder="Jane Smith"
                                            autocomplete="name">
                                    </div>
                                    <div>
                                        <label class="bkw-label" for="bkw-email">Email address</label>
                                        <input id="bkw-email" class="bkw-input" type="email" x-model="email"
                                            placeholder="jane@company.com" autocomplete="email">
                                    </div>
                                    <template x-for="q in questions" :key="q.id">
                                        <div>
                                            <label class="bkw-label" :for="'bkw-q-' + q.id"
                                                x-text="q.label + (q.is_required ? ' *' : '')"></label>
                                            <template x-if="q.field_type === 'text'">
                                                <input :id="'bkw-q-' + q.id" class="bkw-input" type="text" x-model="answers[q.id]"
                                                    :required="q.is_required">
                                            </template>
                                            <template x-if="q.field_type === 'textarea'">
                                                <textarea :id="'bkw-q-' + q.id" class="bkw-input" rows="3" x-model="answers[q.id]"
                                                    :required="q.is_required" style="height:auto;resize:vertical;padding-top:9px"></textarea>
                                            </template>
                                            <template x-if="q.field_type === 'select'">
                                                <select :id="'bkw-q-' + q.id" class="bkw-input" x-model="answers[q.id]"
                                                    :required="q.is_required">
                                                    <option value="">Select…</option>
                                                    <template x-for="opt in (q.options || [])" :key="opt">
                                                        <option :value="opt" x-text="opt"></option>
                                                    </template>
                                                </select>
                                            </template>
                                        </div>
                                    </template>
                                    <button type="button" class="bkw-submit" @click="submit()" :disabled="submitting"
                                        x-text="submitting ? 'Confirming…' : 'Confirm booking'"></button>
                                    <div class="bkw-err" x-show="error" x-text="error"></div>
                                </div>
                            </template>

                        </div>
                    </template>

                </div>

            </div>
        </section>
    </main>

    @include('marketing.partials.footer')
@endsection
