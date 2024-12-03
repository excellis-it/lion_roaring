<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user_img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .mail_subject,
        .mail_text {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body onload="printAndClose()">
    <div class="container-fluid my-1">
        <div class="mail_subject">
            <h4>Subject: {{ !empty($mail_details->reply_of) ? 'RE:' : '' }} {{ $mail_details->subject }}</h4>
        </div>

        <div class="main-mail mb-3">
            <div class="mail_subject">
                <div class="d-flex align-items-center mb-3">
                    <span>
                        @if ($mail_details->user->profile_picture)
                            <img src="{{ Storage::url($mail_details->user->profile_picture) }}" alt="Profile"
                                class="user_img">
                        @else
                            <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="Profile"
                                class="user_img">
                        @endif
                    </span>
                    <div class="ms-2">
                        <h5>{{ $mail_details->user->full_name }}</h5>
                        <h6>From: {{ $mail_details->user->email }}</h6>
                        <h6>TO: <span class="text-dark">{{ $mail_details->to }}</span></h6>
                        <h6>CC: <span class="text-dark">{{ $mail_details->cc }}</span></h6>
                        <h6>Date: {{ $mail_details->created_at->format('d/m/Y h:i A') }}</h6>
                    </div>
                </div>
            </div>

            <div class="mail_text">
                {!! $mail_details->message !!}
            </div>

            <div class="mail_text mail_details_attachments m-2">
                @if ($mail_details->attachment)
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
                    <p hidden>No attachments found.</p>
                @endif
            </div>
        </div>


        @if ($reply_mails->count() > 0)
            @foreach ($reply_mails as $reply)
            <hr>
                <div class="reply-main-mail mb-3">

                    <div class="mail_subject">
                        <div class="d-flex align-items-center mb-3">
                            <span>
                                @if ($reply->user->profile_picture)
                                    <img src="{{ Storage::url($reply->user->profile_picture) }}" alt="Profile"
                                        class="user_img">
                                @else
                                    <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="Profile"
                                        class="user_img">
                                @endif
                            </span>
                            <div class="ms-2">
                                <h5>{{ $reply->user->full_name }}</h5>
                                <h6>From: {{ $reply->user->email }}</h6>
                                <h6>TO: <span class="text-dark">{{ $reply->to }}</span></h6>
                                <h6>CC: <span class="text-dark">{{ $reply->cc }}</span></h6>
                                <h6>Date: {{ $reply->created_at->format('d/m/Y h:i A') }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mail_text">
                        {!! $reply->message !!}
                    </div>

                    <div class="mail_text mail_details_attachments m-2">
                        @if ($reply->attachment)
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
                            <p hidden>No attachments found.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif


    </div>
    <script>
        function printAndClose() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</body>

</html>
