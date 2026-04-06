<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\VaTimeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()->orderByDesc('id')->paginate(20);

        return view('mis.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('mis.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')],
            'phone' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
        Client::create($data);

        return redirect()->route('mis.clients.index')->with('status', 'Client created.');
    }

    public function show(Client $client): View
    {
        $client->load([
            'quotations' => fn ($q) => $q->latest()->limit(20),
            'contracts' => fn ($q) => $q->latest()->limit(20),
            'invoices' => fn ($q) => $q->latest()->limit(20),
            'conversations.messages',
            'documents' => fn ($q) => $q->latest()->limit(20),
            'vaClientAccounts.engagements.assistant',
            'lead',
        ]);

        $bookings = Booking::query()
            ->where('contact_email', $client->email)
            ->orderByDesc('starts_at')
            ->limit(25)
            ->get();

        $yearStart = now()->startOfYear();
        $invoicedYtdPence = (int) Invoice::query()
            ->where('client_id', $client->id)
            ->where('issued_at', '>=', $yearStart)
            ->whereNotIn('status', ['draft', 'written_off'])
            ->sum('total_pence');
        $collectedYtdPence = (int) Invoice::query()
            ->where('client_id', $client->id)
            ->where('issued_at', '>=', $yearStart)
            ->sum('paid_pence');

        $vaAccountIds = $client->vaClientAccounts->pluck('id')->all();
        $vaApprovedHoursMonth = $vaAccountIds === [] ? 0.0 : (float) VaTimeLog::query()
            ->whereIn('va_client_account_id', $vaAccountIds)
            ->where('is_approved', true)
            ->whereYear('work_date', now()->year)
            ->whereMonth('work_date', now()->month)
            ->sum('hours_logged');

        $vaBurnDown = [];
        foreach ($client->vaClientAccounts as $acc) {
            foreach ($acc->engagements as $eng) {
                if ($eng->status !== 'active') {
                    continue;
                }
                $cap = (float) ($eng->hours_per_month ?? 0);
                $used = (float) VaTimeLog::query()
                    ->where('va_engagement_id', $eng->id)
                    ->where('is_approved', true)
                    ->whereYear('work_date', now()->year)
                    ->whereMonth('work_date', now()->month)
                    ->sum('hours_logged');
                $vaBurnDown[] = [
                    'account' => $acc,
                    'engagement' => $eng,
                    'cap' => $cap,
                    'used' => $used,
                ];
            }
        }

        $canDeleteClient = ! $client->quotations()->exists()
            && ! $client->contracts()->exists()
            && ! $client->invoices()->exists()
            && ! $client->vaClientAccounts()->exists()
            && ! $client->documents()->exists()
            && ! $client->conversations()->exists();

        return view('mis.clients.show', compact(
            'client',
            'bookings',
            'invoicedYtdPence',
            'collectedYtdPence',
            'vaApprovedHoursMonth',
            'vaBurnDown',
            'canDeleteClient',
        ));
    }

    public function edit(Client $client): View
    {
        return view('mis.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client->id)],
            'phone' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'zoho_contact_id' => ['nullable', 'string', 'max:128'],
        ]);
        $client->update($data);

        return redirect()->route('mis.clients.show', $client)->with('status', 'Client saved.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->quotations()->exists() || $client->contracts()->exists() || $client->invoices()->exists()) {
            return redirect()->route('mis.clients.show', $client)
                ->with('error', 'This client has quotations, contracts, or invoices. Remove or reassign those records before deleting.');
        }
        if ($client->vaClientAccounts()->exists()) {
            return redirect()->route('mis.clients.show', $client)
                ->with('error', 'Remove VA client accounts for this client before deleting.');
        }
        if ($client->documents()->exists() || $client->conversations()->exists()) {
            return redirect()->route('mis.clients.show', $client)
                ->with('error', 'Delete documents and message threads (or move them) before deleting the client.');
        }

        $client->delete();

        return redirect()->route('mis.clients.index')->with('status', 'Client deleted.');
    }
}
