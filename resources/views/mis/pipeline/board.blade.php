@extends('layouts.mis')

@section('title', 'Pipeline')
@section('heading', 'CRM pipeline')

@section('mainClass', 'max-w-none')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-600 dark:text-slate-400">Drag cards between columns to move leads. <a href="{{ route('mis.leads.index') }}" class="text-verlox-accent">Table view</a></p>
        @if(auth()->user()->is_admin)
            <a href="{{ route('mis.pipeline.stages.index') }}" class="text-sm text-verlox-accent text-verlox-accent-hover">Edit stages</a>
        @endif
    </div>

    <div class="overflow-x-auto pb-4">
        <div class="flex justify-center min-h-[320px]">
            <div id="pipeline-board" class="inline-flex gap-3">
                @foreach ($stages as $stage)
                    @php $stageLeads = $leads->get($stage->id) ?? collect(); @endphp
                    <div class="mis-pipeline-col flex-shrink-0 w-72 flex flex-col rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/90 dark:bg-[#071426] max-h-[70vh]">
                        <div class="px-3 py-2 border-b border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background: {{ $stage->color_hex }}"></span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $stage->name }}</span>
                            <span class="text-xs text-slate-600 dark:text-slate-300 ml-auto">{{ $stageLeads->count() }}</span>
                        </div>
                        <div class="kanban-list flex-1 overflow-y-auto p-2 space-y-2 min-h-[100px]"
                             data-stage-id="{{ $stage->id }}">
                            @foreach ($stageLeads as $lead)
                                <div class="mis-pipeline-card sortable-lead rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-[#0F223C] p-3 shadow-sm cursor-grab active:cursor-grabbing"
                                     data-lead-id="{{ $lead->id }}">
                                    <a href="{{ route('mis.leads.show', $lead) }}" class="text-sm font-semibold text-slate-900 dark:text-slate-50 hover:text-verlox-accent">{{ $lead->contact_name }}</a>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 truncate mt-0.5">{{ $lead->email }}</p>
                                    @if($lead->offering)
                                        <p class="text-[10px] uppercase tracking-wide text-slate-500 dark:text-slate-400 mt-1">{{ $lead->offering->name }}</p>
                                    @endif
                                    @if($lead->deal_value_pence)
                                        <p class="text-[11px] font-mono text-emerald-700 dark:text-emerald-400 mt-1">£{{ number_format($lead->deal_value_pence / 100, 0) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Sortable === 'undefined') return;
    const csrf = document.querySelector('meta[name=csrf-token]')?.content;
    const lists = document.querySelectorAll('.kanban-list');
    lists.forEach(function (el) {
        new Sortable(el, {
            group: 'pipeline',
            animation: 150,
            draggable: '.sortable-lead',
            onEnd: function (evt) {
                const leadId = evt.item?.dataset?.leadId;
                const toStage = evt.to?.dataset?.stageId;
                if (!leadId || !toStage) return;
                fetch('{{ url('/mis/leads') }}/' + leadId + '/stage', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pipeline_stage_id: parseInt(toStage, 10) }),
                }).then(function (r) {
                    if (!r.ok) window.location.reload();
                }).catch(function () {
                    window.location.reload();
                });
            },
        });
    });
});
</script>
@endpush
