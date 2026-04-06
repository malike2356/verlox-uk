<?php

namespace App\Http\Controllers\Mis\Va;

use App\Http\Controllers\Controller;
use App\Models\VaAssistant;
use App\Models\VaClientAccount;
use App\Models\VaEngagement;
use App\Models\VaTimeLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = now();

        $activeEngagements = VaEngagement::query()->where('status', 'active')->count();
        $activeVaClients = VaClientAccount::query()->where('status', 'active')->count();
        $assistantsActive = VaAssistant::query()->where('is_active', true)->count();
        $pendingTimeLogs = VaTimeLog::query()->where('is_approved', false)->count();

        $hoursThisMonth = (float) VaTimeLog::query()
            ->whereYear('work_date', $now->year)
            ->whereMonth('work_date', $now->month)
            ->where('is_approved', true)
            ->sum('hours_logged');

        $recentEngagements = VaEngagement::query()
            ->with(['clientAccount', 'assistant'])
            ->whereIn('status', ['active', 'draft'])
            ->latest()
            ->limit(8)
            ->get();

        return view('mis.va.dashboard', compact(
            'activeEngagements',
            'activeVaClients',
            'assistantsActive',
            'pendingTimeLogs',
            'hoursThisMonth',
            'recentEngagements',
        ));
    }
}
