@extends('layouts.mis')

@section('title', 'New lead')
@section('heading', 'New lead')

@section('content')
    @php($fc = 'mt-1 w-full rounded-lg border px-3 py-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-verlox-accent focus:outline-none dark:border-slate-600 dark:!bg-slate-800 dark:!text-slate-100 dark:[color-scheme:dark]')
    <form method="post" action="{{ route('mis.leads.store') }}" class="max-w-xl space-y-4 text-sm">
        @csrf
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Stage</label>
            <select name="pipeline_stage_id" required class="{{ $fc }}">
                @foreach ($stages as $s)
                    <option value="{{ $s->id }}" @selected(old('pipeline_stage_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Offering (optional)</label>
            <select name="offering_id" class="{{ $fc }}">
                <option value="">None</option>
                @foreach ($offerings as $o)
                    <option value="{{ $o->id }}" @selected(old('offering_id') == $o->id)>{{ $o->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Assigned (optional)</label>
            <select name="assigned_user_id" class="{{ $fc }}">
                <option value="">Unassigned</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected(old('assigned_user_id') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Company (optional)</label>
            <input name="company_name" value="{{ old('company_name') }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Contact name</label>
            <input name="contact_name" required value="{{ old('contact_name') }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Email</label>
            <input type="email" name="email" required value="{{ old('email') }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Phone (optional)</label>
            <input name="phone" value="{{ old('phone') }}" class="{{ $fc }}">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Source (optional)</label>
            <input name="source" value="{{ old('source', 'manual') }}" placeholder="manual, referral, …" class="{{ $fc }} placeholder:text-slate-400 dark:placeholder:text-slate-500">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Notes (optional)</label>
            <textarea name="message" rows="4" class="{{ $fc }}">{{ old('message') }}</textarea>
        </div>
        @if ($errors->any())
            <div class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</div>
        @endif
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="rounded-xl bg-verlox-accent px-5 py-2 text-sm font-semibold text-on-verlox-accent">Create lead</button>
            <a href="{{ route('mis.leads.index') }}" class="rounded-xl border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm">Cancel</a>
        </div>
    </form>
@endsection
