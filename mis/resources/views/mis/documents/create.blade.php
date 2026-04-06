@extends('layouts.mis')

@section('title', 'Upload')
@section('heading', 'Upload document')

@section('content')
    @php($fc = 'mt-1 w-full rounded-lg border px-3 py-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark]')
    <form method="post" action="{{ route('mis.documents.store') }}" enctype="multipart/form-data" class="max-w-lg space-y-3 text-sm">
        @csrf
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Client (optional)</label>
            <select name="client_id" class="{{ $fc }}">
                <option value="">None</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}" @selected((string)$clientId === (string)$c->id)>{{ $c->contact_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Title</label>
            <input name="title" required class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">File</label>
            <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border file:border-gray-300 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-900 hover:file:bg-gray-200 dark:text-slate-200">
        </div>
        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Upload</button>
    </form>
@endsection
