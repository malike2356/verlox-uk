@extends('layouts.mis')

@section('title', 'Leads')
@section('heading', 'Leads')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('mis.leads.create') }}" class="inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New lead</a>
    </div>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Contact</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Stage</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Source</th>
                <th class="px-3 py-2 text-end">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse ($leads as $lead)
                @php
                    $canDelete = $lead->status !== 'converted' && ! $lead->client && $lead->bookings_count === 0;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-900/60">
                    <td class="px-3 py-2">
                        <a href="{{ route('mis.leads.show', $lead) }}" class="font-medium text-verlox-accent hover:text-indigo-600 dark:text-[#E7D59C]">{{ $lead->contact_name }}</a>
                    </td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $lead->email }}</td>
                    <td class="px-3 py-2">
                        <span class="rounded-full px-2 py-0.5 text-xs" style="background: {{ $lead->pipelineStage->color_hex }}22; color: {{ $lead->pipelineStage->color_hex }}">{{ $lead->pipelineStage->name }}</span>
                    </td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ ucfirst($lead->status) }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $lead->source ?? '-' }}</td>
                    <td class="px-3 py-2 text-end whitespace-nowrap">
                        <a href="{{ route('mis.leads.show', $lead) }}" class="text-verlox-accent hover:underline mr-2">View</a>
                        @if($canDelete)
                            <form method="post" action="{{ route('mis.leads.destroy', $lead) }}" class="inline" onsubmit="return confirm('Delete this lead permanently?');">
                                @csrf
                                @method('delete')
                                <button type="submit" class="text-red-500 hover:text-red-400 text-xs font-medium">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-slate-500">
                        No leads yet. <a href="{{ route('mis.leads.create') }}" class="text-verlox-accent">Create the first lead</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($leads->isNotEmpty())
        <div class="mt-4">{{ $leads->links() }}</div>
    @endif
@endsection
