@extends('layouts.mis')
@section('title', 'Edit: '.$contentBlock->key)
@section('heading', $contentBlock->title ?: $contentBlock->key)

@push('head')
{{-- Quill (rich HTML editor) --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js" defer></script>
{{-- Marked (markdown preview) --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js" defer></script>
<style>
/* ── Quill dark-mode overrides ─────────────────────────── */
html.dark .ql-toolbar.ql-snow,
html[data-theme='dark'] .ql-toolbar.ql-snow {
    background: #1e293b;
    border-color: #334155;
}
html.dark .ql-container.ql-snow,
html[data-theme='dark'] .ql-container.ql-snow {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}
html.dark .ql-editor,
html[data-theme='dark'] .ql-editor {
    color: #e2e8f0;
}
html.dark .ql-snow .ql-stroke,
html[data-theme='dark'] .ql-snow .ql-stroke { stroke: #94a3b8; }
html.dark .ql-snow .ql-fill,
html[data-theme='dark'] .ql-snow .ql-fill  { fill: #94a3b8; }
html.dark .ql-snow .ql-picker,
html[data-theme='dark'] .ql-snow .ql-picker { color: #94a3b8; }
html.dark .ql-snow .ql-picker-options,
html[data-theme='dark'] .ql-snow .ql-picker-options {
    background: #1e293b;
    border-color: #334155;
}
html.dark .ql-snow button:hover .ql-stroke,
html[data-theme='dark'] .ql-snow button:hover .ql-stroke { stroke: #c9a84c; }
html.dark .ql-snow .ql-active .ql-stroke,
html[data-theme='dark'] .ql-snow .ql-active .ql-stroke { stroke: #c9a84c; }
html.dark .ql-snow .ql-active .ql-fill,
html[data-theme='dark'] .ql-snow .ql-active .ql-fill  { fill: #c9a84c; }
html.dark .ql-editor.ql-blank::before,
html[data-theme='dark'] .ql-editor.ql-blank::before { color: #475569; }
/* ── Markdown textarea ─────────────────────────────────── */
.cb-markdown-editor {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 13px;
    line-height: 1.7;
}
/* ── Markdown preview ──────────────────────────────────── */
.cb-md-preview { min-height: 200px; }
.cb-md-preview h1, .cb-md-preview h2, .cb-md-preview h3 { font-weight: 700; margin: 1rem 0 .5rem; }
.cb-md-preview h1 { font-size: 1.5rem; }
.cb-md-preview h2 { font-size: 1.25rem; }
.cb-md-preview h3 { font-size: 1.1rem; }
.cb-md-preview p  { margin: .5rem 0; }
.cb-md-preview ul, .cb-md-preview ol { margin: .5rem 0 .5rem 1.5rem; }
.cb-md-preview ul { list-style: disc; }
.cb-md-preview ol { list-style: decimal; }
.cb-md-preview a  { color: #c9a84c; text-decoration: underline; }
.cb-md-preview code { background: #f1f5f9; border-radius: 3px; padding: 0 4px; font-family: monospace; }
html.dark .cb-md-preview code,
html[data-theme='dark'] .cb-md-preview code { background: #1e293b; }
.cb-md-preview pre { background: #f1f5f9; border-radius: 6px; padding: 1rem; overflow-x: auto; }
html.dark .cb-md-preview pre,
html[data-theme='dark'] .cb-md-preview pre { background: #1e293b; }
.cb-md-preview blockquote { border-left: 3px solid #c9a84c; padding-left: .75rem; opacity: .8; }
</style>
@endpush

@section('content')
@php
    $field = 'block w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-verlox-accent focus:outline-none';
    $label = 'mb-1 block text-xs font-medium text-gray-600 dark:text-slate-400';
    $sec   = 'rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-sm';
    $savedMsg = session('status') === 'content-block-saved';
@endphp

<div class="max-w-5xl"
     x-data="{
         type: '{{ $contentBlock->type }}',
         body: {{ json_encode($contentBlock->body ?? '') }},
         mdPreview: false,
         quillReady: false,
     }"
     x-init="
         $nextTick(() => {
             if (type === 'html') initQuill();
             if (type === 'markdown') initMarked();
         });
         $watch('type', (val) => {
             if (val === 'html') $nextTick(() => initQuill());
             if (val === 'markdown') $nextTick(() => initMarked());
         });
     ">

    {{-- Breadcrumb + saved badge --}}
    <div class="mb-5 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400">
            <a href="{{ route('mis.content-blocks.index') }}" class="hover:text-verlox-accent">Site content</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <code class="rounded bg-gray-100 dark:bg-slate-800 px-1.5 py-0.5 text-[11px]">{{ $contentBlock->key }}</code>
        </div>
        @if($savedMsg)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                <i class="fa-solid fa-circle-check"></i> Saved
            </span>
        @endif
    </div>

    <form method="post" action="{{ route('mis.content-blocks.update', $contentBlock) }}"
          class="space-y-5" id="cb-form">
        @csrf @method('patch')

        {{-- Two-column layout on large screens --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[1fr_280px]">

            {{-- ── LEFT: Content editor ──────────────────────────── --}}
            <div class="space-y-5">
                <section class="{{ $sec }}">
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 px-5 py-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Content</h2>
                        {{-- Preview toggle for markdown --}}
                        <template x-if="type === 'markdown'">
                            <div class="flex rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden text-xs">
                                <button type="button" @click="mdPreview = false"
                                        :class="!mdPreview ? 'bg-verlox-accent text-on-verlox-accent' : 'text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800'"
                                        class="px-3 py-1.5 font-medium transition-colors">
                                    <i class="fa-solid fa-code mr-1"></i>Edit
                                </button>
                                <button type="button" @click="mdPreview = true"
                                        :class="mdPreview ? 'bg-verlox-accent text-on-verlox-accent' : 'text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800'"
                                        class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 dark:border-slate-700">
                                    <i class="fa-solid fa-eye mr-1"></i>Preview
                                </button>
                            </div>
                        </template>
                        {{-- Char count for text/textarea --}}
                        <template x-if="type === 'text' || type === 'textarea'">
                            <span class="text-xs text-gray-400 dark:text-slate-500" x-text="body.length + ' chars'"></span>
                        </template>
                    </div>

                    <div class="p-5">

                        {{-- Single-line text --}}
                        <template x-if="type === 'text'">
                            <div>
                                <label class="{{ $label }}">Text value</label>
                                <input type="text" name="body" x-model="body" class="{{ $field }}"
                                       placeholder="Enter text…">
                                <p class="mt-1.5 text-[11px] text-gray-400 dark:text-slate-500">
                                    Short single-line text — headings, labels, eyebrows.
                                </p>
                            </div>
                        </template>

                        {{-- Image URL with upload --}}
                        <template x-if="type === 'image_url'">
                            <div class="space-y-4" x-data="imageUploader()">
                                <div>
                                    <label class="{{ $label }}">Image URL</label>
                                    <div class="flex gap-2">
                                        <input type="url" name="body" x-model="body"
                                               placeholder="https://…" class="{{ $field }} flex-1">
                                    </div>
                                </div>
                                <div>
                                    <label class="{{ $label }}">Or upload an image</label>
                                    <div class="flex items-center gap-3">
                                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800/50 px-4 py-2.5 text-xs text-gray-600 dark:text-slate-400 hover:border-verlox-accent hover:text-verlox-accent transition-colors">
                                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                            <span x-text="uploading ? 'Uploading…' : 'Choose image'"></span>
                                            <input type="file" accept="image/*" class="sr-only"
                                                   @change="upload($event)" :disabled="uploading">
                                        </label>
                                        <template x-if="uploadError">
                                            <span class="text-xs text-red-500" x-text="uploadError"></span>
                                        </template>
                                    </div>
                                </div>
                                <template x-if="body">
                                    <div>
                                        <label class="{{ $label }}">Preview</label>
                                        <img :src="body" alt="Image preview"
                                             class="max-h-64 rounded-xl border border-gray-200 dark:border-slate-700 object-contain"
                                             onerror="this.style.display='none'">
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Plain textarea --}}
                        <template x-if="type === 'textarea'">
                            <div>
                                <label class="{{ $label }}">Text content</label>
                                <textarea name="body" rows="14" x-model="body"
                                          class="{{ $field }} leading-relaxed"
                                          placeholder="Enter multi-line text…"></textarea>
                            </div>
                        </template>

                        {{-- Markdown editor / preview --}}
                        <template x-if="type === 'markdown'">
                            <div>
                                {{-- Editor --}}
                                <div x-show="!mdPreview">
                                    <textarea name="body" rows="16" x-model="body"
                                              id="cb-md-textarea"
                                              class="{{ $field }} cb-markdown-editor leading-loose"
                                              placeholder="# Your heading&#10;&#10;Write **markdown** here…"></textarea>
                                    <p class="mt-1.5 text-[11px] text-gray-400 dark:text-slate-500">
                                        <i class="fa-brands fa-markdown mr-1"></i>
                                        Supports standard Markdown — headings, bold, italic, lists, links, code blocks.
                                    </p>
                                </div>
                                {{-- Preview --}}
                                <div x-show="mdPreview" x-cloak>
                                    <div class="cb-md-preview prose prose-sm dark:prose-invert min-h-[200px] max-w-none rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 px-5 py-4 text-gray-800 dark:text-slate-200"
                                         x-html="typeof marked !== 'undefined' ? marked.parse(body || '') : body"></div>
                                    <input type="hidden" name="body" :value="body">
                                </div>
                            </div>
                        </template>

                        {{-- HTML WYSIWYG (Quill) --}}
                        <template x-if="type === 'html'">
                            <div>
                                <div id="quill-editor" style="min-height:260px;"></div>
                                <textarea name="body" id="quill-body" class="sr-only">{{ $contentBlock->body }}</textarea>
                                <p class="mt-2 text-[11px] text-gray-400 dark:text-slate-500">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    Rich HTML — formatted text, links, lists. Images are uploaded and the URL embedded.
                                </p>
                            </div>
                        </template>

                    </div>
                </section>
            </div>

            {{-- ── RIGHT: Block settings ─────────────────────────── --}}
            <div class="space-y-5">
                <section class="{{ $sec }}">
                    <div class="border-b border-gray-200 dark:border-slate-800 px-5 py-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Block settings</h2>
                    </div>
                    <div class="space-y-4 p-5">
                        <div>
                            <label class="{{ $label }}">Key <span class="font-normal text-gray-400">(read-only)</span></label>
                            <input type="text" value="{{ $contentBlock->key }}" disabled
                                   class="{{ $field }} cursor-not-allowed opacity-50">
                        </div>
                        <div>
                            <label class="{{ $label }}">Display title</label>
                            <input type="text" name="title" value="{{ $contentBlock->title }}" class="{{ $field }}"
                                   placeholder="Human-readable label">
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
                        <div class="pt-1">
                            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-gray-700 dark:text-slate-300">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ $contentBlock->is_active ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-slate-600 accent-[#C9A84C]">
                                <span>Active <span class="text-xs text-gray-400 dark:text-slate-500">(visible to site)</span></span>
                            </label>
                        </div>
                    </div>
                </section>

                {{-- Info / API key card --}}
                <section class="{{ $sec }}">
                    <div class="border-b border-gray-200 dark:border-slate-800 px-5 py-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-slate-500">API reference</h2>
                    </div>
                    <div class="space-y-2 p-5 text-xs text-gray-500 dark:text-slate-400">
                        <p>Public endpoint:</p>
                        <code class="block rounded-lg bg-gray-100 dark:bg-slate-800 px-3 py-2 text-[11px] break-all">GET /api/public/content-blocks</code>
                        <p class="pt-1">Block key:</p>
                        <code class="block rounded-lg bg-gray-100 dark:bg-slate-800 px-3 py-2 text-[11px] break-all">{{ $contentBlock->key }}</code>
                        <p class="pt-1 text-[11px]">Returns <code>body</code> as a string in the JSON response. For HTML/markdown types, the raw source is returned.</p>
                    </div>
                </section>

                {{-- Last modified --}}
                <div class="px-1 text-[11px] text-gray-400 dark:text-slate-600 text-center">
                    Updated {{ $contentBlock->updated_at->diffForHumans() }}
                </div>
            </div>

        </div>

        {{-- ── Action bar ─────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 px-5 py-4 shadow-sm">
            <button type="submit"
                    class="inline-flex h-10 items-center gap-2 rounded-lg bg-verlox-accent px-5 text-sm font-semibold text-on-verlox-accent hover:opacity-95">
                <i class="fa-solid fa-floppy-disk"></i> Save changes
            </button>
            <a href="{{ route('mis.content-blocks.index') }}"
               class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 dark:border-slate-700 px-4 text-sm text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                Cancel
            </a>
            {{-- Duplicate --}}
            <form method="post" action="{{ route('mis.content-blocks.duplicate', $contentBlock) }}" class="contents">
                @csrf
                <button type="submit"
                        class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 dark:border-slate-700 px-4 text-sm text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-copy"></i> Duplicate
                </button>
            </form>
            {{-- Delete --}}
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

@push('scripts')
<script>
/* ── Quill WYSIWYG init ────────────────────────────────── */
function initQuill() {
    if (document.getElementById('quill-editor')?.classList.contains('ql-container')) return;
    if (typeof Quill === 'undefined') {
        setTimeout(initQuill, 200);
        return;
    }

    var uploadUrl  = '{{ route('mis.content-blocks.upload-image') }}';
    var csrfToken  = document.head.querySelector('meta[name="csrf-token"]')?.content || '';

    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Write rich content here…',
        modules: {
            toolbar: {
                container: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ indent: '-1' }, { indent: '+1' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    ['clean'],
                ],
                handlers: {
                    image: function () {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/png,image/jpg,image/jpeg,image/gif,image/webp');
                        input.click();
                        input.addEventListener('change', function () {
                            var file = input.files[0];
                            if (!file) return;
                            var formData = new FormData();
                            formData.append('image', file);
                            fetch(uploadUrl, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrfToken },
                                body: formData,
                            })
                            .then(function (r) { return r.json(); })
                            .then(function (data) {
                                if (data.url) {
                                    var range = quill.getSelection(true);
                                    quill.insertEmbed(range.index, 'image', data.url);
                                }
                            });
                        });
                    },
                },
            },
        },
    });

    // Pre-fill with existing content
    var bodyTextarea = document.getElementById('quill-body');
    if (bodyTextarea && bodyTextarea.value) {
        quill.clipboard.dangerouslyPasteHTML(bodyTextarea.value);
    }

    // Keep hidden textarea in sync so the form submits HTML
    quill.on('text-change', function () {
        if (bodyTextarea) {
            bodyTextarea.value = quill.getSemanticHTML();
        }
        // Also sync to Alpine body for char count
        document.getElementById('cb-form').__x?.$data && (document.getElementById('cb-form').__x.$data.body = quill.getSemanticHTML());
    });
}

/* ── Marked init (nothing needed — x-html handles it) ── */
function initMarked() {
    if (typeof marked !== 'undefined') {
        marked.setOptions({ breaks: true, gfm: true });
    }
}

/* ── Image upload Alpine component ─────────────────────── */
function imageUploader() {
    return {
        uploading: false,
        uploadError: null,
        upload(event) {
            var file = event.target.files[0];
            if (!file) return;
            this.uploading = true;
            this.uploadError = null;
            var formData = new FormData();
            formData.append('image', file);
            var csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('{{ route('mis.content-blocks.upload-image') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData,
            })
            .then(function (r) { return r.json(); })
            .then((data) => {
                if (data.url) {
                    this.$root.closest('[x-data]').__x.$data.body = data.url;
                }
                this.uploading = false;
            })
            .catch(() => {
                this.uploadError = 'Upload failed. Try again.';
                this.uploading = false;
            });
        }
    };
}

// Call inits after all deferred scripts load
window.addEventListener('load', function () {
    var type = document.querySelector('[x-data]')?.__x?.$data?.type;
    if (type === 'html') initQuill();
    if (type === 'markdown') initMarked();
});
</script>
@endpush
