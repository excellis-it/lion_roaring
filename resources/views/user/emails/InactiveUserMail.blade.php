@component('mail::message')
    <h1>Hello {{ $maildata['name'] }},</h1>
    <p>We regret to inform you that your account has been deactivated. If you have any questions or believe this was a
        mistake, please contact our support team.</p>
    <p>Thank you,</p>

    Thanks,
    {{ config('app.name') }}
@endcomponent
