@extends('layouts.mis')

@section('title', 'New VA assistant')
@section('heading', 'New VA assistant')

@section('content')
    <form method="post" action="{{ route('mis.va.assistants.store') }}" class="space-y-6">
        @csrf
        @include('mis.va.assistants._form', ['assistant' => null])
        <button type="submit" class="rounded-xl bg-verlox-accent px-6 py-3 text-sm font-semibold text-on-verlox-accent">Create assistant</button>
        <a href="{{ route('mis.va.assistants.index') }}" class="ml-3 text-sm text-verlox-accent">Cancel</a>
    </form>
@endsection
