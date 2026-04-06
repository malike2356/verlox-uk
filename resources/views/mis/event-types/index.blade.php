@extends('layouts.mis')
@section('title', 'Event Types')
@section('heading', 'Booking event types')

@section('content')
@php
    $field = 'h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-900 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white';
    $label = 'mb-1 block text-xs font-medium text-gray-600 dark:text-slate-400';
    $btnPrimary = 'inline-flex h-10 shrink-0 items-center justify-center rounded-lg bg-verlox-accent px-4 text-sm font-semibold text-on-verlox-accent hover:opacity-95';
    $btnRemove  = 'inline-flex h-8 shrink-0 items-center justify-center rounded-md border border-red-500/25 px-3 text-xs font-medium text-red-400 hover:border-red-500/40 hover:bg-red-500/10 dark:text-red-300';
@endphp

@if(session('status'))
<div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-300">{{ session('status') }}</div>
@endif

{{-- Create form --}}
<section class="mb-8 rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
    <div class="border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">New event type</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Each type can have its own duration, description, and intake questions.</p>
    </div>
    <div class="p-5">
        <form method="post" action="{{ route('mis.event-types.store') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="min-w-[14rem] flex-1">
                <label class="{{ $label }}">Name</label>
                <input type="text" name="name" placeholder="30-min intro call" required class="{{ $field }}">
            </div>
            <div class="min-w-[24rem] flex-[2]">
                <label class="{{ $label }}">Description (optional)</label>
                <input type="text" name="description" placeholder="Quick call to understand your project" class="{{ $field }}">
            </div>
            <div class="min-w-[8rem]">
                <label class="{{ $label }}">Duration (min)</label>
                <input type="number" name="duration_minutes" value="30" min="5" max="480" required class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Colour</label>
                <input type="color" name="color" value="#6366f1" class="h-10 w-14 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer bg-transparent p-1">
            </div>
            <div class="min-w-[8rem]">
                <label class="{{ $label }}">Price (£)</label>
                <input type="number" name="price_gbp" step="0.01" min="0" placeholder="optional" class="{{ $field }}">
            </div>
            <div class="min-w-[12rem] flex-[1]">
                <label class="{{ $label }}">Price caption</label>
                <input type="text" name="price_caption" maxlength="160" placeholder="e.g. Free · or override text" class="{{ $field }}">
            </div>
            <button type="submit" class="{{ $btnPrimary }}">Create</button>
        </form>
    </div>
</section>

{{-- Existing types --}}
@if($types->isEmpty())
    <p class="text-sm text-gray-400 dark:text-slate-500">No event types yet.</p>
@else
    <div class="space-y-6">
    @foreach($types as $type)
        <section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40"
                 x-data="{ editing: false }">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
                <div class="flex items-center gap-3">
                    <span class="inline-block h-3 w-3 rounded-full" style="background:{{ $type->color }}"></span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $type->name }}</span>
                    <span class="text-xs text-gray-500 dark:text-slate-400">{{ $type->duration_minutes }} min</span>
                    @if($type->priceLabel())
                        <span class="text-xs font-medium text-verlox-accent">{{ $type->priceLabel() }}</span>
                    @endif
                    @if(!$type->is_active)
                        <span class="rounded-full bg-gray-100 dark:bg-slate-800 px-2 py-0.5 text-xs text-gray-500 dark:text-slate-400">Inactive</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button @click="editing = !editing" class="text-xs text-verlox-accent hover:underline">Edit</button>
                    <form method="post" action="{{ route('mis.event-types.destroy', $type) }}">
                        @csrf @method('delete')
                        <button type="submit" class="{{ $btnRemove }}" onclick="return confirm('Delete this event type?')">Delete</button>
                    </form>
                </div>
            </div>

            {{-- Edit form --}}
            <div x-show="editing" x-cloak class="border-b border-gray-200 dark:border-slate-700/80 p-5">
                <form method="post" action="{{ route('mis.event-types.update', $type) }}" class="flex flex-wrap items-end gap-3">
                    @csrf @method('patch')
                    <div class="min-w-[14rem] flex-1">
                        <label class="{{ $label }}">Name</label>
                        <input type="text" name="name" value="{{ $type->name }}" required class="{{ $field }}">
                    </div>
                    <div class="min-w-[24rem] flex-[2]">
                        <label class="{{ $label }}">Description</label>
                        <input type="text" name="description" value="{{ $type->description }}" class="{{ $field }}">
                    </div>
                    <div class="min-w-[8rem]">
                        <label class="{{ $label }}">Duration (min)</label>
                        <input type="number" name="duration_minutes" value="{{ $type->duration_minutes }}" min="5" max="480" required class="{{ $field }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">Colour</label>
                        <input type="color" name="color" value="{{ $type->color }}" class="h-10 w-14 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer bg-transparent p-1">
                    </div>
                    <div class="min-w-[8rem]">
                        <label class="{{ $label }}">Price (£)</label>
                        <input type="number" name="price_gbp" step="0.01" min="0"
                               value="{{ $type->price_pence !== null ? number_format($type->price_pence / 100, 2, '.', '') : '' }}"
                               class="{{ $field }}">
                    </div>
                    <div class="min-w-[14rem] flex-[1]">
                        <label class="{{ $label }}">Price caption</label>
                        <input type="text" name="price_caption" maxlength="160" value="{{ $type->price_caption }}" class="{{ $field }}">
                    </div>
                    <div class="flex items-center gap-2 self-end pb-0.5">
                        <input type="checkbox" name="is_active" value="1" id="active_{{ $type->id }}" {{ $type->is_active ? 'checked' : '' }} class="rounded">
                        <label for="active_{{ $type->id }}" class="text-sm text-gray-700 dark:text-slate-300">Active</label>
                    </div>
                    <button type="submit" class="{{ $btnPrimary }}">Save</button>
                </form>
            </div>

            {{-- Questions --}}
            <div class="p-5">
                <p class="mb-3 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-500">Intake questions</p>

                @if($type->questions->isEmpty())
                    <p class="mb-4 text-xs text-gray-400 dark:text-slate-500">No questions yet - add one below.</p>
                @else
                    <ul class="mb-4 overflow-hidden rounded-xl border border-gray-200 dark:border-slate-700">
                        @foreach($type->questions as $q)
                        <li class="flex items-center justify-between border-b border-gray-200 px-4 py-3 last:border-b-0 dark:border-slate-700/80">
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ $q->label }}
                                <span class="ml-2 text-xs text-gray-400 dark:text-slate-500">{{ $q->field_type }}{{ $q->is_required ? ' · required' : '' }}</span>
                            </span>
                            <form method="post" action="{{ route('mis.event-types.questions.destroy', [$type, $q]) }}">
                                @csrf @method('delete')
                                <button type="submit" class="{{ $btnRemove }}">Remove</button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                @endif

                <form method="post" action="{{ route('mis.event-types.questions.store', $type) }}"
                      class="flex flex-wrap items-end gap-3 rounded-xl border border-dashed border-gray-300 bg-gray-50/80 p-4 dark:border-slate-600 dark:bg-slate-950/40"
                      x-data="{ ft: 'text' }">
                    @csrf
                    <div class="min-w-[18rem] flex-[2]">
                        <label class="{{ $label }}">Question label</label>
                        <input type="text" name="label" placeholder="What's your company name?" required class="{{ $field }}">
                    </div>
                    <div class="min-w-[9rem]">
                        <label class="{{ $label }}">Type</label>
                        <select name="field_type" x-model="ft" class="{{ $field }}">
                            <option value="text">Short text</option>
                            <option value="textarea">Long text</option>
                            <option value="select">Dropdown</option>
                        </select>
                    </div>
                    <div class="min-w-[16rem]" x-show="ft === 'select'" x-cloak>
                        <label class="{{ $label }}">Options (one per line)</label>
                        <textarea name="options" rows="3" placeholder="Option A&#10;Option B&#10;Option C"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900/80 dark:text-white"></textarea>
                    </div>
                    <div class="flex items-center gap-2 self-end pb-1">
                        <input type="checkbox" name="is_required" value="1" id="req_{{ $type->id }}">
                        <label for="req_{{ $type->id }}" class="text-sm text-gray-700 dark:text-slate-300">Required</label>
                    </div>
                    <button type="submit" class="{{ $btnPrimary }}">Add question</button>
                </form>
            </div>
        </section>
    @endforeach
    </div>
@endif
@endsection
