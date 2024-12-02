<br>
<div class="container-fluid my-1">


    <div class="main-mail mb-3">
        <div class="mail_subject">
            <div class="d-flex align-items-center mb-3">

                <div class="ms-2">

                    <h6>From: {{ $mail_details->user->full_name }} - {{ $mail_details->user->personal_email }}</h6>
                    <h6>TO: <span class="text-dark">{{ $mail_details->to }}</span></h6>
                    @if (!empty($mail_details->cc))
                        <h6>CC: <span class="text-dark">{{ $mail_details->cc }}</span></h6>
                    @endif
                    <h6>Date: {{ $mail_details->created_at->format('d/m/Y h:i A') }}</h6>
                </div>
            </div>
        </div>

        <div class="mail_text">
            {!! $mail_details->message !!}
        </div>

        <div class="mail_text mail_details_attachments m-2">
            @if ($mail_details->attachment)
                <span>____________</span><br>
                <span>Attachments:</span>
                @php $attachments = json_decode($mail_details->attachment, true); @endphp
                @foreach ($attachments as $attachment)
                    <div class="attachment-item">
                        <i class="fa fa-paperclip"></i>
                        <span>{{ $attachment['original_name'] }} : <a
                                href="{{ asset('storage/' . $attachment['encrypted_name']) }}"
                                target="_blank">{{ asset('storage/' . $attachment['encrypted_name']) }}</a></span>
                    </div>
                @endforeach
            @else
            @endif
        </div>
    </div>
    <br>
    @if ($reply_mails->count() > 0)
        @foreach ($reply_mails as $reply)
            <br>
            <span>-------------------------------------------------------------------------------------</span>
            <div class="reply-main-mail mb-3">

                <div class="mail_subject">
                    <div class="d-flex align-items-center mb-3">

                        <div class="ms-2">
                            <h6>From: {{ $reply->user->full_name }} - {{ $reply->user->personal_email }}</h6>
                            <h6>TO: <span class="text-dark">{{ $reply->to }}</span></h6>
                            @if (!empty($reply->cc))
                                <h6>CC: <span class="text-dark">{{ $reply->cc }}</span></h6>
                            @endif
                            <h6>Date: {{ $reply->created_at->format('d/m/Y h:i A') }}</h6>
                        </div>
                    </div>
                </div>

                <div class="mail_text">
                    {!! $reply->message !!}
                </div>

                <div class="mail_text mail_details_attachments m-2">
                    @if ($reply->attachment)
                        <span>____________</span><br>
                        <span>Attachments:</span>
                        @php $attachments = json_decode($reply->attachment, true); @endphp
                        @foreach ($attachments as $attachment)
                            <div class="attachment-item">
                                <i class="fa fa-paperclip"></i>
                                <span>{{ $attachment['original_name'] }} : <a
                                        href="{{ asset('storage/' . $attachment['encrypted_name']) }}"
                                        target="_blank">{{ asset('storage/' . $attachment['encrypted_name']) }}</a></span>
                            </div>
                        @endforeach
                    @else
                    @endif
                </div>
            </div>
        @endforeach
    @endif


</div>
