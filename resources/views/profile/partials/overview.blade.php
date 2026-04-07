<section class="rounded-2xl border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm">

    {{-- Banner --}}
    <div class="h-24 bg-gradient-to-r from-sky-400 via-sky-500 to-indigo-500 dark:from-sky-700 dark:via-sky-800 dark:to-indigo-900"></div>

    <div class="bg-white dark:bg-slate-900/60 px-6 pb-6">
        {{-- Avatar + name row --}}
        <div class="-mt-10 flex flex-wrap items-end justify-between gap-4">
            <div class="flex items-end gap-4">
                <img
                    src="{{ $user->profilePhotoUrl() }}"
                    alt=""
                    class="h-20 w-20 shrink-0 rounded-2xl border-4 border-white dark:border-slate-900 object-cover shadow-md"
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
                    <span style="display:inline-flex;align-items:center;gap:4px;background:#dcfce7;color:#166534;border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:600;">
                        <svg style="width:12px;height:12px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ __('Verified') }}
                    </span>
                @else
                    <span style="background:#fef9c3;color:#854d0e;border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:600;">{{ __('Unverified') }}</span>
                @endif
                <span style="background:#e0f2fe;color:#075985;border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:600;">
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
