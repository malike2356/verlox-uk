@extends('layouts.mis')
@section('title', 'Site content')
@section('heading', 'Site content blocks')

@section('content')
@php
    $field     = 'block w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/80 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-verlox-accent focus:outline-none';
    $label     = 'mb-1 block text-xs font-medium text-gray-600 dark:text-slate-400';
    $btnPri    = 'inline-flex h-9 shrink-0 items-center gap-2 justify-center rounded-lg bg-verlox-accent px-4 text-sm font-semibold text-on-verlox-accent hover:opacity-95';
    $btnGhost  = 'inline-flex h-8 items-center gap-1.5 rounded-lg border border-gray-200 dark:border-slate-700 px-3 text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800';
    $btnRemove = 'inline-flex h-8 items-center justify-center rounded-lg border border-red-200/60 dark:border-red-800/40 px-3 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20';
@endphp

{{-- ── Create new block ─────────────────────────────────────────────── --}}
<section class="mb-8 rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40"
         x-data="{ open: false }">

    <button type="button" @click="open = !open"
            class="flex w-full items-center justify-between px-5 py-4 text-left">
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">New content block</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">Keyed content for the marketing site and <span class="font-mono text-[10px]">GET /api/public/content-blocks</span>. Hero uses keys <span class="font-mono text-[10px]">marketing_hero_eyebrow</span>, <span class="font-mono text-[10px]">marketing_hero_title</span>, <span class="font-mono text-[10px]">marketing_hero_subtitle</span>.</p>
        </div>
        <i class="fa-solid fa-chevron-down text-gray-400 transition-transform dark:text-slate-500"
           :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="open" x-cloak class="border-t border-gray-200 p-5 dark:border-slate-700/80">
        <form method="post" action="{{ route('mis.content-blocks.store') }}"
              class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <div>
                <label class="{{ $label }}">Key <span class="font-normal text-gray-400">(snake_case, unique)</span></label>
                <input type="text" name="key" placeholder="marketing_hero_title" required
                       pattern="[a-z0-9_]+" title="Lowercase letters, numbers and underscores only"
                       class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Display title</label>
                <input type="text" name="title" placeholder="Hero title" class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Type</label>
                <select name="type" class="{{ $field }}">
                    @foreach(\App\Models\ContentBlock::TYPES as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="{{ $label }}">Initial content (optional)</label>
                <textarea name="body" rows="3" class="{{ $field }}"></textarea>
            </div>
            <div>
                <label class="{{ $label }}">Sort order</label>
                <input type="number" name="sort_order" value="0" min="0" class="{{ $field }}">
            </div>
            <div class="flex items-end sm:col-span-2 lg:col-span-2">
                <button type="submit" class="{{ $btnPri }}">
                    <i class="fa-solid fa-plus"></i> Create block
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ── Grouped blocks ───────────────────────────────────────────────── --}}
@if($grouped->isEmpty())
    <p class="text-sm text-gray-400 dark:text-slate-500">No content blocks yet. Create one above.</p>
@else
    <div class="space-y-6">
        @foreach($grouped as $section => $blocks)
            <section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">

                <div class="border-b border-gray-200 px-5 py-3 dark:border-slate-700/80">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">
                        {{ $section }}
                        <span class="ml-1 font-normal normal-case tracking-normal text-gray-400 dark:text-slate-500">({{ $blocks->count() }})</span>
                    </h2>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                    @foreach($blocks as $block)
                        <li x-data="{ editing: false }" class="px-5">

                            {{-- ── View row ── --}}
                            <div x-show="!editing" class="flex items-start justify-between gap-4 py-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $block->title ?: $block->key }}
                                        </span>
                                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-[11px] text-gray-500 dark:bg-slate-800 dark:text-slate-400">{{ $block->key }}</code>
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-gray-500 dark:bg-slate-800 dark:text-slate-400">
                                            {{ $block->type }}
                                        </span>
                                        @if(!$block->is_active)
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">Inactive</span>
                                        @endif
                                    </div>
                                    @if($block->body)
                                        <p class="mt-1.5 line-clamp-2 text-xs text-gray-500 dark:text-slate-400">
                                            {{ strip_tags($block->body) }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs italic text-gray-300 dark:text-slate-600">Empty</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <button type="button" @click="editing = true" class="{{ $btnGhost }}">
                                        <i class="fa-solid fa-pen text-[10px]"></i> Edit
                                    </button>
                                    <a href="{{ route('mis.content-blocks.edit', $block) }}" class="{{ $btnGhost }}" title="Full editor">
                                        <i class="fa-solid fa-expand text-[10px]"></i>
                                    </a>
                                    <form method="post" action="{{ route('mis.content-blocks.destroy', $block) }}">
                                        @csrf @method('delete')
                                        <button type="submit" class="{{ $btnRemove }}"
                                                onclick="return confirm('Delete block \'{{ $block->key }}\'? This cannot be undone.')">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- ── Inline edit row ── --}}
                            <div x-show="editing" x-cloak class="py-4">
                                <form method="post" action="{{ route('mis.content-blocks.update', $block) }}"
                                      class="space-y-3">
                                    @csrf @method('patch')
                                    <div class="grid gap-3 sm:grid-cols-3">
                                        <div>
                                            <label class="{{ $label }}">Display title</label>
                                            <input type="text" name="title" value="{{ $block->title }}" class="{{ $field }}">
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Type</label>
                                            <select name="type" class="{{ $field }}">
                                                @foreach(\App\Models\ContentBlock::TYPES as $val => $lbl)
                                                    <option value="{{ $val }}" @selected($block->type === $val)>{{ $lbl }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Sort order</label>
                                            <input type="number" name="sort_order" value="{{ $block->sort_order }}" min="0" class="{{ $field }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="{{ $label }}">Content</label>
                                        @if($block->type === 'text' || $block->type === 'image_url')
                                            <input type="text" name="body" value="{{ $block->body }}" class="{{ $field }}">
                                        @else
                                            <textarea name="body" rows="5"
                                                      class="{{ $field }} font-mono text-xs leading-relaxed">{{ $block->body }}</textarea>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="submit" class="{{ $btnPri }}">
                                            <i class="fa-solid fa-floppy-disk"></i> Save
                                        </button>
                                        <button type="button" @click="editing = false" class="{{ $btnGhost }}">
                                            Cancel
                                        </button>
                                        <label class="ml-2 flex cursor-pointer items-center gap-2 text-xs text-gray-600 dark:text-slate-400">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1"
                                                   {{ $block->is_active ? 'checked' : '' }}
                                                   class="rounded border-gray-300 dark:border-slate-600">
                                            Active
                                        </label>
                                    </div>
                                </form>
                            </div>

                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
@endif
@endsection
