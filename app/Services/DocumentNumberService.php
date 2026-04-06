<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Quotation;

class DocumentNumberService
{
    public function nextQuotationNumber(): string
    {
        return $this->next('VEL-Q-', Quotation::class);
    }

    public function nextInvoiceNumber(): string
    {
        return $this->next('VEL-INV-', Invoice::class);
    }

    public function nextContractNumber(): string
    {
        return $this->next('VEL-C-', Contract::class);
    }

    private function next(string $prefix, string $modelClass): string
    {
        $year = now()->year;
        $fullPrefix = $prefix.$year.'-';
        $last = $modelClass::query()
            ->where('number', 'like', $fullPrefix.'%')
            ->orderByDesc('id')
            ->value('number');
        $seq = 1;
        if ($last && str_starts_with($last, $fullPrefix)) {
            $seq = (int) substr($last, strlen($fullPrefix)) + 1;
        }

        return $fullPrefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
