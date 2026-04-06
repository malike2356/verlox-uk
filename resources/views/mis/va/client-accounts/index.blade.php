@extends('layouts.mis')

@section('title', 'VA clients')
@section('heading', 'VA client accounts')

@section('content')
    <a href="{{ route('mis.va.client-accounts.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New VA client</a>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Company</th>
                <th class="px-3 py-2">Contact</th>
                <th class="px-3 py-2">Tier</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2 text-right">£/mo</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($accounts as $a)
                <tr>
                    <td class="px-3 py-2"><a href="{{ route('mis.va.client-accounts.show', $a) }}" class="text-verlox-accent">{{ $a->company_name }}</a></td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $a->contact_name }}</td>
                    <td class="px-3 py-2">{{ $a->tier }}</td>
                    <td class="px-3 py-2">{{ $a->status }}</td>
                    <td class="px-3 py-2 text-right font-mono">{{ number_format($a->monthly_rate_gbp, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $accounts->links() }}</div>
@endsection
