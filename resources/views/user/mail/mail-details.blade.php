@extends('user.layouts.master')
@section('title')
Mail Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
<section id="loading">
    <div id="loading-content"></div>
</section>
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="main__body">
            <!-- Sidebar Starts -->
            @include('user.mail.partials.sidebar')

            <!-- Sidebar Ends -->
            <!-- Email List Starts -->
            <div class="emailList">
                <!-- Settings Starts -->
                <div class="emailList__settings">
                    <div class="emailList__settingsLeft">
                        <a href="{{ route('mail.index') }}"> <span class="material-symbols-outlined">
                                arrow_back</span></a>
                        <a href=""> <span class="material-symbols-outlined"> refresh </span></a>
                        <a href=""> <span class="material-symbols-outlined"> delete </span></a>
                    </div>
                    <div class="emailList__settingsRight">
                        <a href=""> <span class="material-symbols-outlined"> chevron_left </span></a>
                        <a href=""> <span class="material-symbols-outlined"> chevron_right </span></a>
                        <a href=""> <span class="material-symbols-outlined"> settings </span></a>
                    </div>
                </div>
                <div class="mail_subject">
                    <div class="row">
                        <div class="col-lg-9">
                            <h4 class="subject_text_h4">Subject: {{$mail_details->subject}}
                                <span class="inbox_box">inbox <span
                                        class="material-symbols-outlined">close</span></span>
                            </h4>
                        </div>
                        <div class="col-lg-3 text-end">
                            <a href=""> <span class="material-symbols-outlined">print</span></a>
                        </div>
                    </div>
                </div>

                <div class="main-mail card card-body">


                    <div class="mail_subject">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="d-flex">
                                    <div class="man_img">
                                        <span>
                                            @if ($mail_details->user->profile_picture)
                                            <img src="{{ Storage::url($mail_details->user->profile_picture) }}" alt=""
                                                class="user_img">
                                            @else
                                            <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt=""
                                                class="user_img" />
                                            @endif

                                        </span>
                                    </div>
                                    <div class="name_text_p">
                                        <h5>{{$mail_details->user->full_name}}</h5>
                                        <h6><span class="time_text">From: {{$mail_details->user->email}}</span></h6>
                                        <h6><span class="time_text">To: <span
                                                    class="badge bg-badge-dark text-dark">{{$mail_details->to}}</span></span>
                                        </h6>
                                        <h6>
                                            @if($mail_details->cc)
                                            <span class="time_text">CC: </span>
                                            @foreach(explode(',', $mail_details->cc) as $ccEmail)
                                            <span class="badge bg-badge-dark text-dark">{{ trim($ccEmail) }}</span>
                                            @endforeach
                                            @else
                                            <span>No CC emails available</span>
                                            @endif
                                        </h6>
                                        <h6>Date: {{ $mail_details->created_at->format('d/m/Y h:i A') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 text-end">
                                <div class="d-flex justify-content-end">
                                    <span class="time_text">{{ $mail_details->created_at->format('g:iA') }} ({{
                                        $mail_details->created_at->diffForHumans() }})</span>
                                    <a href="javascript:void(0);"> <span
                                            class="material-symbols-outlined open_mail_reply_box">reply</span></a>
                                    <a href=""> <span class="material-symbols-outlined">grade</span></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mail_text">
                        {{$mail_details->message}}
                    </div>

                    <div class="mail_text mail_details_attachments m-2">
                        @if($mail_details->attachment)
                        @php

                        $attachments = json_decode($mail_details->attachment, true);
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

                </div>

                @if ($mail_details->replies->isNotEmpty())
                <div class="reply-mails" hidden>
                    @foreach ($mail_details->replies as $reply)
                    @include('user.mail.partials.reply-mails', ['reply' => $reply])
                    @endforeach
                </div>
                @endif

                <div class="mail_reply">
                    <a href="javascript:void(0);" class="open_mail_reply_box">
                        <span class="material-symbols-outlined">reply</span> Reply
                    </a>
                    <a href="javascript:void(0);" class="open_mail_forward_box">
                        <span class="material-symbols-outlined">forward</span> Forward
                    </a>
                </div>

                <div class="reply_sec mail_send_reply_box" style="display: none">
                    <div class="reply_img_box">
                        <span>
                            @if (Auth::user()->profile_picture)
                            <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="" class="reply_img">
                            @else
                            <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="" class="reply_img" />
                            @endif

                        </span>
                    </div>

                    <div class="reply_text_box">
                        <form action="{{ route('mail.sendReply') }}" method="POST" id="sendUserEMailReply"
                            enctype="multipart/form-data">
                            <input type="hidden" name="main_mail_id" value="{{$mail_details->id}}">
                            <input type="hidden" name="reply_mail" value="1">
                            @csrf
                            <div class="d-flex align-items-center"><span class="material-symbols-outlined">reply</span>
                                &nbsp;&nbsp; | &nbsp;&nbsp; <span
                                    class="badge bg-badge-dark text-dark">{{$mail_details->user->email}}</span>
                            </div>
                            <div class='min-hide'>
                                @php
                                $mailtoArray = !empty($mail_details->user->email) ? explode(',',
                                $mail_details->user->email) : [];
                                $mailtoJson = json_encode(array_map(fn($email) => ['value' => trim($email)],
                                $mailtoArray));

                                $ccArray = !empty($mail_details->cc) ? explode(',', $mail_details->cc) : [];
                                $ccJson = json_encode(array_map(fn($email) => ['value' => trim($email)], $ccArray));
                                @endphp

                                <input name="to" class='input-large' type='hidden' placeholder='Recipients'
                                    value="{{ $mailtoJson }}" />

                                <input name="cc" class='input-large' type='hidden' placeholder='CC'
                                    value="{{ $ccJson }}" />


                                <input readonly class='input-large' name="subject" type='text' placeholder='Subject'
                                    value="RE: {{$mail_details->subject}}" />
                            </div>
                            <textarea class='min-hide_textera' name="message" rows="6" placeholder='Message'></textarea>

                            <div class="m-2" id="reply-mail-selected-file-names"></div>

                            <div class='menu min-hide'>
                                <button type="submit" class='button-large button-blue'>Send</button>
                                <div class="file-input">
                                    <input type="file" name="attachments[]" id="reply-mail-file-input"
                                        class="file-input__input" multiple />
                                    <label class="file-input__label" for="create-mail-file-input">
                                        <span><i class='fa fa-paperclip'></i></span>
                                    </label>
                                </div>
                                <div class="trash_btn">
                                    <a href="javascript:void(0);" class="close_mail_reply_box"><i
                                            class='fa fa-trash'></i></a>
                                </div>
                            </div>
                        </form>


                    </div>

                </div>



                {{-- Forward Mailbox --}}
                <div class="reply_sec mail_forward_reply_box" style="display: none">
                    <div class="reply_img_box">
                        <span>
                            <img src="http://127.0.0.1:8000/user_assets/images/logo.png" alt="user" class="reply_img" />
                        </span>
                    </div>
                    <div class="reply_text_box">
                        <form action="{{ route('mail.sendForward') }}" method="POST" id="sendUserEMailForward"
                            enctype="multipart/form-data">
                            <input type="hidden" name="main_mail_id" value="{{$mail_details->id}}">
                            <input type="hidden" name="forward_mail" value="1">
                            @csrf
                            <div class="d-flex align-items-center">
                                <span class="material-symbols-outlined">forward</span>
                                &nbsp;&nbsp; | &nbsp;&nbsp; 
                            </div>
                            <div class='min-hide'>
                                @php
                                $mailtoArray = !empty($mail_details->user->email) ? explode(',',
                                $mail_details->user->email) : [];
                                $mailtoJson = json_encode(array_map(fn($email) => ['value' => trim($email)],
                                $mailtoArray));

                                $ccArray = !empty($mail_details->cc) ? explode(',', $mail_details->cc) : [];
                                $ccJson = json_encode(array_map(fn($email) => ['value' => trim($email)], $ccArray));
                                @endphp

                                <input id="fw_to" name="to" class='input-large' type='' placeholder='Recipients'
                                    value="" />

                                <input id="fw_cc" name="cc" class='input-large' type='' placeholder='CC'
                                    value="{{ $ccJson }}" />


                                <input readonly class='input-large' name="subject" type='text' placeholder='Subject'
                                    value="{{$mail_details->subject}}" />
                            </div>
                            <textarea class='min-hide_textera' name="message" rows="6" placeholder='Message'></textarea>

                            <div class="m-2" id="forward-mail-selected-file-names"></div>

                            <div class='menu min-hide'>
                                <button type="submit" class='button-large button-blue'>Send</button>
                                <div class="file-input">
                                    <input type="file" name="attachments[]" id="forword-mail-file-input"
                                        class="file-input__input" multiple />
                                    <label class="file-input__label" for="create-mail-file-input">
                                        <span><i class='fa fa-paperclip'></i></span>
                                    </label>
                                </div>
                                <div class="trash_btn">
                                    <a href="javascript:void(0);" class="close_mail_forward_box"><i
                                            class='fa fa-trash'></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Email List rows Ends -->
            </div>
            <!-- Email List Ends -->
        </div>

        @include('user.mail.partials.create-mail')


    </div>
</div>
@endsection

@push('scripts')


<link rel="stylesheet" href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" />
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const userEmails = {!! json_encode($allMailIds->pluck('email')) !!};
            // Ensure that you are encoding this correctly
            //  const userFwEmailsTo = {mailtoJson};
            //  const userFwEmailsCc = {ccJson};

            // Initialize Tagify for "To" and "CC" fields
            const toInputFw = document.getElementById('fw_to');
            const ccInputFw = document.getElementById('fw_cc');

            const tagifyToFw = new Tagify(toInputFw, {
                whitelist: userEmails,
                enforceWhitelist: true,
                dropdown: {
                    maxItems: 20, // Adjust the max items shown in the dropdown
                    classname: "tags-dropdown",
                    enabled: 0, // all ways to be enabled
                    closeOnSelect: false, // keep dropdown open after selection
                    highlight: true // highlight matched results
                }
            });

            const tagifyCCFw = new Tagify(ccInputFw, {
                whitelist: userEmails,
                enforceWhitelist: true,
                dropdown: {
                    maxItems: 20, // Adjust the max items shown in the dropdown
                    classname: "tags-dropdown",
                    enabled: 0, // all ways to be enabled
                    closeOnSelect: false, // keep dropdown open after selection
                    highlight: true // highlight matched results
                }
            });
        });
</script>

@endpush