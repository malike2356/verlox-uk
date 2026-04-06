@extends('layouts.mis')

@section('title', 'Edit offering')
@section('heading', 'Edit offering')

@section('content')
    <form method="post" action="{{ route('mis.offerings.update', $offering) }}" class="max-w-lg space-y-3 text-sm">
        @csrf @method('patch')
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Name</label><input name="name" value="{{ $offering->name }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Slug</label><input name="slug" value="{{ $offering->slug }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Summary</label><input name="summary" value="{{ $offering->summary }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Type</label>
            <select name="offering_type_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white" required>
                @foreach ($types as $t)
                    <option value="{{ $t->id }}" @selected((int) $offering->offering_type_id === (int) $t->id)>{{ $t->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-400 dark:text-slate-500">Manage types in <a class="text-verlox-accent hover:underline" href="{{ route('mis.offering-types.index') }}">Offering types</a>.</p>
        </div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Price (pence)</label><input type="number" name="price_pence" value="{{ $offering->price_pence }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Currency</label><input name="currency" value="{{ $offering->currency }}" maxlength="3" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Stripe price id</label><input name="stripe_price_id" value="{{ $offering->stripe_price_id }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400"><input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-slate-700" @checked($offering->is_active)> Active</label>
        <button class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection
