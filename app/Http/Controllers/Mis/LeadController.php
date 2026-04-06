<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Offering;
use App\Models\PipelineStage;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(): View
    {
        $leads = Lead::query()
            ->with(['pipelineStage', 'offering', 'assignedUser', 'client'])
            ->withCount('bookings')
            ->orderByDesc('id')
            ->paginate(20);

        return view('mis.leads.index', compact('leads'));
    }

    public function create(): View
    {
        $stages = PipelineStage::query()->orderBy('sort_order')->get();
        $offerings = Offering::query()->where('is_active', true)->orderBy('name')->get();
        $users = User::query()
            ->where(function ($q): void {
                $q->where('is_admin', true)->orWhereNotNull('mis_role');
            })
            ->orderBy('name')
            ->get();

        return view('mis.leads.create', compact('stages', 'offerings', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_stage_id' => ['required', 'exists:pipeline_stages,id'],
            'offering_id' => ['nullable', 'exists:offerings,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'message' => ['nullable', 'string', 'max:10000'],
            'source' => ['nullable', 'string', 'max:64'],
        ]);

        $lead = Lead::create([
            'pipeline_stage_id' => $data['pipeline_stage_id'],
            'offering_id' => $data['offering_id'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'contact_name' => $data['contact_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'] ?? null,
            'source' => $data['source'] ?? 'manual',
            'status' => 'new',
        ]);

        return redirect()->route('mis.leads.show', $lead)->with('status', 'Lead created.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['pipelineStage', 'offering', 'assignedUser', 'bookings', 'activities.user', 'client']);
        $stages = PipelineStage::query()->orderBy('sort_order')->get();
        $offerings = Offering::query()->where('is_active', true)->orderBy('name')->get();
        $users = User::query()
            ->where(function ($q): void {
                $q->where('is_admin', true)->orWhereNotNull('mis_role');
            })
            ->orderBy('name')
            ->get();

        return view('mis.leads.show', compact('lead', 'stages', 'offerings', 'users'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse|JsonResponse
    {
        $identityLocked = $lead->status === 'converted';
        $htmlForm = ! $request->wantsJson();

        $rules = [
            'pipeline_stage_id' => ['required', 'exists:pipeline_stages,id'],
            'offering_id' => ['nullable', 'exists:offerings,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(Lead::STATUSES)],
            'deal_value_gbp' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'expected_close_date' => ['nullable', 'date'],
            'loss_reason' => ['nullable', 'string', 'max:500'],
        ];

        if (! $identityLocked && $htmlForm) {
            $rules['contact_name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:64'];
            $rules['company_name'] = ['nullable', 'string', 'max:255'];
            $rules['source'] = ['nullable', 'string', 'max:64'];
            $rules['message'] = ['nullable', 'string', 'max:10000'];
        }

        $data = $request->validate($rules);

        if ($data['status'] === 'lost' && empty(trim((string) ($data['loss_reason'] ?? '')))) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Loss reason is required when status is lost.'], 422);
            }

            return back()->withErrors(['loss_reason' => 'Loss reason is required when status is lost.'])->withInput();
        }

        $dealPence = isset($data['deal_value_gbp']) && $data['deal_value_gbp'] !== ''
            ? (int) round((float) $data['deal_value_gbp'] * 100)
            : null;

        $payload = [
            'pipeline_stage_id' => $data['pipeline_stage_id'],
            'offering_id' => $data['offering_id'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'status' => $data['status'],
            'deal_value_pence' => $dealPence,
            'expected_close_date' => $data['expected_close_date'] ?? null,
            'loss_reason' => $data['status'] === 'lost' ? ($data['loss_reason'] ?? null) : null,
        ];

        if (! $identityLocked && $htmlForm) {
            $payload['contact_name'] = $data['contact_name'];
            $payload['email'] = $data['email'];
            $payload['phone'] = $data['phone'] ?? null;
            $payload['company_name'] = $data['company_name'] ?? null;
            $payload['source'] = $data['source'] ?? null;
            $payload['message'] = $data['message'] ?? null;
        }

        $auditKeys = array_keys($payload);
        $old = $lead->only($auditKeys);

        $lead->update($payload);

        $new = $lead->only($auditKeys);
        if ($old !== $new) {
            AuditLogger::record($lead, 'updated', $old, $new, $request);
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'lead_id' => $lead->id, 'stage_id' => $lead->pipeline_stage_id]);
        }

        return back()->with('status', 'Lead updated.');
    }

    public function updateStage(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'pipeline_stage_id' => ['required', 'exists:pipeline_stages,id'],
        ]);
        $old = $lead->only(['pipeline_stage_id']);
        $lead->update(['pipeline_stage_id' => $data['pipeline_stage_id']]);
        AuditLogger::record($lead, 'stage_changed', $old, $lead->only(['pipeline_stage_id']), $request);

        return response()->json(['ok' => true, 'lead_id' => $lead->id, 'stage_id' => $lead->pipeline_stage_id]);
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        if ($lead->status === 'converted' || $lead->client) {
            return redirect()->route('mis.leads.show', $lead)
                ->with('error', 'Converted leads cannot be deleted. Remove the client record first if you really need to remove this trail.');
        }
        if ($lead->bookings()->exists()) {
            return redirect()->route('mis.leads.show', $lead)
                ->with('error', 'Detach or delete linked bookings before deleting this lead.');
        }

        $lead->delete();

        return redirect()->route('mis.leads.index')->with('status', 'Lead deleted.');
    }
}
