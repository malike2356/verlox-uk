@extends('layouts.mis')

@section('title', 'Offerings')
@section('heading', 'Service catalog')

@section('content')
    @include('mis.partials.zoho-accounting-strip')
    <a href="{{ route('mis.offerings.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New offering</a>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr><th class="px-3 py-2">Name</th><th class="px-3 py-2">Type</th><th class="px-3 py-2">Price</th><th class="px-3 py-2">Active</th><th></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($offerings as $o)
                <tr>
                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $o->name }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $o->type }}</td>
                    <td class="px-3 py-2 font-mono text-gray-600 dark:text-slate-400">@if($o->price_pence){{ number_format($o->price_pence/100,2) }} {{ $o->currency }}@else - @endif</td>
                    <td class="px-3 py-2">{{ $o->is_active ? 'yes' : 'no' }}</td>
                    <td class="px-3 py-2 flex flex-wrap gap-2">
                        <a href="{{ route('mis.offerings.edit', $o) }}" class="text-verlox-accent">Edit</a>
                        <form method="post" action="{{ route('mis.offerings.destroy', $o) }}" class="inline" onsubmit="return confirm('Delete this offering?');">@csrf @method('delete')
                            <button type="submit" class="text-red-400 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
