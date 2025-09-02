<!DOCTYPE html>
<html>
<head>
    <title>Thank You for Contacting Us</title>
</head>
<body>
    <p>Dear {{ $contactData['first_name'] }} {{ $contactData['last_name'] }},</p>

    <p>Thank you for reaching out to us. We have received your message and will get back to you as soon as possible.</p>

    <p><strong>Your Message:</strong></p>
    <p>{{ $contactData['message'] }}</p>

    <p>If you have any urgent inquiries, feel free to reach us at <strong>{{ config('app.support_email') }}</strong>.</p>

    <p>Best regards,</p>
    <p><strong>{{ config('app.name') }}</strong></p>
</body>
</html>
