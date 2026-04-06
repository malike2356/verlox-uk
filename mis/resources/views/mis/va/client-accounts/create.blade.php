@extends('layouts.mis')

@section('title', 'New VA client')
@section('heading', 'New VA client account')

@section('content')
    <form method="post" action="{{ route('mis.va.client-accounts.store') }}" class="space-y-6">
        @csrf
        @include('mis.va.client-accounts._form', ['va_client_account' => new \App\Models\VaClientAccount])
        <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Create</button>
        <a href="{{ route('mis.va.client-accounts.index') }}" class="ml-3 text-sm text-verlox-accent">Cancel</a>
    </form>
@endsection
