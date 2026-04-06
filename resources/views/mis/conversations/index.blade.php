@extends('layouts.mis')

@section('title', 'Messages')
@section('heading', 'Client messages')

@section('content')
    <a href="{{ route('mis.conversations.create') }}" class="mb-4 inline-block rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">Compose</a>
    <ul class="space-y-2 text-sm">
        @foreach ($conversations as $c)
            <li class="rounded-xl border border-gray-200 dark:border-slate-800 px-3 py-2 flex justify-between">
                <a href="{{ route('mis.conversations.show', $c) }}" class="text-verlox-accent">{{ $c->subject ?? 'Conversation' }}</a>
                <span class="text-gray-500 dark:text-slate-500">{{ $c->client->contact_name }}</span>
            </li>
        @endforeach
    </ul>
    <div class="mt-4">{{ $conversations->links() }}</div>
@endsection
