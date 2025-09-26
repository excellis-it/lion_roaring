<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $subjectText ?? 'Newsletter' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f8f9fa; font-family: Arial, sans-serif; color:#333;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f8f9fa">
    <tr>
      <td align="center" style="padding:30px 15px;">
        <!-- Main Container -->
        <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05); overflow:hidden;">

          <!-- Logo -->
          <tr>
            <td align="center" style="background-color:#004080; padding:20px;">
              <img src="{{ asset('ecom_assets/images/estore_logo.png') }}" alt="{{ config('app.name') }}" width="150" style="display:block; max-width:150px; height:auto;">
            </td>
          </tr>

          <!-- Greeting -->
          <tr>
            <td style="padding:20px 30px; font-size:16px; line-height:24px; color:#333;">
              @if(!empty($recipientName))
                <p style="margin:0 0 15px;">Dear <strong>{{ $recipientName }}</strong>,</p>
              @endif

              <!-- CKEditor Body -->
              <div style="font-size:15px; line-height:1.6; color:#444;">
                {!! $bodyHtml !!}
              </div>
            </td>
          </tr>

          <!-- Divider -->
          <tr>
            <td align="center" style="padding:0 30px;">
              <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:20px 30px; font-size:14px; line-height:22px; color:#777; text-align:center;">
              <p style="margin:0;">Regards,<br><strong>{{ config('app.name') }}</strong></p>
              <p style="margin:10px 0 0; font-size:12px; color:#999;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
              </p>
              <p style="margin:5px 0 0; font-size:12px; color:#999;">
                If you no longer wish to receive these emails, you may <a href="{{ url('/unsubscribe') }}" style="color:#004080; text-decoration:none;">unsubscribe</a>.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
