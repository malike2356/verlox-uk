<?php

namespace App\Http\Controllers\Mis\Va;

use App\Http\Controllers\Controller;
use App\Models\VaAssistant;
use App\Models\VaClientAccount;
use App\Models\VaEngagement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EngagementController extends Controller
{
    public function create(VaClientAccount $va_client_account): View
    {
        $assistants = VaAssistant::query()
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return view('mis.va.engagements.create', compact('va_client_account', 'assistants'));
    }

    public function store(Request $request, VaClientAccount $va_client_account): RedirectResponse
    {
        $data = $this->validated($request);
        $data['va_client_account_id'] = $va_client_account->id;
        $engagement = VaEngagement::query()->create($data);

        return redirect()->route('mis.va.client-accounts.show', $va_client_account)
            ->with('status', 'Engagement created: #'.$engagement->id);
    }

    public function edit(VaEngagement $va_engagement): View
    {
        $va_engagement->load('clientAccount');
        $va_engagement->loadCount('timeLogs');
        $assistants = VaAssistant::query()
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return view('mis.va.engagements.edit', compact('va_engagement', 'assistants'));
    }

    public function update(Request $request, VaEngagement $va_engagement): RedirectResponse
    {
        $va_engagement->update($this->validated($request));

        return redirect()->route('mis.va.client-accounts.show', $va_engagement->clientAccount)
            ->with('status', 'Engagement updated.');
    }

    public function destroy(VaEngagement $va_engagement): RedirectResponse
    {
        $account = $va_engagement->clientAccount;

        if ($va_engagement->timeLogs()->exists()) {
            return redirect()->route('mis.va.engagements.edit', $va_engagement)
                ->with('error', 'Engagements with logged time cannot be deleted. End the engagement or contact an admin.');
        }

        $va_engagement->delete();

        return redirect()->route('mis.va.client-accounts.show', $account)
            ->with('status', 'Engagement removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'va_assistant_id' => ['required', 'exists:va_assistants,id'],
            'tier' => ['required', 'in:'.implode(',', VaClientAccount::$tiers)],
            'hours_per_month' => ['required', 'integer', 'min:1', 'max:1000'],
            'client_rate_monthly_gbp' => ['required', 'numeric', 'min:0'],
            'va_hourly_rate_gbp' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:'.implode(',', VaEngagement::$statuses)],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
