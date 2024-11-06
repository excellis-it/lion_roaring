@if ($reply_mails->count() > 0)
    @foreach ($reply_mails as $reply)
        <hr hidden class="ms-5" style="height: 20px;width:5px">
        <div class="main-mail mt-2">           

            <div class="mail_subject">
                <div class="row">
                    <span class="mail_text" hidden>RE:</span>
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
                                
                                <h5>{{ $reply->user->full_name }}</h5>
                                <h6><span class="time_text">From: {{ $reply->user->email }}</span></h6>
                                <h6 hidden><span class="time_text">To: </span>
                                    @foreach (explode(',', $reply->to) as $toEmail)
                                        <span class="badge bg-badge-dark text-dark">{{ trim($toEmail) }}</span>
                                    @endforeach
                                </h6>
                                <h6>
                                    @if ($reply->cc)
                                        <span class="time_text">CC: </span>
                                        @foreach (explode(',', $reply->cc) as $ccEmail)
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
                            <span class="time_text">{{ $reply->created_at->format('g:iA') }}
                                ({{ $reply->created_at->diffForHumans() }})
                            </span>
                            {{-- @if ($reply->form_id != auth()->id() && !Request::is('user/mail/trash-mail-view/*'))
                            <a href="javascript:void(0);"> <span
                                    class="material-symbols-outlined open_mail_reply_box">reply</span></a>
                            @if ($reply->$ownUserMailInfo->is_starred == 1)
                                <a href="javascript:void(0);"
                                    onclick="setMailStar(this, {{ $reply->id }})">
                                    <span class="material-symbols-outlined"
                                        style="color: orange; font-variation-settings: 'FILL' 1;">grade</span></a>
                            @else
                                <a href="javascript:void(0);"
                                    onclick="setMailStar(this, {{ $reply->id }})">
                                    <span class="material-symbols-outlined">grade</span></a>
                            @endif
                        @endif --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mail_text">
                {!! $reply->message !!}
            </div>

            <div class="mail_text mail_details_attachments">
                @if ($reply->attachment)
                    @php

                        $attachments = json_decode($reply->attachment, true);
                    @endphp

                    @foreach ($attachments as $attachment)
                        <div class="other_attch">
                            <a class="attatched_file_box" href="{{ asset('storage/' . $attachment['encrypted_name']) }}"
                                target="_blank">
                                <div class="mail_img_box">
                                    <span><img src="{{ asset('user_assets/images/atatched.png') }}" alt="user"
                                            class="" /></span>
                                    <div>
                                        <p>{{ substr($attachment['original_name'], 0,8) }}</p>
                                    </div>
                                </div>
                                <div class="download_attetched_file">
                                    <span class="material-symbols-outlined">download</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p hidden>No attachments found.</p>
                @endif
            </div>

        </div>
    @endforeach
@endif
