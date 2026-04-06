<section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Account overview') }}</h2>
    <div class="mt-3 flex flex-wrap items-start gap-3 sm:gap-4">
        <img
            src="{{ $user->profilePhotoUrl() }}"
            alt=""
            class="h-14 w-14 shrink-0 rounded-full border border-gray-200 object-cover dark:border-slate-700"
            width="56"
            height="56"
            loading="lazy"
            referrerpolicy="no-referrer"
        >
        <dl class="min-w-0 flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm text-gray-900 dark:text-white">
            <div class="flex flex-wrap gap-x-2 sm:col-span-2">
                <dt class="text-gray-500 dark:text-slate-500">{{ __('Role') }}</dt>
                <dd>{{ $user->is_admin ? __('MIS administrator') : __('Staff') }}</dd>
            </div>
            <div class="flex flex-wrap gap-x-2">
                <dt class="text-gray-500 dark:text-slate-500">{{ __('Member since') }}</dt>
                <dd>{{ $user->created_at->timezone(config('app.timezone'))->format('j M Y') }}</dd>
            </div>
            <div class="flex flex-wrap gap-x-2 items-center min-w-0">
                <dt class="text-gray-500 dark:text-slate-500 shrink-0">{{ __('Verification') }}</dt>
                <dd>
                    @if ($user->hasVerifiedEmail())
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200">{{ __('Verified') }}</span>
                    @else
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-900 dark:bg-amber-950/60 dark:text-amber-200">{{ __('Not verified') }}</span>
                    @endif
                </dd>
            </div>
            <div class="flex flex-wrap gap-x-2 sm:col-span-2 min-w-0">
                <dt class="text-gray-500 dark:text-slate-500 shrink-0">{{ __('Email') }}</dt>
                <dd class="break-all min-w-0">{{ $user->email }}</dd>
            </div>
        </dl>
    </div>
</section>
