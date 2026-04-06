@extends('layouts.mis')

@section('title', 'Log VA time')
@section('heading', 'Log VA time')

@section('content')
    @if($engagements->isEmpty())
        <p class="text-sm text-gray-600 dark:text-slate-400">Create an engagement first (VA client → assign assistant).</p>
        <a href="{{ route('mis.va.dashboard') }}" class="text-verlox-accent text-sm">VA dashboard</a>
    @else
        <form method="post" action="{{ route('mis.va.time-logs.store') }}" class="max-w-xl space-y-3">
            @csrf
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Engagement</label>
                <select name="va_engagement_id" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">
                    @foreach ($engagements as $eng)
                        <option value="{{ $eng->id }}">
                            {{ $eng->clientAccount->company_name }} - {{ $eng->assistant->full_name }} (#{{ $eng->id }}, {{ $eng->status }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Work date</label>
                <input type="date" name="work_date" value="{{ old('work_date', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Hours</label>
                <input type="number" step="0.25" min="0.25" max="24" name="hours_logged" value="{{ old('hours_logged') }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Task description</label>
                <textarea name="task_description" rows="3" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">{{ old('task_description') }}</textarea>
            </div>
            <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Submit log</button>
            <a href="{{ route('mis.va.time-logs.index') }}" class="ml-3 text-sm text-verlox-accent">Cancel</a>
        </form>
    @endif
@endsection
