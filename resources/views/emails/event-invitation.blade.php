<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Event Invitation</title>
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
                background: #7851a9;
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
                background: #28a745;
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
                <h1>You're Invited from {{ env('APP_NAME') }}</h1>
            </div>
            <div class="content">
                <h2>{{ $event->title }}</h2>

                <p>{{ $event->description }}</p>

                <p><strong>When:</strong> {{ $event->start->format('l, F j, Y \a\t g:i A') }}
                    ({{ $event->start->format('T') }})</p>
                <p><strong>Ends:</strong> {{ $event->end->format('l, F j, Y \a\t g:i A') }}</p>
                <p><strong>Host:</strong> {{ $event->user->getFullNameAttribute() }}</p>

                @if ($event->type === 'paid')
                    <p><strong>Price:</strong> ${{ number_format($event->price, 2) }} USD</p>
                @else
                    <p><strong>Price:</strong> FREE</p>
                @endif

                @if ($event->capacity)
                    <p><strong>Limited Spots:</strong> Only {{ $event->capacity }} seats available</p>
                @endif

                <div style="text-align: center;">
                    <a href="{{ $registrationUrl }}" class="button">
                        Register Now
                    </a>
                </div>

                <p style="margin-top: 30px; font-size: 14px; color: #666;">
                    Don't miss out on this exciting event! Click the button above to secure your spot.
                </p>
            </div>
            <div class="footer">
                <p>© {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
                <p>You received this email because you're a member of our community.</p>
            </div>
        </div>
    </body>

</html>
