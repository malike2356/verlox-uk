@extends('layouts.mis')

@section('title', 'Settings')
@section('heading', 'Company settings')

@section('content')
    <div class="mx-auto max-w-6xl">
    <form method="post" action="{{ route('mis.settings.update') }}" enctype="multipart/form-data" class="space-y-8 text-sm">
        @csrf @method('patch')

        <div class="grid gap-6 md:grid-cols-2 md:items-start">
            <div class="space-y-6">
                <section class="rounded-2xl border border-gray-200 dark:border-slate-600 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Company</h2>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Legal name</label><input name="company_name" value="{{ $settings->company_name }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Tagline</label><input name="tagline" value="{{ $settings->tagline }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Website</label><input name="website_url" value="{{ $settings->website_url }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Companies House no.</label><input name="registration_number" value="{{ $settings->registration_number }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">VAT number</label><input name="vat_number" value="{{ $settings->vat_number }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    </div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Tax reference</label><input name="tax_reference" value="{{ $settings->tax_reference }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Address line 1</label><input name="address_line1" value="{{ $settings->address_line1 }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Address line 2</label><input name="address_line2" value="{{ $settings->address_line2 }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">City</label><input name="city" value="{{ $settings->city }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Postcode</label><input name="postcode" value="{{ $settings->postcode }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    </div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Country</label><input name="country" value="{{ $settings->country }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Phone</label><input name="phone" value="{{ $settings->phone }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Support email</label><input type="email" name="support_email" value="{{ $settings->support_email }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 dark:border-slate-600 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Email sending</h2>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">From name</label><input name="mail_from_name" value="{{ $settings->mail_from_name }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">From address</label><input type="email" name="mail_from_address" value="{{ $settings->mail_from_address }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Reply-To</label><input type="email" name="mail_reply_to" value="{{ $settings->mail_reply_to }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                </section>

                <section class="rounded-2xl border border-gray-200 dark:border-slate-600 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Brand</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-300">Files are stored in <span class="font-mono text-gray-900 dark:text-slate-200">storage/app/public/company-branding</span> and served at <span class="font-mono text-gray-900 dark:text-slate-200">/storage/…</span> (run <span class="font-mono text-gray-900 dark:text-slate-200">php artisan storage:link</span> if images 404).</p>
                    <div class="grid gap-4 sm:grid-cols-1">
                        <div class="rounded-lg border border-gray-200 dark:border-slate-700 p-3 space-y-2">
                            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Favicon</label>
                            <p class="text-xs text-gray-500 dark:text-slate-300">Browser tab; ICO, PNG, SVG, or WebP (max 512&nbsp;KB).</p>
                            @if($url = $settings->faviconPublicUrl())
                                <p class="text-xs"><img src="{{ $url }}" alt="" class="h-8 w-8 object-contain rounded bg-white/10 border border-gray-200 dark:border-slate-700"></p>
                            @endif
                            <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg,.gif,.webp,.svg,image/*" class="block w-full text-xs text-gray-600 dark:text-slate-300 file:mr-2 file:rounded file:border-0 file:bg-gray-200 dark:file:bg-slate-700 file:px-2 file:py-1">
                            @if($settings->favicon_path)
                                <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-slate-300"><input type="checkbox" name="clear_favicon" value="1" class="rounded border-gray-300 dark:border-slate-600"> Remove current favicon</label>
                            @endif
                        </div>
                        <div class="rounded-lg border border-gray-200 dark:border-slate-700 p-3 space-y-2">
                            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Logo</label>
                            <p class="text-xs text-gray-500 dark:text-slate-300">Marketing site header, MIS shell, transactional emails (PNG, JPG, WebP, SVG; max 2&nbsp;MB).</p>
                            @if($url = $settings->logoPublicUrl())
                                <p class="text-xs"><img src="{{ $url }}" alt="" class="max-h-12 max-w-[200px] object-contain object-left rounded border border-gray-200 dark:border-slate-700 bg-white/10 p-1"></p>
                            @endif
                            <input type="file" name="logo" accept=".png,.jpg,.jpeg,.gif,.webp,.svg,image/*" class="block w-full text-xs text-gray-600 dark:text-slate-300 file:mr-2 file:rounded file:border-0 file:bg-gray-200 dark:file:bg-slate-700 file:px-2 file:py-1">
                            @if($settings->logo_path)
                                <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-slate-300"><input type="checkbox" name="clear_logo" value="1" class="rounded border-gray-300 dark:border-slate-600"> Remove current logo</label>
                            @endif
                        </div>
                        <div class="rounded-lg border border-gray-200 dark:border-slate-700 p-3 space-y-2">
                            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Invoice logo</label>
                            <p class="text-xs text-gray-500 dark:text-slate-300">Shown on MIS invoice screens; falls back to main logo if empty. Same formats as logo.</p>
                            @if($url = $settings->invoiceOnlyPublicUrl())
                                <p class="text-xs"><img src="{{ $url }}" alt="" class="max-h-12 max-w-[200px] object-contain object-left rounded border border-gray-200 dark:border-slate-700 bg-white/10 p-1"></p>
                            @elseif($settings->logo_path && ($fallback = $settings->logoPublicUrl()))
                                <p class="text-xs text-gray-500 dark:text-slate-300">Using main logo: <img src="{{ $fallback }}" alt="" class="inline-block max-h-8 max-w-[120px] object-contain align-middle ml-1 rounded border border-gray-200 dark:border-slate-700"></p>
                            @endif
                            <input type="file" name="invoice_logo" accept=".png,.jpg,.jpeg,.gif,.webp,.svg,image/*" class="block w-full text-xs text-gray-600 dark:text-slate-300 file:mr-2 file:rounded file:border-0 file:bg-gray-200 dark:file:bg-slate-700 file:px-2 file:py-1">
                            @if($settings->invoice_logo_path)
                                <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-slate-300"><input type="checkbox" name="clear_invoice_logo" value="1" class="rounded border-gray-300 dark:border-slate-600"> Remove invoice-only logo (main logo still used)</label>
                            @endif
                        </div>
                    </div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Primary colour (hex)</label><input name="primary_hex" value="{{ $settings->primary_hex }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Footer HTML (legal)</label><textarea name="footer_legal_html" rows="4" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs">{{ $settings->footer_legal_html }}</textarea></div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-2xl border border-gray-200 dark:border-slate-600 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Stripe</h2>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Publishable key</label><input name="stripe_publishable_key" value="{{ $settings->stripe_publishable_key }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Secret key</label><input name="stripe_secret_key" value="{{ $settings->stripe_secret_key }}" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs" autocomplete="new-password"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Webhook signing secret</label><input name="stripe_webhook_secret" value="{{ $settings->stripe_webhook_secret }}" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs" autocomplete="new-password"></div>
                    <p class="text-xs text-gray-500 dark:text-slate-300">Webhook URL: <span class="font-mono text-gray-700 dark:text-slate-300">{{ url('/webhooks/stripe') }}</span></p>
                </section>

                <section id="zoho-books" class="rounded-2xl border border-gray-200 dark:border-slate-600 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3 scroll-mt-24">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Zoho Books (OAuth)</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-300">Create a self-client or server-based app in Zoho API Console, then paste refresh token and org id. Use the Zoho page to test the connection.</p>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Client id</label><input name="zoho_client_id" value="{{ $settings->zoho_client_id }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Client secret</label><input name="zoho_client_secret" value="{{ $settings->zoho_client_secret }}" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs" autocomplete="new-password"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Refresh token</label><textarea name="zoho_refresh_token" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs">{{ $settings->zoho_refresh_token }}</textarea></div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Organization id</label><input name="zoho_org_id" value="{{ $settings->zoho_org_id }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs"></div>
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Data center</label>
                            <select name="zoho_dc" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900">
                                @foreach (['com','eu','in','au'] as $dc)
                                    <option value="{{ $dc }}" @selected($settings->zoho_dc === $dc)>{{ $dc }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2 space-y-2 rounded-lg border border-gray-200 dark:border-slate-700 p-3">
                        <p class="text-xs font-medium text-gray-600 dark:text-slate-300">{{ __('Accounting automation') }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-300">{{ __('With valid Zoho credentials, MIS pushes financial data into Zoho Books so invoices and expenses stay aligned with your ledger.') }}</p>
                        <input type="hidden" name="zoho_auto_sync_invoices" value="0">
                        <label class="flex items-start gap-2 text-xs text-gray-700 dark:text-slate-300">
                            <input type="checkbox" name="zoho_auto_sync_invoices" value="1" @checked($settings->zoho_auto_sync_invoices) class="mt-0.5 rounded border-gray-300 dark:border-slate-600">
                            <span>{{ __('Auto-sync invoices (website checkout, quotation-based invoices, Stripe checkout send, and when payment is received)') }}</span>
                        </label>
                        <input type="hidden" name="zoho_auto_sync_expenses" value="0">
                        <label class="flex items-start gap-2 text-xs text-gray-700 dark:text-slate-300">
                            <input type="checkbox" name="zoho_auto_sync_expenses" value="1" @checked($settings->zoho_auto_sync_expenses) class="mt-0.5 rounded border-gray-300 dark:border-slate-600">
                            <span>{{ __('Auto-sync expenses when saved (create or update)') }}</span>
                        </label>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 dark:border-slate-600 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Booking embed</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-300">Public scheduler: <span class="font-mono text-gray-700 dark:text-slate-300">{{ url('/embed/booking') }}</span> · Embed iframe or link from your marketing site.</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Slot length (minutes)</label><input type="number" name="booking_slot_minutes" value="{{ $settings->booking_slot_minutes }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Buffer after each meeting (minutes)</label><input type="number" name="booking_buffer_minutes" value="{{ $settings->booking_buffer_minutes ?? 0 }}" min="0" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Minimum notice (hours)</label><input type="number" name="booking_min_notice_hours" value="{{ $settings->booking_min_notice_hours ?? 2 }}" min="0" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                        <div><label class="text-xs text-gray-500 dark:text-slate-300">Max days ahead bookable</label><input type="number" name="booking_max_days_ahead" value="{{ $settings->booking_max_days_ahead ?? 60 }}" min="1" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                    </div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Default timezone (IANA)</label><input name="booking_timezone" value="{{ $settings->booking_timezone }}" placeholder="Europe/London" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs"></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Meeting link (Zoom or Meet URL)</label><textarea name="meeting_link_template" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900 font-mono text-xs">{{ $settings->meeting_link_template }}</textarea></div>
                    <div><label class="text-xs text-gray-500 dark:text-slate-300">Meeting provider label</label><input name="meeting_provider" value="{{ $settings->meeting_provider }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark] px-3 py-2 text-gray-900"></div>
                </section>
            </div>

            <div class="md:col-span-2 pt-2">
                <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Save all settings</button>
            </div>
        </div>
    </form>

    {{-- Google Calendar - outside the form (OAuth, not a regular save). Card + CTA use .mis-google-* so dark mode beats Bootstrap .bg-white !important. --}}
    <section class="mis-google-calendar-card mt-8 rounded-2xl border border-gray-200 bg-white/90 p-4 space-y-3 text-sm">
        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-300">Google Calendar</h2>
        @if($googleIntegration)
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                        Connected
                    </p>
                    @if($googleIntegration->owner_email)
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-300">{{ $googleIntegration->owner_email }}</p>
                    @endif
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-300">
                        Busy slots are automatically excluded from the public booking calendar.
                    </p>
                </div>
                <form method="post" action="{{ route('mis.google-calendar.disconnect') }}" class="ml-4 shrink-0">
                    @csrf @method('delete')
                    <button type="submit"
                        class="rounded-lg border border-red-300 dark:border-red-700/50 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        Disconnect
                    </button>
                </form>
            </div>
        @else
            <p class="text-xs text-gray-500 dark:text-slate-300">
                Connect Google Calendar so existing events are automatically blocked out on the public booking page.
                Requires <code class="rounded bg-slate-100 px-1 font-mono text-gray-900 dark:bg-slate-700 dark:text-slate-100">GOOGLE_CLIENT_ID</code> and <code class="rounded bg-slate-100 px-1 font-mono text-gray-900 dark:bg-slate-700 dark:text-slate-100">GOOGLE_CLIENT_SECRET</code> in <code class="rounded bg-slate-100 px-1 font-mono text-gray-900 dark:bg-slate-700 dark:text-slate-100">.env</code>.
            </p>
            <a href="{{ route('mis.google-calendar.connect') }}"
               class="mis-google-calendar-connect transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Connect Google Calendar
            </a>
        @endif
    </section>
    </div>
@endsection
