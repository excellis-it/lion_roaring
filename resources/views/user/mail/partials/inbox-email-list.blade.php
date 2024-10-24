@if ($mails->count() > 0)
@foreach ($mails as $mail)
    @php
        $mailUser = $mail->mailUsers()->where('user_id', auth()->id())->first();
        $isRead = isset($mailUser['is_read']) && $mailUser['is_read'] == 1;
    @endphp

    <div class="emailRow {{ $isRead ? '' : 'mail_read' }}">
        <div class="emailRow__options">
            <input type="checkbox" class="selectMail" id="check-box" data-id="{{$mail->id}}"/>
            <span class="material-symbols-outlined"> star_border </span>
        </div>

        <h3 class="emailRow__title view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">{{ $mail->user->full_name ?? '' }}</h3>

        <div class="emailRow__message view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
            <h4>
                {{ $mail->subject }}
                <span class="emailRow__description"> - {!! $mail->message !!} </span>
            </h4>
        </div>

        <p class="emailRow__time view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">{{ $mail->created_at->diffForHumans() }}</p>
    </div>
@endforeach
@else
<div class="mt-3 text-center">
    <h3 class="emailRow__title">No mail found</h3>
</div>
@endif
