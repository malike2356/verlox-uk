@extends('layouts.mis')

@section('title', 'Settings')
@section('heading', 'Company settings')

@section('content')
@php
    $inp = 'w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500';
    $lbl = 'block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1';
    $sec = 'rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 space-y-4';
@endphp

<div class="mx-auto max-w-7xl space-y-4">

    {{-- Hero card --}}
    <section class="rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm bg-white dark:bg-slate-900/60">
        <div class="h-20 bg-gradient-to-r from-verlox-accent/30 via-sky-500/20 to-indigo-500/10 dark:from-indigo-950 dark:via-sky-950 dark:to-slate-900"></div>
        <div class="px-6 pb-5">
            <div class="-mt-6 flex flex-wrap items-end justify-between gap-4">
                <div class="flex items-end gap-4">
                    @if($logo = $settings->logoPublicUrl())
                        <img src="{{ $logo }}" alt="" class="h-16 w-16 shrink-0 rounded-2xl border-4 border-white dark:border-slate-900 object-contain bg-white dark:bg-slate-800 shadow-md p-1">
                    @else
                        <div class="h-16 w-16 shrink-0 rounded-2xl border-4 border-white dark:border-slate-900 bg-verlox-accent/10 shadow-md flex items-center justify-center">
                            <span class="text-2xl font-bold text-verlox-accent">{{ substr($settings->company_name ?? 'V', 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="mb-1 min-w-0">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $settings->company_name ?? 'Company settings' }}</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400 truncate">{{ $settings->tagline ?? 'No tagline set' }}</p>
                    </div>
                </div>
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    @if($settings->website_url)
                        <a href="{{ $settings->website_url }}" target="_blank"
                            class="rounded-full border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/50 px-3 py-1 text-xs font-semibold text-sky-700 dark:text-sky-300">
                            {{ parse_url($settings->website_url, PHP_URL_HOST) ?? $settings->website_url }}
                        </a>
                    @endif
                    @if($settings->support_email)
                        <span class="rounded-full border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-gray-600 dark:text-slate-300">
                            {{ $settings->support_email }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-x-8 gap-y-2 border-t border-gray-100 dark:border-slate-800 pt-4 text-sm">
                @if($settings->registration_number)
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-500">Companies House</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $settings->registration_number }}</p>
                    </div>
                @endif
                @if($settings->vat_number)
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-500">VAT</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $settings->vat_number }}</p>
                    </div>
                @endif
                @if($settings->city || $settings->country)
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-500">Location</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ implode(', ', array_filter([$settings->city, $settings->country])) }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <form method="post" action="{{ route('mis.settings.update') }}" enctype="multipart/form-data" class="space-y-4 text-sm">
        @csrf @method('patch')

        <div class="grid gap-4 lg:grid-cols-5">

            {{-- Left column --}}
            <div class="min-w-0 lg:col-span-3 space-y-4">

                {{-- Company --}}
                <section class="{{ $sec }}">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Company</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">Legal details, address, and contact information.</p>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2"><label class="{{ $lbl }}">Legal name</label><input name="company_name" value="{{ $settings->company_name }}" required class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Tagline</label><input name="tagline" value="{{ $settings->tagline }}" class="{{ $inp }}" placeholder="Optional"></div>
                        <div><label class="{{ $lbl }}">Website</label><input name="website_url" value="{{ $settings->website_url }}" class="{{ $inp }}" placeholder="https://"></div>
                        <div><label class="{{ $lbl }}">Companies House no.</label><input name="registration_number" value="{{ $settings->registration_number }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">VAT number</label><input name="vat_number" value="{{ $settings->vat_number }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Tax reference</label><input name="tax_reference" value="{{ $settings->tax_reference }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Phone</label><input name="phone" value="{{ $settings->phone }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Support email</label><input type="email" name="support_email" value="{{ $settings->support_email }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Address line 1</label><input name="address_line1" value="{{ $settings->address_line1 }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Address line 2</label><input name="address_line2" value="{{ $settings->address_line2 }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">City</label><input name="city" value="{{ $settings->city }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Postcode</label><input name="postcode" value="{{ $settings->postcode }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Country</label><input name="country" value="{{ $settings->country }}" class="{{ $inp }}"></div>
                    </div>
                </section>

                {{-- Email sending --}}
                <section class="{{ $sec }}">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Email sending</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">From name and address used for all outbound emails.</p>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="{{ $lbl }}">From name</label><input name="mail_from_name" value="{{ $settings->mail_from_name }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">From address</label><input type="email" name="mail_from_address" value="{{ $settings->mail_from_address }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Reply-To</label><input type="email" name="mail_reply_to" value="{{ $settings->mail_reply_to }}" class="{{ $inp }}"></div>
                    </div>
                </section>

                {{-- Brand --}}
                <section class="{{ $sec }}">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Brand</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">
                            Files stored in <code class="rounded bg-gray-100 dark:bg-slate-800 px-1 font-mono text-xs">storage/app/public/company-branding</code>.
                            Run <code class="rounded bg-gray-100 dark:bg-slate-800 px-1 font-mono text-xs">php artisan storage:link</code> if images 404.
                        </p>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-3">
                        {{-- Favicon --}}
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950/40 p-3 space-y-2">
                            <p class="text-xs font-medium text-gray-700 dark:text-slate-300">Favicon</p>
                            <p class="text-[11px] text-gray-400 dark:text-slate-500">Browser tab. ICO, PNG, SVG, WebP · max 512 KB</p>
                            @if($url = $settings->faviconPublicUrl())
                                <img src="{{ $url }}" alt="" class="h-8 w-8 object-contain rounded border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            @endif
                            <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg,.gif,.webp,.svg,image/*"
                                class="block w-full text-xs text-gray-600 dark:text-slate-400 file:mr-2 file:rounded-md file:border-0 file:bg-gray-200 dark:file:bg-slate-700 file:px-2 file:py-1 file:text-xs file:font-medium">
                            @if($settings->favicon_path)
                                <label class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-slate-400 cursor-pointer">
                                    <input type="checkbox" name="clear_favicon" value="1" class="rounded border-gray-300 dark:border-slate-600"> Remove
                                </label>
                            @endif
                        </div>
                        {{-- Logo --}}
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950/40 p-3 space-y-2">
                            <p class="text-xs font-medium text-gray-700 dark:text-slate-300">Logo</p>
                            <p class="text-[11px] text-gray-400 dark:text-slate-500">Header, emails. PNG, JPG, WebP, SVG · max 2 MB</p>
                            @if($url = $settings->logoPublicUrl())
                                <img src="{{ $url }}" alt="" class="max-h-10 max-w-[140px] object-contain rounded border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-0.5">
                            @endif
                            <input type="file" name="logo" accept=".png,.jpg,.jpeg,.gif,.webp,.svg,image/*"
                                class="block w-full text-xs text-gray-600 dark:text-slate-400 file:mr-2 file:rounded-md file:border-0 file:bg-gray-200 dark:file:bg-slate-700 file:px-2 file:py-1 file:text-xs file:font-medium">
                            @if($settings->logo_path)
                                <label class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-slate-400 cursor-pointer">
                                    <input type="checkbox" name="clear_logo" value="1" class="rounded border-gray-300 dark:border-slate-600"> Remove
                                </label>
                            @endif
                        </div>
                        {{-- Invoice logo --}}
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950/40 p-3 space-y-2">
                            <p class="text-xs font-medium text-gray-700 dark:text-slate-300">Invoice logo</p>
                            <p class="text-[11px] text-gray-400 dark:text-slate-500">Invoice screens only. Falls back to main logo.</p>
                            @if($url = $settings->invoiceOnlyPublicUrl())
                                <img src="{{ $url }}" alt="" class="max-h-10 max-w-[140px] object-contain rounded border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-0.5">
                            @elseif($settings->logo_path && ($fallback = $settings->logoPublicUrl()))
                                <img src="{{ $fallback }}" alt="" class="max-h-10 max-w-[140px] object-contain rounded border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-0.5 opacity-50">
                            @endif
                            <input type="file" name="invoice_logo" accept=".png,.jpg,.jpeg,.gif,.webp,.svg,image/*"
                                class="block w-full text-xs text-gray-600 dark:text-slate-400 file:mr-2 file:rounded-md file:border-0 file:bg-gray-200 dark:file:bg-slate-700 file:px-2 file:py-1 file:text-xs file:font-medium">
                            @if($settings->invoice_logo_path)
                                <label class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-slate-400 cursor-pointer">
                                    <input type="checkbox" name="clear_invoice_logo" value="1" class="rounded border-gray-300 dark:border-slate-600"> Remove
                                </label>
                            @endif
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-[12rem_1fr]">
                        <div><label class="{{ $lbl }}">Primary colour (hex)</label><input name="primary_hex" value="{{ $settings->primary_hex }}" class="{{ $inp }} font-mono"></div>
                        <div><label class="{{ $lbl }}">Footer HTML (legal)</label><textarea name="footer_legal_html" rows="3" class="{{ $inp }} font-mono text-xs">{{ $settings->footer_legal_html }}</textarea></div>
                    </div>
                </section>

            </div>

            {{-- Right column --}}
            <div class="min-w-0 lg:col-span-2 space-y-4">

                {{-- Stripe --}}
                <section class="{{ $sec }}">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Stripe</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">
                            Webhook URL: <code class="font-mono text-[11px] break-all">{{ url('/webhooks/stripe') }}</code>
                        </p>
                    </header>
                    <div class="space-y-3">
                        <div><label class="{{ $lbl }}">Publishable key</label><input name="stripe_publishable_key" value="{{ $settings->stripe_publishable_key }}" class="{{ $inp }} font-mono text-xs"></div>
                        <div><label class="{{ $lbl }}">Secret key</label><input name="stripe_secret_key" value="{{ $settings->stripe_secret_key }}" type="password" autocomplete="new-password" class="{{ $inp }} font-mono text-xs"></div>
                        <div><label class="{{ $lbl }}">Webhook signing secret</label><input name="stripe_webhook_secret" value="{{ $settings->stripe_webhook_secret }}" type="password" autocomplete="new-password" class="{{ $inp }} font-mono text-xs"></div>
                    </div>
                </section>

                {{-- Zoho --}}
                <section id="zoho-books" class="{{ $sec }} scroll-mt-24">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Zoho Books</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">Create a self-client in Zoho API Console, paste credentials below.</p>
                    </header>
                    <div class="space-y-3">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div><label class="{{ $lbl }}">Client ID</label><input name="zoho_client_id" value="{{ $settings->zoho_client_id }}" class="{{ $inp }} font-mono text-xs"></div>
                            <div><label class="{{ $lbl }}">Client secret</label><input name="zoho_client_secret" value="{{ $settings->zoho_client_secret }}" type="password" autocomplete="new-password" class="{{ $inp }} font-mono text-xs"></div>
                        </div>
                        <div><label class="{{ $lbl }}">Refresh token</label><textarea name="zoho_refresh_token" rows="2" class="{{ $inp }} font-mono text-xs">{{ $settings->zoho_refresh_token }}</textarea></div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div><label class="{{ $lbl }}">Organisation ID</label><input name="zoho_org_id" value="{{ $settings->zoho_org_id }}" class="{{ $inp }} font-mono text-xs"></div>
                            <div><label class="{{ $lbl }}">Data centre</label>
                                <select name="zoho_dc" class="{{ $inp }}">
                                    @foreach (['com','eu','in','au'] as $dc)
                                        <option value="{{ $dc }}" @selected($settings->zoho_dc === $dc)>{{ $dc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950/40 p-3 space-y-2">
                            <p class="text-xs font-medium text-gray-700 dark:text-slate-300">Accounting automation</p>
                            <p class="text-[11px] text-gray-400 dark:text-slate-500">With valid credentials, MIS syncs invoices and expenses into Zoho Books.</p>
                            <input type="hidden" name="zoho_auto_sync_invoices" value="0">
                            <label class="flex items-start gap-2 text-xs text-gray-700 dark:text-slate-300 cursor-pointer">
                                <input type="checkbox" name="zoho_auto_sync_invoices" value="1" @checked($settings->zoho_auto_sync_invoices) class="mt-0.5 rounded border-gray-300 dark:border-slate-600">
                                <span>Auto-sync invoices</span>
                            </label>
                            <input type="hidden" name="zoho_auto_sync_expenses" value="0">
                            <label class="flex items-start gap-2 text-xs text-gray-700 dark:text-slate-300 cursor-pointer">
                                <input type="checkbox" name="zoho_auto_sync_expenses" value="1" @checked($settings->zoho_auto_sync_expenses) class="mt-0.5 rounded border-gray-300 dark:border-slate-600">
                                <span>Auto-sync expenses when saved</span>
                            </label>
                        </div>
                    </div>
                </section>

                {{-- Booking --}}
                <section class="{{ $sec }}">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Booking embed</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">
                            Public scheduler: <code class="font-mono text-[11px]">{{ url('/embed/booking') }}</code>
                        </p>
                    </header>
                    <div class="space-y-3">
                        <div class="grid gap-3 grid-cols-2">
                            <div><label class="{{ $lbl }}">Slot (min)</label><input type="number" name="booking_slot_minutes" value="{{ $settings->booking_slot_minutes }}" class="{{ $inp }}"></div>
                            <div><label class="{{ $lbl }}">Buffer (min)</label><input type="number" name="booking_buffer_minutes" value="{{ $settings->booking_buffer_minutes ?? 0 }}" min="0" class="{{ $inp }}"></div>
                            <div><label class="{{ $lbl }}">Min notice (h)</label><input type="number" name="booking_min_notice_hours" value="{{ $settings->booking_min_notice_hours ?? 2 }}" min="0" class="{{ $inp }}"></div>
                            <div><label class="{{ $lbl }}">Max days ahead</label><input type="number" name="booking_max_days_ahead" value="{{ $settings->booking_max_days_ahead ?? 60 }}" min="1" class="{{ $inp }}"></div>
                        </div>
                        <div><label class="{{ $lbl }}">Timezone (IANA)</label><input name="booking_timezone" value="{{ $settings->booking_timezone }}" placeholder="Europe/London" class="{{ $inp }} font-mono text-xs"></div>
                        <div><label class="{{ $lbl }}">Meeting provider label</label><input name="meeting_provider" value="{{ $settings->meeting_provider }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Meeting link (Zoom or Meet URL)</label><textarea name="meeting_link_template" rows="2" class="{{ $inp }} font-mono text-xs">{{ $settings->meeting_link_template }}</textarea></div>
                    </div>
                </section>

                {{-- Google Calendar --}}
                <section class="{{ $sec }}">
                    <header class="border-b border-gray-100 dark:border-slate-800 pb-4">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Google Calendar</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">Busy slots are automatically excluded from the public booking calendar.</p>
                    </header>
                    @if($googleIntegration)
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Connected</span>
                                </div>
                                @if($googleIntegration->owner_email)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">{{ $googleIntegration->owner_email }}</p>
                                @endif
                            </div>
                            <form method="post" action="{{ route('mis.google-calendar.disconnect') }}" class="shrink-0">
                                @csrf @method('delete')
                                <button type="submit" class="rounded-lg border border-red-200 dark:border-red-800 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30">
                                    Disconnect
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="text-xs text-gray-500 dark:text-slate-400">
                            Requires <code class="rounded bg-gray-100 dark:bg-slate-800 px-1 font-mono">GOOGLE_CLIENT_ID</code> and
                            <code class="rounded bg-gray-100 dark:bg-slate-800 px-1 font-mono">GOOGLE_CLIENT_SECRET</code> in <code class="rounded bg-gray-100 dark:bg-slate-800 px-1 font-mono">.env</code>.
                        </p>
                        <a href="{{ route('mis.google-calendar.connect') }}"
                            class="mis-google-calendar-connect inline-flex items-center gap-2 transition-colors">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
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
        </div>

        {{-- Save --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 dark:border-slate-700 pt-4">
            <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-2.5 text-sm font-semibold text-on-verlox-accent hover:opacity-90 transition-opacity">
                Save all settings
            </button>
            @if(session('status') === 'settings-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Saved.</p>
            @endif
        </div>

    </form>

</div>
@endsection
