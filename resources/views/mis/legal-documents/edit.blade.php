@extends('layouts.mis')

@section('title', 'Edit legal document')
@section('heading', 'Edit legal document')

@section('content')
    <form method="post" action="{{ route('mis.legal-documents.update', $legalDocument) }}" class="space-y-4">
        @csrf
        @method('patch')

        @include('mis.legal-documents._form', ['legalDocument' => $legalDocument])

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-xl bg-verlox-accent px-5 py-2 text-sm font-semibold text-on-verlox-accent">Update</button>
            <a href="{{ route('mis.legal-documents.index') }}" class="rounded-xl border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm">Back</a>
        </div>
    </form>
@endsection

