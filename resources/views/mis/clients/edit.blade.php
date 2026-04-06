@extends('layouts.mis')

@section('title', 'Edit client')
@section('heading', 'Edit client')

@section('content')
    @php($fc = 'mt-1 w-full rounded-lg border px-3 py-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark]')
    <form method="post" action="{{ route('mis.clients.update', $client) }}" class="max-w-lg space-y-3 text-sm">
        @csrf @method('patch')
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Contact name</label>
            <input name="contact_name" value="{{ $client->contact_name }}" required class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Email</label>
            <input type="email" name="email" value="{{ $client->email }}" required class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Company</label>
            <input name="company_name" value="{{ $client->company_name }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Phone</label>
            <input name="phone" value="{{ $client->phone }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Address</label>
            <textarea name="address" rows="3" class="{{ $fc }}">{{ $client->address }}</textarea>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Zoho contact id</label>
            <input name="zoho_contact_id" value="{{ $client->zoho_contact_id }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Notes</label>
            <textarea name="notes" rows="3" class="{{ $fc }}">{{ $client->notes }}</textarea>
        </div>
        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection
