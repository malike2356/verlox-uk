@extends('layouts.mis')

@section('title', 'Contract templates')
@section('heading', 'Contract templates')

@section('content')
    <a href="{{ route('mis.contract-templates.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New template</a>
    <ul class="space-y-2 text-sm">
        @foreach ($templates as $t)
            <li class="flex items-center justify-between gap-2 rounded-xl border border-gray-200 dark:border-slate-800 px-3 py-2">
                <span>{{ $t->name }} @if($t->is_default)<span class="text-xs text-verlox-accent">(default)</span>@endif</span>
                <span class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('mis.contract-templates.edit', $t) }}" class="text-verlox-accent">Edit</a>
                    <form method="post" action="{{ route('mis.contract-templates.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete this template?');">@csrf @method('delete')
                        <button type="submit" class="text-red-400 text-xs">Delete</button>
                    </form>
                </span>
            </li>
        @endforeach
    </ul>
@endsection
