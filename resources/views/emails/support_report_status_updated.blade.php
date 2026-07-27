<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
    <h2>Your Support Report Has Been Updated</h2>
    <p>Your support report has been reviewed and its status has been updated.</p>
    <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
        <tr>
            <td style="padding: 8px; font-weight: bold; width: 120px;">Subject:</td>
            <td style="padding: 8px;">{{ $report->subject }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">New Status:</td>
            <td style="padding: 8px; text-transform: capitalize;">{{ str_replace('_', ' ', $report->status) }}</td>
        </tr>
        @if($report->admin_notes)
        <tr>
            <td style="padding: 8px; font-weight: bold; vertical-align: top;">Notes:</td>
            <td style="padding: 8px;">{{ $report->admin_notes }}</td>
        </tr>
        @endif
    </table>
    <p style="margin-top: 20px;">
        <a href="{{ url('/user/support-reports/' . $report->id) }}"
           style="background: #0d6efd; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            View Your Report
        </a>
    </p>
</body>
</html>
