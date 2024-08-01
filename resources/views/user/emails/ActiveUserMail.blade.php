@component('mail::message')

<h1>Welcome, {{ $maildata['name'] }}!</h1>
<p>Your account has been successfully activated. You can now log in and start using our services.</p>
<p>If you have any questions, feel free to contact us.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
