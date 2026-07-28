<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Support Report Updated</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f0f7;-webkit-text-size-adjust:100%;">
@php
    $appName = config('app.name', env('APP_NAME', 'Lion Roaring'));
    $viewUrl = url('/user/support-reports/' . $report->id);
    $statusKey = $report->status ?? 'open';
    $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
    $statusStyles = match ($statusKey) {
        'open' => ['bg' => '#e8f1fb', 'fg' => '#1565c0'],
        'in_progress' => ['bg' => '#fff3e6', 'fg' => '#ef6c00'],
        'resolved' => ['bg' => '#e8f6ec', 'fg' => '#2e7d32'],
        'closed' => ['bg' => '#f3f4f6', 'fg' => '#4b5563'],
        default => ['bg' => '#efe9f7', 'fg' => '#5b2d8e'],
    };
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f0f7;padding:32px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 28px rgba(91,45,142,0.10);">
                {{-- Brand header --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#5b2d8e 0%,#7851a9 55%,#9b6bc9 100%);padding:28px 32px;text-align:center;">
                        <p style="margin:0 0 6px;font-family:Georgia,'Times New Roman',serif;font-size:22px;line-height:1.3;color:#ffffff;letter-spacing:0.02em;">
                            {{ $appName }}
                        </p>
                        <p style="margin:0;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:rgba(255,255,255,0.82);">
                            Support Desk
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="height:4px;background:#d98b1c;font-size:0;line-height:0;">&nbsp;</td>
                </tr>

                <tr>
                    <td style="padding:32px 28px 8px;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
                        <p style="margin:0 0 8px;font-size:13px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#7851a9;">
                            Status update
                        </p>
                        <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;font-weight:700;color:#2a1845;">
                            Your report has been updated
                        </h1>
                        <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#4b5563;">
                            The support team reviewed your report and updated its status.
                        </p>

                        <p style="margin:0 0 24px;">
                            <span style="display:inline-block;padding:8px 14px;border-radius:999px;background:{{ $statusStyles['bg'] }};color:{{ $statusStyles['fg'] }};font-size:13px;font-weight:700;letter-spacing:0.02em;">
                                {{ $statusLabel }}
                            </span>
                        </p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#faf8fd;border:1px solid #ece8f4;border-radius:12px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:18px 20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="padding:0 0 14px;border-bottom:1px solid #ece8f4;">
                                                <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#9ca3af;">Subject</p>
                                                <p style="margin:0;font-size:16px;font-weight:700;line-height:1.4;color:#1f2937;">{{ $report->subject }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:14px 0 {{ $report->admin_notes ? '14px' : '0' }};{{ $report->admin_notes ? 'border-bottom:1px solid #ece8f4;' : '' }}">
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td width="50%" valign="top" style="padding-right:8px;">
                                                            <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#9ca3af;">Report ID</p>
                                                            <p style="margin:0;font-size:14px;font-weight:600;color:#1f2937;">#{{ $report->id }}</p>
                                                        </td>
                                                        <td width="50%" valign="top" style="padding-left:8px;">
                                                            <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#9ca3af;">Updated</p>
                                                            <p style="margin:0;font-size:14px;font-weight:600;color:#1f2937;">{{ $report->updated_at->format('d M Y · H:i') }}</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        @if($report->admin_notes)
                                        <tr>
                                            <td style="padding:14px 0 0;">
                                                <p style="margin:0 0 6px;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#9ca3af;">
                                                    Response from Support Team
                                                </p>
                                                <p style="margin:0;padding:12px 14px;background:#ffffff;border:1px solid #ece8f4;border-radius:10px;font-size:14px;line-height:1.65;color:#374151;white-space:pre-wrap;">{{ $report->admin_notes }}</p>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 8px;">
                            <tr>
                                <td align="center" style="border-radius:10px;background:#5b2d8e;">
                                    <a href="{{ $viewUrl }}"
                                       style="display:inline-block;padding:14px 28px;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">
                                        View Your Report
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:12px 0 0;font-size:12px;line-height:1.5;color:#9ca3af;text-align:center;">
                            You can always check status anytime in Support Reports.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 28px 28px;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;text-align:center;border-top:1px solid #f0ebf6;">
                        <p style="margin:0 0 4px;font-size:12px;color:#6b7280;">
                            © {{ date('Y') }} {{ $appName }}
                        </p>
                        <p style="margin:0;font-size:11px;color:#9ca3af;">
                            You’re receiving this because you submitted a support report.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
