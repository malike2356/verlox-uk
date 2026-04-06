@extends('layouts.mis')

@section('title', 'Contracts')
@section('heading', 'Contracts')

@section('content')
    @include('mis.partials.zoho-accounting-strip')
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Number</th>
                <th class="px-3 py-2">Client</th>
                <th class="px-3 py-2">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($contracts as $c)
                <tr>
                    <td class="px-3 py-2"><a href="{{ route('mis.contracts.show', $c) }}" class="text-verlox-accent">{{ $c->number }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $c->client->contact_name }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $c->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $contracts->links() }}</div>
@endsection
