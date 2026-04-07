@php($settings = $settings ?? \App\Models\CompanySetting::current())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="" data-theme="light">
<head>
    @include('partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Verlox UK builds secure, high-impact websites, platforms, and automation for modern businesses.">
    <meta name="theme-color" content="#0B1829">
    @if($fav = $settings->faviconPublicUrl())
        <link rel="icon" href="{{ $fav }}">
    @endif
    <title>@yield('title', $settings->company_name.' | Websites, Platforms, Automation')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/marketing-site.css'])
    @stack('vite')
    @stack('head')
</head>
<body @isset($misBase) data-mis-base="{{ $misBase }}" @endisset>
@yield('content')
<button type="button" class="theme-toggle theme-toggle--floating" id="themeToggle" aria-label="Toggle colour mode">
    <span class="theme-toggle__icon" aria-hidden="true"></span>
    <span class="theme-toggle__text">Light</span>
</button>
</body>
</html>
