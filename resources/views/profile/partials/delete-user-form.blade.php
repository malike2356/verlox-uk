<section class="rounded-2xl border border-red-200 dark:border-red-900/50 bg-white/90 dark:bg-slate-900/40 p-4 space-y-2">
    <header>
        <h2 class="text-xs font-semibold uppercase tracking-wide text-red-800 dark:text-red-300">{{ __('Delete account') }}</h2>
        <p class="mt-0.5 text-xs text-gray-600 dark:text-slate-400 leading-snug">
            {{ __('Permanently delete this account. This cannot be undone.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete account…') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ __('Delete this account permanently?') }}
            </h2>

            <p class="text-sm text-gray-600 dark:text-slate-400">
                {{ __('Enter your password to confirm. You will be signed out immediately.') }}
            </p>

            <div>
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-password-input
                    id="password"
                    name="password"
                    class="mt-1 block w-full max-w-md"
                    required
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-wrap justify-end gap-2 pt-2">
                <x-secondary-button type="button" class="dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button>
                    {{ __('Delete account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
