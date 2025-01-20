<!DOCTYPE html>
<html>

    <head>
        <title>New Newsletter Subscription - Lion Roaring</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                color: #333;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            h1 {

                font-size: 24px;
                text-align: center;
                margin-bottom: 20px;
            }

            p {
                font-size: 16px;
                line-height: 1.5;
                margin: 10px 0;
            }

            p strong {
                color: #555;
            }

            .footer {
                font-size: 12px;
                text-align: center;
                color: #999;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
        <br>
        <div class="container">
            <h1>New Newsletter Subscription - Lion Roaring</h1>
            <p><strong>Name:</strong> {{ $mailData['name'] }}</p>
            <p><strong>Email:</strong> {{ $mailData['email'] }}</p>
            <p><strong>Message:</strong> {{ $mailData['message'] }}</p>
        </div>

        <div class="footer">
            <p>Thank you for subscribing to our newsletter. Stay tuned for the latest updates and news from Lion
                Roaring!</p>
        </div>
    </body>

</html>
