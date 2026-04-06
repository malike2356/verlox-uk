<a class="skip-link" href="#main">Skip to content</a>

<header class="topbar">
    <div class="container topbar__inner">
        <a class="brand" href="{{ url('/') }}" aria-label="{{ $settings->company_name }} home">
            @if($logo = $settings->logoPublicUrl())
                <img class="brand__logo" src="{{ $logo }}" alt="" width="36" height="36">
            @else
                <span class="brand__mark" aria-hidden="true">V</span>
            @endif
            <span>
                <span class="brand__name">{{ $settings->company_name }}</span>
                <span class="brand__meta">{{ $settings->tagline ?? 'Websites • Platforms • Automation' }}</span>
            </span>
        </a>

        <nav class="nav" aria-label="Primary">
            <a class="nav__link" href="{{ url('/#work') }}">Services</a>
            <a class="nav__link" href="{{ url('/#builds') }}">Portfolio</a>
            <a class="nav__link {{ request()->routeIs('marketing.virtual-assistant') ? 'nav__link--active' : '' }}" href="{{ route('marketing.virtual-assistant') }}">Virtual assistants</a>
            <a class="nav__link {{ request()->routeIs('marketing.book') ? 'nav__link--active' : '' }}" href="{{ route('marketing.book') }}">Book a call</a>
            <a class="btn btn--primary" href="{{ url('/#contact') }}">Get a proposal</a>
        </nav>

        <a class="nav__link topbar__staff-login" href="{{ route('login') }}">Staff login</a>
        <button type="button" class="nav-toggle" aria-label="Open menu" aria-expanded="false">
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
        </button>
    </div>
</header>
