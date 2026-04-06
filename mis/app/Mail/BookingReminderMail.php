<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly string $window // '24h' | '1h'
    ) {}

    public function envelope(): Envelope
    {
        $settings = CompanySetting::current();
        $localTime = $this->booking->starts_at
            ->setTimezone($this->booking->timezone ?: 'Europe/London')
            ->format('D, d M Y \a\t H:i');

        return new Envelope(
            from: $settings->mail_from_address
                ? new Address($settings->mail_from_address, $settings->mail_from_name ?: $settings->company_name)
                : null,
            subject: 'Reminder: your call is '.($this->window === '1h' ? 'in 1 hour' : 'tomorrow')." — {$localTime}",
        );
    }

    public function content(): Content
    {
        $s = CompanySetting::current();

        return new Content(
            view: 'emails.booking-reminder',
            with: [
                'window' => $this->window === '1h' ? '1h' : '24h',
                'companyName' => $s->company_name,
                'logoUrl' => $s->logoPublicUrlAbsolute(),
                'accentHex' => $s->primary_hex ?: '#C9A84C',
            ],
        );
    }
}
