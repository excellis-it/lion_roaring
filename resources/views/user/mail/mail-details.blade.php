@extends('user.layouts.master')
@section('title')
    Mail Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
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
                                <h4 class="subject_text_h4">Re: Lorem ipsum dolor sit, amet consectetur adipisicing elit....
                                    <span class="inbox_box">inbox <span
                                            class="material-symbols-outlined">close</span></span>
                                </h4>
                            </div>
                            <div class="col-lg-3 text-end">
                                <a href=""> <span class="material-symbols-outlined">print</span></a>
                            </div>
                        </div>
                    </div>

                    <div class="mail_subject">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="d-flex">
                                    <div class="man_img">
                                        <span>
                                            <img src="http://127.0.0.1:8000/user_assets/images/logo.png" alt="user"
                                                class="user_img">
                                        </span>
                                    </div>
                                    <div class="name_text_p">
                                        <h5>Swarnadip Nath</h5>
                                        <span class="time_text">To Me</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 text-end">
                                <div class="d-flex justify-content-end">
                                    <span class="time_text">7:10PM (10minutes ago)</span>
                                    <a href=""> <span class="material-symbols-outlined">reply</span></a>
                                    <a href=""> <span class="material-symbols-outlined">grade</span></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mail_text">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt,
                            cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in
                            dignissimos vel necessitatibus.</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt,
                            cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in
                            dignissimos vel necessitatibus.</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt,
                            cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in
                            dignissimos vel necessitatibus.</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt,
                            cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in
                            dignissimos vel necessitatibus.</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt,
                            cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in
                            dignissimos vel necessitatibus.</p>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt,
                            cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in
                            dignissimos vel necessitatibus.</p>
                    </div>

                    <div class="mail_reply">
                        <a href=""><span class="material-symbols-outlined">reply</span> Reply</a>
                        <a href=""><span class="material-symbols-outlined">forward</span> Forward</a>
                    </div>

                    <div class="reply_sec">
                        <div class="reply_img_box">
                            <span>
                                <img src="http://127.0.0.1:8000/user_assets/images/logo.png" alt="user"
                                    class="reply_img" />
                            </span>
                        </div>
                        <div class="reply_text_box">
                            <div class="">
                                <div class="d-flex align-items-center"><span class="material-symbols-outlined">reply</span>
                                    &nbsp;&nbsp; | &nbsp;&nbsp; subhasiskoley@gmail.com</div>
                            </div>
                            <div class="big_textara">
                                <textarea name="" id="" rows="10" placeholder="Message"></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="" class="send_btn">Send</a>
                                    <div class="file-input">
                                        <input type="file" name="file-input" id="file-input" class="file-input__input">
                                        <label class="file-input__label" for="file-input">
                                            <span><i class="fa fa-paperclip"></i></span></label>
                                    </div>
                                </div>
                                <div class="trash_btn">
                                    <a href="" class=""><i class='fa fa-trash'></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reply_sec">
                        <div class="reply_img_box">
                            <span>
                                <img src="http://127.0.0.1:8000/user_assets/images/logo.png" alt="user"
                                    class="reply_img" />
                            </span>
                        </div>
                        <div class="reply_text_box">
                            <div class="">
                                <div class="d-flex align-items-center"><span
                                        class="material-symbols-outlined">forward</span> &nbsp;&nbsp; | &nbsp;&nbsp; <input
                                        type="text" class="text_box" /></div>
                            </div>
                            <div class="big_textara">
                                <textarea name="" id="" rows="5" placeholder="Message"></textarea>
                            </div>
                            <div class="fowroad">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero
                                    sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio
                                    reiciendis in dignissimos vel necessitatibus.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero
                                    sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio
                                    reiciendis in dignissimos vel necessitatibus.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero
                                    sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio
                                    reiciendis in dignissimos vel necessitatibus.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero
                                    sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio
                                    reiciendis in dignissimos vel necessitatibus.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero
                                    sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio
                                    reiciendis in dignissimos vel necessitatibus.</p>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero
                                    sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio
                                    reiciendis in dignissimos vel necessitatibus.</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="" class="send_btn">Send</a>
                                    <div class="file-input">
                                        <input type="file" name="file-input" id="file-input"
                                            class="file-input__input">
                                        <label class="file-input__label" for="file-input">
                                            <span><i class="fa fa-paperclip"></i></span></label>
                                    </div>
                                </div>
                                <div class="trash_btn">
                                    <a href="" class=""><i class='fa fa-trash'></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email List rows Ends -->
                </div>
                <!-- Email List Ends -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
