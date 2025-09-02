@component('mail::message')
<h1>Dear user,</h1>
<p>Your user name is <b>{{$check['user_name']}}</b>. Thank you for using our service. </p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent

