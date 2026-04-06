@props(['disabled' => false, 'rounded' => 'rounded-md'])

@php
    $outerClass = trim($attributes->get('class') ?? '');
    $inputAttrs = $attributes->except('class');
    $round = in_array($rounded, ['rounded-md', 'rounded-lg', 'rounded-xl'], true) ? $rounded : 'rounded-md';
@endphp

<div class="{{ $outerClass }}" x-data="{ showPassword: false }">
    <div
        class="password-field-well flex w-full min-h-[42px] min-w-0 items-stretch overflow-hidden border border-gray-300 bg-white shadow-sm transition-colors focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:focus-within:border-[#C9A84C] dark:focus-within:ring-[#C9A84C] {{ $round }}"
    >
        <input
            x-bind:type="showPassword ? 'text' : 'password'"
            @disabled($disabled)
            {{ $inputAttrs->merge([
                'class' => 'password-field-well-input min-w-0 flex-1 border-0 bg-transparent py-2 pl-3 pr-2 text-gray-900 shadow-none ring-0 placeholder:text-gray-500 focus:border-0 focus:outline-none focus:ring-0 caret-gray-900 disabled:cursor-not-allowed disabled:opacity-60 dark:text-white dark:placeholder:text-slate-400 dark:caret-white',
            ]) }}
        />
        <button
            type="button"
            class="flex shrink-0 items-center self-stretch border-l border-gray-200 bg-slate-50 px-3 text-gray-600 hover:bg-slate-100 hover:text-gray-900 focus:outline-none focus-visible:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 dark:hover:text-white"
            @if($disabled) disabled @endif
            @click="if (!$el.disabled) showPassword = !showPassword"
            :aria-label="showPassword ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
            :aria-pressed="showPassword"
        >
            <span x-show="!showPassword" x-cloak class="flex" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            <span x-show="showPassword" x-cloak class="flex" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.006 5.432M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </span>
        </button>
    </div>
</div>
