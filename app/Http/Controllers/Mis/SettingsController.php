<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\CalendarIntegration;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = CompanySetting::current();
        $googleIntegration = CalendarIntegration::where('provider', 'google')->first();

        return view('mis.settings.edit', compact('settings', 'googleIntegration'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'registration_number' => ['nullable', 'string', 'max:64'],
            'vat_number' => ['nullable', 'string', 'max:64'],
            'tax_reference' => ['nullable', 'string', 'max:64'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:128'],
            'postcode' => ['nullable', 'string', 'max:32'],
            'country' => ['nullable', 'string', 'max:128'],
            'phone' => ['nullable', 'string', 'max:64'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_reply_to' => ['nullable', 'email', 'max:255'],
            'stripe_publishable_key' => ['nullable', 'string', 'max:500'],
            'stripe_secret_key' => ['nullable', 'string', 'max:500'],
            'stripe_webhook_secret' => ['nullable', 'string', 'max:255'],
            'zoho_client_id' => ['nullable', 'string', 'max:255'],
            'zoho_client_secret' => ['nullable', 'string', 'max:500'],
            'zoho_refresh_token' => ['nullable', 'string', 'max:2000'],
            'zoho_org_id' => ['nullable', 'string', 'max:64'],
            'zoho_dc' => ['nullable', 'in:com,eu,in,au'],
            'booking_slot_minutes' => ['nullable', 'integer', 'min:15', 'max:240'],
            'booking_buffer_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'booking_min_notice_hours' => ['nullable', 'integer', 'min:0', 'max:168'],
            'booking_max_days_ahead' => ['nullable', 'integer', 'min:1', 'max:365'],
            'booking_timezone' => ['nullable', 'string', 'max:64'],
            'meeting_provider' => ['nullable', 'string', 'max:32'],
            'meeting_link_template' => ['nullable', 'string', 'max:2000'],
            'primary_hex' => ['nullable', 'string', 'max:16'],
            'footer_legal_html' => ['nullable', 'string', 'max:20000'],
            'favicon' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'ico', 'x-icon'])->max(512)],
            'logo' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'])->max(2048)],
            'invoice_logo' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'])->max(2048)],
            'clear_favicon' => ['sometimes', 'boolean'],
            'clear_logo' => ['sometimes', 'boolean'],
            'clear_invoice_logo' => ['sometimes', 'boolean'],
        ]);

        $settings = CompanySetting::current();
        $disk = Storage::disk('public');

        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path && $disk->exists($settings->favicon_path)) {
                $disk->delete($settings->favicon_path);
            }
            $settings->favicon_path = $request->file('favicon')->store('company-branding', 'public');
        } elseif ($request->boolean('clear_favicon')) {
            if ($settings->favicon_path && $disk->exists($settings->favicon_path)) {
                $disk->delete($settings->favicon_path);
            }
            $settings->favicon_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path && $disk->exists($settings->logo_path)) {
                $disk->delete($settings->logo_path);
            }
            $settings->logo_path = $request->file('logo')->store('company-branding', 'public');
        } elseif ($request->boolean('clear_logo')) {
            if ($settings->logo_path && $disk->exists($settings->logo_path)) {
                $disk->delete($settings->logo_path);
            }
            $settings->logo_path = null;
        }

        if ($request->hasFile('invoice_logo')) {
            if ($settings->invoice_logo_path && $disk->exists($settings->invoice_logo_path)) {
                $disk->delete($settings->invoice_logo_path);
            }
            $settings->invoice_logo_path = $request->file('invoice_logo')->store('company-branding', 'public');
        } elseif ($request->boolean('clear_invoice_logo')) {
            if ($settings->invoice_logo_path && $disk->exists($settings->invoice_logo_path)) {
                $disk->delete($settings->invoice_logo_path);
            }
            $settings->invoice_logo_path = null;
        }

        $payload = collect($data)->except([
            'favicon', 'logo', 'invoice_logo',
            'clear_favicon', 'clear_logo', 'clear_invoice_logo',
        ])->all();
        $payload['zoho_auto_sync_invoices'] = $request->boolean('zoho_auto_sync_invoices');
        $payload['zoho_auto_sync_expenses'] = $request->boolean('zoho_auto_sync_expenses');
        $payload['favicon_path']      = $settings->favicon_path;
        $payload['logo_path']         = $settings->logo_path;
        $payload['invoice_logo_path'] = $settings->invoice_logo_path;
        $settings->update($payload);

        return back()->with('status', 'settings-updated');
    }
}
