<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\CompanySetting;
use App\Services\IcsBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $s = CompanySetting::current();
        $fromAddress = $s->mail_from_address ?: config('mail.from.address');
        $fromName = $s->mail_from_name ?: config('mail.from.name');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: array_values(array_filter([
                $s->mail_reply_to ? new Address($s->mail_reply_to) : null,
                ! $s->mail_reply_to && $s->support_email ? new Address($s->support_email) : null,
            ])),
            subject: 'Meeting confirmed: '.$this->booking->starts_at->timezone($this->booking->timezone)->format('D j M Y, H:i'),
        );
    }

    public function content(): Content
    {
        $s = CompanySetting::current();

        return new Content(
            view: 'emails.booking-confirmed',
            with: $this->branding($s),
        );
    }

    private function branding(CompanySetting $s): array
    {
        $manageUrl = $this->booking->manage_token
            ? route('public.booking.manage', [
                'booking' => $this->booking->id,
                'token' => $this->booking->manage_token,
            ])
            : null;

        return [
            'booking' => $this->booking,
            'companyName' => $s->company_name,
            'logoUrl' => $s->logoPublicUrlAbsolute(),
            'headline' => 'Your meeting is confirmed',
            'primary' => $s->primary_hex ?: '#0ea5e9',
            'registrationNumber' => $s->registration_number,
            'vatNumber' => $s->vat_number,
            'address' => trim(implode(', ', array_filter([
                $s->address_line1,
                $s->city,
                $s->postcode,
                $s->country,
            ]))),
            'supportEmail' => $s->support_email,
            'manageUrl' => $manageUrl,
        ];
    }

    public function attachments(): array
    {
        $ics = app(IcsBuilder::class)->forBooking($this->booking);

        return [
            Attachment::fromData(fn () => $ics, IcsBuilder::attachmentFilename(), [
                'mime' => 'text/calendar',
            ]),
        ];
    }
}
