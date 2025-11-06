<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Collaboration Accepted</title>
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
                background-color: #f4f4f4;
            }

            .content {
                background-color: #fff;
                padding: 30px;
                border-radius: 5px;
            }

            .button {
                display: inline-block;
                padding: 12px 30px;
                background-color: #7851A9;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }

            .details {
                background-color: #f9f9f9;
                padding: 15px;
                border-left: 4px solid #28a745;
                margin: 20px 0;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="content">
                <h2>Invitation Accepted</h2>

                <p>Hello {{ $creator->full_name }},</p>

                <p><strong>{{ $acceptedUser->full_name }}</strong> has accepted your invitation to the private
                    collaboration meeting.</p>

                <div class="details">
                    <h3>{{ $collaboration->title }}</h3>
                    <p><strong>Start Time:</strong> {{ date('F j, Y, g:i A', strtotime($collaboration->start_time)) }}
                    </p>
                    <p><strong>End Time:</strong> {{ date('F j, Y, g:i A', strtotime($collaboration->end_time)) }}</p>
                </div>

                <a href="{{ route('private-collaborations.show', $collaboration->id) }}" class="button">View
                    Collaboration</a>

                <p>Best regards,<br>
                    {{ env('APP_NAME') }}</p>
            </div>
        </div>
    </body>

</html>
