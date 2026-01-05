<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>RSVP Confirmation</title>
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

            .button {
                display: inline-block;
                background: #7851a9;
                color: white;
                padding: 12px 30px;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }

            .info-box {
                background: white;
                padding: 15px;
                border-left: 4px solid #28a745;
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
                <h1>✓ Registration Confirmed!</h1>
            </div>
            <div class="content">
                <p>Hi {{ $user->getFullNameAttribute() }},</p>

                <p>Your registration for <strong>{{ $event->title }}</strong> has been confirmed!</p>

                <div class="info-box">
                    <h3>Event Details</h3>
                    <p><strong>Event:</strong> {{ $event->title }}</p>
                    <p><strong>Date:</strong> {{ $event->start->format('l, F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ $event->start->format('g:i A') }} - {{ $event->end->format('g:i A') }}
                    </p>
                    <p><strong>Host:</strong> {{ $event->user->getFullNameAttribute() }}</p>
                </div>

                <p>Your event access link is ready. You can join the event using the button below:</p>

                <div style="text-align: center;">
                    <a href="{{ $accessUrl }}" class="button">
                        Access Event
                    </a>
                </div>

                <p style="margin-top: 30px; font-size: 14px; color: #666;">
                    <strong>Important:</strong> Save this email for your records. You'll need the access link to join
                    the event.
                </p>

                <p style="font-size: 14px; color: #666;">
                    Add this event to your calendar so you don't miss it!
                </p>
            </div>
            <div class="footer">
                <p>© {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
            </div>
        </div>
    </body>

</html>
