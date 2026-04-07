@php
    $currentSessionId = request()->session()->getId();
    $sessionDriver = config('session.driver');
@endphp

<section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-5 space-y-4">

    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Active sessions') }}</h2>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400 leading-snug">
            {{ __('Devices currently signed in to your account.') }}
        </p>
    </header>

    @if ($sessionDriver !== 'database')
        <p class="text-xs text-gray-500 dark:text-slate-400 italic">
            {{ __('Session listing requires SESSION_DRIVER=database.') }}
        </p>
    @elseif ($sessions->isEmpty())
        <p class="text-xs text-gray-500 dark:text-slate-400">{{ __('No active sessions found.') }}</p>
    @else
        <ul class="divide-y divide-gray-100 dark:divide-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            @foreach ($sessions as $row)
                @php
                    $isCurrent = $row->id === $currentSessionId;
                    $ua = $row->user_agent ?? '';
                    $label = __('Unknown browser');
                    if (str_contains($ua, 'Firefox'))       $label = 'Firefox';
                    elseif (str_contains($ua, 'Edg/'))      $label = 'Microsoft Edge';
                    elseif (str_contains($ua, 'Chrome'))    $label = 'Chrome';
                    elseif (str_contains($ua, 'Safari') && ! str_contains($ua, 'Chrome')) $label = 'Safari';
                    $last = \Illuminate\Support\Carbon::createFromTimestamp($row->last_activity)->timezone(config('app.timezone'));
                @endphp
                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 {{ $isCurrent ? 'bg-sky-50/60 dark:bg-sky-950/20' : '' }}">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $label }}
                            @if ($isCurrent)
                                <span class="ms-1.5 rounded-full bg-sky-100 dark:bg-sky-900/40 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-700 dark:text-sky-300">{{ __('This device') }}</span>
                            @endif
                        </p>
                        <p class="mt-0.5 text-xs text-gray-400 dark:text-slate-500">
                            {{ $row->ip_address ?: '—' }} &middot; {{ $last->diffForHumans() }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($sessions->count() > 1)
            <form method="post" action="{{ route('profile.sessions.logout-others') }}"
                class="rounded-xl border border-amber-200 dark:border-amber-800/50 bg-amber-50/60 dark:bg-amber-950/20 p-4 space-y-3">
                @csrf
                <p class="text-xs text-amber-800 dark:text-amber-200/80">{{ __('Sign out all other devices except this browser.') }}</p>
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-0 flex-1">
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="sessions_logout_password">{{ __('Confirm password') }}</label>
                        <x-password-input id="sessions_logout_password" name="password" class="mt-0 block w-full" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <button type="submit"
                        class="shrink-0 inline-flex items-center justify-center rounded-lg border border-amber-700 bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700 dark:border-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700">
                        {{ __('Sign out others') }}
                    </button>
                </div>
            </form>
        @endif
    @endif

</section>
