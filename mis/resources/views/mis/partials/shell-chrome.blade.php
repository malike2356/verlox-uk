@php($u = auth()->user())
@php($vaOnly = $u->isMisVaOnly())
@php($isAdmin = $u->is_admin)
@php($topBarDisplayName = \Illuminate\Support\Str::before($u->name, ' ') ?: $u->name)

<header class="top-bar">
    <div class="top-bar-start">
        <button type="button" class="mobile-menu-toggle d-md-none" onclick="window.misToggleSidebar?.()" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('mis.dashboard') }}" class="top-bar-brand">
            @if($logo = $misCompany->logoPublicUrl())
                <img src="{{ $logo }}" alt="" class="top-bar-brand-img" width="32" height="32" aria-hidden="true">
            @endif
            <span class="top-bar-brand-name">{{ $misCompany->company_name }} MIS</span>
        </a>
        <div class="top-bar-quick-wrap--md top-bar-ml-1">
            <nav class="top-bar-quicklinks" aria-label="Shortcuts">
                @if($vaOnly)
                    <a href="{{ route('mis.dashboard') }}" class="top-bar-link">Dashboard</a>
                    <a href="{{ route('mis.va.dashboard') }}" class="top-bar-link">VA</a>
                    <a href="{{ route('mis.va.client-accounts.index') }}" class="top-bar-link">Clients</a>
                @else
                    <a href="{{ route('mis.dashboard') }}" class="top-bar-link">Dashboard</a>
                    <a href="{{ route('mis.pipeline.index') }}" class="top-bar-link">Pipeline</a>
                    <a href="{{ route('mis.clients.index') }}" class="top-bar-link">Clients</a>
                    <a href="{{ route('mis.invoices.index') }}" class="top-bar-link">Invoices</a>
                    <a href="{{ route('mis.bookings.index', ['view' => 'list']) }}" class="top-bar-link">Bookings</a>
                    @if($isAdmin)
                        <a href="{{ route('mis.users.index') }}" class="top-bar-link">Users</a>
                        <a href="{{ route('mis.settings.edit') }}" class="top-bar-link">System</a>
                    @endif
                @endif
                <a href="{{ route('marketing.home') }}" class="top-bar-link" target="_blank" rel="noopener">View site</a>
                <a href="{{ route('mis.help.index') }}" class="top-bar-link">Help</a>
            </nav>
        </div>
        <div class="dropdown top-bar-ml-1">
            <button type="button" class="quick-links-toggle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Quick links menu" id="misTopBarQuickLinksToggle">
                <i class="fas fa-bolt" aria-hidden="true"></i><span class="top-bar-show-sm">Quick links</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end top-bar-quick-menu py-2" aria-labelledby="misTopBarQuickLinksToggle">
                @include('mis.partials.top-bar-quick-menu')
            </ul>
        </div>
    </div>

    <div class="top-bar-center">
        <div class="top-bar-search-wrap">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" id="smartSearchTrigger" class="top-bar-search-input" placeholder="Search MIS - leads, clients, invoices, bookings…" tabindex="0" aria-label="Search MIS" autocomplete="off" title="Search everything (Ctrl+K)">
        </div>
    </div>

    <div class="top-bar-actions">
        @if($isAdmin)
            <a href="{{ route('mis.settings.edit') }}" class="top-bar-link top-bar-show-sm-flex" title="Company settings" aria-label="Company settings"><i class="fas fa-gear"></i></a>
        @endif
        <button type="button" class="theme-toggle !px-2.5 !py-1.5" data-theme-toggle aria-label="Toggle colour mode">
            <span class="theme-toggle__icon" aria-hidden="true"></span>
            <span data-theme-label class="theme-toggle__text text-xs">Dark</span>
        </button>
        <div class="top-bar-user">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="misTopBarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $u->profilePhotoUrl() }}" alt="" class="top-bar-user-avatar" width="28" height="28" loading="lazy" referrerpolicy="no-referrer">
                    <span class="top-bar-show-sm">{{ $topBarDisplayName }}</span>
                    <i class="fas fa-chevron-down fa-xs ms-1"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="misTopBarUserDropdown">
                    <li><h6 class="dropdown-header text-muted text-uppercase small">Account</h6></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>View profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}#mis-profile-edit"><i class="fas fa-pen me-2"></i>Edit profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header text-muted text-uppercase small">Workspace</h6></li>
                    @if($isAdmin)
                        <li><a class="dropdown-item" href="{{ route('mis.settings.edit') }}"><i class="fas fa-gear me-2"></i>Company settings</a></li>
                        <li><a class="dropdown-item" href="{{ route('mis.users.index') }}"><i class="fas fa-user-gear me-2"></i>Users</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ route('mis.help.index') }}"><i class="fas fa-circle-question me-2"></i>Help &amp; documentation</a></li>
                    <li><a class="dropdown-item" href="{{ route('mis.network.index') }}"><i class="fas fa-diagram-project me-2"></i>MIS network map</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
