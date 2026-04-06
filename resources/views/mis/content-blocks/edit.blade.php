@extends('layouts.mis')
@section('title', 'Edit: '.$contentBlock->key)
@section('heading', $contentBlock->title ?: $contentBlock->key)

@section('content')
@php
    $field = 'block w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/80 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-verlox-accent focus:outline-none';
    $label = 'mb-1 block text-xs font-medium text-gray-600 dark:text-slate-400';
@endphp

<div class="max-w-3xl space-y-6"
     x-data="{
         type: '{{ $contentBlock->type }}',
         body: {{ json_encode($contentBlock->body ?? '') }},
         preview: false,
     }">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400">
        <a href="{{ route('mis.content-blocks.index') }}" class="hover:text-verlox-accent">Site content</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-[11px] dark:bg-slate-800">{{ $contentBlock->key }}</code>
    </div>

    <form method="post" action="{{ route('mis.content-blocks.update', $contentBlock) }}" class="space-y-6">
        @csrf @method('patch')

        {{-- Meta card --}}
        <section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Block settings</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-3">
                <div>
                    <label class="{{ $label }}">Key <span class="font-normal text-gray-400">(read-only)</span></label>
                    <input type="text" value="{{ $contentBlock->key }}" disabled
                           class="{{ $field }} cursor-not-allowed opacity-50">
                </div>
                <div>
                    <label class="{{ $label }}">Display title</label>
                    <input type="text" name="title" value="{{ $contentBlock->title }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Type</label>
                    <select name="type" x-model="type" class="{{ $field }}">
                        @foreach(\App\Models\ContentBlock::TYPES as $val => $lbl)
                            <option value="{{ $val }}" @selected($contentBlock->type === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Sort order</label>
                    <input type="number" name="sort_order" value="{{ $contentBlock->sort_order }}" min="0" class="{{ $field }}">
                </div>
                <div class="flex items-end">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-slate-300">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ $contentBlock->is_active ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-slate-600">
                        Active (visible to site)
                    </label>
                </div>
            </div>
        </section>

        {{-- Content editor card --}}
        <section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Content</h2>
                {{-- Preview toggle for HTML blocks --}}
                <template x-if="type === 'html'">
                    <button type="button" @click="preview = !preview"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-slate-700 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                        <i class="fa-solid" :class="preview ? 'fa-code' : 'fa-eye'"></i>
                        <span x-text="preview ? 'Show editor' : 'Preview HTML'"></span>
                    </button>
                </template>
            </div>

            <div class="p-5">
                {{-- Single-line text --}}
                <template x-if="type === 'text'">
                    <div>
                        <label class="{{ $label }}">Text value</label>
                        <input type="text" name="body" x-model="body" class="{{ $field }}">
                    </div>
                </template>

                {{-- Image URL --}}
                <template x-if="type === 'image_url'">
                    <div class="space-y-3">
                        <div>
                            <label class="{{ $label }}">Image URL</label>
                            <input type="url" name="body" x-model="body" placeholder="https://…" class="{{ $field }}">
                        </div>
                        <template x-if="body">
                            <img :src="body" alt="Preview"
                                 class="mt-2 max-h-48 rounded-lg border border-gray-200 object-contain dark:border-slate-700"
                                 onerror="this.style.display='none'">
                        </template>
                    </div>
                </template>

                {{-- Plain textarea --}}
                <template x-if="type === 'textarea'">
                    <div>
                        <label class="{{ $label }}">Text content</label>
                        <textarea name="body" rows="12" x-model="body"
                                  class="{{ $field }} leading-relaxed"></textarea>
                    </div>
                </template>

                {{-- HTML editor + preview --}}
                <template x-if="type === 'html'">
                    <div>
                        <div x-show="!preview">
                            <label class="{{ $label }}">HTML source</label>
                            <textarea name="body" rows="16" x-model="body"
                                      class="{{ $field }} font-mono text-xs leading-loose"></textarea>
                            <p class="mt-1.5 text-[11px] text-gray-400 dark:text-slate-500">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Raw HTML is stored and rendered wherever this block is consumed.
                            </p>
                        </div>
                        <div x-show="preview" x-cloak>
                            <label class="{{ $label }}">Rendered preview</label>
                            <div class="prose prose-sm dark:prose-invert mt-1 min-h-[120px] max-w-none rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900/60"
                                 x-html="body"></div>
                        </div>
                        {{-- Hidden textarea keeps the value when in preview mode --}}
                        <input type="hidden" name="body" x-bind:value="body" x-show="preview">
                    </div>
                </template>
            </div>
        </section>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex h-10 items-center gap-2 rounded-lg bg-verlox-accent px-5 text-sm font-semibold text-on-verlox-accent hover:opacity-95">
                <i class="fa-solid fa-floppy-disk"></i> Save changes
            </button>
            <a href="{{ route('mis.content-blocks.index') }}"
               class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 dark:border-slate-700 px-4 text-sm text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                Cancel
            </a>
            <form method="post" action="{{ route('mis.content-blocks.destroy', $contentBlock) }}" class="ml-auto">
                @csrf @method('delete')
                <button type="submit"
                        class="inline-flex h-10 items-center gap-2 rounded-lg border border-red-200 dark:border-red-800/40 px-4 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                        onclick="return confirm('Delete block \'{{ $contentBlock->key }}\'?')">
                    <i class="fa-solid fa-trash-can"></i> Delete
                </button>
            </form>
        </div>
    </form>
</div>
@endsection
