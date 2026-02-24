<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Lion Roaring</title>
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
                                Welcome, {{ $maildata['name'] }}! 🎉</h1>
                            <p style="font-size: 16px; margin-bottom: 20px; color: #555555;">We're thrilled to have you
                                join our pack at <strong style="color: #1a1a1a;">Lion Roaring</strong>. Your journey
                                towards greatness starts here.</p>

                            @if ($maildata['status'] == 1)
                                <div
                                    style="background-color: #fffdf5; border-left: 4px solid #d4af37; padding: 20px; margin: 25px 0;">
                                    <h3 style="margin-top: 0; color: #d4af37; font-size: 18px;">Success! Your Account is
                                        Active</h3>
                                    <p style="margin-bottom: 0; font-size: 15px; color: #666666;">Good news! Your
                                        account has been successfully activated. You now have full access to all our
                                        premier features and services.</p>
                                </div>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                    style="margin: 30px 0;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ url('/') }}"
                                                style="background-color: #d4af37; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px;">Visit
                                                Your Dashboard</a>
                                        </td>
                                    </tr>
                                </table>
                                <p style="font-size: 16px; color: #555555;">If you have any questions or need a quick
                                    tour, our support team is just an email away.</p>
                            @else
                                <div
                                    style="background-color: #f9f9f9; border-left: 4px solid #1a1a1a; padding: 20px; margin: 25px 0;">
                                    <h3 style="margin-top: 0; color: #1a1a1a; font-size: 18px;">Account Status: Pending
                                        Approval</h3>
                                    <p style="margin-bottom: 0; font-size: 15px; color: #666666;">Thank you for
                                        registering. Your account is currently undergoing our standard review process
                                        for admin approval. This usually takes less than 24 hours.</p>
                                </div>
                                <p style="font-size: 16px; color: #555555;">We'll send you another confirmation email as
                                    soon as your account is ready to dive in. We appreciate your patience while we
                                    ensure everything is set up perfectly for you.</p>
                            @endif

                            <p style="font-size: 16px; margin-top: 30px; color: #1a1a1a;">Best regards,<br>
                                <strong style="color: #d4af37;">The Lion Roaring Team</strong>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="padding: 30px; background-color: #fcfcfc; border-top: 1px solid #eeeeee;">
                           
                            <p style="font-size: 12px; color: #999999; margin: 0;">&copy; {{ date('Y') }}
                                {{ config('app.name') }}. All rights reserved.</p>
                            <p style="font-size: 11px; color: #bbbbbb; margin-top: 10px;">If you did not request this
                                email, please ignore it or contact us.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
