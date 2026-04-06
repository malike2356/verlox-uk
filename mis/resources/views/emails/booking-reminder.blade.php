<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Booking reminder</title></head>
<body style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;background:#f8f9fc;margin:0;padding:40px 0;">
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);">
  <tr><td style="background:#0b1829;padding:28px 36px;">
    @if(!empty($logoUrl))
      <p style="margin:0 0 16px;"><img src="{{ $logoUrl }}" alt="" width="140" style="max-width:140px;height:auto;display:block;"></p>
    @endif
    <p style="margin:0;font-size:20px;font-weight:700;color:#fff;">{{ $booking->eventType?->name ?? ('Call with '.$companyName) }}</p>
  </td></tr>
  <tr><td style="padding:32px 36px;">
    <p style="margin:0 0 6px;font-size:13px;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">
      @if($window === '1h') Your call is in 1 hour @else Your call is tomorrow @endif
    </p>
    <p style="margin:0 0 24px;font-size:22px;font-weight:700;color:#0b1829;">
      {{ $booking->starts_at->setTimezone($booking->timezone ?: 'Europe/London')->format('l, d F Y') }}<br>
      <span style="font-size:18px;font-weight:400;">
        {{ $booking->starts_at->setTimezone($booking->timezone ?: 'Europe/London')->format('H:i') }}
        – {{ $booking->ends_at->setTimezone($booking->timezone ?: 'Europe/London')->format('H:i') }}
        ({{ $booking->timezone ?: 'Europe/London' }})
      </span>
    </p>

    @if($booking->meeting_url)
    <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
      <tr><td style="background:{{ $accentHex }};border-radius:8px;">
        <a href="{{ $booking->meeting_url }}" style="display:inline-block;padding:12px 24px;color:#ffffff;font-weight:600;font-size:14px;text-decoration:none;">
          Join meeting
        </a>
      </td></tr>
    </table>
    @endif

    <p style="margin:0 0 24px;font-size:14px;color:#374151;">
      Hi {{ $booking->contact_name }}, this is a reminder for your upcoming call.
    </p>

    <table cellpadding="0" cellspacing="0">
      <tr><td style="background:#f3f4f6;border-radius:8px;padding:12px 20px;">
        <a href="{{ route('public.booking.manage', ['booking' => $booking->id, 'token' => $booking->manage_token]) }}"
           style="font-size:13px;color:{{ $accentHex }};text-decoration:none;">
          Manage or reschedule booking
        </a>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="padding:16px 36px 28px;border-top:1px solid #f0f0f0;">
    <p style="margin:0;font-size:11px;color:#9ca3af;">
      {{ \App\Models\CompanySetting::current()->company_name }} &mdash; {{ \App\Models\CompanySetting::current()->address_line1 }}, {{ \App\Models\CompanySetting::current()->city }}, {{ \App\Models\CompanySetting::current()->postcode }}
    </p>
  </td></tr>
</table>
</td></tr></table>
</body>
</html>
