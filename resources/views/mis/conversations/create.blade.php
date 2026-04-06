@extends('layouts.mis')

@section('title', 'Compose')
@section('heading', 'New message')

@section('content')
    <form method="post" action="{{ route('mis.conversations.store') }}" class="max-w-xl space-y-4 text-sm">
        @csrf
        @php($fc = 'rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 placeholder:text-gray-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400')
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Client</label>
            <select name="client_id" required class="mt-1.5 w-full {{ $fc }}">
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}" @selected((string)$clientId === (string)$c->id)>{{ $c->contact_name }} ({{ $c->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Subject</label>
            <input type="text" name="subject" class="mt-1.5 w-full {{ $fc }}">
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Message</label>
            <textarea name="body" rows="6" required class="mt-1.5 w-full {{ $fc }}"></textarea>
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-200">
            <input type="checkbox" name="send_email" value="1" checked class="size-4 rounded border-gray-300 accent-[#C9A84C] dark:border-slate-500 dark:bg-slate-800">
            Send email to client
        </label>
        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Send</button>
    </form>
@endsection
