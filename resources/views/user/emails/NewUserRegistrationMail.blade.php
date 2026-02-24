<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
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
                                New User Registration 🆕</h1>
                            <p style="font-size: 16px; margin-bottom: 20px; color: #555555;">A new user has registered
                                on <strong style="color: #1a1a1a;">{{ config('app.name') }}</strong>. Here are the
                                details:</p>

                            <div
                                style="background-color: #fffdf5; border-left: 4px solid #d4af37; padding: 20px; margin: 25px 0;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                    style="border-spacing: 0;">
                                    <tr>
                                        <td
                                            style="padding: 8px 0; font-size: 14px; color: #888888; font-weight: bold; width: 140px; vertical-align: top;">
                                            Name:</td>
                                        <td style="padding: 8px 0; font-size: 15px; color: #333333;">
                                            {{ $maildata['new_user_name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="padding: 8px 0; font-size: 14px; color: #888888; font-weight: bold; width: 140px; vertical-align: top;">
                                            Email:</td>
                                        <td style="padding: 8px 0; font-size: 15px; color: #333333;">
                                            {{ $maildata['new_user_email'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="padding: 8px 0; font-size: 14px; color: #888888; font-weight: bold; width: 140px; vertical-align: top;">
                                            Username:</td>
                                        <td style="padding: 8px 0; font-size: 15px; color: #333333;">
                                            {{ $maildata['new_user_username'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="padding: 8px 0; font-size: 14px; color: #888888; font-weight: bold; width: 140px; vertical-align: top;">
                                            Membership Tier:</td>
                                        <td style="padding: 8px 0; font-size: 15px; color: #333333;">
                                            {{ $maildata['membership_tier'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="padding: 8px 0; font-size: 14px; color: #888888; font-weight: bold; width: 140px; vertical-align: top;">
                                            Status:</td>
                                        <td style="padding: 8px 0; font-size: 15px;">
                                            @if ($maildata['new_user_status'] == 1)
                                                <span style="color: #28a745; font-weight: bold;">Active</span>
                                            @else
                                                <span style="color: #dc3545; font-weight: bold;">Pending
                                                    Approval</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="padding: 8px 0; font-size: 14px; color: #888888; font-weight: bold; width: 140px; vertical-align: top;">
                                            Registered At:</td>
                                        <td style="padding: 8px 0; font-size: 15px; color: #333333;">
                                            {{ $maildata['registered_at'] }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if ($maildata['new_user_status'] != 1)
                                <div
                                    style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                                    <p style="margin: 0; font-size: 14px; color: #856404;">
                                        <strong>Action Required:</strong> This user requires admin approval before they
                                        can access the platform.
                                    </p>
                                </div>
                            @endif

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{route('partners.index')}}"
                                            style="background-color: #d4af37; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px;">View
                                            Members</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 16px; margin-top: 30px; color: #1a1a1a;">Best regards,<br>
                                <strong style="color: #d4af37;">{{ config('app.name') }} System</strong>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="padding: 30px; background-color: #fcfcfc; border-top: 1px solid #eeeeee;">

                            <p style="font-size: 12px; color: #999999; margin: 0;">&copy; {{ date('Y') }}
                                {{ config('app.name') }}. All rights reserved.</p>
                            <p style="font-size: 11px; color: #bbbbbb; margin-top: 10px;">This is an automated
                                notification sent to all Super Admin users.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
