@extends('layouts.mis')
@section('title', 'Availability')
@section('heading', 'Booking availability')

@section('content')
@php
    $dayNames    = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
    $dayShort    = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
    $orderedDays = [1, 2, 3, 4, 5, 6, 0];
    $rulesByDay  = $rules->groupBy('weekday');

    $field      = 'block w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark]';
    $fieldSm    = 'rounded border border-gray-200 bg-white px-2 py-1 text-xs text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:[color-scheme:dark]';
    $btnPrimary = 'inline-flex h-9 shrink-0 items-center gap-2 justify-center rounded-lg bg-verlox-accent px-4 text-sm font-semibold text-on-verlox-accent hover:opacity-95';
    $btnRemove  = 'inline-flex h-7 shrink-0 items-center justify-center rounded-md border border-red-500/20 px-2.5 text-xs font-medium text-red-400 hover:bg-red-500/10 dark:text-red-400';
    $btnEdit    = 'inline-flex h-7 shrink-0 items-center justify-center rounded-md border border-sky-500/20 px-2.5 text-xs font-medium text-sky-500 hover:bg-sky-500/10 dark:text-sky-400';
@endphp

<div class="space-y-8">

{{-- ── Weekly schedule ────────────────────────────────────────────── --}}
<section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">

    <div class="border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Weekly schedule</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
            Default recurring hours. Date overrides below always take priority.
            Drag <i class="fa-solid fa-grip-vertical text-[9px]"></i> to move a window to a different day.
        </p>
    </div>

    {{-- Day dots - quick at-a-glance status --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        @foreach($orderedDays as $wd)
            @php $hasRules = $rulesByDay->has($wd); @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium
                {{ $hasRules
                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                    : 'bg-gray-100 text-gray-400 dark:bg-slate-800 dark:text-slate-500' }}">
                @if($hasRules)
                    <i class="fa-solid fa-circle text-[6px]"></i>
                @else
                    <i class="fa-regular fa-circle text-[6px]"></i>
                @endif
                {{ $dayShort[$wd] }}
            </span>
        @endforeach
    </div>

    {{-- Add window form --}}
    <div class="px-5 py-5">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Add a time window</p>
        <form method="post" action="{{ route('mis.bookings.availability.store') }}"
              class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="w-36">
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Day</label>
                <select name="weekday" required class="{{ $field }}">
                    <option value="1">Monday</option>
                    <option value="2">Tuesday</option>
                    <option value="3">Wednesday</option>
                    <option value="4">Thursday</option>
                    <option value="5">Friday</option>
                    <option value="6">Saturday</option>
                    <option value="0">Sunday</option>
                </select>
            </div>
            <div class="w-32">
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">From</label>
                <input type="time" name="start_time" value="09:00" required class="{{ $field }}">
            </div>
            <div class="w-32">
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">To</label>
                <input type="time" name="end_time" value="17:00" required class="{{ $field }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="{{ $btnPrimary }}">
                    <i class="fa-solid fa-plus"></i> Add window
                </button>
            </div>
        </form>
    </div>

    {{-- Rules list - all 7 days always visible for drag-and-drop --}}
    <div class="border-t border-gray-200 dark:border-slate-700/80 divide-y divide-gray-100 dark:divide-slate-800">
        @foreach($orderedDays as $wd)
            @php $dayRules = $rulesByDay->get($wd, collect()); @endphp
            <div class="flex items-start gap-4 px-5 py-3">
                <span class="w-28 shrink-0 pt-2 text-sm font-semibold text-gray-800 dark:text-slate-200">
                    {{ $dayNames[$wd] }}
                </span>
                <div class="flex flex-wrap gap-2 flex-1 min-h-8"
                     data-weekday="{{ $wd }}"
                     id="day-pills-{{ $wd }}">
                    @if($dayRules->isEmpty())
                        <span class="avail-empty-label pt-1.5 text-xs italic text-gray-300 dark:text-slate-600">
                            No windows – drag here to add
                        </span>
                    @endif
                    @foreach($dayRules as $rule)
                        <div x-data="{ editing: false }"
                             data-rule-id="{{ $rule->id }}"
                             class="avail-pill-wrap">
                            {{-- View mode --}}
                            <div x-show="!editing"
                                 class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs shadow-sm dark:border-slate-600 dark:!bg-slate-800 dark:shadow-none">
                                <span class="avail-drag-handle cursor-grab active:cursor-grabbing px-0.5 text-gray-300 hover:text-gray-400 dark:text-slate-600"
                                      title="Drag to move to another day">
                                    <i class="fa-solid fa-grip-vertical text-[9px]"></i>
                                </span>
                                <i class="fa-regular fa-clock text-gray-500 dark:text-slate-400"></i>
                                <span class="tabular-nums font-medium text-gray-900 dark:text-slate-100">
                                    {{ substr($rule->start_time, 0, 5) }} – {{ substr($rule->end_time, 0, 5) }}
                                </span>
                                {{-- Edit --}}
                                <button type="button" @click.prevent="editing = true"
                                        class="text-gray-400 hover:text-verlox-accent transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen text-[9px]"></i>
                                </button>
                                {{-- Duplicate --}}
                                <form method="post"
                                      action="{{ route('mis.bookings.availability.duplicate', $rule) }}"
                                      style="display:inline;margin:0;padding:0">
                                    @csrf
                                    <button type="submit"
                                            class="text-gray-400 hover:text-emerald-500 transition-colors" title="Duplicate">
                                        <i class="fa-regular fa-copy text-[9px]"></i>
                                    </button>
                                </form>
                                {{-- Delete --}}
                                <form method="post"
                                      action="{{ route('mis.bookings.availability.destroy', $rule) }}"
                                      style="display:inline;margin:0;padding:0">
                                    @csrf @method('delete')
                                    <button type="submit"
                                            class="text-gray-400 hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 transition-colors"
                                            title="Remove"
                                            onclick="return confirm('Remove this window?')">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </form>
                            </div>
                            {{-- Edit mode --}}
                            <form x-show="editing" x-cloak method="post"
                                  action="{{ route('mis.bookings.availability.update', $rule) }}"
                                  class="inline-flex items-center gap-1.5 rounded-lg border border-verlox-accent/40 bg-verlox-accent/5 px-2.5 py-1.5 shadow-sm dark:bg-verlox-accent/10">
                                @csrf @method('patch')
                                <select name="weekday" class="{{ $fieldSm }}">
                                    @foreach([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 0 => 'Sun'] as $v => $l)
                                        <option value="{{ $v }}" @selected($rule->weekday == $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <input type="time" name="start_time" value="{{ substr($rule->start_time, 0, 5) }}"
                                       class="{{ $fieldSm }} w-28">
                                <span class="text-xs text-gray-400">–</span>
                                <input type="time" name="end_time" value="{{ substr($rule->end_time, 0, 5) }}"
                                       class="{{ $fieldSm }} w-28">
                                <button type="submit"
                                        class="shrink-0 rounded px-2 py-1 text-xs font-semibold text-verlox-accent hover:opacity-80">
                                    Save
                                </button>
                                <button type="button" @click="editing = false"
                                        class="shrink-0 rounded px-1 py-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-slate-300">
                                    ✕
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @if($rules->isEmpty())
        <div class="border-t border-gray-200 px-5 py-4 dark:border-slate-700/80">
            <p class="text-xs text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                No windows configured – the booking calendar will show no available slots.
            </p>
        </div>
    @endif

</section>

{{-- ── Date overrides ──────────────────────────────────────────────── --}}
<section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40"
         x-data="{ otype: 'unavailable' }">

    <div class="border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Date overrides</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
            Block a specific date or give it custom hours. Always overrides the weekly schedule.
        </p>
    </div>

    {{-- Add override form --}}
    <div class="px-5 py-5">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Add an override</p>
        <form method="post" action="{{ route('mis.bookings.overrides.store') }}"
              class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="w-40">
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Date</label>
                <input type="date" name="date" min="{{ now()->toDateString() }}" required class="{{ $field }}">
            </div>
            <div class="w-48">
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Type</label>
                <select name="type" x-model="otype" required class="{{ $field }}">
                    <option value="unavailable">Block day (unavailable)</option>
                    <option value="hours">Custom hours</option>
                </select>
            </div>
            <div class="w-28" x-show="otype === 'hours'" x-cloak>
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">From</label>
                <input type="time" name="start_time" value="09:00" class="{{ $field }}">
            </div>
            <div class="w-28" x-show="otype === 'hours'" x-cloak>
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">To</label>
                <input type="time" name="end_time" value="17:00" class="{{ $field }}">
            </div>
            <div class="w-48">
                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Note (optional)</label>
                <input type="text" name="note" placeholder="e.g. Bank holiday" class="{{ $field }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="{{ $btnPrimary }}">
                    <i class="fa-solid fa-plus"></i> Add override
                </button>
            </div>
        </form>
    </div>

    {{-- Overrides list --}}
    @if($overrides->isEmpty())
        <div class="border-t border-gray-200 px-5 py-4 text-xs text-gray-400 dark:border-slate-700/80 dark:text-slate-500">
            No date overrides set.
        </div>
    @else
        <div class="border-t border-gray-200 dark:border-slate-700/80 divide-y divide-gray-100 dark:divide-slate-800">
            @foreach($overrides as $o)
                @php $isPast = \Carbon\Carbon::parse($o->date)->endOfDay()->isPast(); @endphp
                <div x-data="{ editing: false, otype: '{{ $o->type }}' }"
                     class="{{ $isPast ? 'opacity-40' : '' }}">
                    {{-- View mode --}}
                    <div x-show="!editing" class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="w-36 shrink-0 tabular-nums text-sm font-semibold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($o->date)->format('D, d M Y') }}
                            </span>
                            @if($o->type === 'unavailable')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600 dark:bg-red-900/20 dark:text-red-400">
                                    <i class="fa-solid fa-ban text-[10px]"></i> Blocked
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-600 dark:bg-sky-900/20 dark:text-sky-400">
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                    {{ substr($o->start_time, 0, 5) }} – {{ substr($o->end_time, 0, 5) }}
                                </span>
                            @endif
                            @if($o->note)
                                <span class="text-xs text-gray-500 dark:text-slate-400">{{ $o->note }}</span>
                            @endif
                            @if($isPast)
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-slate-600">Past</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="editing = true" class="{{ $btnEdit }}">
                                <i class="fa-solid fa-pen text-[10px] mr-1"></i> Edit
                            </button>
                            <form method="post" action="{{ route('mis.bookings.overrides.destroy', $o) }}">
                                @csrf @method('delete')
                                <button type="submit" class="{{ $btnRemove }}">Remove</button>
                            </form>
                        </div>
                    </div>
                    {{-- Edit mode --}}
                    <div x-show="editing" x-cloak class="px-5 py-4 bg-gray-50/50 dark:bg-slate-800/30">
                        <form method="post" action="{{ route('mis.bookings.overrides.update', $o) }}"
                              class="flex flex-wrap items-end gap-3">
                            @csrf @method('patch')
                            <div class="w-40">
                                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Date</label>
                                <input type="date" name="date" value="{{ $o->date->toDateString() }}" required class="{{ $field }}">
                            </div>
                            <div class="w-48">
                                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Type</label>
                                <select name="type" x-model="otype" required class="{{ $field }}">
                                    <option value="unavailable" @selected($o->type === 'unavailable')>Block day (unavailable)</option>
                                    <option value="hours" @selected($o->type === 'hours')>Custom hours</option>
                                </select>
                            </div>
                            <div class="w-28" x-show="otype === 'hours'" x-cloak>
                                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">From</label>
                                <input type="time" name="start_time"
                                       value="{{ $o->start_time ? substr($o->start_time, 0, 5) : '09:00' }}"
                                       class="{{ $field }}">
                            </div>
                            <div class="w-28" x-show="otype === 'hours'" x-cloak>
                                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">To</label>
                                <input type="time" name="end_time"
                                       value="{{ $o->end_time ? substr($o->end_time, 0, 5) : '17:00' }}"
                                       class="{{ $field }}">
                            </div>
                            <div class="w-48">
                                <label class="mb-1 block text-xs text-gray-500 dark:text-slate-400">Note (optional)</label>
                                <input type="text" name="note" value="{{ $o->note }}"
                                       placeholder="e.g. Bank holiday" class="{{ $field }}">
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="{{ $btnPrimary }}">Save</button>
                                <button type="button" @click="editing = false" class="{{ $btnRemove }}">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('[id^="day-pills-"]').forEach(function (container) {
        Sortable.create(container, {
            group: 'availability-rules',
            animation: 150,
            ghostClass: 'opacity-40',
            handle: '.avail-drag-handle',
            onEnd: function (evt) {
                var ruleId = evt.item.dataset.ruleId;
                var newWeekday = parseInt(evt.to.dataset.weekday, 10);
                var oldWeekday = parseInt(evt.from.dataset.weekday, 10);

                // Update empty-day placeholders
                var destPlaceholder = evt.to.querySelector('.avail-empty-label');
                if (destPlaceholder) destPlaceholder.remove();

                if (!evt.from.querySelector('[data-rule-id]')) {
                    var ph = document.createElement('span');
                    ph.className = 'avail-empty-label pt-1.5 text-xs italic text-gray-300 dark:text-slate-600';
                    ph.textContent = 'No windows \u2013 drag here to add';
                    evt.from.appendChild(ph);
                }

                if (newWeekday === oldWeekday) return;

                fetch('/mis/bookings-meta/availability/' + ruleId + '/weekday', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ weekday: newWeekday }),
                }).then(function (r) {
                    if (!r.ok) {
                        alert('Could not update day \u2013 please refresh.');
                        evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex] || null);
                    }
                }).catch(function () {
                    alert('Network error \u2013 please refresh.');
                });
            },
        });
    });
}());
</script>
@endpush
