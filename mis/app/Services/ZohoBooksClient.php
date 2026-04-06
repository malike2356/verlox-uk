<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\ZohoSyncLog;
use Illuminate\Support\Facades\Http;

class ZohoBooksClient
{
    public function isConfigured(): bool
    {
        $s = CompanySetting::current();

        return filled($s->zoho_client_id)
            && filled($s->zoho_client_secret)
            && filled($s->zoho_refresh_token)
            && filled($s->zoho_org_id);
    }

    public function accountsServer(): string
    {
        $dc = CompanySetting::current()->zoho_dc ?: 'com';

        return match ($dc) {
            'eu' => 'https://accounts.zoho.eu',
            'in' => 'https://accounts.zoho.in',
            'au' => 'https://accounts.zoho.com.au',
            default => 'https://accounts.zoho.com',
        };
    }

    public function booksBaseUrl(): string
    {
        $dc = CompanySetting::current()->zoho_dc ?: 'com';

        return match ($dc) {
            'eu' => 'https://www.zohoapis.eu/books/v3',
            'in' => 'https://www.zohoapis.in/books/v3',
            'au' => 'https://www.zohoapis.com.au/books/v3',
            default => 'https://www.zohoapis.com/books/v3',
        };
    }

    public function refreshAccessToken(): ?string
    {
        $s = CompanySetting::current();
        if (! $this->isConfigured()) {
            return null;
        }

        $response = Http::asForm()->post($this->accountsServer().'/oauth/v2/token', [
            'refresh_token' => $s->zoho_refresh_token,
            'client_id' => $s->zoho_client_id,
            'client_secret' => $s->zoho_client_secret,
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful()) {
            ZohoSyncLog::create([
                'direction' => 'auth',
                'entity_type' => 'oauth',
                'status' => 'error',
                'message' => $response->body(),
            ]);

            return null;
        }

        return $response->json('access_token');
    }

    public function testConnection(): array
    {
        $token = $this->refreshAccessToken();
        if (! $token) {
            return ['ok' => false, 'message' => 'Could not obtain access token. Check credentials.'];
        }

        $s = CompanySetting::current();
        $response = Http::withToken($token)
            ->get($this->booksBaseUrl().'/organizations', [
                'organization_id' => $s->zoho_org_id,
            ]);

        if (! $response->successful()) {
            return ['ok' => false, 'message' => 'API error: '.$response->body()];
        }

        ZohoSyncLog::create([
            'direction' => 'pull',
            'entity_type' => 'organization',
            'status' => 'success',
            'message' => 'Connection test OK',
        ]);

        return ['ok' => true, 'message' => 'Zoho Books responded successfully.'];
    }

    /**
     * Push a local Expense to Zoho Books.
     * Creates if no zoho_expense_id; updates if one already exists.
     */
    public function pushExpense(Expense $expense): array
    {
        $token = $this->refreshAccessToken();
        if (! $token) {
            return ['ok' => false, 'message' => 'Could not obtain Zoho access token.'];
        }

        $s = CompanySetting::current();

        // Map our category names to sensible Zoho account names
        $accountMap = [
            'software' => 'Software Expense',
            'hosting' => 'Internet & Telephone',
            'office' => 'Office Supplies',
            'marketing' => 'Advertising & Marketing',
            'professional_services' => 'Professional Fees',
            'travel' => 'Travel Expense',
            'equipment' => 'Equipment',
            'subcontractors' => 'Subcontractor Expense',
            'bank_charges' => 'Bank Charges & Fees',
            'other' => 'Other Expenses',
        ];

        $payload = [
            'account_name' => $accountMap[$expense->category] ?? 'Other Expenses',
            'date' => $expense->date->format('Y-m-d'),
            'amount' => number_format($expense->amount_pence / 100, 2, '.', ''),
            'description' => $expense->description,
            'reference_number' => $expense->reference,
            'vendor_name' => $expense->vendor,
            'currency_code' => $expense->currency,
            'is_billable' => false,
        ];

        $isNew = ! filled($expense->zoho_expense_id);
        $url = $this->booksBaseUrl().'/expenses'.($isNew ? '' : '/'.$expense->zoho_expense_id);

        $response = Http::withToken($token)
            ->withQueryParameters(['organization_id' => $s->zoho_org_id])
            ->when($isNew, fn ($r) => $r->post($url, $payload), fn ($r) => $r->put($url, $payload));

        $ok = $response->successful() && in_array($response->json('code'), [0, null], true);

        $zohoId = $response->json('expense.expense_id') ?? $expense->zoho_expense_id;

        ZohoSyncLog::create([
            'direction' => 'push',
            'entity_type' => 'expense',
            'local_id' => $expense->id,
            'remote_id' => $zohoId,
            'status' => $ok ? 'success' : 'error',
            'message' => $ok ? 'Expense synced to Zoho Books' : $response->body(),
            'payload' => $payload,
        ]);

        if ($ok && $zohoId) {
            $expense->update(['zoho_expense_id' => $zohoId]);
        }

        return [
            'ok' => $ok,
            'message' => $ok ? 'Synced to Zoho Books.' : 'Zoho error: '.$response->json('message', $response->body()),
        ];
    }

    /**
     * Push a local Invoice to Zoho Books (create or update).
     * Uses customer_id when the client has zoho_contact_id, otherwise customer_name.
     */
    public function pushInvoice(Invoice $invoice): array
    {
        $token = $this->refreshAccessToken();
        if (! $token) {
            return ['ok' => false, 'message' => 'Could not obtain Zoho access token.'];
        }

        $invoice->loadMissing(['client', 'lines', 'offering', 'quotation']);
        if ($invoice->lines->isEmpty()) {
            return ['ok' => false, 'message' => 'Invoice has no line items.'];
        }

        $s = CompanySetting::current();
        $client = $invoice->client;

        $lineItems = [];
        foreach ($invoice->lines as $line) {
            $lineItems[] = [
                'name' => $line->description ?: 'Line item',
                'quantity' => (float) $line->quantity,
                'rate' => round($line->unit_price_pence / 100, 2),
            ];
        }

        $noteParts = ['MIS invoice id '.$invoice->id];
        if ($invoice->offering) {
            $noteParts[] = 'Revenue: catalogue / '.($invoice->offering->name ?: 'offering');
        }
        if ($invoice->quotation) {
            $noteParts[] = 'Quotation '.$invoice->quotation->number;
        }

        $payload = [
            'invoice_number' => $invoice->number,
            'date' => $invoice->issued_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'due_date' => $invoice->due_at?->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d'),
            'line_items' => $lineItems,
            'currency_code' => strtoupper($invoice->currency ?: 'GBP'),
            'notes' => implode(' · ', $noteParts),
        ];

        if (filled($client->zoho_contact_id)) {
            $payload['customer_id'] = $client->zoho_contact_id;
        } else {
            $payload['customer_name'] = $client->company_name ?: $client->contact_name ?: 'Customer';
        }

        $isNew = ! filled($invoice->zoho_invoice_id);
        $url = $this->booksBaseUrl().'/invoices'.($isNew ? '' : '/'.$invoice->zoho_invoice_id);

        $http = Http::withToken($token)
            ->asJson()
            ->withQueryParameters(['organization_id' => $s->zoho_org_id]);

        $response = $isNew
            ? $http->post($url, $payload)
            : $http->put($url, $payload);

        $ok = $response->successful() && in_array($response->json('code'), [0, null], true);

        $zohoId = $response->json('invoice.invoice_id') ?? $invoice->zoho_invoice_id;

        ZohoSyncLog::create([
            'direction' => 'push',
            'entity_type' => 'invoice',
            'local_id' => $invoice->id,
            'remote_id' => $zohoId,
            'status' => $ok ? 'success' : 'error',
            'message' => $ok ? 'Invoice synced to Zoho Books' : $response->body(),
            'payload' => $payload,
        ]);

        if ($ok && $zohoId) {
            $invoice->update(['zoho_invoice_id' => $zohoId]);
        }

        return [
            'ok' => $ok,
            'message' => $ok ? 'Invoice synced to Zoho Books.' : 'Zoho error: '.$response->json('message', $response->body()),
        ];
    }
}
