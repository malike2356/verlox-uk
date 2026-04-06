@extends('layouts.mis')

@section('title', 'Lead')
@section('heading', 'Lead: '.$lead->contact_name)

@section('content')
    <div class="flex flex-wrap gap-3 mb-6">
        @if($lead->status !== 'converted')
            <form method="post" action="{{ route('mis.leads.convert', $lead) }}">@csrf
                <button type="submit" class="rounded-xl bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">Convert to client</button>
            </form>
        @elseif($lead->client)
            <a href="{{ route('mis.clients.show', $lead->client) }}" class="rounded-xl border border-gray-300 dark:border-slate-600 px-4 py-2 text-sm">View client</a>
        @endif
        <a href="{{ route('mis.leads.index') }}" class="rounded-xl border border-gray-300 dark:border-slate-600 px-4 py-2 text-sm">All leads</a>
        @if($lead->status !== 'converted' && !$lead->client && $lead->bookings->isEmpty())
            <form method="post" action="{{ route('mis.leads.destroy', $lead) }}" class="inline" onsubmit="return confirm('Delete this lead permanently?');">@csrf @method('delete')
                <button type="submit" class="rounded-xl border border-red-500/50 text-red-400 px-4 py-2 text-sm">Delete lead</button>
            </form>
        @endif
    </div>

    @php($canEditContact = $lead->status !== 'converted')

    <form method="post" action="{{ route('mis.leads.update', $lead) }}" class="grid gap-6 lg:grid-cols-2 text-sm">
        @csrf
        @method('patch')

        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Contact</h2>

            @if($canEditContact)
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Contact name</label>
                    <input name="contact_name" required value="{{ old('contact_name', $lead->contact_name) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Email</label>
                    <input type="email" name="email" required value="{{ old('email', $lead->email) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Phone</label>
                    <input name="phone" value="{{ old('phone', $lead->phone) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Company</label>
                    <input name="company_name" value="{{ old('company_name', $lead->company_name) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Source</label>
                    <input name="source" value="{{ old('source', $lead->source) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Notes</label>
                    <textarea name="message" rows="4" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">{{ old('message', $lead->message) }}</textarea>
                </div>
            @else
                <p><span class="text-gray-500 dark:text-slate-500">Email:</span> <span class="text-gray-900 dark:text-white">{{ $lead->email }}</span></p>
                @if($lead->phone)<p><span class="text-gray-500 dark:text-slate-500">Phone:</span> <span class="text-gray-900 dark:text-white">{{ $lead->phone }}</span></p>@endif
                @if($lead->company_name)<p><span class="text-gray-500 dark:text-slate-500">Company:</span> <span class="text-gray-900 dark:text-white">{{ $lead->company_name }}</span></p>@endif
                @if($lead->source)<p><span class="text-gray-500 dark:text-slate-500">Source:</span> <span class="text-gray-900 dark:text-white">{{ $lead->source }}</span></p>@endif
                @if($lead->message)<p class="text-gray-700 dark:text-slate-300 whitespace-pre-wrap pt-2">{{ $lead->message }}</p>@endif
            @endif

            @if($lead->utm_source || $lead->utm_medium || $lead->utm_campaign)
                <div class="pt-2 border-t border-gray-200 dark:border-slate-800 text-xs text-gray-600 dark:text-slate-400 space-y-0.5">
                    <p class="font-semibold text-gray-700 dark:text-slate-300">Attribution</p>
                    @if($lead->utm_source)<p>utm_source: {{ $lead->utm_source }}</p>@endif
                    @if($lead->utm_medium)<p>utm_medium: {{ $lead->utm_medium }}</p>@endif
                    @if($lead->utm_campaign)<p>utm_campaign: {{ $lead->utm_campaign }}</p>@endif
                    @if($lead->utm_term)<p>utm_term: {{ $lead->utm_term }}</p>@endif
                    @if($lead->utm_content)<p>utm_content: {{ $lead->utm_content }}</p>@endif
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4 space-y-3">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Pipeline</h2>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Stage</label>
                <select name="pipeline_stage_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                    @foreach ($stages as $s)
                        <option value="{{ $s->id }}" @selected(old('pipeline_stage_id', $lead->pipeline_stage_id) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Offering</label>
                <select name="offering_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                    <option value="">None</option>
                    @foreach ($offerings as $o)
                        <option value="{{ $o->id }}" @selected(old('offering_id', $lead->offering_id) == $o->id)>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Assigned</label>
                <select name="assigned_user_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                    <option value="">Unassigned</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected(old('assigned_user_id', $lead->assigned_user_id) == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Status</label>
                <select name="status" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                    @foreach (\App\Models\Lead::STATUSES as $st)
                        <option value="{{ $st }}" @selected(old('status', $lead->status) === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Deal value (GBP)</label>
                    <input type="number" step="0.01" min="0" name="deal_value_gbp" value="{{ old('deal_value_gbp', $lead->deal_value_pence !== null ? number_format($lead->deal_value_pence / 100, 2, '.', '') : '') }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Expected close</label>
                    <input type="date" name="expected_close_date" value="{{ old('expected_close_date', $lead->expected_close_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Loss reason (if status is lost)</label>
                <textarea name="loss_reason" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">{{ old('loss_reason', $lead->loss_reason) }}</textarea>
            </div>
            @if ($errors->any())
                <div class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 dark:bg-slate-800 dark:text-white">Save lead</button>
        </div>
    </form>

    <div class="grid gap-6 lg:grid-cols-2 mt-8">
        <section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Activity timeline</h2>
            <ul class="space-y-3 text-sm border-l-2 border-gray-200 dark:border-slate-700 pl-4 ml-1">
                @forelse ($lead->activities as $a)
                    <li class="relative">
                        <span class="absolute -left-[1.15rem] top-1.5 h-2 w-2 rounded-full bg-verlox-accent"></span>
                        <p class="text-xs text-gray-500 dark:text-slate-500">
                            {{ $a->created_at->format('Y-m-d H:i') }}
                            · <span class="uppercase tracking-wide">{{ $a->type }}</span>
                            @if($a->user) · {{ $a->user->name }} @endif
                        </p>
                        <p class="text-gray-800 dark:text-slate-200 whitespace-pre-wrap mt-0.5">{{ $a->body }}</p>
                    </li>
                @empty
                    <li class="text-gray-500 dark:text-slate-500">No activities yet.</li>
                @endforelse
            </ul>
            <form method="post" action="{{ route('mis.leads.activities.store', $lead) }}" class="mt-4 space-y-2 border-t border-gray-200 dark:border-slate-800 pt-4">
                @csrf
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Type</label>
                    <select name="type" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <option value="note">Note</option>
                        <option value="call">Call</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-slate-500">Details</label>
                    <textarea name="body" required rows="3" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-sm text-gray-900 dark:text-white"></textarea>
                </div>
                <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">Log activity</button>
            </form>
        </section>

        <section class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Bookings</h2>
            <ul class="text-sm space-y-2">
                @forelse ($lead->bookings as $b)
                    <li class="flex flex-wrap justify-between gap-2 border-b border-gray-100 dark:border-slate-800 pb-2">
                        <a href="{{ route('mis.bookings.show', $b) }}" class="text-verlox-accent">{{ $b->contact_name }}</a>
                        <span class="text-xs font-mono text-gray-500 dark:text-slate-500">{{ $b->starts_at->timezone($b->timezone)->format('D j M H:i') }}</span>
                    </li>
                @empty
                    <li class="text-gray-500 dark:text-slate-500">No bookings linked.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
