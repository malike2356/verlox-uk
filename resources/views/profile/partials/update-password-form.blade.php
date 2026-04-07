<section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-5 space-y-4">

    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Password') }}</h2>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400 leading-snug">
            {{ __('Use a long, random password. A password manager can help.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="update_password_current_password">{{ __('Current password') }}</label>
            <x-password-input id="update_password_current_password" name="current_password" class="mt-0 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="update_password_password">{{ __('New password') }}</label>
            <x-password-input id="update_password_password" name="password" class="mt-0 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="update_password_password_confirmation">{{ __('Confirm new password') }}</label>
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" class="mt-0 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 dark:border-slate-800 pt-3">
            <x-primary-button>{{ __('Update password') }}</x-primary-button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

</section>
