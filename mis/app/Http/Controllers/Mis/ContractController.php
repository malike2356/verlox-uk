<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(): View
    {
        $contracts = Contract::query()->with(['client', 'quotation'])->orderByDesc('id')->paginate(20);

        return view('mis.contracts.index', compact('contracts'));
    }

    public function show(Contract $contract): View
    {
        $contract->load(['client', 'template', 'quotation']);

        return view('mis.contracts.show', compact('contract'));
    }

    public function updateStatus(Request $request, Contract $contract): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,sent,signed,cancelled'],
        ]);
        $updates = ['status' => $data['status']];
        if ($data['status'] === 'signed' && ! $contract->signed_at) {
            $updates['signed_at'] = now();
        }
        $contract->update($updates);

        return back()->with('status', 'Contract updated.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        if (in_array($contract->status, ['signed'], true) || $contract->signed_at) {
            return redirect()->route('mis.contracts.show', $contract)
                ->with('error', 'Signed contracts cannot be deleted.');
        }

        $contract->delete();

        return redirect()->route('mis.contracts.index')->with('status', 'Contract deleted.');
    }
}
