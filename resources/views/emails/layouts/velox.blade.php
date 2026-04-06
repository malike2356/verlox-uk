<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;background:#f1f5f9;font-family:system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;color:#0f172a;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" style="max-width:560px;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
            <tr>
            <td style="padding:20px 24px;border-bottom:1px solid #e2e8f0;background:linear-gradient(135deg,{{ $primary }}22,{{ $primary }}08);">
              @if(!empty($logoUrl))
                <p style="margin:0 0 12px;"><img src="{{ $logoUrl }}" alt="" width="160" style="max-width:160px;height:auto;display:block;"></p>
              @endif
              <p style="margin:0;font-size:11px;letter-spacing:0.12em;text-transform:uppercase;color:#64748b;">{{ $companyName }}</p>
              <p style="margin:8px 0 0;font-size:18px;font-weight:600;color:#0f172a;">{{ $headline }}</p>
            </td>
          </tr>
          <tr>
            <td style="padding:24px;font-size:15px;line-height:1.6;color:#334155;">
              @yield('content')
            </td>
          </tr>
          <tr>
            <td style="padding:16px 24px 24px;font-size:12px;line-height:1.5;color:#94a3b8;border-top:1px solid #f1f5f9;">
              @if($registrationNumber)Company no. {{ $registrationNumber }}@endif
              @if($vatNumber)@if($registrationNumber) &middot; @endif VAT {{ $vatNumber }}@endif
              @if($address)<br>{{ $address }}@endif
              @if($supportEmail)<br><a href="mailto:{{ $supportEmail }}" style="color:{{ $primary }};">{{ $supportEmail }}</a>@endif
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
