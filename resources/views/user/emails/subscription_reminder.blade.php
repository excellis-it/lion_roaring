<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Renewal Reminder</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; width: 100% !important; height: 100% !important;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="background-color: #f4f7f6; padding-bottom: 40px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; margin-top: 40px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); border-spacing: 0;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #1a1a1a; padding: 30px;">
                            <img src="{{ asset('frontend_assets/images/logo.png') }}" alt="Lion Roaring Logo"
                                style="max-width: 150px; height: auto; display: block; border: 0;">
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px; color: #333333; line-height: 1.6;">
                            <h1
                                style="color: #1a1a1a; font-size: 24px; margin-top: 0; font-weight: 700; margin-bottom: 20px;">
                                Your subscription will renew soon ⏰
                            </h1>
                            <p style="font-size: 16px; margin-bottom: 20px; color: #555555;">
                                Hi <strong style="color: #1a1a1a;">{{ $maildata['name'] }}</strong>,
                            </p>
                            <p style="font-size: 16px; margin-bottom: 20px; color: #555555;">
                                This is a friendly reminder that your
                                <strong style="color: #1a1a1a;">{{ $maildata['subscription_name'] }}</strong>
                                subscription will expire on
                                <strong style="color: #d4af37;">{{ $maildata['expire_date'] }}</strong>.
                            </p>

                            <div
                                style="background-color: #fffdf5; border-left: 4px solid #d4af37; padding: 20px; margin: 25px 0; border-radius: 0 6px 6px 0;">
                                <h3 style="margin-top: 0; color: #d4af37; font-size: 18px;">Subscription Details</h3>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                    style="font-size: 15px; color: #555555;">
                                    <tr>
                                        <td style="padding: 6px 0; font-weight: 600; color: #333; width: 40%;">Plan:
                                        </td>
                                        <td style="padding: 6px 0;">{{ $maildata['subscription_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-weight: 600; color: #333;">Start Date:</td>
                                        <td style="padding: 6px 0;">{{ $maildata['start_date'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-weight: 600; color: #333;">Expiry Date:</td>
                                        <td style="padding: 6px 0;">
                                            <span
                                                style="color: #e74c3c; font-weight: 600;">{{ $maildata['expire_date'] }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-weight: 600; color: #333;">Days Remaining:</td>
                                        <td style="padding: 6px 0;">
                                            <span
                                                style="color: #e74c3c; font-weight: 600;">{{ $maildata['days_remaining'] }}
                                                day(s)</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style="font-size: 16px; margin-bottom: 25px; color: #555555;">
                                To continue enjoying uninterrupted access to all features, please renew your
                                subscription before it expires.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('user.membership.index') }}"
                                            style="background-color: #d4af37; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px;">
                                            Renew Subscription
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 14px; color: #999999; margin-top: 20px;">
                                If you have already renewed or do not wish to continue, you can safely ignore this
                                email.
                            </p>

                            <p style="font-size: 16px; margin-top: 30px; color: #1a1a1a;">Best regards,<br>
                                <strong style="color: #d4af37;">The {{ config('app.name') }} Team</strong>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="padding: 30px; background-color: #fcfcfc; border-top: 1px solid #eeeeee;">
                            <p style="font-size: 12px; color: #999999; margin: 0;">&copy; {{ date('Y') }}
                                {{ config('app.name') }}. All rights reserved.</p>
                            <p style="font-size: 11px; color: #bbbbbb; margin-top: 10px;">
                                If you did not request this email, please ignore it or contact us.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
