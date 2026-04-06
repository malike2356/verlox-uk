@php
    /** @var \App\Models\OfferingType|null $type */
    $field = 'mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white';
    $label = 'text-xs text-gray-500 dark:text-slate-500';
@endphp

<div>
    <label class="{{ $label }}">Name</label>
    <input name="name" value="{{ old('name', $type?->name) }}" required class="{{ $field }}">
</div>
<div>
    <label class="{{ $label }}">Slug</label>
    <input name="slug" value="{{ old('slug', $type?->slug) }}" class="{{ $field }}" placeholder="e.g. consultation">
    <p class="mt-1 text-xs text-gray-400 dark:text-slate-500">Used internally and for legacy compatibility. Letters/numbers/dashes recommended.</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="{{ $label }}">Display order</label>
        <input type="number" name="display_order" value="{{ old('display_order', $type?->display_order ?? 0) }}" min="0" class="{{ $field }}">
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-slate-700"
                   @checked(old('is_active', $type?->is_active ?? true))>
            Active
        </label>
    </div>
</div>

