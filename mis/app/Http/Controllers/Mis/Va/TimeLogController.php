<?php

namespace App\Http\Controllers\Mis\Va;

use App\Http\Controllers\Controller;
use App\Models\VaEngagement;
use App\Models\VaTimeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = VaTimeLog::query()->with(['engagement.clientAccount', 'assistant'])->latest('work_date');

        if ($request->query('pending') === '1') {
            $query->where('is_approved', false);
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('mis.va.time-logs.index', compact('logs'));
    }

    public function create(): View
    {
        $engagements = VaEngagement::query()
            ->whereIn('status', ['active', 'draft'])
            ->with(['clientAccount', 'assistant'])
            ->orderByDesc('id')
            ->get();

        return view('mis.va.time-logs.create', compact('engagements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'va_engagement_id' => ['required', 'exists:va_engagements,id'],
            'work_date' => ['required', 'date'],
            'hours_logged' => ['required', 'numeric', 'min:0.25', 'max:24'],
            'task_description' => ['required', 'string', 'max:5000'],
        ]);

        $engagement = VaEngagement::query()->findOrFail($data['va_engagement_id']);

        VaTimeLog::query()->create([
            'va_engagement_id' => $engagement->id,
            'va_assistant_id' => $engagement->va_assistant_id,
            'va_client_account_id' => $engagement->va_client_account_id,
            'work_date' => $data['work_date'],
            'hours_logged' => $data['hours_logged'],
            'task_description' => $data['task_description'],
            'is_approved' => false,
        ]);

        return redirect()->route('mis.va.time-logs.index')->with('status', 'Time log added (pending approval).');
    }

    public function approve(Request $request, VaTimeLog $va_time_log): RedirectResponse
    {
        if ($va_time_log->is_approved) {
            return back()->with('status', 'Already approved.');
        }

        $user = $request->user();
        $va_time_log->update([
            'is_approved' => true,
            'approved_by' => $user?->name ?? $user?->email ?? 'admin',
            'approved_at' => now(),
        ]);

        return back()->with('status', 'Time log approved.');
    }
}
