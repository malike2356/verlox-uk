@extends('layouts.mis')

@section('title', 'Clients')
@section('heading', 'Clients')

@section('content')
    <a href="{{ route('mis.clients.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New client</a>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Company</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($clients as $c)
                <tr>
                    <td class="px-3 py-2"><a href="{{ route('mis.clients.show', $c) }}" class="text-verlox-accent">{{ $c->contact_name }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $c->email }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $c->company_name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $clients->links() }}</div>
@endsection
