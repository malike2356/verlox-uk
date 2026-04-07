<section class="rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm bg-white dark:bg-slate-900/60">

    {{-- Banner: vibrant in light, deep blue-indigo in dark --}}
    <div class="h-32 bg-gradient-to-r from-sky-400 via-sky-500 to-indigo-500 dark:from-indigo-950 dark:via-sky-950 dark:to-slate-900"></div>

    <div class="px-6 pb-6">

        {{-- Avatar + badges --}}
        <div class="-mt-6 flex flex-wrap items-end justify-between gap-4">
            <div class="flex items-end gap-4">
                <img
                    src="{{ $user->profilePhotoUrl() }}"
                    alt=""
                    class="h-20 w-20 shrink-0 rounded-2xl border-4 border-white dark:border-slate-900 object-cover object-top shadow-lg"
                    width="80" height="80"
                    loading="lazy"
                    referrerpolicy="no-referrer"
                >
                <div class="mb-1 min-w-0">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 truncate">{{ $user->job_title ?: __('No job title set') }}</p>
                </div>
            </div>

            <div class="mb-2 flex flex-wrap items-center gap-2">
                @if ($user->hasVerifiedEmail())
                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                        <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ __('Verified') }}
                    </span>
                @else
                    <span class="rounded-full border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/50 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">{{ __('Unverified') }}</span>
                @endif
                <span class="rounded-full border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/50 px-3 py-1 text-xs font-semibold text-sky-700 dark:text-sky-300">
                    {{ $user->is_admin ? __('Administrator') : __('Staff') }}
                </span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mt-5 flex flex-wrap gap-x-10 gap-y-3 border-t border-gray-100 dark:border-slate-800 pt-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-500">{{ __('Email') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white break-all">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-500">{{ __('Member since') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->timezone(config('app.timezone'))->format('j M Y') }}</p>
            </div>
            @if ($user->phone)
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-500">{{ __('Phone') }}</p>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone }}</p>
                </div>
            @endif
        </div>

    </div>

</section>
