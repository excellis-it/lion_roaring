<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Payment Receipt</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
            }

            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }

            .header {
                background: #28a745;
                color: white;
                padding: 20px;
                text-align: center;
            }

            .content {
                background: #f8f9fa;
                padding: 30px;
            }

            .receipt-box {
                background: white;
                padding: 20px;
                border: 2px solid #28a745;
                margin: 20px 0;
            }

            .button {
                display: inline-block;
                background: #7851a9;
                color: white;
                padding: 12px 30px;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }

            .footer {
                text-align: center;
                padding: 20px;
                color: #666;
                font-size: 12px;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="header">
                <h1>Payment Successful!</h1>
            </div>
            <div class="content">
                <p>Hi {{ $user->getFullNameAttribute() }},</p>

                <p>Thank you for your payment. Your transaction has been successfully processed.</p>

                <div class="receipt-box">
                    <h3>Payment Receipt</h3>
                    <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                    <p><strong>Event:</strong> {{ $event->title }}</p>
                    <p><strong>Amount Paid:</strong> ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                    </p>
                    <p><strong>Payment Date:</strong> {{ $payment->paid_at->format('F j, Y \a\t g:i A') }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                    <p><strong>Status:</strong> Completed</p>
                </div>

                <p>You are now registered for the event. Use the button below to access your event details:</p>

                <div style="text-align: center;">
                    <a href="{{ $accessUrl }}" class="button">
                        Access Event
                    </a>
                </div>

                <p style="margin-top: 30px; font-size: 14px; color: #666;">
                    <strong>Event Details:</strong><br>
                    Date: {{ $event->start->format('l, F j, Y') }}<br>
                    Time: {{ $event->start->format('g:i A') }} - {{ $event->end->format('g:i A') }}
                    ({{ $event->start->format('T') }})
                </p>

                <p style="font-size: 14px; color: #666;">
                    Keep this receipt for your records. If you have any questions, please contact our support team.
                </p>
            </div>
            <div class="footer">
                <p>© {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
                <p>This is an automated receipt. Please do not reply to this email.</p>
            </div>
        </div>
    </body>

</html>
