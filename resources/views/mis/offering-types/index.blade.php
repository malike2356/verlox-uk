@extends('layouts.mis')

@section('title', 'Offering types')
@section('heading', 'Offering types')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-500 dark:text-slate-500">Types are used to categorize offerings and power your forms and checkout flows.</p>
        <a href="{{ route('mis.offering-types.create') }}" class="rounded-lg bg-verlox-accent px-3 py-1.5 text-sm font-semibold text-on-verlox-accent">Add type</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Slug</th>
                <th class="px-3 py-2">Offerings</th>
                <th class="px-3 py-2">Active</th>
                <th class="px-3 py-2">Order</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse ($types as $t)
                <tr>
                    <td class="px-3 py-2 text-gray-900 dark:text-white font-medium">{{ $t->name }}</td>
                    <td class="px-3 py-2 font-mono text-gray-600 dark:text-slate-400">{{ $t->slug }}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $t->offerings_count }}</td>
                    <td class="px-3 py-2">{{ $t->is_active ? 'yes' : 'no' }}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $t->display_order }}</td>
                    <td class="px-3 py-2 flex flex-wrap gap-2 justify-end">
                        <a href="{{ route('mis.offering-types.edit', $t) }}" class="text-verlox-accent">Edit</a>
                        <form method="post" action="{{ route('mis.offering-types.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete this type?');">
                            @csrf @method('delete')
                            <button type="submit" class="text-red-600 dark:text-red-400 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-3 py-6 text-center text-gray-500 dark:text-slate-500">No offering types yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection

