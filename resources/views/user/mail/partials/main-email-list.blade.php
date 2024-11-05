@if ($mails->count() > 0)
    @foreach ($mails as $mail)
        @php
            // $mailUser = $mail->mailUsers()->where('user_id', auth()->id())->first();
            $mailUser = $mail->ownUserMailInfo;
            $isRead = isset($mailUser['is_read']) && $mailUser['is_read'] == 1;
            $isStar = $mailUser->is_starred;
        @endphp
        <div class="emailRow {{ $isRead ? '' : 'mail_read' }}">
            <div class="emailRow__options">
                <input type="checkbox" class="selectMail" id="check-box" data-id="{{ $mail->id }}" />
                {{-- <span class="material-symbols-outlined"> star_border </span> --}}
                @if ($isStar == 1)
                    <a href="javascript:void(0);" onclick="setMailStar(this, {{ $mail->id }})">
                        <span class="material-symbols-outlined"
                            style="color: orange; font-variation-settings: 'FILL' 1;">grade</span></a>
                @else
                    <a href="javascript:void(0);" onclick="setMailStar(this, {{ $mail->id }})">
                        <span class="material-symbols-outlined">grade</span></a>
                @endif
            </div>


            <h3 class="emailRow__title view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
                {{ $mail->user->full_name ?? '' }}
            </h3>

            <div class="emailRow__message view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
                <h4>
                    {{ !empty($mail->reply_of) ? 'RE:' : '' }} {{ $mail->subject }}
                    
                    <span class="emailRow__description"> - {!! substr(strip_tags($mail->message), 0, 100) !!} </span>
                   
                </h4>
            </div>

            <div class="emailRow__unread-count">
                @if ($mail->unread_count > 0)
                    <span class="badge bg-danger">{{ $mail->unread_count }} new</span>
                @endif
            </div>

            <p class="emailRow__time view-mail" data-route="{{ route('mail.view', base64_encode($mail->id)) }}">
                {{ $mail->created_at->diffForHumans() }}
            </p>
        </div>
    @endforeach

    <div class="pagination-links float-end mt-2" hidden>
        {{ $mails->links() }}
    </div>

@else
    <div class="mt-3 text-center">
        <h3 class="emailRow__title">No mail found</h3>
    </div>
@endif
