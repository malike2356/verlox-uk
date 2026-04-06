@extends('layouts.mis')

@section('title', 'Thread')
@section('heading', $conversation->subject ?? 'Conversation')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
        <p class="text-sm text-gray-600 dark:text-slate-300">{{ $conversation->client->contact_name }} &middot; {{ $conversation->client->email }}</p>
        <form method="post" action="{{ route('mis.conversations.destroy', $conversation) }}" onsubmit="return confirm('Delete this entire thread?');">@csrf @method('delete')
            <button type="submit" class="text-xs text-red-400">Delete thread</button>
        </form>
    </div>
    <div class="space-y-3 mb-6">
        @foreach ($conversation->messages as $m)
            <div class="rounded-xl border px-3 py-2 text-sm {{ $m->direction === 'outbound' ? 'border-indigo-200 dark:border-[#C9A84C]/40 bg-verlox-accent/5 ml-4' : 'border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 mr-4' }}">
                <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">{{ $m->direction }} @if($m->user) &middot; {{ $m->user->name }} @endif &middot; {{ $m->created_at->format('Y-m-d H:i') }}</p>
                <div class="whitespace-pre-wrap text-gray-800 dark:text-slate-200">{{ $m->body }}</div>
            </div>
        @endforeach
    </div>
    <form method="post" action="{{ route('mis.conversations.reply', $conversation) }}" class="max-w-xl space-y-4 text-sm">
        @csrf
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-slate-300">Reply</label>
            <textarea name="body" rows="4" required class="mt-1.5 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 placeholder:text-gray-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400" placeholder="Type your reply…"></textarea>
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-200">
            <input type="checkbox" name="send_email" value="1" checked class="size-4 rounded border-gray-300 accent-[#C9A84C] dark:border-slate-500 dark:bg-slate-800">
            Email client
        </label>
        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Send reply</button>
    </form>
@endsection
