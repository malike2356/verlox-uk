@php
    /** @var \App\Models\LegalDocument|null $legalDocument */
    $isEdit = isset($legalDocument) && $legalDocument;
    $fc = 'w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white';
@endphp

<div class="grid gap-4 text-sm max-w-4xl">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Title</label>
            <input name="title" class="{{ $fc }}" required value="{{ old('title', $legalDocument->title ?? '') }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Slug</label>
            <input name="slug" class="{{ $fc }}" required value="{{ old('slug', $legalDocument->slug ?? '') }}" placeholder="privacy-policy">
            <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Used in URLs and references. Lowercase with dashes.</p>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Category</label>
            <input name="category" class="{{ $fc }}" required value="{{ old('category', $legalDocument->category ?? '') }}" placeholder="privacy / terms / cookies">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Effective date</label>
            <input type="date" name="effective_at" class="{{ $fc }}" value="{{ old('effective_at', optional($legalDocument->effective_at ?? null)->format('Y-m-d')) }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Status</label>
            <select name="status" class="{{ $fc }}">
                @foreach (\App\Models\LegalDocument::STATUSES as $st)
                    <option value="{{ $st }}" @selected(old('status', $legalDocument->status ?? 'draft') === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="text-xs text-gray-500 dark:text-slate-300">Body (HTML)</label>
        <textarea name="body_html" rows="18" class="{{ $fc }} font-mono text-xs" required>{{ old('body_html', $legalDocument->body_html ?? '') }}</textarea>
        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">This is rendered as HTML. Keep it simple: headings, paragraphs, lists, links.</p>
    </div>
</div>

