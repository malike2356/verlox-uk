@extends('layouts.mis')
@section('title', 'Availability')
@section('heading', 'Booking availability')

@section('content')
@php
    $dayNames   = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
    $dayShort   = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
    $orderedDays = [1, 2, 3, 4, 5, 6, 0];
    $rulesByDay  = $rules->groupBy('weekday');

    $field      = 'block w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark]';
    $btnPrimary = 'inline-flex h-9 shrink-0 items-center gap-2 justify-center rounded-lg bg-verlox-accent px-4 text-sm font-semibold text-on-verlox-accent hover:opacity-95';
    $btnRemove  = 'inline-flex h-7 shrink-0 items-center justify-center rounded-md border border-red-500/20 px-2.5 text-xs font-medium text-red-400 hover:bg-red-500/10 dark:text-red-400';
@endphp

<div class="space-y-8">

{{-- ── Weekly schedule ────────────────────────────────────────────── --}}
<section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">

    <div class="border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Weekly schedule</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
            Default recurring hours. Date overrides below always take priority.
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

    {{-- Rules list grouped by day --}}
    @if($rules->isNotEmpty())
        <div class="border-t border-gray-200 dark:border-slate-700/80">
            @foreach($orderedDays as $wd)
                @php $dayRules = $rulesByDay->get($wd, collect()); @endphp
                @if($dayRules->isNotEmpty())
                    <div class="flex items-center gap-4 border-b border-gray-100 px-5 py-3 last:border-b-0 dark:border-slate-800">
                        <span class="w-28 shrink-0 text-sm font-semibold text-gray-800 dark:text-slate-200">
                            {{ $dayNames[$wd] }}
                        </span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($dayRules as $rule)
                                <div class="mis-availability-window inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs shadow-sm dark:border-slate-600 dark:!bg-slate-800 dark:shadow-none">
                                    <i class="fa-regular fa-clock text-gray-500 dark:text-slate-400"></i>
                                    <span class="tabular-nums font-medium text-gray-900 dark:text-slate-100">
                                        {{ substr($rule->start_time, 0, 5) }} – {{ substr($rule->end_time, 0, 5) }}
                                    </span>
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
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="border-t border-gray-200 px-5 py-4 dark:border-slate-700/80">
            <p class="text-xs text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                No windows configured - the booking calendar will show no available slots.
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
        <div class="border-t border-gray-200 dark:border-slate-700/80">
            @foreach($overrides as $o)
                @php $isPast = \Carbon\Carbon::parse($o->date)->endOfDay()->isPast(); @endphp
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-3 last:border-b-0 dark:border-slate-800 {{ $isPast ? 'opacity-40' : '' }}">
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
                    <form method="post" action="{{ route('mis.bookings.overrides.destroy', $o) }}">
                        @csrf @method('delete')
                        <button type="submit" class="{{ $btnRemove }}">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

</section>

</div>
@endsection
