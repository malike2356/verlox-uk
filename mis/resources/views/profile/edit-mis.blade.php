@extends('layouts.mis')

@section('title', __('Profile'))
@section('heading', __('Your profile'))

@section('content')
    @include('profile.stack')
@endsection
