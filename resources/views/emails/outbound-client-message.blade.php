@extends('emails.layouts.velox')

@section('content')
  <p style="margin:0 0 12px;">Hello {{ $client->contact_name }},</p>
  <div style="margin:0;padding:0;">
    {!! $bodyHtml !!}
  </div>
@endsection
