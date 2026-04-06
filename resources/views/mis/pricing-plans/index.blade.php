@extends('layouts.mis')

@section('title', 'Pricing plans')
@section('heading', 'Marketing pricing plans')

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('mis.pricing-plans.create') }}"
           class="inline-flex rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent hover:opacity-95">
            New plan
        </a>
        <p class="text-xs text-gray-500 dark:text-slate-400">Shown on the site where each plan’s toggles allow (home, book, VA).</p>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-left text-xs uppercase text-gray-500 dark:bg-slate-900 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Price</th>
                <th class="px-3 py-2">Placement</th>
                <th class="px-3 py-2">Features</th>
                <th class="px-3 py-2">Active</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse ($plans as $p)
                <tr>
                    <td class="px-3 py-2 text-gray-900 dark:text-white">
                        <span class="font-medium">{{ $p->name }}</span>
                        @if($p->is_featured)
                            <span class="ms-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-amber-900 dark:bg-amber-900/40 dark:text-amber-200">Featured</span>
                        @endif
                        <div class="mt-0.5 font-mono text-[11px] text-gray-400 dark:text-slate-500">{{ $p->slug }}</div>
                    </td>
                    <td class="px-3 py-2 text-gray-700 dark:text-slate-300">{{ $p->formattedPrice() }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500 dark:text-slate-400">
                        @if($p->show_on_home)<span class="me-1 rounded bg-gray-100 px-1.5 py-0.5 dark:bg-slate-800">Home</span>@endif
                        @if($p->show_on_book)<span class="me-1 rounded bg-gray-100 px-1.5 py-0.5 dark:bg-slate-800">Book</span>@endif
                        @if($p->show_on_va)<span class="rounded bg-gray-100 px-1.5 py-0.5 dark:bg-slate-800">VA</span>@endif
                    </td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $p->features_count }}</td>
                    <td class="px-3 py-2">{{ $p->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <a href="{{ route('mis.pricing-plans.edit', $p) }}" class="text-verlox-accent hover:underline">Edit</a>
                        <form method="post" action="{{ route('mis.pricing-plans.destroy', $p) }}" class="ms-3 inline">
                            @csrf @method('delete')
                            <button type="submit" class="text-xs text-red-500 hover:underline"
                                    onclick="return confirm('Delete this pricing plan?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-slate-500">No pricing plans yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
