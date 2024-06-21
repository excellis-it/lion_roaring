@component('mail::message')

{!! nl2br(e($mail['message'])) !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
