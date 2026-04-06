@extends('layouts.mis')

@section('title', $contract->number)
@section('heading', $contract->number)

@section('content')
    <div class="mb-4">@include('mis.partials.zoho-accounting-strip', ['compact' => true])</div>
    <div class="mb-4 flex flex-wrap items-center gap-3 text-sm">
        <form method="post" action="{{ route('mis.contracts.status', $contract) }}">@csrf @method('patch')
            <label class="text-gray-500 dark:text-slate-500 text-xs">Status</label>
            <select name="status" onchange="this.form.submit()" class="ml-2 rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-2 py-1 text-gray-900 dark:text-white">
                @foreach (['draft','sent','signed','cancelled'] as $s)
                    <option value="{{ $s }}" @selected($contract->status === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </form>
        @if($contract->status !== 'signed' && !$contract->signed_at)
            <form method="post" action="{{ route('mis.contracts.destroy', $contract) }}" class="inline" onsubmit="return confirm('Delete this contract?');">@csrf @method('delete')
                <button type="submit" class="rounded-lg border border-red-500/50 text-red-400 px-3 py-1 text-xs">Delete contract</button>
            </form>
        @endif
    </div>
    <div class="prose prose-sm dark:prose-invert max-w-none rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 p-4 text-gray-800 dark:text-slate-200">
        {!! $contract->body_snapshot !!}
    </div>
@endsection
