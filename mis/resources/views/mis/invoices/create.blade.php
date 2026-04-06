@extends('layouts.mis')

@section('title', 'New invoice')
@section('heading', 'New invoice')

@section('content')
    <p class="mb-4 text-sm text-gray-600 dark:text-slate-400">
        Creates a <strong>draft</strong> invoice with line items. You can change status, sync to Zoho, or send a Stripe link from the invoice page.
        To bill from a quote, open the accepted quotation and use <strong>Create invoice</strong> there instead.
    </p>

    <form method="post" action="{{ route('mis.invoices.store') }}" class="max-w-3xl space-y-4 text-sm" id="invoice-create-form">
        @csrf
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Client</label>
            <select name="client_id" id="inv-client-id" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}" @selected((string) old('client_id') === (string) $c->id)>{{ $c->contact_name }} ({{ $c->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Linked lead (optional)</label>
            <select name="lead_id" id="inv-lead-id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                <option value="">None</option>
                @foreach ($leadsForLink as $l)
                    <option value="{{ $l->id }}" data-client-id="{{ $l->client?->id }}" @selected((string) old('lead_id') === (string) $l->id)>{{ $l->contact_name }} ({{ $l->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Catalogue offering (optional)</label>
            <select name="offering_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                <option value="">None</option>
                @foreach ($offerings as $o)
                    <option value="{{ $o->id }}" @selected((string) old('offering_id') === (string) $o->id)>{{ $o->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Issued</label>
                <input type="date" name="issued_at" value="{{ old('issued_at', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Due</label>
                <input type="date" name="due_at" value="{{ old('due_at', now()->addDays(14)->toDateString()) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-slate-500">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', 'GBP') }}" maxlength="3" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 font-mono uppercase text-gray-900 dark:text-white">
            </div>
        </div>

        <div>
            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Line items</span>
                <button type="button" id="inv-add-line" class="rounded-lg border border-gray-300 px-3 py-1 text-xs text-gray-700 dark:border-slate-600 dark:text-slate-300">Add line</button>
            </div>
            <p class="mb-2 text-xs text-gray-500 dark:text-slate-500">Unit price in pounds (e.g. 120.00). VAT at 20% is calculated on the subtotal after save.</p>
            <div id="inv-lines" class="space-y-2"></div>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc ps-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Create draft invoice</button>
    </form>

    <template id="inv-line-template">
        <div class="inv-line-row grid gap-2 rounded-lg border border-gray-200 dark:border-slate-800 p-3 sm:grid-cols-12">
            <div class="sm:col-span-5">
                <label class="text-[10px] uppercase text-gray-500 dark:text-slate-500">Description</label>
                <input type="text" data-field="description" class="mt-0.5 w-full rounded border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950 px-2 py-1.5 text-gray-900 dark:text-white">
            </div>
            <div class="sm:col-span-2">
                <label class="text-[10px] uppercase text-gray-500 dark:text-slate-500">Qty</label>
                <input type="number" data-field="quantity" step="0.01" min="0.01" value="1" class="mt-0.5 w-full rounded border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950 px-2 py-1.5 text-gray-900 dark:text-white">
            </div>
            <div class="sm:col-span-3">
                <label class="text-[10px] uppercase text-gray-500 dark:text-slate-500">Unit (£)</label>
                <input type="number" data-field="unit_price" step="0.01" min="0" placeholder="0.00" class="mt-0.5 w-full rounded border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950 px-2 py-1.5 font-mono text-gray-900 dark:text-white">
            </div>
            <div class="flex items-end sm:col-span-2">
                <button type="button" class="inv-remove-line w-full rounded border border-red-500/40 py-1.5 text-xs text-red-600 dark:text-red-400">Remove</button>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('inv-lines');
        var tpl = document.getElementById('inv-line-template');
        var addBtn = document.getElementById('inv-add-line');
        var form = document.getElementById('invoice-create-form');
        var clientSel = document.getElementById('inv-client-id');
        var leadSel = document.getElementById('inv-lead-id');
        if (!container || !tpl || !addBtn || !form) return;

        function reindexLines() {
            var rows = container.querySelectorAll('.inv-line-row');
            rows.forEach(function (row, i) {
                row.querySelectorAll('[data-field]').forEach(function (input) {
                    var field = input.getAttribute('data-field');
                    input.name = 'lines[' + i + '][' + field + ']';
                });
            });
        }

        function addLine() {
            var node = tpl.content.cloneNode(true);
            var row = node.querySelector('.inv-line-row');
            container.appendChild(row);
            reindexLines();
        }

        addBtn.addEventListener('click', function () {
            addLine();
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('inv-remove-line')) {
                var row = e.target.closest('.inv-line-row');
                if (row && container.querySelectorAll('.inv-line-row').length > 1) {
                    row.remove();
                    reindexLines();
                }
            }
        });

        form.addEventListener('submit', function () {
            reindexLines();
        });

        @if (old('lines'))
            @foreach (old('lines', []) as $i => $line)
                addLine();
            @endforeach
        @else
            addLine();
        @endif

        @if (old('lines'))
            @foreach (old('lines', []) as $i => $line)
                (function () {
                    var rows = container.querySelectorAll('.inv-line-row');
                    var r = rows[{{ $i }}];
                    if (!r) return;
                    var d = r.querySelector('[data-field="description"]');
                    var q = r.querySelector('[data-field="quantity"]');
                    var u = r.querySelector('[data-field="unit_price"]');
                    if (d) d.value = @json($line['description'] ?? '');
                    if (q) q.value = @json($line['quantity'] ?? '1');
                    if (u) u.value = @json($line['unit_price'] ?? '');
                })();
            @endforeach
        @endif

        if (clientSel && leadSel) {
            function syncLeads() {
                var cid = clientSel.value;
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
        }
    });
    </script>
    @endpush
@endsection
