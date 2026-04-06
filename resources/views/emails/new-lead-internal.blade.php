@extends('emails.layouts.velox')

@section('content')
  <p style="margin:0 0 12px;"><strong>{{ $lead->contact_name }}</strong>@if($lead->company_name) ({{ $lead->company_name }})@endif</p>
  <p style="margin:0 0 8px;font-size:13px;color:#64748b;">{{ $lead->email }}@if($lead->phone) &middot; {{ $lead->phone }}@endif</p>
  <p style="margin:0 0 8px;font-size:13px;color:#64748b;">Source: {{ $lead->source }} &middot; Stage: {{ $lead->pipelineStage->name ?? '' }}</p>
  @if($lead->message)
    <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
      {!! nl2br(e($lead->message)) !!}
    </div>
  @endif
  <p style="margin:20px 0 0;"><a href="{{ url('/mis/leads/'.$lead->id) }}" style="display:inline-block;background:{{ $primary }};color:#ffffff !important;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:600;">Open in MIS</a></p>
@endsection
