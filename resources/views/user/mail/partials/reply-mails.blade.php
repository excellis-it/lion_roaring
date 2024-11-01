<div class="reply-mail card card-body">
    {{-- <div class="mail_subject">
        <h5>{{ $reply->user->full_name }}</h5>
        <h6>From: {{ $reply->user->email }}</h6>
        <p>{{ $reply->message }}</p>
    </div> --}}

    <div class="mail_subject">
        <div class="row">
            <div class="col-lg-7">
                <div class="d-flex">
                    <div class="man_img">
                        <span>
                            @if ($reply->user->profile_picture)
                            <img src="{{ Storage::url($reply->user->profile_picture) }}" alt=""
                                class="user_img">
                            @else
                            <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt=""
                                class="user_img" />
                            @endif

                        </span>
                    </div>
                    <div class="name_text_p">
                        <h5>{{$reply->user->full_name}}</h5>
                        <h6><span class="time_text">From: {{$reply->user->email}}</span></h6>
                        <h6><span class="time_text">To: <span
                                    class="badge bg-badge-dark text-dark">{{$reply->to}}</span></span>
                        </h6>
                        <h6>
                            @if($reply->cc)
                            <span class="time_text">CC: </span>
                            @foreach(explode(',', $reply->cc) as $ccEmail)
                            <span class="badge bg-badge-dark text-dark">{{ trim($ccEmail) }}</span>
                            @endforeach
                            @else
                            <span hidden>No CC emails available</span>
                            @endif
                        </h6>
                        <h6>Date: {{ $reply->created_at->format('d/m/Y h:i A') }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-end">
                <div class="d-flex justify-content-end">
                    <span class="time_text">{{ $reply->created_at->format('g:iA') }} ({{
                        $reply->created_at->diffForHumans() }})</span>
                    <a href="javascript:void(0);"> <span
                            class="material-symbols-outlined open_mail_reply_box">reply</span></a>
                    <a href=""> <span class="material-symbols-outlined">grade</span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="mail_text">
        {{$reply->message}}
    </div>

    <div class="mail_text mail_details_attachments m-2">
        @if($reply->attachment)
        @php

        $attachments = json_decode($reply->attachment, true);
        @endphp

        @foreach($attachments as $attachment)
        <div class="attachment-item">
            <i class="fa fa-paperclip"></i>
            <a href="{{ asset('storage/' . $attachment['encrypted_name']) }}" target="_blank">{{
                $attachment['original_name'] }}</a>
        </div>
        @endforeach
        @else
        <p hidden>No attachments found.</p>
        @endif
    </div>

    <!-- Recursive replies if exist -->
    @if ($reply->replies->isNotEmpty())
        <div class="nested-replies">
            @foreach ($reply->replies as $nestedReply)
                @include('user.mail.partials.reply-mails', ['reply' => $nestedReply])
            @endforeach
        </div>
    @endif
</div>
