{{-- @component('mail::message')

{!! nl2br(e(strip_tags($mail['message']))) !!} 


Thanks,<br>
{{ config('app.name') }}
@endcomponent --}}

<!DOCTYPE html>
<html>
<head>
    <style>
        
    </style>
</head>
<body>
    <p>{!! $mail->message !!}</p>
    <!-- Add other HTML and styles as needed -->

    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>