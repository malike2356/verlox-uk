<?php

namespace App\Http\Controllers\Mis\Va;

use App\Http\Controllers\Controller;
use App\Models\VaClientAccount;
use App\Models\VaCommunicationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommunicationLogController extends Controller
{
    public function store(Request $request, VaClientAccount $va_client_account): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:32'],
            'summary' => ['required', 'string', 'max:500'],
            'details' => ['nullable', 'string', 'max:10000'],
        ]);

        VaCommunicationLog::query()->create([
            'related_type' => 'client',
            'va_client_account_id' => $va_client_account->id,
            'va_assistant_id' => null,
            'type' => $data['type'],
            'summary' => $data['summary'],
            'details' => $data['details'] ?? null,
            'created_by' => $request->user()->name ?? $request->user()->email ?? 'staff',
        ]);

        return back()->with('status', 'Note logged.');
    }
}
