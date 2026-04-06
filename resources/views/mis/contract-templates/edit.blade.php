@extends('layouts.mis')

@section('title', 'Edit template')
@section('heading', 'Edit contract template')

@section('content')
    <form method="post" action="{{ route('mis.contract-templates.update', $contractTemplate) }}" class="max-w-3xl space-y-3 text-sm">
        @csrf @method('patch')
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Name</label><input name="name" value="{{ $contractTemplate->name }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Slug</label><input name="slug" value="{{ $contractTemplate->slug }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Body</label>
            <textarea name="body" rows="14" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white font-mono text-xs">{{ $contractTemplate->body }}</textarea></div>
        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400"><input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 dark:border-slate-700" @checked($contractTemplate->is_default)> Default template</label>
        <button class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection
