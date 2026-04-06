@extends('layouts.mis')

@section('title', $isEdit ? 'Edit pricing plan' : 'New pricing plan')
@section('heading', $isEdit ? 'Edit pricing plan' : 'New pricing plan')

@section('content')
@php
    $field = 'h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-900 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white';
    $label = 'mb-1 block text-xs font-medium text-gray-600 dark:text-slate-400';
    $ta = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white';
@endphp

<form method="post"
      action="{{ $isEdit ? route('mis.pricing-plans.update', $plan) : route('mis.pricing-plans.store') }}"
      class="max-w-3xl space-y-8 text-sm">
    @csrf
    @if($isEdit)
        @method('patch')
    @endif

    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Basics</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="{{ $label }}">Name</label>
                <input type="text" name="name" required value="{{ old('name', $plan->name) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">URL slug (optional - generated from name if empty)</label>
                <input type="text" name="slug" value="{{ old('slug', $plan->slug) }}" class="{{ $field }}" placeholder="starter-monthly">
            </div>
            <div>
                <label class="{{ $label }}">Display order</label>
                <input type="number" name="display_order" min="0" value="{{ old('display_order', $plan->display_order) }}" class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $plan->tagline) }}" class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">Description</label>
                <textarea name="description" rows="3" class="{{ $ta }}">{{ old('description', $plan->description) }}</textarea>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Price</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Amount (£)</label>
                <input type="number" name="price_gbp" step="0.01" min="0"
                       value="{{ old('price_gbp', $plan->price_pence !== null ? number_format($plan->price_pence / 100, 2, '.', '') : '') }}"
                       class="{{ $field }}" placeholder="Leave empty for “Contact us”">
            </div>
            <div>
                <label class="{{ $label }}">Compare-at (£) <span class="font-normal text-gray-400">optional</span></label>
                <input type="number" name="compare_at_gbp" step="0.01" min="0"
                       value="{{ old('compare_at_gbp', $plan->compare_at_pence !== null ? number_format($plan->compare_at_pence / 100, 2, '.', '') : '') }}"
                       class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Currency</label>
                <input type="text" name="currency" maxlength="3" value="{{ old('currency', $plan->currency) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Billing period</label>
                <select name="billing_period" class="{{ $field }}">
                    @foreach($billingPeriods as $key => $lbl)
                        <option value="{{ $key }}" @selected(old('billing_period', $plan->billing_period) === $key)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Sessions included <span class="font-normal text-gray-400">one-off packs</span></label>
                <input type="number" name="sessions_included" min="1" max="9999"
                       value="{{ old('sessions_included', $plan->sessions_included) }}" class="{{ $field }}" placeholder="e.g. 5">
            </div>
            <div>
                <label class="{{ $label }}">Price display override</label>
                <input type="text" name="price_display_override" value="{{ old('price_display_override', $plan->price_display_override) }}"
                       class="{{ $field }}" placeholder="Overrides computed price text">
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Call to action</h2>
        <p class="mb-3 text-xs text-gray-500 dark:text-slate-400">First match wins: custom URL → linked offering checkout → named route.</p>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Button label</label>
                <input type="text" name="cta_label" value="{{ old('cta_label', $plan->cta_label) }}" class="{{ $field }}" placeholder="Get started">
            </div>
            <div>
                <label class="{{ $label }}">Link to offering (checkout)</label>
                <select name="offering_id" class="{{ $field }}">
                    <option value="">None</option>
                    @foreach($offerings as $o)
                        <option value="{{ $o->id }}" @selected((string) old('offering_id', $plan->offering_id) === (string) $o->id)>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Or internal route</label>
                <select name="cta_route" class="{{ $field }}">
                    <option value="">None</option>
                    @foreach($ctaRoutes as $routeName => $_)
                        <option value="{{ $routeName }}" @selected(old('cta_route', $plan->cta_route) === $routeName)>{{ $routeName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">Or full URL</label>
                <input type="url" name="cta_url" value="{{ old('cta_url', $plan->cta_url) }}" class="{{ $field }}" placeholder="https://…">
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Features</h2>
        <p class="mb-2 text-xs text-gray-500 dark:text-slate-400">One per line. Prefix with <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">!</code> for “not included” (shown muted).</p>
        <textarea name="features_text" rows="10" class="{{ $ta }}">{{ old('features_text', $featuresText) }}</textarea>
    </section>

    <section class="rounded-2xl border border-gray-200/80 bg-white/60 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Visibility &amp; status</h2>
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                <input type="hidden" name="show_on_home" value="0">
                <input type="checkbox" name="show_on_home" value="1" class="rounded" @checked((int) old('show_on_home', $plan->show_on_home ? 1 : 0) === 1)>
                Home page
            </label>
            <label class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                <input type="hidden" name="show_on_book" value="0">
                <input type="checkbox" name="show_on_book" value="1" class="rounded" @checked((int) old('show_on_book', $plan->show_on_book ? 1 : 0) === 1)>
                Book page
            </label>
            <label class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                <input type="hidden" name="show_on_va" value="0">
                <input type="checkbox" name="show_on_va" value="1" class="rounded" @checked((int) old('show_on_va', $plan->show_on_va ? 1 : 0) === 1)>
                VA page
            </label>
            <label class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded" @checked((int) old('is_active', $plan->is_active ? 1 : 0) === 1)>
                Active
            </label>
            <label class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" class="rounded" @checked((int) old('is_featured', $plan->is_featured ? 1 : 0) === 1)>
                Featured card
            </label>
        </div>
    </section>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-verlox-accent px-5 text-sm font-semibold text-on-verlox-accent hover:opacity-95">
            {{ $isEdit ? 'Save changes' : 'Create plan' }}
        </button>
        <a href="{{ route('mis.pricing-plans.index') }}" class="inline-flex h-10 items-center rounded-lg border border-gray-300 px-5 text-sm text-gray-700 dark:border-slate-600 dark:text-slate-300">Cancel</a>
    </div>
</form>
@endsection
