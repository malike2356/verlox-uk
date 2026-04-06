<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Quotation;
use App\Models\VaTimeLog;
use App\Models\ZohoSyncLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $from30 = now()->subDays(30)->startOfDay();
        $from6m = now()->subMonths(6)->startOfDay();
        $yearStart = now()->startOfYear();

        $leadCount = Lead::query()->count();
        $clientCount = Client::query()->count();
        $openQuotes = Quotation::query()->whereIn('status', ['draft', 'sent'])->count();
        $unpaidInvoices = Invoice::query()->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();

        $upcomingBookings = Booking::query()
            ->where('starts_at', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('starts_at')
            ->limit(6)
            ->get();

        $leadsLast30Days = Lead::query()
            ->where('created_at', '>=', $from30)
            ->orderBy('created_at')
            ->get(['created_at']);

        $leadTrendLabels = [];
        $leadTrendData = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->startOfDay();
            $leadTrendLabels[] = $d->format('j M');
            $leadTrendData[] = $leadsLast30Days->filter(fn ($l) => $l->created_at->isSameDay($d))->count();
        }

        $stages = PipelineStage::query()->orderBy('sort_order')->get();
        $stageCounts = Lead::query()
            ->selectRaw('pipeline_stage_id, count(*) as c')
            ->groupBy('pipeline_stage_id')
            ->pluck('c', 'pipeline_stage_id');
        $pipelineLabels = $stages->pluck('name')->values()->all();
        $pipelineData = $stages->map(fn ($s) => (int) ($stageCounts[$s->id] ?? 0))->values()->all();
        $pipelineColors = $stages->pluck('color_hex')->values()->all();

        $stageDealTotals = Lead::query()
            ->selectRaw('pipeline_stage_id, SUM(COALESCE(deal_value_pence, 0)) as sum_pence')
            ->whereNotIn('status', ['converted', 'lost'])
            ->groupBy('pipeline_stage_id')
            ->pluck('sum_pence', 'pipeline_stage_id');

        $pipelineValuePence = (int) Lead::query()
            ->open()
            ->sum('deal_value_pence');

        $paidInvoices = Invoice::query()
            ->where('status', 'paid')
            ->where('updated_at', '>=', $from6m)
            ->get(['total_pence', 'updated_at']);

        $revenueLabels = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $key = $month->format('Y-m');
            $revenueLabels[] = $month->format('M Y');
            $sum = $paidInvoices->filter(fn ($inv) => $inv->updated_at->format('Y-m') === $key)->sum('total_pence');
            $revenueData[] = round($sum / 100, 2);
        }

        $bookingsLast30 = Booking::query()
            ->where('created_at', '>=', $from30)
            ->where('status', '!=', 'cancelled')
            ->count();

        $newLeadsWeek = Lead::query()->where('created_at', '>=', now()->subDays(7))->count();
        $wonQuotes = Quotation::query()->where('status', 'accepted')->count();
        $convertedLeads = Lead::query()->where('status', 'converted')->count();
        $activePipeline = max(1, Lead::query()->whereNotIn('status', ['converted', 'lost'])->count());
        $conversionRate = round(100 * $convertedLeads / ($convertedLeads + $activePipeline), 1);

        $invoicedYtdPence = (int) Invoice::query()
            ->where('issued_at', '>=', $yearStart)
            ->whereNotIn('status', ['draft', 'written_off'])
            ->sum('total_pence');
        $collectedYtdPence = (int) Invoice::query()
            ->where('issued_at', '>=', $yearStart)
            ->sum('paid_pence');

        $vaHoursMonth = (float) VaTimeLog::query()
            ->where('is_approved', true)
            ->whereYear('work_date', now()->year)
            ->whereMonth('work_date', now()->month)
            ->sum('hours_logged');

        $zohoRecentFailures = ZohoSyncLog::query()
            ->where('status', 'error')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $zohoFailureCount24h = ZohoSyncLog::query()
            ->where('status', 'error')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $lastZohoSync = ZohoSyncLog::query()->orderByDesc('id')->first();

        return view('mis.dashboard', [
            'leadCount' => $leadCount,
            'clientCount' => $clientCount,
            'openQuotes' => $openQuotes,
            'unpaidInvoices' => $unpaidInvoices,
            'upcomingBookings' => $upcomingBookings,
            'leadTrendLabels' => $leadTrendLabels,
            'leadTrendData' => $leadTrendData,
            'pipelineLabels' => $pipelineLabels,
            'pipelineData' => $pipelineData,
            'pipelineColors' => $pipelineColors,
            'stageDealTotals' => $stageDealTotals,
            'pipelineValuePence' => $pipelineValuePence,
            'revenueLabels' => $revenueLabels,
            'revenueData' => $revenueData,
            'bookingsLast30' => $bookingsLast30,
            'newLeadsWeek' => $newLeadsWeek,
            'wonQuotes' => $wonQuotes,
            'conversionRate' => $conversionRate,
            'invoicedYtdPence' => $invoicedYtdPence,
            'collectedYtdPence' => $collectedYtdPence,
            'vaHoursMonth' => $vaHoursMonth,
            'zohoRecentFailures' => $zohoRecentFailures,
            'zohoFailureCount24h' => $zohoFailureCount24h,
            'lastZohoSync' => $lastZohoSync,
        ]);
    }
}
