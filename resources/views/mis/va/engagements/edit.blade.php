@extends('layouts.mis')

@section('title', 'Engagement #'.$va_engagement->id)
@section('heading', 'Edit engagement #'.$va_engagement->id)

@section('content')
    <p class="mb-4 text-sm text-gray-600 dark:text-slate-400">
        Client: <a href="{{ route('mis.va.client-accounts.show', $va_engagement->clientAccount) }}" class="text-verlox-accent">{{ $va_engagement->clientAccount->company_name }}</a>
    </p>
    <form method="post" action="{{ route('mis.va.engagements.update', $va_engagement) }}" class="max-w-xl space-y-3">
        @csrf @method('patch')
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Assistant</label>
            <select name="va_assistant_id" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach ($assistants as $a)
                    <option value="{{ $a->id }}" @selected(old('va_assistant_id', $va_engagement->va_assistant_id) == $a->id)>{{ $a->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Tier</label>
                <select name="tier" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                    @foreach (\App\Models\VaClientAccount::$tiers as $t)
                        <option value="{{ $t }}" @selected(old('tier', $va_engagement->tier) === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Hours per month</label>
                <input type="number" min="1" max="1000" name="hours_per_month" value="{{ old('hours_per_month', $va_engagement->hours_per_month) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Client rate (£/month)</label>
                <input type="number" step="0.01" min="0" name="client_rate_monthly_gbp" value="{{ old('client_rate_monthly_gbp', $va_engagement->client_rate_monthly_gbp) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">VA cost (£/hour)</label>
                <input type="number" step="0.01" min="0" name="va_hourly_rate_gbp" value="{{ old('va_hourly_rate_gbp', $va_engagement->va_hourly_rate_gbp) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Start date</label>
                <input type="date" name="start_date" value="{{ old('start_date', $va_engagement->start_date->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">End date</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($va_engagement->end_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach (\App\Models\VaEngagement::$statuses as $s)
                    <option value="{{ $s }}" @selected(old('status', $va_engagement->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Notes</label>
            <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">{{ old('notes', $va_engagement->notes) }}</textarea>
        </div>
        <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Save</button>
        <a href="{{ route('mis.va.client-accounts.show', $va_engagement->clientAccount) }}" class="ml-3 text-sm text-verlox-accent">Back to client</a>
    </form>
    @if($va_engagement->time_logs_count === 0)
        <form method="post" action="{{ route('mis.va.engagements.destroy', $va_engagement) }}" class="mt-6 max-w-xl" onsubmit="return confirm('Remove this engagement?');">@csrf @method('delete')
            <button type="submit" class="text-sm text-red-400">Delete engagement (no time logged)</button>
        </form>
    @endif
@endsection
