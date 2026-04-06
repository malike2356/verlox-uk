@extends('layouts.mis')

@section('title', 'Zoho')
@section('heading', 'Zoho Books integration')

@section('content')
    <p class="mb-4 text-xs">
        <a href="{{ route('mis.finance.dashboard') }}" class="font-medium text-verlox-accent hover:underline">{{ __('Finance dashboard') }}</a>
        <span class="text-gray-400 dark:text-slate-600">·</span>
        <a href="{{ route('mis.invoices.index') }}" class="font-medium text-verlox-accent hover:underline">{{ __('Invoices') }}</a>
        <span class="text-gray-400 dark:text-slate-600">·</span>
        <a href="{{ route('mis.finance.expenses.index') }}" class="font-medium text-verlox-accent hover:underline">{{ __('Expenses') }}</a>
        <span class="text-gray-400 dark:text-slate-600">·</span>
        <a href="{{ route('mis.settings.edit') }}#zoho-books" class="font-medium text-verlox-accent hover:underline">{{ __('Company settings (Zoho)') }}</a>
    </p>
    <p class="text-sm text-gray-600 dark:text-slate-400 mb-4 max-w-2xl">Credentials and <strong>auto-sync toggles</strong> live in Company settings → Zoho Books. When enabled, MIS pushes <strong>invoices</strong> after catalogue checkout, quotation-based invoice creation, Stripe checkout send, and successful payment; and can push <strong>expenses</strong> whenever they are saved. You can still use “Sync to Zoho” on individual invoices or expenses. This page tests connectivity and lists recent sync log entries.</p>
    <form method="post" action="{{ route('mis.zoho.test') }}" class="mb-8">@csrf
        <button type="submit" class="rounded-lg bg-verlox-accent px-4 py-2 text-sm font-semibold text-on-verlox-accent">Test connection</button>
    </form>
    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Recent logs</h2>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-800 text-xs font-mono">
        <table class="min-w-full">
            <thead class="bg-gray-100 dark:bg-slate-900 text-gray-500 dark:text-slate-500 text-left">
            <tr><th class="px-2 py-1">When</th><th class="px-2 py-1">Dir</th><th class="px-2 py-1">Entity</th><th class="px-2 py-1">Status</th><th class="px-2 py-1">Message</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-gray-700 dark:text-slate-300">
            @forelse ($logs as $log)
                <tr>
                    <td class="px-2 py-1">{{ $log->created_at }}</td>
                    <td class="px-2 py-1">{{ $log->direction }}</td>
                    <td class="px-2 py-1">{{ $log->entity_type }}</td>
                    <td class="px-2 py-1">{{ $log->status }}</td>
                    <td class="px-2 py-1 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($log->message, 120) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-2 py-2 text-gray-500 dark:text-slate-500">No log entries yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
