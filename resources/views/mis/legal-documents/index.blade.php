@extends('layouts.mis')

@section('title', 'Legal documents')
@section('heading', 'Legal documents')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600 dark:text-slate-400">Policies and legal documents for Verlox. These can be linked from invoices and shared with clients.</p>
        <a href="{{ route('mis.legal-documents.create') }}" class="inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">New legal document</a>
    </div>

    <ul class="space-y-2 text-sm">
        @foreach ($documents as $d)
            <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 dark:border-slate-800 px-3 py-2">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $d->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">
                        <span class="uppercase tracking-wide">{{ $d->category }}</span>
                        <span class="mx-1 text-gray-300 dark:text-slate-600">·</span>
                        <span class="{{ $d->status === 'published' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-300' }}">{{ $d->status }}</span>
                        @if($d->effective_at)
                            <span class="mx-1 text-gray-300 dark:text-slate-600">·</span>
                            Effective {{ $d->effective_at->format('Y-m-d') }}
                        @endif
                        <span class="mx-1 text-gray-300 dark:text-slate-600">·</span>
                        <span class="font-mono">/{{ $d->slug }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a class="text-verlox-accent" href="{{ route('mis.legal-documents.edit', $d) }}">Edit</a>
                    <a class="text-verlox-accent" href="{{ route('legal.show', $d->slug) }}" target="_blank" rel="noreferrer">View</a>
                    <a class="text-verlox-accent" href="{{ route('mis.legal-documents.download', $d) }}">Download</a>
                    <form method="post" action="{{ route('mis.legal-documents.create-document', $d) }}" class="inline">@csrf
                        <button type="submit" class="text-verlox-accent">Save to Documents</button>
                    </form>
                    <form method="post" action="{{ route('mis.legal-documents.destroy', $d) }}" class="inline" onsubmit="return confirm('Delete this legal document?');">
                        @csrf @method('delete')
                        <button type="submit" class="text-red-400 text-xs">Delete</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endsection

