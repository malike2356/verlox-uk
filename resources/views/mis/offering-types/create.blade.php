@extends('layouts.mis')

@section('title', 'New offering type')
@section('heading', 'New offering type')

@section('content')
    <form method="post" action="{{ route('mis.offering-types.store') }}" class="max-w-lg space-y-3 text-sm">
        @csrf
        @include('mis.offering-types._form', ['type' => null])
        <button class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection

