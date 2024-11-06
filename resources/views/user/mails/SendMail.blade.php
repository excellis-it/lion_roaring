@component('mail::message')

{{-- {!! nl2br(e($mail['message'])) !!} --}}
{!! nl2br(e(strip_tags($mail['message']))) !!}


Thanks,<br>
{{ config('app.name') }}
@endcomponent
