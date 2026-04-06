@extends('layouts.mis')

@section('title', 'Edit offering type')
@section('heading', 'Edit offering type')

@section('content')
    <form method="post" action="{{ route('mis.offering-types.update', $type) }}" class="max-w-lg space-y-3 text-sm">
        @csrf @method('patch')
        @include('mis.offering-types._form', ['type' => $type])
        <button class="rounded-lg bg-verlox-accent px-4 py-2 font-semibold text-on-verlox-accent">Save</button>
    </form>
@endsection

