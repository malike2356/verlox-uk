@php($u = auth()->user())
@php($vaOnly = $u->isMisVaOnly())
@php($isAdmin = $u->is_admin)

@if($vaOnly)
    <div class="mis-quick-section-label">VA</div>
    <a href="{{ route('mis.va.dashboard') }}" class="mis-topbar-flyout__link"><i class="fas fa-gauge-high mis-topbar-flyout__ico" aria-hidden="true"></i><span>VA dashboard</span></a>
    <a href="{{ route('mis.va.client-accounts.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-handshake mis-topbar-flyout__ico" aria-hidden="true"></i><span>VA clients</span></a>
    <a href="{{ route('mis.va.assistants.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-user-tie mis-topbar-flyout__ico" aria-hidden="true"></i><span>Assistants</span></a>
    <a href="{{ route('mis.va.time-logs.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-clock mis-topbar-flyout__ico" aria-hidden="true"></i><span>Time logs</span></a>
@else
    <div class="mis-quick-section-label">Overview</div>
    <a href="{{ route('mis.dashboard') }}" class="mis-topbar-flyout__link"><i class="fas fa-gauge mis-topbar-flyout__ico" aria-hidden="true"></i><span>Dashboard</span></a>
    <a href="{{ route('mis.pipeline.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-diagram-project mis-topbar-flyout__ico" aria-hidden="true"></i><span>Pipeline</span></a>
    <a href="{{ route('mis.leads.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-user-plus mis-topbar-flyout__ico" aria-hidden="true"></i><span>Leads</span></a>

    <div class="mis-quick-section-label">People &amp; files</div>
    <a href="{{ route('mis.clients.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-building mis-topbar-flyout__ico" aria-hidden="true"></i><span>Clients</span></a>
    <a href="{{ route('mis.documents.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-folder-open mis-topbar-flyout__ico" aria-hidden="true"></i><span>Documents</span></a>
    <a href="{{ route('mis.conversations.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-comments mis-topbar-flyout__ico" aria-hidden="true"></i><span>Messages</span></a>

    <div class="mis-quick-section-label">Finance</div>
    <a href="{{ route('mis.finance.dashboard') }}" class="mis-topbar-flyout__link"><i class="fas fa-chart-line mis-topbar-flyout__ico" aria-hidden="true"></i><span>Finance dashboard</span></a>
    <a href="{{ route('mis.invoices.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-file-invoice mis-topbar-flyout__ico" aria-hidden="true"></i><span>Invoices</span></a>
    <a href="{{ route('mis.finance.receivables') }}" class="mis-topbar-flyout__link"><i class="fas fa-coins mis-topbar-flyout__ico" aria-hidden="true"></i><span>Receivables</span></a>
    <a href="{{ route('mis.finance.expenses.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-receipt mis-topbar-flyout__ico" aria-hidden="true"></i><span>Expenses</span></a>
    <a href="{{ route('mis.quotations.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-file-lines mis-topbar-flyout__ico" aria-hidden="true"></i><span>Quotations</span></a>
    <a href="{{ route('mis.contracts.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-file-contract mis-topbar-flyout__ico" aria-hidden="true"></i><span>Contracts</span></a>

    <div class="mis-quick-section-label">Bookings</div>
    <a href="{{ route('mis.bookings.index', ['view' => 'list']) }}" class="mis-topbar-flyout__link"><i class="fas fa-list mis-topbar-flyout__ico" aria-hidden="true"></i><span>All bookings</span></a>
    <a href="{{ route('mis.bookings.index', ['view' => 'calendar']) }}" class="mis-topbar-flyout__link"><i class="fas fa-calendar-alt mis-topbar-flyout__ico" aria-hidden="true"></i><span>Calendar</span></a>

    <div class="mis-quick-section-label">VA division</div>
    <a href="{{ route('mis.va.dashboard') }}" class="mis-topbar-flyout__link"><i class="fas fa-headset mis-topbar-flyout__ico" aria-hidden="true"></i><span>VA dashboard</span></a>
    <a href="{{ route('mis.va.client-accounts.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-handshake mis-topbar-flyout__ico" aria-hidden="true"></i><span>VA clients</span></a>

    @if($isAdmin)
        <div class="mis-quick-section-label">Administration</div>
        <a href="{{ route('mis.offerings.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-box-open mis-topbar-flyout__ico" aria-hidden="true"></i><span>Offerings</span></a>
        <a href="{{ route('mis.pricing-plans.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-tags mis-topbar-flyout__ico" aria-hidden="true"></i><span>Pricing plans</span></a>
        <a href="{{ route('mis.content-blocks.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-pen-to-square mis-topbar-flyout__ico" aria-hidden="true"></i><span>Site content</span></a>
        <a href="{{ route('mis.users.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-user-gear mis-topbar-flyout__ico" aria-hidden="true"></i><span>Users</span></a>
        <a href="{{ route('mis.settings.edit') }}" class="mis-topbar-flyout__link"><i class="fas fa-gear mis-topbar-flyout__ico" aria-hidden="true"></i><span>Company settings</span></a>
    @endif
@endif

<div class="mis-quick-section-label">Help</div>
<a href="{{ route('mis.help.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-circle-question mis-topbar-flyout__ico" aria-hidden="true"></i><span>Help &amp; documentation</span></a>
<a href="{{ route('mis.network.index') }}" class="mis-topbar-flyout__link"><i class="fas fa-diagram-project mis-topbar-flyout__ico" aria-hidden="true"></i><span>MIS network map</span></a>
