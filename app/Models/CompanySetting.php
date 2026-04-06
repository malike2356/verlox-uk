<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name', 'tagline', 'website_url', 'registration_number', 'vat_number', 'tax_reference',
        'address_line1', 'address_line2', 'city', 'postcode', 'country', 'phone', 'support_email',
        'mail_from_name', 'mail_from_address', 'mail_reply_to',
        'stripe_publishable_key', 'stripe_secret_key', 'stripe_webhook_secret',
        'zoho_client_id', 'zoho_client_secret', 'zoho_refresh_token', 'zoho_org_id', 'zoho_dc',
        'zoho_auto_sync_invoices', 'zoho_auto_sync_expenses',
        'booking_slot_minutes', 'booking_buffer_minutes', 'booking_min_notice_hours', 'booking_max_days_ahead',
        'booking_timezone', 'meeting_provider', 'meeting_link_template', 'primary_hex', 'footer_legal_html',
        'favicon_path', 'logo_path', 'invoice_logo_path',
    ];

    /** Root-relative `/storage/...` for same-site HTML (works when APP_URL host/port ≠ browser). */
    public function faviconPublicUrl(): ?string
    {
        return $this->publicUrlForStoredPath($this->favicon_path, false);
    }

    /** Root-relative path for web; use {@see logoPublicUrlAbsolute()} in email HTML. */
    public function logoPublicUrl(): ?string
    {
        return $this->publicUrlForStoredPath($this->logo_path, false);
    }

    /** Fully qualified URL for image src in emails (set APP_URL to your public site URL). */
    public function logoPublicUrlAbsolute(): ?string
    {
        return $this->publicUrlForStoredPath($this->logo_path, true);
    }

    /** Invoice / PDF header: dedicated asset, else main logo. */
    public function invoiceLogoPublicUrl(): ?string
    {
        return $this->publicUrlForStoredPath($this->invoice_logo_path, false)
            ?? $this->publicUrlForStoredPath($this->logo_path, false);
    }

    /** Invoice asset only (no fallback), for admin preview. */
    public function invoiceOnlyPublicUrl(): ?string
    {
        return $this->publicUrlForStoredPath($this->invoice_logo_path, false);
    }

    protected function casts(): array
    {
        return [
            'zoho_auto_sync_invoices' => 'boolean',
            'zoho_auto_sync_expenses' => 'boolean',
        ];
    }

    protected function publicUrlForStoredPath(?string $path, bool $absolute): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        $path = str_replace('\\', '/', $path);
        $relative = '/storage/'.$path;

        if ($absolute) {
            $base = rtrim((string) config('app.url'), '/');

            return ($base !== '' ? $base : '').$relative;
        }

        return $relative;
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'company_name' => 'Velox UK',
                'country' => 'United Kingdom',
                'primary_hex' => '#0ea5e9',
                'zoho_dc' => 'com',
                'booking_slot_minutes' => 30,
                'booking_buffer_minutes' => 0,
                'booking_min_notice_hours' => 2,
                'booking_max_days_ahead' => 60,
                'booking_timezone' => 'Europe/London',
                'meeting_provider' => 'custom',
            ]
        );
    }
}
