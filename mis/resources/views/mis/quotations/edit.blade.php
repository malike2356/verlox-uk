@extends('layouts.mis')

@section('title', 'Edit '.$quotation->number)
@section('heading', 'Edit '.$quotation->number)

@section('content')
    <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">
        Client: <a href="{{ route('mis.clients.show', $quotation->client) }}" class="text-verlox-accent">{{ $quotation->client->contact_name }}</a>
        · <a href="{{ route('mis.quotations.show', $quotation) }}" class="text-verlox-accent">Back to quotation</a>
    </p>

    <form method="post" action="{{ route('mis.quotations.update', $quotation) }}" class="max-w-xl space-y-4 text-sm">
        @csrf @method('patch')
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Valid until</label>
            <input type="date" name="valid_until" value="{{ old('valid_until', $quotation->valid_until?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-gray-900 dark:text-slate-100">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Linked lead (optional)</label>
            <select name="lead_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-gray-900 dark:text-slate-100">
                <option value="">None</option>
                @foreach ($leadsForLink as $l)
                    <option value="{{ $l->id }}" @selected(old('lead_id', $quotation->lead_id) == $l->id)>{{ $l->contact_name }} ({{ $l->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Terms</label>
            <textarea name="terms" rows="8" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-gray-900 dark:text-slate-100">{{ old('terms', $quotation->terms) }}</textarea>
        </div>
        @if ($errors->any())
            <div class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</div>
        @endif
        <button type="submit" class="rounded-xl bg-verlox-accent px-5 py-2 text-sm font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection
