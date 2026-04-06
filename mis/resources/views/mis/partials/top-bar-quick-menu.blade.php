@php($u = auth()->user())
@php($vaOnly = $u->isMisVaOnly())
@php($isAdmin = $u->is_admin)

@if($vaOnly)
    <li><h6 class="dropdown-header">VA</h6></li>
    <li><a class="dropdown-item" href="{{ route('mis.va.dashboard') }}"><i class="fas fa-gauge-high"></i>VA dashboard</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.va.client-accounts.index') }}"><i class="fas fa-handshake"></i>VA clients</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.va.assistants.index') }}"><i class="fas fa-user-tie"></i>Assistants</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.va.time-logs.index') }}"><i class="fas fa-clock"></i>Time logs</a></li>
@else
    <li><h6 class="dropdown-header">Overview</h6></li>
    <li><a class="dropdown-item" href="{{ route('mis.dashboard') }}"><i class="fas fa-gauge"></i>Dashboard</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.pipeline.index') }}"><i class="fas fa-diagram-project"></i>Pipeline</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.leads.index') }}"><i class="fas fa-user-plus"></i>Leads</a></li>

    <li><hr class="dropdown-divider"></li>
    <li><h6 class="dropdown-header">People &amp; files</h6></li>
    <li><a class="dropdown-item" href="{{ route('mis.clients.index') }}"><i class="fas fa-building"></i>Clients</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.documents.index') }}"><i class="fas fa-folder-open"></i>Documents</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.conversations.index') }}"><i class="fas fa-comments"></i>Messages</a></li>

    <li><hr class="dropdown-divider"></li>
    <li><h6 class="dropdown-header">Finance</h6></li>
    <li><a class="dropdown-item" href="{{ route('mis.finance.dashboard') }}"><i class="fas fa-chart-line"></i>Finance dashboard</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.invoices.index') }}"><i class="fas fa-file-invoice"></i>Invoices</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.finance.receivables') }}"><i class="fas fa-coins"></i>Receivables</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.finance.expenses.index') }}"><i class="fas fa-receipt"></i>Expenses</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.quotations.index') }}"><i class="fas fa-file-lines"></i>Quotations</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.contracts.index') }}"><i class="fas fa-file-contract"></i>Contracts</a></li>

    <li><hr class="dropdown-divider"></li>
    <li><h6 class="dropdown-header">Bookings</h6></li>
    <li><a class="dropdown-item" href="{{ route('mis.bookings.index', ['view' => 'list']) }}"><i class="fas fa-list"></i>All bookings</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.bookings.index', ['view' => 'calendar']) }}"><i class="fas fa-calendar-alt"></i>Calendar</a></li>

    <li><hr class="dropdown-divider"></li>
    <li><h6 class="dropdown-header">VA division</h6></li>
    <li><a class="dropdown-item" href="{{ route('mis.va.dashboard') }}"><i class="fas fa-headset"></i>VA dashboard</a></li>
    <li><a class="dropdown-item" href="{{ route('mis.va.client-accounts.index') }}"><i class="fas fa-handshake"></i>VA clients</a></li>

    @if($isAdmin)
        <li><hr class="dropdown-divider"></li>
        <li><h6 class="dropdown-header">Administration</h6></li>
        <li><a class="dropdown-item" href="{{ route('mis.offerings.index') }}"><i class="fas fa-box-open"></i>Offerings</a></li>
        <li><a class="dropdown-item" href="{{ route('mis.pricing-plans.index') }}"><i class="fas fa-tags"></i>Pricing plans</a></li>
        <li><a class="dropdown-item" href="{{ route('mis.content-blocks.index') }}"><i class="fas fa-pen-to-square"></i>Site content</a></li>
        <li><a class="dropdown-item" href="{{ route('mis.users.index') }}"><i class="fas fa-user-gear"></i>Users</a></li>
        <li><a class="dropdown-item" href="{{ route('mis.settings.edit') }}"><i class="fas fa-gear"></i>Company settings</a></li>
    @endif
@endif

<li><hr class="dropdown-divider"></li>
<li><h6 class="dropdown-header">Help</h6></li>
<li><a class="dropdown-item" href="{{ route('mis.help.index') }}"><i class="fas fa-circle-question"></i>Help &amp; documentation</a></li>
<li><a class="dropdown-item" href="{{ route('mis.network.index') }}"><i class="fas fa-diagram-project"></i>MIS network map</a></li>
