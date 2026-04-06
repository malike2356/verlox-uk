@extends('layouts.mis')

@section('title', $va_client_account->company_name)
@section('heading', 'Edit: '.$va_client_account->company_name)

@section('content')
    <form method="post" action="{{ route('mis.va.client-accounts.update', $va_client_account) }}" class="space-y-6">
        @csrf @method('patch')
        @include('mis.va.client-accounts._form', ['va_client_account' => $va_client_account])
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Save</button>
            <a href="{{ route('mis.va.client-accounts.show', $va_client_account) }}" class="rounded-xl border border-gray-200 px-4 py-3 text-sm dark:border-slate-700">View</a>
        </div>
    </form>

    <form method="post" action="{{ route('mis.va.client-accounts.destroy', $va_client_account) }}" class="mt-10 border-t border-gray-200 pt-6 dark:border-slate-800" onsubmit="return confirm('Delete this VA client account and related engagements?');">
        @csrf @method('delete')
        <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm text-red-700 dark:border-red-800 dark:text-red-300">Delete account</button>
    </form>
@endsection
