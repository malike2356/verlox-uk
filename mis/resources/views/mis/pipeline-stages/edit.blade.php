@extends('layouts.mis')

@section('title', 'Edit stage')
@section('heading', 'Edit pipeline stage')

@section('content')
    <form method="post" action="{{ route('mis.pipeline.stages.update', $stage) }}" class="max-w-md space-y-4 text-sm">
        @csrf @method('patch')
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Name</label>
            <input name="name" required value="{{ old('name', $stage->name) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $stage->sort_order) }}" min="0" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Colour (#hex)</label>
            <input name="color_hex" required value="{{ old('color_hex', $stage->color_hex) }}" pattern="#[0-9A-Fa-f]{6}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white font-mono">
        </div>
        <button type="submit" class="rounded-xl bg-verlox-accent px-5 py-2 text-sm font-semibold text-on-verlox-accent">Save</button>
        <a href="{{ route('mis.pipeline.stages.index') }}" class="block text-xs text-verlox-accent">Back</a>
    </form>
@endsection
