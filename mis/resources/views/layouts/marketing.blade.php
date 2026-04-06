@php($settings = $settings ?? \App\Models\CompanySetting::current())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">
<head>
    @include('partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->company_name }} | SaaS delivery and platforms</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:300,400,500,600,700|cormorant-garamond:500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'DM Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', Georgia, serif; }
    </style>
</head>
<body class="bg-verlox-page text-gray-900 dark:text-[#E5E7EB] antialiased min-h-screen">
@yield('content')
</body>
</html>
