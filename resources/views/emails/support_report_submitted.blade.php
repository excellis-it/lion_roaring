<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
    <h2>New Support Report Submitted</h2>
    <p>A new support report has been submitted by <strong>{{ $report->user->name ?? 'A user' }}</strong>.</p>
    <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
        <tr>
            <td style="padding: 8px; font-weight: bold; width: 120px;">Subject:</td>
            <td style="padding: 8px;">{{ $report->subject }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Message:</td>
            <td style="padding: 8px;">{{ $report->message }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Submitted:</td>
            <td style="padding: 8px;">{{ $report->created_at->format('d M Y, H:i') }}</td>
        </tr>
    </table>
    <p style="margin-top: 20px;">
        <a href="{{ url('/user/support-reports/' . $report->id) }}"
           style="background: #0d6efd; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            View Report
        </a>
    </p>
</body>
</html>
