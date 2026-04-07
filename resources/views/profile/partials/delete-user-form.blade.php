<section class="rounded-2xl border border-red-200 dark:border-red-900/40 bg-white dark:bg-slate-900/80 p-5 space-y-4">

    <header class="border-b border-red-100 dark:border-red-900/30 pb-4">
        <h2 class="text-sm font-semibold text-red-700 dark:text-red-400">{{ __('Delete account') }}</h2>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400 leading-snug">
            {{ __('Permanently remove your account and all associated data. This cannot be undone.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete account…') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5 p-6">
            @csrf
            @method('delete')

            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Delete this account permanently?') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                    {{ __('Enter your password to confirm. You will be signed out immediately and all your data will be removed.') }}
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="password">{{ __('Password') }}</label>
                <x-password-input
                    id="password"
                    name="password"
                    class="mt-0 block w-full max-w-sm"
                    required
                    placeholder="{{ __('Enter your password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-gray-100 dark:border-slate-800 pt-4">
                <x-secondary-button type="button"
                    class="dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button>{{ __('Delete account') }}</x-danger-button>
            </div>
        </form>
    </x-modal>

</section>
