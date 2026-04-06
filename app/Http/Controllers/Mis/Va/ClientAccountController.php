<?php

namespace App\Http\Controllers\Mis\Va;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\VaClientAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientAccountController extends Controller
{
    public function index(): View
    {
        $accounts = VaClientAccount::query()
            ->with('misClient')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('mis.va.client-accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $misClients = Client::query()->orderBy('contact_name')->get(['id', 'company_name', 'contact_name', 'email']);

        return view('mis.va.client-accounts.create', compact('misClients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $account = VaClientAccount::query()->create($data);

        return redirect()->route('mis.va.client-accounts.show', $account)->with('status', 'VA client account created.');
    }

    public function show(VaClientAccount $va_client_account): View
    {
        $va_client_account->load([
            'misClient',
            'engagements.assistant',
            'communicationLogs' => fn ($q) => $q->latest()->limit(20),
        ]);

        return view('mis.va.client-accounts.show', ['account' => $va_client_account]);
    }

    public function edit(VaClientAccount $va_client_account): View
    {
        $misClients = Client::query()->orderBy('contact_name')->get(['id', 'company_name', 'contact_name', 'email']);

        return view('mis.va.client-accounts.edit', compact('va_client_account', 'misClients'));
    }

    public function update(Request $request, VaClientAccount $va_client_account): RedirectResponse
    {
        $va_client_account->update($this->validated($request, $va_client_account));

        return redirect()->route('mis.va.client-accounts.show', $va_client_account)->with('status', 'Account updated.');
    }

    public function destroy(VaClientAccount $va_client_account): RedirectResponse
    {
        $va_client_account->delete();

        return redirect()->route('mis.va.client-accounts.index')->with('status', 'VA client account removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?VaClientAccount $existing): array
    {
        return $request->validate([
            'mis_client_id' => ['nullable', 'exists:clients,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('va_client_accounts', 'email')->ignore($existing?->id),
            ],
            'phone' => ['nullable', 'string', 'max:64'],
            'tier' => ['required', 'in:'.implode(',', VaClientAccount::$tiers)],
            'status' => ['required', 'in:'.implode(',', VaClientAccount::$statuses)],
            'monthly_rate_gbp' => ['required', 'numeric', 'min:0'],
            'hours_included' => ['required', 'integer', 'min:0', 'max:1000'],
            'overage_rate_gbp' => ['required', 'numeric', 'min:0'],
            'contract_start' => ['nullable', 'date'],
            'contract_end' => ['nullable', 'date', 'after_or_equal:contract_start'],
            'minimum_term_end' => ['nullable', 'date'],
            'stripe_customer_id' => ['nullable', 'string', 'max:255'],
            'account_manager' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
