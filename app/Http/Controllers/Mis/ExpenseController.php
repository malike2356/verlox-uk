<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Services\AccountingSyncService;
use App\Services\ZohoBooksClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::orderByDesc('date')->orderByDesc('id')->paginate(40);
        $categories = Expense::CATEGORIES;

        return view('mis.finance.expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request, AccountingSyncService $accounting): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Expense::CATEGORIES))],
            'vendor' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', 'in:draft,paid'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $expense = Expense::create([
            'date' => $data['date'],
            'category' => $data['category'],
            'vendor' => $data['vendor'] ?? null,
            'description' => $data['description'],
            'amount_pence' => (int) round($data['amount'] * 100),
            'currency' => strtoupper($data['currency'] ?? 'GBP'),
            'status' => $data['status'] ?? 'paid',
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $accounting->syncExpenseToZoho($expense);

        return back()->with('status', 'Expense recorded.');
    }

    public function update(Request $request, Expense $expense, AccountingSyncService $accounting): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Expense::CATEGORIES))],
            'vendor' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['nullable', 'in:draft,paid'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $expense->update([
            'date' => $data['date'],
            'category' => $data['category'],
            'vendor' => $data['vendor'] ?? null,
            'description' => $data['description'],
            'amount_pence' => (int) round($data['amount'] * 100),
            'status' => $data['status'] ?? 'paid',
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $accounting->syncExpenseToZoho($expense->fresh());

        return back()->with('status', 'Expense updated.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('status', 'Expense deleted.');
    }

    public function syncZoho(Expense $expense, ZohoBooksClient $client): RedirectResponse
    {
        if (! $client->isConfigured()) {
            return back()->with('error', 'Zoho Books is not configured. Add credentials in Settings.');
        }

        $result = $client->pushExpense($expense);

        return back()->with($result['ok'] ? 'status' : 'error', $result['message']);
    }
}
