@php($misCompany = \App\Models\CompanySetting::current())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    @include('partials.theme-init', ['themeDefault' => 'dark'])
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($fav = $misCompany->faviconPublicUrl())
        <link rel="icon" href="{{ $fav }}">
    @endif
    <title>@yield('title', 'MIS') | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|cormorant-garamond:600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @vite(['resources/css/app.css', 'resources/css/mis-overrides.css', 'resources/js/app.js'])
    @stack('head')
    <style>body { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; }</style>
</head>
<body class="h-full bg-verlox-page text-gray-900 dark:text-[#E5E7EB] antialiased">
<div class="min-h-full">
@include('mis.partials.shell-chrome')
<x-mis-smart-search />

<div class="mis-mobile-overlay" id="mis-mobile-overlay" hidden onclick="window.misCloseSidebar?.()"></div>

<aside class="mis-shell-sidebar" id="mis-shell-sidebar" aria-label="Main navigation">
    <nav class="mis-shell-menu-wrap">
        @include('mis.partials.nav-links')
    </nav>
    <div class="mis-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fas fa-right-from-bracket me-1" aria-hidden="true"></i>Sign out</button>
        </form>
    </div>
</aside>

<div class="mis-shell-main">
    <header class="sticky top-12 z-20 flex items-center justify-between gap-3 border-b border-verlox bg-white/90 px-4 py-3 backdrop-blur dark:border-slate-800 dark:bg-[#0B1829]/90">
        <h1 class="truncate text-base font-semibold text-gray-900 dark:text-white">@yield('heading')</h1>
        <div class="flex shrink-0 items-center gap-2 sm:hidden">
            <a href="{{ route('marketing.home') }}" class="text-xs text-verlox-accent text-verlox-accent-hover">View site</a>
        </div>
    </header>
    <main class="mis-main mx-auto w-full flex-1 px-4 py-4 sm:px-6 lg:px-8 @yield('mainClass', 'max-w-[min(100%,85rem)]')">
        @php($misSilentStatus = ['profile-updated', 'password-updated', 'verification-link-sent'])
        @if (session('status') && ! in_array(session('status'), $misSilentStatus, true))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-200">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/50 dark:text-red-200">
                <ul class="ms-4 list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>

<script>
(function () {
    var sidebar = document.getElementById('mis-shell-sidebar');
    var overlay = document.getElementById('mis-mobile-overlay');
    if (!sidebar || !overlay) return;

    function isMobile() {
        return window.matchMedia('(max-width: 767px)').matches;
    }

    window.misOpenSidebar = function () {
        if (!isMobile()) return;
        sidebar.classList.add('mis-shell-sidebar--open');
        overlay.hidden = false;
    };
    window.misCloseSidebar = function () {
        sidebar.classList.remove('mis-shell-sidebar--open');
        overlay.hidden = true;
    };
    window.misToggleSidebar = function () {
        if (sidebar.classList.contains('mis-shell-sidebar--open')) {
            window.misCloseSidebar();
        } else {
            window.misOpenSidebar();
        }
    };

    document.querySelectorAll('.mis-sidebar-details').forEach(function (d) {
        d.addEventListener('toggle', function () {
            if (!d.open) return;
            document.querySelectorAll('.mis-sidebar-details').forEach(function (other) {
                if (other !== d) other.removeAttribute('open');
            });
        });
    });

    sidebar.querySelectorAll('a[href]').forEach(function (a) {
        a.addEventListener('click', function () {
            if (isMobile()) window.misCloseSidebar();
        });
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) window.misCloseSidebar();
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</div>
</body>
</html>
