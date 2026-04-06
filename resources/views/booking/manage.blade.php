<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    @include('partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($fav = $settings->faviconPublicUrl())
        <link rel="icon" href="{{ $fav }}">
    @endif
    <title>Manage booking | {{ $settings->company_name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'DM Sans', system-ui, sans-serif; }</style>
</head>
<body class="bg-verlox-page text-gray-900 dark:text-[#E5E7EB] antialiased min-h-screen p-4">
<div class="max-w-md mx-auto">
    <div class="flex justify-end mb-4">
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle colour mode">
            <span class="theme-toggle__icon" aria-hidden="true"></span>
            <span data-theme-label class="theme-toggle__text">Dark</span>
        </button>
    </div>
    <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/50 p-5 shadow-sm">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Your booking</h1>
        @if (session('status'))
            <p class="mt-3 text-sm text-emerald-700 dark:text-emerald-300">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
        @endif

        @if($booking->status === 'cancelled')
            <p class="mt-4 text-sm text-gray-600 dark:text-slate-400">This booking has been cancelled.</p>
        @else
            <p class="mt-3 text-sm text-gray-600 dark:text-slate-400">{{ $booking->contact_name }}, here are your meeting details.</p>
            <div class="mt-4 rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-sm">
                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->starts_at->timezone($booking->timezone)->format('l j F Y') }}</p>
                <p class="text-gray-700 dark:text-slate-300 mt-1">{{ $booking->starts_at->timezone($booking->timezone)->format('H:i') }} - {{ $booking->ends_at->timezone($booking->timezone)->format('H:i') }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-500 mt-2">{{ $booking->timezone }}</p>
            </div>
            @if($booking->meeting_url)
                <p class="mt-4 text-xs text-gray-500 dark:text-slate-500">Join link</p>
                <a href="{{ $booking->meeting_url }}" class="text-sm text-verlox-accent break-all">{{ $booking->meeting_url }}</a>
            @endif

            <div class="mt-6 flex flex-col gap-3">
                <a href="{{ route('public.booking.reschedule.form', [$booking, $token]) }}" class="text-center rounded-full bg-verlox-accent py-2.5 text-sm font-semibold text-on-verlox-accent">Reschedule</a>
                <form method="post" action="{{ route('public.booking.cancel', [$booking, $token]) }}" onsubmit="return confirm('Cancel this booking?');">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-red-300 dark:border-red-800 py-2.5 text-sm font-medium text-red-700 dark:text-red-300">Cancel booking</button>
                </form>
            </div>
        @endif
    </div>
</div>
</body>
</html>
