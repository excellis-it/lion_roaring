<!DOCTYPE html>
<html>
<head>
    <title>Partner Rejection Notification</title>
</head>
<body>
    <h3>Dear {{ $partner->full_name }},</h3>
    <p>We regret to inform you that your application has been rejected. Below is the reason provided:</p>
    <p><strong>Reason: </strong>{{ $reason }}</p>
    <p>If you have any questions, please contact us.</p>
    <p>Best regards,</p>
    <p>Lion Roaring</p>
</body>
</html>
