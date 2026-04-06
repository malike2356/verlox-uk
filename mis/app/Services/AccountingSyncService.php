<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

/**
 * Pushes MIS financial records to Zoho Books when configured and enabled in company settings.
 * Failures are logged; callers that must not fail (e.g. Stripe webhooks) rely on this service
 * swallowing exceptions after logging.
 */
class AccountingSyncService
{
    public function __construct(private ZohoBooksClient $zoho) {}

    public function invoiceAutoSyncEnabled(): bool
    {
        if (! $this->zoho->isConfigured()) {
            return false;
        }

        return (bool) CompanySetting::current()->zoho_auto_sync_invoices;
    }

    public function expenseAutoSyncEnabled(): bool
    {
        if (! $this->zoho->isConfigured()) {
            return false;
        }

        return (bool) CompanySetting::current()->zoho_auto_sync_expenses;
    }

    public function syncInvoiceToZoho(Invoice $invoice): void
    {
        if (! $this->invoiceAutoSyncEnabled()) {
            return;
        }

        $invoice->loadMissing(['client', 'lines']);
        if ($invoice->lines->isEmpty()) {
            return;
        }

        try {
            $this->zoho->pushInvoice($invoice);
        } catch (\Throwable $e) {
            Log::warning('accounting.zoho.invoice_failed', [
                'invoice_id' => $invoice->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function syncExpenseToZoho(Expense $expense): void
    {
        if (! $this->expenseAutoSyncEnabled()) {
            return;
        }

        try {
            $this->zoho->pushExpense($expense);
        } catch (\Throwable $e) {
            Log::warning('accounting.zoho.expense_failed', [
                'expense_id' => $expense->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
