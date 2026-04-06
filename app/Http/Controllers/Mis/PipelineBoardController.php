<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\PipelineStage;
use Illuminate\View\View;

class PipelineBoardController extends Controller
{
    public function index(): View
    {
        $stages = PipelineStage::query()->orderBy('sort_order')->get();
        $leads = Lead::query()
            ->with(['offering', 'assignedUser', 'pipelineStage'])
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('pipeline_stage_id');

        $stageDealTotals = Lead::query()
            ->selectRaw('pipeline_stage_id, SUM(COALESCE(deal_value_pence, 0)) as sum_pence')
            ->whereNotIn('status', ['converted', 'lost'])
            ->groupBy('pipeline_stage_id')
            ->pluck('sum_pence', 'pipeline_stage_id');

        return view('mis.pipeline.board', compact('stages', 'leads', 'stageDealTotals'));
    }
}
