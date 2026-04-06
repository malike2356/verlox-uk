@extends('layouts.mis')

@section('title', 'New quotation')
@section('heading', 'New quotation')

@section('content')
    <form method="post" action="{{ route('mis.quotations.store') }}" class="max-w-lg space-y-3 text-sm" id="quotation-create-form">
        @csrf
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Client</label>
            <select name="client_id" id="quote-client-id" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->contact_name }} ({{ $c->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Linked lead (optional)</label>
            <select name="lead_id" id="quote-lead-id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                <option value="">None</option>
                @foreach ($leadsForLink as $l)
                    <option value="{{ $l->id }}" data-client-id="{{ $l->client?->id }}">{{ $l->contact_name }} ({{ $l->email }})</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-slate-500">Only leads already tied to the selected client are listed.</p>
        </div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Valid until</label><input type="date" name="valid_until" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></div>
        <div><label class="text-xs text-gray-500 dark:text-slate-500">Terms</label><textarea name="terms" rows="4" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white"></textarea></div>
        @if ($errors->any())
            <div class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</div>
        @endif
        <button class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Create</button>
    </form>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var clientSel = document.getElementById('quote-client-id');
        var leadSel = document.getElementById('quote-lead-id');
        if (!clientSel || !leadSel) return;
        function syncLeads() {
            var cid = clientSel.value;
            var keep = '';
            Array.prototype.forEach.call(leadSel.options, function (opt) {
                if (!opt.value) return;
                var match = opt.getAttribute('data-client-id') === cid;
                opt.hidden = !match;
                if (!match && opt.selected) {
                    opt.selected = false;
                    leadSel.querySelector('option[value=""]').selected = true;
                }
            });
        }
        clientSel.addEventListener('change', syncLeads);
        syncLeads();
    });
    </script>
    @endpush
@endsection
