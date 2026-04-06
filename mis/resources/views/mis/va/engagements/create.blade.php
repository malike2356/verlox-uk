@extends('layouts.mis')

@section('title', 'New engagement')
@section('heading', 'New engagement: '.$va_client_account->company_name)

@section('content')
    @if($assistants->isEmpty())
        <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">Add at least one active assistant before creating an engagement.</p>
        <a href="{{ route('mis.va.assistants.create') }}" class="text-verlox-accent text-sm font-medium">New assistant</a>
    @else
    <form method="post" action="{{ route('mis.va.client-accounts.engagements.store', $va_client_account) }}" class="max-w-xl space-y-3">
        @csrf
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Assistant</label>
            <select name="va_assistant_id" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach ($assistants as $a)
                    <option value="{{ $a->id }}">{{ $a->full_name }} ({{ $a->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Tier</label>
                <select name="tier" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                    @foreach (\App\Models\VaClientAccount::$tiers as $t)
                        <option value="{{ $t }}" @selected(old('tier', $va_client_account->tier) === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Hours per month</label>
                <input type="number" min="1" max="1000" name="hours_per_month" value="{{ old('hours_per_month', $va_client_account->hours_included) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Client rate (£/month)</label>
                <input type="number" step="0.01" min="0" name="client_rate_monthly_gbp" value="{{ old('client_rate_monthly_gbp', $va_client_account->monthly_rate_gbp) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">VA cost (£/hour)</label>
                <input type="number" step="0.01" min="0" name="va_hourly_rate_gbp" value="{{ old('va_hourly_rate_gbp') }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Start date</label>
                <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">End date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach (\App\Models\VaEngagement::$statuses as $s)
                    <option value="{{ $s }}" @selected(old('status', 'draft') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Notes</label>
            <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Create engagement</button>
        <a href="{{ route('mis.va.client-accounts.show', $va_client_account) }}" class="ml-3 text-sm text-verlox-accent">Cancel</a>
    </form>
    @endif
@endsection
