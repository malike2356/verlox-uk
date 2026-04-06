<section id="mis-profile-edit" class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
    <header class="space-y-0.5">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">{{ __('Profile information') }}</h2>
        <p class="text-xs text-gray-600 dark:text-slate-400 leading-snug">
            {{ __('Name, photo, contact, and email. Changing email may require verification again.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        @method('patch')

        <div class="rounded-lg border border-gray-200 dark:border-slate-700 p-3">
            <label class="text-xs font-medium text-gray-600 dark:text-slate-400">{{ __('Profile photo') }}</label>
            <p class="text-xs text-gray-500 dark:text-slate-500">{{ __('Square images work best. PNG, JPG, WebP or GIF, up to 2&nbsp;MB.') }}</p>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <img
                    src="{{ $user->profilePhotoUrl() }}"
                    alt=""
                    class="h-12 w-12 rounded-full border border-gray-200 object-cover dark:border-slate-700"
                    width="48"
                    height="48"
                    loading="lazy"
                    referrerpolicy="no-referrer"
                >
                <div class="min-w-0 flex-1">
                    <input type="file" name="avatar" accept="image/*" class="block w-full max-w-full text-xs text-gray-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-gray-200 file:px-2 file:py-1 dark:file:bg-slate-700">
                    @if ($user->avatar_path)
                        <label class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 dark:border-slate-600">
                            {{ __('Remove uploaded photo') }}
                        </label>
                    @endif
                    <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <div class="min-w-0 sm:col-span-2 xl:col-span-1">
                <label class="text-xs text-gray-500 dark:text-slate-500" for="name">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>
            <div class="min-w-0">
                <label class="text-xs text-gray-500 dark:text-slate-500" for="job_title">{{ __('Job title') }}</label>
                <input id="job_title" name="job_title" type="text" value="{{ old('job_title', $user->job_title) }}" autocomplete="organization-title" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white" placeholder="{{ __('Optional') }}">
                <x-input-error class="mt-1" :messages="$errors->get('job_title')" />
            </div>
            <div class="min-w-0">
                <label class="text-xs text-gray-500 dark:text-slate-500" for="phone">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white" placeholder="{{ __('Optional') }}">
                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
            </div>
        </div>

        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-xs text-gray-800 dark:text-slate-200">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" type="submit" class="underline text-verlox-accent hover:opacity-90 rounded-md focus:outline-none focus:ring-2 focus:ring-[#C9A84C]">
                            {{ __('Resend verification email') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-0.5">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
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
