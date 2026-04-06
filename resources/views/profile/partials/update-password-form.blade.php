<section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4 space-y-4">
    <header>
        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Update password') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
            {{ __('Use a long, random password. A password manager can generate and store one for you.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500" for="update_password_current_password">{{ __('Current password') }}</label>
            <x-password-input id="update_password_current_password" name="current_password" class="mt-1 block w-full max-w-xl" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500" for="update_password_password">{{ __('New password') }}</label>
            <x-password-input id="update_password_password" name="password" class="mt-1 block w-full max-w-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500" for="update_password_password_confirmation">{{ __('Confirm new password') }}</label>
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" class="mt-1 block w-full max-w-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <x-primary-button>{{ __('Save password') }}</x-primary-button>
            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-slate-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
