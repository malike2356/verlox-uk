@extends('layouts.mis')

@section('title', 'Pipeline stages')
@section('heading', 'Pipeline stages')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-500 dark:text-slate-500">Stages define your funnel. Leads must sit in exactly one stage.</p>
        <div class="flex gap-2">
            <a href="{{ route('mis.pipeline.index') }}" class="text-sm text-verlox-accent text-verlox-accent-hover">Board view</a>
            <a href="{{ route('mis.pipeline.stages.create') }}" class="rounded-lg bg-verlox-accent px-3 py-1.5 text-sm font-semibold text-on-verlox-accent">Add stage</a>
        </div>
    </div>
    <div class="space-y-2">
        @foreach ($stages as $s)
            <div class="flex flex-wrap items-center gap-3 rounded-xl border border-gray-200 dark:border-slate-800 px-3 py-2 text-sm bg-white/80 dark:bg-slate-900/40">
                <span class="h-3 w-3 rounded-full shrink-0" style="background: {{ $s->color_hex }}"></span>
                <span class="text-gray-900 dark:text-white font-medium flex-1 min-w-0">{{ $s->name }}</span>
                <span class="text-gray-500 dark:text-slate-500 text-xs">order {{ $s->sort_order }}</span>
                <a href="{{ route('mis.pipeline.stages.edit', $s) }}" class="text-xs text-verlox-accent">Edit</a>
                <form method="post" action="{{ route('mis.pipeline.stages.destroy', $s) }}" class="inline" onsubmit="return confirm('Delete this stage?');">
                    @csrf @method('delete')
                    <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">Delete</button>
                </form>
            </div>
        @endforeach
    </div>
@endsection
