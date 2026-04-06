<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutboundClientMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public string $subjectLine,
        public string $bodyHtml,
    ) {}

    public function envelope(): Envelope
    {
        $s = CompanySetting::current();
        $fromAddress = $s->mail_from_address ?: config('mail.from.address');
        $fromName = $s->mail_from_name ?: config('mail.from.name');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: array_filter([$s->mail_reply_to ?: $s->support_email]),
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        $s = CompanySetting::current();

        return new Content(
            view: 'emails.outbound-client-message',
            with: [
                'client' => $this->client,
                'bodyHtml' => $this->bodyHtml,
                'companyName' => $s->company_name,
                'logoUrl' => $s->logoPublicUrlAbsolute(),
                'headline' => 'Message from '.$s->company_name,
                'primary' => $s->primary_hex ?: '#0ea5e9',
                'registrationNumber' => $s->registration_number,
                'vatNumber' => $s->vat_number,
                'address' => trim(implode(', ', array_filter([
                    $s->address_line1,
                    $s->city,
                    $s->postcode,
                ]))),
                'supportEmail' => $s->support_email,
            ],
        );
    }
}
