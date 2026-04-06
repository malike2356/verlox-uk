@extends('layouts.mis')

@section('title', 'New client')
@section('heading', 'New client')

@section('content')
    @php($fc = 'mt-1 w-full rounded-lg border px-3 py-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark]')
    <form method="post" action="{{ route('mis.clients.store') }}" class="max-w-lg space-y-3 text-sm">
        @csrf
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Contact name</label>
            <input name="contact_name" required class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Email</label>
            <input type="email" name="email" required class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Company</label>
            <input name="company_name" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Phone</label>
            <input name="phone" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Address</label>
            <textarea name="address" rows="3" class="{{ $fc }}"></textarea>
        </div>
        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Create</button>
    </form>
@endsection
