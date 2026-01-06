<!doctype html>
<html>

    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
                color: #111;
            }

            h1 {
                font-size: 18px;
                margin: 0 0 10px;
            }

            .meta {
                margin: 0 0 12px;
            }

            .meta strong {
                display: inline-block;
                min-width: 80px;
            }

            .box {
                border: 1px solid #ddd;
                padding: 12px;
                border-radius: 6px;
            }
        </style>
    </head>

    <body>
        <h1>{{ $title }}</h1>

        <p class="meta"><strong>Signer:</strong> {{ $signerName }}@if (!empty($signerInitials))
                ({{ $signerInitials }})
            @endif
        </p>

        <div class="box">
            {!! $html !!}
        </div>
    </body>

</html>
