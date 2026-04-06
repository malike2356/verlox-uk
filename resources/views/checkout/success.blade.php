@extends('layouts.marketing')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12 text-center relative">
    <div class="absolute top-4 right-4">
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle colour mode">
            <span class="theme-toggle__icon" aria-hidden="true"></span>
            <span data-theme-label class="theme-toggle__text">Dark</span>
        </button>
    </div>
    <div class="max-w-md">
        <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-white">Payment received</h1>
        <p class="mt-3 text-gray-600 dark:text-[#B8C0D8] text-sm">Thank you. Your invoice will be marked paid automatically once Stripe confirms the session.</p>
        @if($sessionId)
            <p class="mt-4 text-xs font-mono text-gray-500 dark:text-[#9AA5B9] break-all">Ref: {{ $sessionId }}</p>
        @endif
        <a href="{{ route('marketing.home') }}" class="mt-8 inline-block text-verlox-accent text-verlox-accent-hover text-sm">Return home</a>
    </div>
</div>
@endsection
