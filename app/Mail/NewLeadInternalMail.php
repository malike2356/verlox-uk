<?php

namespace App\Mail;

use App\Models\CompanySetting;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadInternalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        $s = CompanySetting::current();
        $to = $s->support_email ?: config('mail.from.address');

        return new Envelope(
            to: [new Address($to, $s->company_name)],
            subject: 'New lead: '.$this->lead->contact_name,
        );
    }

    public function content(): Content
    {
        $s = CompanySetting::current();

        return new Content(
            view: 'emails.new-lead-internal',
            with: [
                'lead' => $this->lead,
                'companyName' => $s->company_name,
                'logoUrl' => $s->logoPublicUrlAbsolute(),
                'headline' => 'New lead captured',
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
