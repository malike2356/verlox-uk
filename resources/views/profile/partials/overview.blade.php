<section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 overflow-hidden">

    {{-- Banner --}}
    <div class="h-20 bg-gradient-to-r from-sky-500/20 via-verlox-accent/10 to-transparent dark:from-sky-900/30 dark:via-slate-800/60 dark:to-slate-900/0"></div>

    <div class="px-5 pb-5">
        {{-- Avatar row --}}
        <div class="-mt-9 flex flex-wrap items-end justify-between gap-3">
            <div class="flex items-end gap-3">
                <img
                    src="{{ $user->profilePhotoUrl() }}"
                    alt=""
                    class="h-16 w-16 shrink-0 rounded-xl border-2 border-white dark:border-slate-900 object-cover shadow"
                    width="64" height="64"
                    loading="lazy"
                    referrerpolicy="no-referrer"
                >
                <div class="mb-0.5 min-w-0">
                    <h1 class="truncate text-base font-semibold text-gray-900 dark:text-white leading-snug">{{ $user->name }}</h1>
                    <p class="truncate text-xs text-gray-500 dark:text-slate-400">{{ $user->job_title ?: __('No job title set') }}</p>
                </div>
            </div>
            <div class="mb-1 flex flex-wrap items-center gap-1.5">
                @if ($user->hasVerifiedEmail())
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ __('Verified') }}
                    </span>
                @else
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">{{ __('Unverified') }}</span>
                @endif
                <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-medium text-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                    {{ $user->is_admin ? __('Administrator') : __('Staff') }}
                </span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mt-4 flex flex-wrap gap-x-8 gap-y-2 border-t border-gray-100 dark:border-slate-800 pt-4">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-slate-500">{{ __('Email') }}</p>
                <p class="mt-0.5 text-sm text-gray-900 dark:text-white break-all">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-slate-500">{{ __('Member since') }}</p>
                <p class="mt-0.5 text-sm text-gray-900 dark:text-white">{{ $user->created_at->timezone(config('app.timezone'))->format('j M Y') }}</p>
            </div>
            @if ($user->phone)
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-slate-500">{{ __('Phone') }}</p>
                    <p class="mt-0.5 text-sm text-gray-900 dark:text-white">{{ $user->phone }}</p>
                </div>
            @endif
        </div>
    </div>

</section>
