@extends('layouts.mis')
@section('title', 'Expenses')
@section('heading', 'Expenses')

@section('content')
@php
    /* Strong dark tokens + ! bg/text so @tailwindcss/forms base (#fff) does not win; placeholders explicit */
    $field      = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-600 shadow-sm [color-scheme:light] dark:border-slate-500 dark:!bg-slate-950 dark:!text-slate-100 dark:placeholder:text-slate-400 dark:[color-scheme:dark] focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 dark:focus:border-[#C9A84C] dark:focus:ring-[#C9A84C]/20';
    $label      = 'block text-xs font-medium text-gray-700 dark:text-slate-300';
    $btnPrimary = 'inline-flex h-9 shrink-0 items-center justify-center rounded-lg bg-verlox-accent px-4 text-sm font-semibold text-on-verlox-accent hover:opacity-95';
    $btnRemove  = 'inline-flex h-7 items-center justify-center rounded-md border border-red-500/25 px-2.5 text-xs font-medium text-red-400 hover:border-red-500/40 hover:bg-red-500/10 dark:text-red-300';
    $zohoConfigured = app(\App\Services\ZohoBooksClient::class)->isConfigured();
@endphp

@include('mis.partials.zoho-accounting-strip')

{{-- ── Create form ──────────────────────────────────────────────────── --}}
<section class="mb-8 rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
    <div class="border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Record an expense</h2>
    </div>
    <div class="p-5">
        <form method="post" action="{{ route('mis.finance.expenses.store') }}"
              class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
            @csrf
            <div>
                <label class="{{ $label }}">Date</label>
                <input type="date" name="date" value="{{ now()->toDateString() }}" required class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Category</label>
                <select name="category" required class="{{ $field }}">
                    @foreach($categories as $key => $label_text)
                        <option value="{{ $key }}">{{ $label_text }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Vendor / supplier</label>
                <input type="text" name="vendor" placeholder="e.g. AWS, Notion" class="{{ $field }}">
            </div>
            <div class="col-span-2 sm:col-span-1 xl:col-span-2">
                <label class="{{ $label }}">Description</label>
                <input type="text" name="description" placeholder="Monthly hosting subscription" required class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Amount (£)</label>
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Reference</label>
                <input type="text" name="reference" placeholder="INV-001" class="{{ $field }}">
            </div>
            <div>
                <label class="{{ $label }}">Status</label>
                <select name="status" class="{{ $field }}">
                    <option value="paid">Paid</option>
                    <option value="draft">Draft / pending</option>
                </select>
            </div>
            <div class="col-span-2 flex items-end sm:col-span-1">
                <button type="submit" class="{{ $btnPrimary }} w-full">
                    <i class="fa-solid fa-plus mr-1.5"></i> Add expense
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ── Expense list ─────────────────────────────────────────────────── --}}
<section class="rounded-2xl border border-gray-200/80 bg-white/60 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/40">
    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-slate-700/80">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
            All expenses
            <span class="ml-1 text-xs font-normal text-gray-400 dark:text-slate-500">({{ $expenses->total() }})</span>
        </h2>
        <a href="{{ route('mis.finance.dashboard') }}"
           class="text-xs text-verlox-accent hover:underline">
            <i class="fa-solid fa-chart-line mr-1"></i> Finance dashboard
        </a>
    </div>

    @if($expenses->isEmpty())
        <p class="px-5 py-8 text-center text-sm text-gray-500 dark:text-slate-400">No expenses recorded yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-700/80">
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Date</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Category</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Description</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Vendor</th>
                        <th class="px-3 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Amount</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Status</th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @foreach($expenses as $exp)
                        <tr x-data="{ editing: false }" class="group hover:bg-gray-50/60 dark:hover:bg-slate-800/30">

                            {{-- View row --}}
                            <template x-if="!editing">
                                <td class="px-5 py-3 tabular-nums text-gray-700 dark:text-slate-300 whitespace-nowrap">
                                    {{ $exp->date->format('d M Y') }}
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-3">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        {{ $exp->category_label }}
                                    </span>
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="max-w-[260px] px-3 py-3">
                                    <span class="block truncate text-gray-900 dark:text-white">{{ $exp->description }}</span>
                                    @if($exp->reference)
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500">{{ $exp->reference }}</span>
                                    @endif
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-3 text-gray-600 dark:text-slate-400">{{ $exp->vendor ?? '-' }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-3 text-right font-semibold tabular-nums text-rose-500 dark:text-rose-400 whitespace-nowrap">
                                    −£{{ number_format($exp->amount_pence / 100, 2) }}
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-3">
                                    @if($exp->status === 'paid')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">Paid</span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">Draft</span>
                                    @endif
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Zoho sync --}}
                                        @if($zohoConfigured)
                                            <form method="post" action="{{ route('mis.finance.expenses.sync-zoho', $exp) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    title="{{ $exp->zoho_expense_id ? 'Re-sync to Zoho' : 'Sync to Zoho' }}"
                                                    class="text-xs {{ $exp->zoho_expense_id ? 'text-emerald-500' : 'text-gray-400 dark:text-slate-600' }} hover:text-emerald-500">
                                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button @click="editing = true"
                                            class="text-xs text-verlox-accent hover:underline">Edit</button>
                                        <form method="post" action="{{ route('mis.finance.expenses.destroy', $exp) }}" class="inline">
                                            @csrf @method('delete')
                                            <button type="submit" class="{{ $btnRemove }}"
                                                onclick="return confirm('Delete this expense?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </template>

                            {{-- Edit row (inline, spans full width via nested table approach) --}}
                            <template x-if="editing">
                                <td colspan="7" class="px-5 py-4 bg-gray-50/60 dark:bg-slate-800/30">
                                    <form method="post" action="{{ route('mis.finance.expenses.update', $exp) }}"
                                          class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7">
                                        @csrf @method('patch')
                                        <div>
                                            <label class="{{ $label }}">Date</label>
                                            <input type="date" name="date" value="{{ $exp->date->format('Y-m-d') }}" required class="{{ $field }}">
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Category</label>
                                            <select name="category" required class="{{ $field }}">
                                                @foreach($categories as $key => $label_text)
                                                    <option value="{{ $key }}" @selected($exp->category === $key)>{{ $label_text }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Vendor</label>
                                            <input type="text" name="vendor" value="{{ $exp->vendor }}" class="{{ $field }}">
                                        </div>
                                        <div class="col-span-2 sm:col-span-1 xl:col-span-2">
                                            <label class="{{ $label }}">Description</label>
                                            <input type="text" name="description" value="{{ $exp->description }}" required class="{{ $field }}">
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Amount (£)</label>
                                            <input type="number" name="amount" step="0.01" value="{{ number_format($exp->amount_pence / 100, 2, '.', '') }}" required class="{{ $field }}">
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Ref</label>
                                            <input type="text" name="reference" value="{{ $exp->reference }}" class="{{ $field }}">
                                        </div>
                                        <div>
                                            <label class="{{ $label }}">Status</label>
                                            <select name="status" class="{{ $field }}">
                                                <option value="paid" @selected($exp->status === 'paid')>Paid</option>
                                                <option value="draft" @selected($exp->status === 'draft')>Draft</option>
                                            </select>
                                        </div>
                                        <div class="col-span-2 flex items-end gap-2 sm:col-span-1">
                                            <button type="submit" class="{{ $btnPrimary }} flex-1">Save</button>
                                            <button type="button" @click="editing = false"
                                                class="inline-flex h-9 items-center justify-center rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </template>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="border-t border-gray-200 px-5 py-4 dark:border-slate-700/80">
                {{ $expenses->links() }}
            </div>
        @endif
    @endif
</section>
@endsection
