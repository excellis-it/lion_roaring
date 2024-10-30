{{-- @if ($mails->count() > 0)
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
@endif --}}


@if ($mails->count() > 0)
    @foreach ($mails as $mail)
        @php
            $mailUser = $mail->mailUsers()->where('user_id', auth()->id())->first();
            $isRead = isset($mailUser['is_read']) && $mailUser['is_read'] == 1;
            $latestReply = $mail->replies->first();
        @endphp
        <div class="emailRow {{ $isRead ? '' : 'mail_read' }}">
            <div class="emailRow__options">
                <input type="checkbox" class="selectMail" id="check-box" data-id="{{ $mail->id }}" />
                <span class="material-symbols-outlined"> star_border </span>
            </div>

            <h3 class="emailRow__title view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
                {{ $mail->user->full_name ?? '' }}
            </h3>

            <div class="emailRow__message view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
                <h4>
                    {{ $mail->subject }}
                    @if($latestReply)
                        {{-- <span class="emailRow__description"> - {!! $latestReply->message !!} </span> --}}
                        <span class="emailRow__description"> - {!! $mail->message !!} </span>
                    @else
                        <span class="emailRow__description"> - {!! $mail->message !!} </span>
                    @endif
                </h4>
            </div>

            <div class="emailRow__unread-count">
                @if($mail->unread_count > 0)
                    <span class="badge bg-danger">{{ $mail->unread_count }} new</span>
                @endif
            </div>

            <p class="emailRow__time view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
                {{ $latestReply ? $latestReply->created_at->diffForHumans() : $mail->created_at->diffForHumans() }}
            </p>
        </div>
    @endforeach
@else
    <div class="mt-3 text-center">
        <h3 class="emailRow__title">No mail found</h3>
    </div>
@endif
