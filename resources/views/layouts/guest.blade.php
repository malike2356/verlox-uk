@php($guestCompany = \App\Models\CompanySetting::current())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($fav = $guestCompany->faviconPublicUrl())
        <link rel="icon" href="{{ $fav }}">
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|cormorant-garamond:600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; }</style>
</head>
<body class="font-sans antialiased bg-verlox-page text-gray-900 dark:text-[#E5E7EB]">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
        <div class="absolute top-4 right-4">
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle colour mode">
                <span class="theme-toggle__icon" aria-hidden="true"></span>
                <span data-theme-label class="theme-toggle__text">Dark</span>
            </button>
        </div>
        <div>
            <a href="/" class="flex flex-col items-center gap-2 text-center">
                <span class="inline-flex h-14 w-14 items-center justify-center rounded-[11px] bg-gradient-to-br from-indigo-100 to-sky-100 text-indigo-900 font-display text-2xl font-bold border border-indigo-200/80 dark:from-[rgba(201,168,76,.22)] dark:to-[rgba(201,168,76,.08)] dark:text-[#C9A84C] dark:border-[rgba(201,168,76,.4)]">V</span>
                <span class="text-sm text-gray-600 dark:text-slate-200">Back to site</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-[#0F223C] border border-gray-200 dark:border-slate-600/80 shadow-sm dark:shadow-none overflow-hidden sm:rounded-[22px]">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
