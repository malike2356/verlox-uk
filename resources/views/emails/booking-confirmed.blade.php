@extends('emails.layouts.velox')

@section('content')
  <p style="margin:0 0 12px;">Hello {{ $booking->contact_name }},</p>
  <p style="margin:0 0 12px;">Your appointment is confirmed.</p>
  <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;margin:16px 0;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
    <tr>
      <td style="padding:16px;">
        <p style="margin:0 0 6px;font-size:13px;color:#64748b;">Time</p>
        <p style="margin:0;font-weight:600;">{{ $booking->starts_at->timezone($booking->timezone)->format('l j F Y') }}</p>
        <p style="margin:4px 0 0;font-weight:600;">{{ $booking->starts_at->timezone($booking->timezone)->format('H:i') }} - {{ $booking->ends_at->timezone($booking->timezone)->format('H:i') }}</p>
      </td>
    </tr>
  </table>
  @if($booking->meeting_url)
    <p style="margin:16px 0 8px;">Join link:</p>
    <p style="margin:0;"><a href="{{ $booking->meeting_url }}" style="color:{{ $primary }};word-break:break-all;">{{ $booking->meeting_url }}</a></p>
  @endif
  @if(!empty($manageUrl))
    <p style="margin:20px 0 8px;">Manage this booking:</p>
    <p style="margin:0;"><a href="{{ $manageUrl }}" style="color:{{ $primary }};word-break:break-all;">Reschedule or cancel</a></p>
  @endif
  <p style="margin:20px 0 0;font-size:13px;color:#64748b;">Add to your calendar using the ICS file attached to this message, if your mail client supports it.</p>
@endsection
