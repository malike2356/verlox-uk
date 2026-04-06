@extends('layouts.mis')

@section('title', $assistant->full_name)
@section('heading', 'Edit: '.$assistant->full_name)

@section('content')
    <form method="post" action="{{ route('mis.va.assistants.update', $assistant) }}" class="space-y-6">
        @csrf @method('patch')
        @include('mis.va.assistants._form', ['assistant' => $assistant])
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Save</button>
            <a href="{{ route('mis.va.assistants.index') }}" class="text-sm text-verlox-accent">Back</a>
        </div>
    </form>

    <form method="post" action="{{ route('mis.va.assistants.destroy', $assistant) }}" class="mt-10 border-t border-gray-200 pt-6 dark:border-slate-800" onsubmit="return confirm('Mark this assistant inactive?');">
        @csrf @method('delete')
        <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm text-red-700 dark:border-red-800 dark:text-red-300">Mark inactive</button>
    </form>
@endsection
