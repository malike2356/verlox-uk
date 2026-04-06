@extends('layouts.mis')

@section('title', 'VA time logs')
@section('heading', 'VA time logs')

@section('content')
    <div class="mb-4 flex flex-wrap gap-3 text-sm">
        <a href="{{ route('mis.va.time-logs.create') }}" class="rounded-xl bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">New log</a>
        <a href="{{ route('mis.va.time-logs.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 dark:border-slate-700 {{ request('pending') ? '' : 'ring-2 ring-verlox-accent/40' }}">All</a>
        <a href="{{ route('mis.va.time-logs.index', ['pending' => 1]) }}" class="rounded-xl border border-gray-200 px-4 py-2 dark:border-slate-700 {{ request('pending') === '1' ? 'ring-2 ring-amber-400/50' : '' }}">Pending only</a>
    </div>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Client</th>
                <th class="px-3 py-2">VA</th>
                <th class="px-3 py-2">Hours</th>
                <th class="px-3 py-2">Task</th>
                <th class="px-3 py-2">Approved</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($logs as $log)
                <tr>
                    <td class="px-3 py-2 font-mono text-xs">{{ $log->work_date->format('Y-m-d') }}</td>
                    <td class="px-3 py-2"><a href="{{ route('mis.va.client-accounts.show', $log->clientAccount) }}" class="text-verlox-accent">{{ $log->clientAccount->company_name }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $log->assistant->full_name }}</td>
                    <td class="px-3 py-2 font-mono">{{ $log->hours_logged }}</td>
                    <td class="px-3 py-2 max-w-xs truncate text-gray-600 dark:text-slate-400" title="{{ $log->task_description }}">{{ \Illuminate\Support\Str::limit($log->task_description, 48) }}</td>
                    <td class="px-3 py-2">{{ $log->is_approved ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-2">
                        @if(!$log->is_approved)
                            <form method="post" action="{{ route('mis.va.time-logs.approve', $log) }}" class="inline">@csrf @method('patch')
                                <button type="submit" class="text-xs text-emerald-600 dark:text-emerald-400">Approve</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
