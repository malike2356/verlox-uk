@php($settings = \App\Models\CompanySetting::current())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $doc->title }} - {{ $settings->company_name }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|cormorant-garamond:600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-verlox-page text-gray-900 dark:text-[#E5E7EB]">
<main class="max-w-[min(100%,70rem)] mx-auto px-4 py-10">
    <div class="flex items-center justify-between gap-3 mb-6">
        <a class="text-sm text-verlox-accent text-verlox-accent-hover" href="{{ url('/') }}">← Back to site</a>
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle colour mode">
            <span class="theme-toggle__icon" aria-hidden="true"></span>
            <span data-theme-label class="theme-toggle__text">Dark</span>
        </button>
    </div>

    <article class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white/80 dark:bg-[#0F223C] p-6 sm:p-8 prose prose-slate max-w-none dark:prose-invert">
        <header class="mb-6">
            <h1 class="font-display">{{ $doc->title }}</h1>
            @if($doc->effective_at)
                <p class="text-sm text-gray-600 dark:text-slate-300">Effective {{ $doc->effective_at->format('j F Y') }}</p>
            @endif
        </header>
        {!! $doc->body_html !!}
    </article>
</main>
</body>
</html>

