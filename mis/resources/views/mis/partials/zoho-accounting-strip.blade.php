@php
    $compact = $compact ?? false;
    $automation = $automation ?? null;
    $zohoOk = app(\App\Services\ZohoBooksClient::class)->isConfigured();
    $settingsFragmentUrl = route('mis.settings.edit').'#zoho-books';
@endphp

@if ($compact)
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-600 dark:text-slate-400">
        <span class="inline-flex items-center gap-1.5 font-medium text-gray-800 dark:text-slate-200">
            <i class="fa-solid fa-cloud text-sky-500 dark:text-sky-400" aria-hidden="true"></i>
            {{ __('Zoho Books') }}
        </span>
        @if ($zohoOk)
            <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-800 dark:text-emerald-300">{{ __('Linked') }}</span>
        @else
            <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-900 dark:text-amber-200">{{ __('Not set up') }}</span>
        @endif
        <span class="text-gray-400 dark:text-slate-600">·</span>
        <a href="{{ route('mis.zoho.index') }}" class="text-verlox-accent hover:underline">{{ __('Sync log') }}</a>
        <span class="text-gray-400 dark:text-slate-600">·</span>
        <a href="{{ $settingsFragmentUrl }}" class="text-verlox-accent hover:underline">{{ __('Credentials') }}</a>
    </div>
@else
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-sky-200/80 bg-sky-50/60 px-4 py-3 dark:border-sky-900/50 dark:bg-sky-950/25">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-900 dark:text-sky-200">{{ __('Accounting: Zoho Books') }}</p>
            <p class="mt-1 text-xs text-gray-600 dark:text-slate-400">
                {{ __('MIS invoices and expenses sync to your Zoho ledger. Open the sync log to audit pushes, or company settings for OAuth and auto-sync.') }}
            </p>
            <p class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs">
                <a href="{{ route('mis.zoho.index') }}" class="font-medium text-verlox-accent hover:underline">{{ __('Zoho sync log') }}</a>
                <a href="{{ $settingsFragmentUrl }}" class="font-medium text-verlox-accent hover:underline">{{ __('Zoho credentials & automation') }}</a>
            </p>
            @if (is_array($automation) && $zohoOk)
                <p class="mt-2 text-xs text-gray-700 dark:text-slate-300">
                    {{ __('Invoice auto-sync') }}:
                    <strong>{{ ! empty($automation['invoice']) ? __('On') : __('Off') }}</strong>
                    <span class="mx-1 text-gray-400">·</span>
                    {{ __('Expense auto-sync') }}:
                    <strong>{{ ! empty($automation['expense']) ? __('On') : __('Off') }}</strong>
                </p>
            @endif
        </div>
        <div class="flex shrink-0 flex-col items-end gap-1">
            @if ($zohoOk)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-800 dark:text-emerald-300">
                    <i class="fa-solid fa-link text-[10px]" aria-hidden="true"></i> {{ __('Connected') }}
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-900 dark:text-amber-200">
                    <i class="fa-solid fa-triangle-exclamation text-[10px]" aria-hidden="true"></i> {{ __('Not configured') }}
                </span>
                <a href="{{ $settingsFragmentUrl }}" class="text-xs font-medium text-verlox-accent hover:underline">{{ __('Connect in settings') }}</a>
            @endif
        </div>
    </div>
@endif
