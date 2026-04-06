@extends('layouts.mis')

@section('title', 'New offering')
@section('heading', 'New offering')

@section('content')
    <form method="post" action="{{ route('mis.offerings.store') }}" class="max-w-lg space-y-3 text-sm">
        @csrf
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Name</label><input name="name" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Slug (optional)</label><input name="slug" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Summary</label><input name="summary" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Type</label>
            <select name="offering_type_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white" required>
                @foreach ($types as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-400 dark:text-slate-500">Manage types in <a class="text-verlox-accent hover:underline" href="{{ route('mis.offering-types.index') }}">Offering types</a>.</p>
        </div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Price (pence, ex VAT)</label><input type="number" name="price_pence" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Currency</label><input name="currency" value="GBP" maxlength="3" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 dark:border-slate-700"> Active</label>
        <button class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection
