@if ($mails->count() > 0)
@foreach ($mails as $mail)
<div class="emailRow">
    <div class="emailRow__options">
        <input type="checkbox" class="selectMail" id="check-box" data-id="{{ $mail->id }}" />
        {{-- <span class="material-symbols-outlined"> star_border </span> --}}
        
    </div>


    <h3 class="emailRow__title view-mail" data-route="{{ route('mail.trash.view', base64_encode(!empty($mail->reply_of) ? $mail->reply_of : $mail->id)) }}">
        {{ $mail->user->full_name ?? '' }}
    </h3>

    <div class="emailRow__message view-mail" data-route="{{ route('mail.trash.view', base64_encode(!empty($mail->reply_of) ? $mail->reply_of : $mail->id)) }}">
        <h4>
            {{ $mail->subject }}
            <span class="emailRow__description"> - {!! $mail->message !!} </span>
            
        </h4>
    </div>

    

    <p class="emailRow__time view-mail" data-route="{{ route('mail.trash.view', base64_encode(!empty($mail->reply_of) ? $mail->reply_of : $mail->id)) }}">
        {{ $mail->created_at->diffForHumans() }}
    </p>
</div>
@endforeach
@else
<div class="mt-3 text-center">
    <h3 class="emailRow__title">No mail found</h3>
</div>
@endif