@extends('layouts.mis')

@section('title', 'VA assistants')
@section('heading', 'VA assistants')

@section('content')
    <a href="{{ route('mis.va.assistants.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New assistant</a>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Rate £/hr</th>
                <th class="px-3 py-2">Availability</th>
                <th class="px-3 py-2">Active</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($assistants as $a)
                <tr class="{{ !$a->is_active ? 'opacity-60' : '' }}">
                    <td class="px-3 py-2"><a href="{{ route('mis.va.assistants.edit', $a) }}" class="text-verlox-accent">{{ $a->full_name }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $a->email }}</td>
                    <td class="px-3 py-2 font-mono">{{ number_format($a->hourly_rate_gbp, 2) }}</td>
                    <td class="px-3 py-2">{{ $a->availability }}</td>
                    <td class="px-3 py-2">{{ $a->is_active ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $assistants->links() }}</div>
@endsection
