<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\PipelineStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PipelineStageController extends Controller
{
    public function index(): View
    {
        $stages = PipelineStage::query()->orderBy('sort_order')->get();

        return view('mis.pipeline-stages.index', compact('stages'));
    }

    public function create(): View
    {
        return view('mis.pipeline-stages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'color_hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
        PipelineStage::query()->create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? (PipelineStage::query()->max('sort_order') + 10),
            'color_hex' => $data['color_hex'],
        ]);

        return redirect()->route('mis.pipeline.stages.index')->with('status', 'Stage created.');
    }

    public function edit(PipelineStage $pipelineStage): View
    {
        return view('mis.pipeline-stages.edit', ['stage' => $pipelineStage]);
    }

    public function update(Request $request, PipelineStage $pipelineStage): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'color_hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
        $pipelineStage->update($data);

        return redirect()->route('mis.pipeline.stages.index')->with('status', 'Stage saved.');
    }

    public function destroy(PipelineStage $pipelineStage): RedirectResponse
    {
        if (Lead::query()->where('pipeline_stage_id', $pipelineStage->id)->exists()) {
            return back()->with('error', 'Move or delete leads in this stage before removing it.');
        }
        if (PipelineStage::query()->count() <= 1) {
            return back()->with('error', 'You must keep at least one pipeline stage.');
        }
        $pipelineStage->delete();

        return redirect()->route('mis.pipeline.stages.index')->with('status', 'Stage removed.');
    }
}
