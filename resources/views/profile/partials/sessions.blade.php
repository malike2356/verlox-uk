@php
    $currentSessionId = request()->session()->getId();
    $sessionDriver = config('session.driver');
@endphp

<section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4 space-y-2">
    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Active sessions') }}</h2>

    @if ($sessionDriver !== 'database')
        <p class="text-xs text-gray-600 dark:text-slate-400">
            {{ __('Sessions are stored in :driver mode. To list and sign out other devices, set SESSION_DRIVER=database in your environment.', ['driver' => $sessionDriver]) }}
        </p>
    @elseif ($sessions->isEmpty())
        <p class="text-xs text-gray-600 dark:text-slate-400">{{ __('No session records were found for your account.') }}</p>
    @else
        <p class="text-xs text-gray-600 dark:text-slate-400">
            {{ __('Devices where you are signed in. Sign out elsewhere if you lose a device.') }}
        </p>
        <ul class="divide-y divide-gray-200 dark:divide-slate-800 rounded-lg border border-gray-200 dark:border-slate-800">
            @foreach ($sessions as $row)
                @php
                    $isCurrent = $row->id === $currentSessionId;
                    $ua = $row->user_agent ?? '';
                    $label = __('Unknown browser');
                    if (str_contains($ua, 'Firefox')) {
                        $label = 'Firefox';
                    } elseif (str_contains($ua, 'Edg/')) {
                        $label = 'Microsoft Edge';
                    } elseif (str_contains($ua, 'Chrome')) {
                        $label = 'Chrome';
                    } elseif (str_contains($ua, 'Safari') && ! str_contains($ua, 'Chrome')) {
                        $label = 'Safari';
                    }
                    $last = \Illuminate\Support\Carbon::createFromTimestamp($row->last_activity)->timezone(config('app.timezone'));
                @endphp
                <li class="flex flex-wrap items-start justify-between gap-2 px-3 py-2 text-sm">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $label }}
                            @if ($isCurrent)
                                <span class="ms-1 rounded bg-verlox-accent/20 px-1.5 py-0.5 text-xs font-semibold text-gray-900 dark:text-[#E7D59C]">{{ __('This device') }}</span>
                            @endif
                        </p>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-500">
                            {{ $row->ip_address ?: '-' }} · {{ __('Last active') }} {{ $last->diffForHumans() }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($sessions->count() > 1)
            <form method="post" action="{{ route('profile.sessions.logout-others') }}" class="space-y-2 rounded-lg border border-amber-200 bg-amber-50/80 p-3 dark:border-amber-900/50 dark:bg-amber-950/20">
                @csrf
                <p class="text-xs text-gray-800 dark:text-amber-100/90">{{ __('Sign out all sessions except this browser.') }}</p>
                <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                    <div class="min-w-0">
                        <label class="text-xs text-gray-600 dark:text-slate-400" for="sessions_logout_password">{{ __('Confirm your password') }}</label>
                        <x-password-input id="sessions_logout_password" name="password" class="mt-1 block w-full" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <button type="submit" class="inline-flex h-10 shrink-0 items-center justify-center rounded-lg border border-amber-800 bg-amber-700 px-4 text-xs font-semibold uppercase tracking-wide text-white hover:bg-amber-800 dark:border-amber-700 dark:bg-amber-900 dark:hover:bg-amber-800 sm:h-[42px]">
                        {{ __('Sign out others') }}
                    </button>
                </div>
            </form>
        @endif
    @endif
</section>
