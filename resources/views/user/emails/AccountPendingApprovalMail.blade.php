@component('mail::message')

<h1>Welcome, {{ $maildata['name'] }}!</h1>
<p>Lion Roaring will contact you regarding approval process.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
