@extends('layouts.mis')

@section('title', 'New legal document')
@section('heading', 'New legal document')

@section('content')
    <form method="post" action="{{ route('mis.legal-documents.store') }}" class="space-y-4">
        @csrf
        @include('mis.legal-documents._form', ['legalDocument' => null])

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-xl bg-verlox-accent px-5 py-2 text-sm font-semibold text-on-verlox-accent">Save</button>
            <a href="{{ route('mis.legal-documents.index') }}" class="rounded-xl border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm">Cancel</a>
        </div>
    </form>
@endsection

