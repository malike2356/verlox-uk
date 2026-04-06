@extends('layouts.mis')

@section('title', 'Documents')
@section('heading', 'Documents')

@section('content')
    <a href="{{ route('mis.documents.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">Upload</a>
    <ul class="space-y-2 text-sm">
        @foreach ($documents as $d)
            <li class="flex justify-between rounded-xl border border-gray-200 dark:border-slate-800 px-3 py-2">
                <span>{{ $d->title }} @if($d->client)<span class="text-gray-500 dark:text-slate-500">({{ $d->client->contact_name }})</span>@endif</span>
                <form method="post" action="{{ route('mis.documents.destroy', $d) }}">@csrf @method('delete')
                    <button type="submit" class="text-red-400 text-xs">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
    <div class="mt-4">{{ $documents->links() }}</div>
@endsection
