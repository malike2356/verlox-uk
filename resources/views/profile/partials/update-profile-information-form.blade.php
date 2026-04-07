<section id="mis-profile-edit" class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-5 space-y-5">

    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Profile information') }}</h2>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400 leading-snug">
            {{ __('Update your name, photo, contact details, and email address.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        {{-- Photo --}}
        <div>
            <p class="text-xs font-medium text-gray-700 dark:text-slate-300 mb-2">{{ __('Profile photo') }}</p>
            <div class="flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950/50 p-3">
                <img
                    src="{{ $user->profilePhotoUrl() }}"
                    alt=""
                    class="h-14 w-14 shrink-0 rounded-xl border border-gray-200 dark:border-slate-700 object-cover"
                    width="56" height="56"
                    loading="lazy"
                    referrerpolicy="no-referrer"
                >
                <div class="min-w-0 flex-1 space-y-1">
                    <input type="file" name="avatar" accept="image/*"
                        class="block w-full max-w-full text-xs text-gray-600 dark:text-slate-400
                               file:mr-2 file:rounded-md file:border-0 file:bg-gray-200 dark:file:bg-slate-700
                               file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-gray-700 dark:file:text-slate-200
                               file:cursor-pointer hover:file:bg-gray-300 dark:hover:file:bg-slate-600">
                    <p class="text-[11px] text-gray-400 dark:text-slate-500">{{ __('Square image, PNG · JPG · WebP, max 2 MB') }}</p>
                    @if ($user->avatar_path)
                        <label class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 dark:border-slate-600">
                            {{ __('Remove uploaded photo') }}
                        </label>
                    @endif
                    <x-input-error :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        {{-- Name / Title / Phone --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="name">{{ __('Full name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="job_title">{{ __('Job title') }}</label>
                <input id="job_title" name="job_title" type="text" value="{{ old('job_title', $user->job_title) }}" autocomplete="organization-title"
                    placeholder="{{ __('e.g. Operations Manager') }}"
                    class="w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-600 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <x-input-error class="mt-1" :messages="$errors->get('job_title')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="phone">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                    placeholder="{{ __('Optional') }}"
                    class="w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-600 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1" for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                    class="w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <x-input-error class="mt-1" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 rounded-md bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 px-3 py-2">
                        <p class="text-xs text-amber-800 dark:text-amber-200">
                            {{ __('Your email is unverified.') }}
                            <button form="send-verification" type="submit" class="font-medium underline hover:no-underline">{{ __('Resend link') }}</button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-400">{{ __('Verification link sent.') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 dark:border-slate-800 pt-4">
            <x-primary-button>{{ __('Save changes') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

</section>
