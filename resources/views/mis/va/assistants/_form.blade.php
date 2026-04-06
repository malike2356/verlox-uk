@php
    /** @var \App\Models\VaAssistant|null $assistant */
    $assistant = $assistant ?? null;
@endphp
<div class="space-y-3 max-w-xl">
    <div>
        <label class="text-xs text-gray-500 dark:text-slate-500">Full name</label>
        <input name="full_name" value="{{ old('full_name', $assistant?->full_name) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
    </div>
    <div>
        <label class="text-xs text-gray-500 dark:text-slate-500">Email</label>
        <input type="email" name="email" value="{{ old('email', $assistant?->email) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Country</label>
            <input name="country" value="{{ old('country', $assistant?->country) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Timezone (IANA)</label>
            <input name="timezone" value="{{ old('timezone', $assistant?->timezone ?? 'Europe/London') }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white font-mono text-xs">
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Hourly rate (£)</label>
            <input type="number" step="0.01" min="0" name="hourly_rate_gbp" value="{{ old('hourly_rate_gbp', $assistant?->hourly_rate_gbp) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Availability</label>
            <select name="availability" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach (\App\Models\VaAssistant::$availabilities as $av)
                    <option value="{{ $av }}" @selected(old('availability', $assistant?->availability ?? 'available') === $av)>{{ $av }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="text-xs text-gray-500 dark:text-slate-500">Skills (comma or newline separated)</label>
        <textarea name="skills_raw" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-xs font-mono" placeholder="admin, social media, CRM">{{ old('skills_raw', $assistant && $assistant->skills ? implode(', ', $assistant->skills) : '') }}</textarea>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Performance score (1–5)</label>
            <input type="number" step="0.1" min="1" max="5" name="perform_score" value="{{ old('perform_score', $assistant?->perform_score) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Wise / payout email</label>
            <input type="email" name="wise_email" value="{{ old('wise_email', $assistant?->wise_email) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-xs">
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Payment currency</label>
            <input name="payment_currency" maxlength="3" value="{{ old('payment_currency', $assistant?->payment_currency ?? 'GBP') }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white font-mono text-xs">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Phone</label>
            <input name="phone" value="{{ old('phone', $assistant?->phone) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
    </div>
    <div>
        <label class="text-xs text-gray-500 dark:text-slate-500">Notes</label>
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">{{ old('notes', $assistant?->notes) }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-300">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $assistant?->is_active ?? true)) class="rounded border-gray-300">
        Active on roster
    </label>
</div>
