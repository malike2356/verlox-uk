<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;

class LeadConvertController extends Controller
{
    public function __invoke(Lead $lead): RedirectResponse
    {
        $existing = Client::query()->where('email', $lead->email)->first();
        if ($existing) {
            $existing->update([
                'lead_id' => $lead->id,
                'company_name' => $lead->company_name ?? $existing->company_name,
                'contact_name' => $lead->contact_name,
                'phone' => $lead->phone ?? $existing->phone,
            ]);

            return redirect()->route('mis.clients.show', $existing)->with('status', 'Linked to existing client.');
        }

        $client = Client::create([
            'lead_id' => $lead->id,
            'company_name' => $lead->company_name,
            'contact_name' => $lead->contact_name,
            'email' => $lead->email,
            'phone' => $lead->phone,
        ]);

        $lead->update(['status' => 'converted']);

        return redirect()->route('mis.clients.show', $client)->with('status', 'Client created from lead.');
    }
}
