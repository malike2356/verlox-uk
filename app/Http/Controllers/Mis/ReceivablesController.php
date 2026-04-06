<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\View\View;

class ReceivablesController extends Controller
{
    public function __invoke(): View
    {
        $invoices = Invoice::query()
            ->receivable()
            ->with(['client', 'lead'])
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->orderByDesc('id')
            ->get();

        return view('mis.finance.receivables', compact('invoices'));
    }
}
