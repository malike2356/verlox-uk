<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\ContractTemplate;
use App\Models\Quotation;

class ContractRenderer
{
    public function renderFromTemplate(ContractTemplate $template, Client $client, ?Quotation $quotation = null): string
    {
        $settings = CompanySetting::current();
        $map = [
            '{{company_name}}' => e($settings->company_name),
            '{{company_address}}' => e(trim(implode(', ', array_filter([
                $settings->address_line1,
                $settings->address_line2,
                $settings->city,
                $settings->postcode,
                $settings->country,
            ])))),
            '{{company_number}}' => e((string) ($settings->registration_number ?? '')),
            '{{vat_number}}' => e((string) ($settings->vat_number ?? '')),
            '{{client_company}}' => e((string) ($client->company_name ?? '')),
            '{{client_name}}' => e($client->contact_name),
            '{{client_email}}' => e($client->email),
            '{{today}}' => e(now()->format('j F Y')),
            '{{quotation_number}}' => e($quotation?->number ?? ''),
            '{{quotation_total}}' => e($quotation ? number_format($quotation->total_pence / 100, 2).' '.$quotation->currency : ''),
        ];

        $body = $template->body;

        return strtr($body, $map);
    }
}
