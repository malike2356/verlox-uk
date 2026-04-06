@extends('layouts.mis')

@section('title', 'Help & documentation')
@section('heading', 'MIS help & documentation')

@push('head')
<style>
    html { scroll-behavior: smooth; }
</style>
@endpush

@section('content')
    @php
        $u = auth()->user();
        $isAdmin = $u->is_admin;
        $vaOnly = $u->isMisVaOnly();
    @endphp

    <div class="mx-auto max-w-3xl text-sm text-gray-800 dark:text-slate-300">
        <p class="mb-6 text-gray-600 dark:text-slate-400">
            Internal notes for the Verlox UK MIS (<code class="rounded bg-gray-100 px-1 text-xs dark:bg-slate-800">/mis</code>). Use the list below to jump to a section.
        </p>

        <nav class="mis-help-toc mb-10" aria-labelledby="help-toc-heading">
            <h2 id="help-toc-heading" class="mis-help-toc__title">On this page</h2>
            <ul class="list-disc space-y-1 pl-5 text-gray-800 dark:text-slate-200">
                <li><a href="#overview">What the MIS is</a></li>
                <li><a href="#access">Signing in &amp; roles</a></li>
                <li><a href="#navigation">Sidebar by role</a></li>
                <li>
                    <a href="#workflows">Core workflows</a>
                    <ul class="mt-1 list-disc space-y-0.5 pl-5 text-xs">
                        <li><a href="#wf-lead-client">Lead → client</a></li>
                        <li><a href="#wf-pipeline">Pipeline board</a></li>
                        <li><a href="#wf-quote-invoice">Quotation → contract → invoice</a></li>
                        <li><a href="#wf-ar">Accounts receivable</a></li>
                        <li><a href="#wf-client-hub">Client workspace</a></li>
                        <li><a href="#wf-bookings">Bookings &amp; leads</a></li>
                        <li><a href="#wf-va">VA delivery</a></li>
                        <li><a href="#wf-exports">Exports &amp; board packs</a></li>
                    </ul>
                </li>
                <li><a href="#modules">Module reference</a></li>
                <li><a href="#data">Statuses &amp; key fields</a></li>
                <li><a href="#integrations">Integrations</a></li>
                <li><a href="#public">Public site &amp; APIs</a></li>
                <li><a href="#troubleshooting">Troubleshooting</a></li>
            </ul>
        </nav>

        <section id="overview" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">What the MIS is</h2>
            <p class="mt-3">The Management Information System (MIS) is your internal console for Verlox UK operations. It sits behind login and groups tools into:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <li><strong class="text-gray-900 dark:text-white">CRM &amp; pipeline</strong> - leads, stages, clients, messages, documents.</li>
                <li><strong class="text-gray-900 dark:text-white">Sales &amp; legal</strong> - quotations, contracts, contract templates (admin).</li>
                <li><strong class="text-gray-900 dark:text-white">Finance</strong> - invoices, accounts receivable, expenses, Zoho Books sync, CSV exports.</li>
                <li><strong class="text-gray-900 dark:text-white">Bookings</strong> - calendar of calls/meetings, event types and availability (admin).</li>
                <li><strong class="text-gray-900 dark:text-white">VA division</strong> - client accounts, assistants, engagements, time logs.</li>
                <li><strong class="text-gray-900 dark:text-white">Catalogue &amp; site</strong> (admin) - offerings, pricing plans, editable content blocks.</li>
                <li><strong class="text-gray-900 dark:text-white">System</strong> (admin) - users, company settings, Google Calendar connection, Zoho test.</li>
            </ul>
            <p class="mt-3">The <a href="{{ route('mis.network.index') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">MIS network map</a> diagrams how marketing, bookings, CRM, and accounting connect.</p>
        </section>

        <section id="access" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Signing in &amp; roles</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5">
                <li>You sign in with your <strong class="text-gray-900 dark:text-white">email address</strong> and password on the main login page (not a separate username).</li>
                <li>Your account must be <strong class="text-gray-900 dark:text-white">email verified</strong> before MIS routes work.</li>
                <li>MIS access requires one of: <strong class="text-gray-900 dark:text-white">Admin</strong>, <strong class="text-gray-900 dark:text-white">Finance</strong> role, or <strong class="text-gray-900 dark:text-white">VA</strong> role. Users with none of these cannot open <code class="rounded bg-gray-100 px-1 text-xs dark:bg-slate-800">/mis/*</code>.</li>
            </ul>
            <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700">
                <table class="min-w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-3 py-2 font-semibold text-gray-900 dark:text-white">Role</th>
                            <th class="px-3 py-2 font-semibold text-gray-900 dark:text-white">Typical use</th>
                            <th class="px-3 py-2 font-semibold text-gray-900 dark:text-white">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">Admin</td>
                            <td class="px-3 py-2">Full MIS, settings, catalogue, pipeline stage editor, contract templates, users.</td>
                            <td class="px-3 py-2">“Super” routes require <code class="rounded bg-gray-100 px-1 text-[10px] dark:bg-slate-800">is_admin</code>; finance-only users do not see those menu items.</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">Finance</td>
                            <td class="px-3 py-2">CRM, pipeline, quotes, contracts (not templates), invoices, AR, expenses, Zoho log, exports, bookings list/calendar.</td>
                            <td class="px-3 py-2">No offerings/pricing/content/users/settings/event types/availability editing. Offering names on invoices are read-only links for you.</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">VA only</td>
                            <td class="px-3 py-2">Dashboard plus the whole VA subsection (accounts, assistants, time logs).</td>
                            <td class="px-3 py-2">Opening most other MIS URLs redirects you to the VA dashboard.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($isAdmin)
                <p class="mt-3 text-xs text-gray-600 dark:text-slate-400">Admins set roles under <a href="{{ route('mis.users.index') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">Users</a>: tick Admin for full access, or choose Finance / VA for limited MIS. Admin clears the MIS role field.</p>
            @endif
        </section>

        <section id="navigation" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Sidebar by role</h2>
            @if ($vaOnly)
                <p class="mt-3">Your menu is limited to:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    <li><strong class="text-gray-900 dark:text-white">Dashboard</strong> - MIS home.</li>
                    <li><strong class="text-gray-900 dark:text-white">Help &amp; documentation</strong> - this page.</li>
                    <li><strong class="text-gray-900 dark:text-white">Network map</strong> - read-only diagram of how areas connect (same as Overview for other roles).</li>
                    <li><strong class="text-gray-900 dark:text-white">VA division</strong> - VA dashboard, VA clients (accounts), assistants, time logs.</li>
                </ul>
            @else
                <ul class="mt-3 list-disc space-y-2 pl-5">
                    <li><strong class="text-gray-900 dark:text-white">Overview</strong> - Dashboard, this help page, network map.</li>
                    <li><strong class="text-gray-900 dark:text-white">CRM &amp; pipeline</strong> - Leads, clients, pipeline board; pipeline stage admin @if ($isAdmin) (you) @else (admins only) @endif; messages; documents.</li>
                    <li><strong class="text-gray-900 dark:text-white">Finance &amp; contracts</strong> - Finance dashboard, receivables (AR), invoices, expenses, quotations, contracts @if (! $isAdmin) (templates: admins only) @endif, Zoho Books, CSV exports for leads / invoices / expenses.</li>
                    <li><strong class="text-gray-900 dark:text-white">VA division</strong> - Same VA tools as VA-only users (your team can still log VA time and manage accounts).</li>
                    <li><strong class="text-gray-900 dark:text-white">Bookings</strong> - List and calendar views; availability rules and event types @if ($isAdmin) for you @else for admins @endif.</li>
                    @if ($isAdmin)
                        <li><strong class="text-gray-900 dark:text-white">Website &amp; catalogue</strong> - Offerings, pricing plans, site content blocks.</li>
                        <li><strong class="text-gray-900 dark:text-white">Users &amp; system</strong> - Users, company settings (branding, Stripe, Zoho, mail).</li>
                    @endif
                </ul>
            @endif
        </section>

        <section id="workflows" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Core workflows</h2>
            <p class="mt-2 text-xs text-gray-600 dark:text-slate-400">Eight short guides.</p>

            <div class="mt-6 space-y-8">
                <div id="wf-lead-client" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">1. Lead → client</h3>
                    <ol class="mt-2 list-decimal space-y-1.5 pl-5">
                        <li>Leads arrive from the public site (contact forms capture UTMs where configured).</li>
                        <li>Open <a href="{{ route('mis.leads.index') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">Leads</a>, pick a record, set stage, status, optional deal value and expected close date.</li>
                        <li>Use the <strong class="text-gray-900 dark:text-white">activity timeline</strong> for notes, calls, and emails so handoffs stay visible.</li>
                        <li>When qualified, use <strong class="text-gray-900 dark:text-white">Convert to client</strong> to create a client (duplicate emails may merge or link to an existing client).</li>
                    </ol>
                </div>

                <div id="wf-pipeline" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">2. Pipeline board</h3>
                    <p class="mt-2">The <a href="{{ route('mis.pipeline.index') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">Pipeline</a> is a kanban of leads by stage. Drag cards to move stages (updates are logged). Column headers can show aggregate deal value where filled on leads.</p>
                    @if ($isAdmin)
                        <p class="mt-2 text-xs text-gray-600 dark:text-slate-400">Edit stage names and colours under Pipeline stages.</p>
                    @endif
                </div>

                <div id="wf-quote-invoice" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">3. Quotation → contract → invoice</h3>
                    <ol class="mt-2 list-decimal space-y-1.5 pl-5">
                        <li>Create a <a href="{{ route('mis.quotations.create') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">quotation</a> for a client; optionally link a lead that already belongs to that client.</li>
                        <li>Add line items, set status (e.g. sent). When the customer accepts, use <strong class="text-gray-900 dark:text-white">Accept and generate contract</strong> @if (! $isAdmin) (requires a default contract template - ask an admin) @else (after a default template exists under Contract templates) @endif.</li>
                        <li>From an <strong class="text-gray-900 dark:text-white">accepted</strong> quotation, create an invoice. The invoice can inherit the quotation’s lead link for reporting.</li>
                        <li>On the invoice: set lifecycle status, log payment reminders, use Stripe checkout for payment, sync to Zoho where configured.</li>
                    </ol>
                </div>

                <div id="wf-ar" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">4. Accounts receivable</h3>
                    <p class="mt-2"><a href="{{ route('mis.finance.receivables') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">Receivables (AR)</a> lists open invoices (sent, partial, overdue) with balances and simple ageing buckets. Use it as the single operational view of money still owed.</p>
                </div>

                <div id="wf-client-hub" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">5. Client workspace</h3>
                    <p class="mt-2">The <a href="{{ route('mis.clients.index') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">client</a> record uses tabs for profile, quotations, contracts, invoices, bookings matched by email, VA accounts (retainer burn-down vs approved time this month), documents, and conversations - so you do not have to hunt through separate menus for one customer.</p>
                </div>

                <div id="wf-bookings" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">6. Bookings &amp; leads</h3>
                    <p class="mt-2">Public booking uses the embeddable flow. When a booking email matches an open lead, the system can attach the booking to that lead. Lead detail shows linked bookings.</p>
                </div>

                <div id="wf-va" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">7. VA delivery</h3>
                    <p class="mt-2">Create <strong class="text-gray-900 dark:text-white">VA client accounts</strong> linked to MIS clients where relevant, add <strong class="text-gray-900 dark:text-white">engagements</strong> (hours per month, rates), and record <strong class="text-gray-900 dark:text-white">time logs</strong>. Approvers mark logs approved; the client tab shows month-to-date hours and retainer burn-down for active engagements.</p>
                </div>

                <div id="wf-exports" class="scroll-mt-32">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">8. Exports &amp; board packs</h3>
                    <p class="mt-2">From the sidebar (Finance &amp; contracts), download CSV files for <strong class="text-gray-900 dark:text-white">leads</strong>, <strong class="text-gray-900 dark:text-white">invoices</strong>, and <strong class="text-gray-900 dark:text-white">expenses</strong> for accountants or reporting.</p>
                </div>
            </div>
        </section>

        <section id="modules" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Module reference</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Dashboard</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">Counts, charts (leads, pipeline mix, paid revenue), KPI strip (pipeline value, conversion proxy, YTD invoiced/collected, VA hours, quotes won), Zoho sync health summary, upcoming bookings.</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Leads</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">Table and detail with structured status, deal value, expected close, loss reason, UTM fields, activity log, convert action, bookings list.</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Clients</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">CRM companies/people; client email should be unique. Tabbed hub for related records and VA snapshot.</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Quotations &amp; contracts</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">Quote lines, statuses, optional lead link on quote and invoice. Contracts generated from templates; contract statuses updated from the contract screen.</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Invoices &amp; finance</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">Invoice lines, Stripe checkout, Zoho push, status and reminder tracking, AR view, expenses with Zoho sync option @if (! $isAdmin) (settings: admin) @endif.</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Conversations &amp; documents</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">Threaded client messages; file uploads tied to clients for internal reference.</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Zoho Books</h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">Sync log lists push/pull activity. Dashboard highlights recent errors. Connection and tokens live in company settings (admin).</p>
                </div>
                @if ($isAdmin)
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Offerings &amp; checkout</h3>
                        <p class="mt-1 text-gray-600 dark:text-slate-400">Catalogue items used on the marketing site and checkout; pricing plans where used.</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Company settings</h3>
                        <p class="mt-1 text-gray-600 dark:text-slate-400">Branding, mail, Stripe keys, Zoho OAuth, and other integration parameters. Keep secrets out of git; use <code class="rounded bg-gray-200 px-1 text-xs dark:bg-slate-900">.env</code> where appropriate.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="data" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Statuses &amp; key fields</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-200 p-3 dark:border-slate-700">
                    <h3 class="text-xs font-medium text-gray-600 dark:text-slate-400">Lead status (enum)</h3>
                    <p class="mt-1 font-mono text-xs">{{ implode(', ', \App\Models\Lead::STATUSES) }}</p>
                    <p class="mt-2 text-xs text-gray-600 dark:text-slate-400">If status is <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">lost</code>, provide a loss reason for reporting.</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 dark:border-slate-700">
                    <h3 class="text-xs font-medium text-gray-600 dark:text-slate-400">Quotation status</h3>
                    <p class="mt-1 font-mono text-xs">draft, sent, accepted, declined, expired</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 dark:border-slate-700">
                    <h3 class="text-xs font-medium text-gray-600 dark:text-slate-400">Invoice status</h3>
                    <p class="mt-1 font-mono text-xs">{{ implode(', ', \App\Models\Invoice::STATUSES) }}</p>
                    <p class="mt-2 text-xs text-gray-600 dark:text-slate-400">AR view includes sent, partial, and overdue (not written off). <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">written_off</code> records the write-off timestamp.</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 dark:border-slate-700">
                    <h3 class="text-xs font-medium text-gray-600 dark:text-slate-400">Contract status</h3>
                    <p class="mt-1 font-mono text-xs">draft, sent, signed, cancelled</p>
                </div>
            </div>
        </section>

        <section id="integrations" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Integrations</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5">
                <li><strong class="text-gray-900 dark:text-white">Stripe</strong> - Invoice checkout creates a payment session; webhooks update paid amounts and invoice status when configured. Failed or duplicate webhook events are handled safely where implemented.</li>
                <li><strong class="text-gray-900 dark:text-white">Zoho Books</strong> - Push invoices and expenses; inspect the sync log for errors. Admins can run a connection test from settings.</li>
                <li><strong class="text-gray-900 dark:text-white">Google Calendar</strong> - Optional connection from company settings (admin) for booking/calendar features where enabled.</li>
            </ul>
        </section>

        <section id="public" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Public website &amp; APIs</h2>
            <p class="mt-3">Visitors use routes outside <code class="rounded bg-gray-100 px-1 text-xs dark:bg-slate-800">/mis</code>: marketing home, VA page, book page, embed booking, checkout, lead capture <code class="rounded bg-gray-100 px-1 text-xs dark:bg-slate-800">POST /leads</code>, and JSON endpoints under <code class="rounded bg-gray-100 px-1 text-xs dark:bg-slate-800">/api/public/*</code> for slots, calendar, and event types.</p>
            <p class="mt-2 text-xs text-gray-600 dark:text-slate-400">Stripe webhooks POST to <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">/webhooks/stripe</code> (configure URL and signing secret in Stripe and in env/settings).</p>
        </section>

        <section id="troubleshooting" class="scroll-mt-32 border-t border-gray-200 pt-8 dark:border-slate-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Troubleshooting</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5">
                <li><strong class="text-gray-900 dark:text-white">“Credentials do not match”</strong> - Ensure a user row exists (e.g. run <code class="rounded bg-gray-100 px-1 text-xs dark:bg-slate-800">php artisan db:seed</code> on a fresh database). Sign in with email, not a username.</li>
                <li><strong class="text-gray-900 dark:text-white">403 on a screen</strong> - You may be finance or VA; that route may be admin-only. Ask an admin to perform the action or adjust your role.</li>
                <li><strong class="text-gray-900 dark:text-white">Duplicate client email</strong> - Client emails are unique; merge or edit the existing client instead of creating a second record.</li>
                <li><strong class="text-gray-900 dark:text-white">Cannot accept quotation</strong> - A default contract template must exist (admin sets under Contract templates).</li>
                <li><strong class="text-gray-900 dark:text-white">Zoho or Stripe errors</strong> - Check company settings credentials, sync log, and dashboard Zoho health strip; confirm webhook URL and secrets in production.</li>
            </ul>
            @if ($isAdmin)
                <p class="mt-4 text-xs text-gray-600 dark:text-slate-400">First-time environment: <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">php artisan migrate</code>, <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">php artisan db:seed</code>, <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">npm run build</code> if assets are missing, and set <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">APP_URL</code> to match how users reach the app.</p>
            @endif
        </section>

        <p class="mt-10 border-t border-gray-200 pt-6 text-xs text-gray-500 dark:border-slate-800 dark:text-slate-500">
            <a href="{{ route('mis.dashboard') }}" class="text-verlox-accent text-verlox-accent-hover underline underline-offset-2">Back to dashboard</a>
        </p>
    </div>
@endsection
