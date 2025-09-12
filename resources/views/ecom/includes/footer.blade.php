@php
    use App\Helpers\Helper;
@endphp
<footer class="footer_sec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="left_logo me-0 me-xl-5">
                    {{-- <div class="ftr_logo mb-3">
                        <img src="{{ Helper::getFooterCms() ? Storage::url(Helper::getFooterCms()->footer_logo) : asset('ecom_assets/images/logo.png') }}"
                            alt="logo" />
                    </div> --}}
                    <div class="ftr_logo_sec">
                        <div class="d-flex align-items-center">
                            <div>
                                <a class="ftr_logo">
                                    {{-- <img src="{{ Helper::getFooterCms() ? Storage::url(Helper::getFooterCms()->footer_logo) : asset('ecom_assets/images/logo.png') }}"
                                        alt="logo" /> --}}
                                    <img src="{{ asset('ecom_assets/images/estore_logo.png') }}" alt="logo" />
                                </a>
                            </div>
                            {{-- <div>
                                <a class="ftr_logo_right">
                                    @if (isset(Helper::getFooter()['footer_flag']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_flag']) }}"
                                            alt="">
                                    @else
                                        <img src="{{ asset('frontend_assets/uploads/2024/02/Group-2029.png') }}"
                                            alt="">
                                    @endif
                                </a>
                            </div> --}}
                        </div>
                    </div>


                    <p>
                        {!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_title : '' !!}
                    </p>
                    <span>Follow us</span>
                    <ul>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_facebook_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-facebook"></i></a></li>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_instagram_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_twitter_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-twitter"></i></a></li>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_youtube_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-youtube"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="left_ali">
                    <h4>Find Us</h4>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon_map">
                            <span><i class="fa-solid fa-location-dot"></i></span>
                        </div>
                        <div class="ftr_text_h">
                            <p><b>{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_address_title : '' !!}

                                </b><br>
                                {!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_address : '' !!}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon_map">
                            <span><i class="fa-solid fa-phone"></i></span>
                        </div>
                        <div class="ftr_text_h">
                            <a href="javascript:void(0);">{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_phone_number : '' !!}</a>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon_map">
                            <span><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <div class="ftr_text_h">
                            <a href="javascript:void(0);">{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_email : '' !!}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="find-us left_ali">
                    <h4>
                        {!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_newsletter_title : '' !!}
                    </h4>
                    <div class="ftr-frm">
                        <form action="{{ route('e-store.newsletter') }}" method="post" id="submit-newsletter">
                            @csrf
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <input size="40" class="form-control" id="newsletter_name"
                                        placeholder="Full Name" value="" type="text" name="newsletter_name">
                                    <span class="text-danger" id="newsletter_name_error"></span>
                                </div>
                                <div class="form-group col-lg-6 col-md-12">
                                    <input class="form-control" placeholder="Email ADDRESS" id="newsletter_email"
                                        value="" type="email" name="newsletter_email">
                                    <span class="text-danger" id="newsletter_email_error"></span>
                                </div>
                                <div class="form-group col-12">
                                    <textarea cols="40" rows="3" class="form-control" id="newsletter_message" placeholder="Message"
                                        name="newsletter_message"></textarea>
                                    <span class="text-danger" id="newsletter_message_error"></span>
                                </div>
                            </div>
                            <div class="main-btn">
                                <input class="red_btn_submit" type="submit" value="Submit" />
                            </div>
                        </form>
                        {{-- <form action="">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" class="form-control" id="" value=""
                                        placeholder="FULL NAME" required="" />
                                </div>
                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" class="form-control" id="" value=""
                                        placeholder="EMAIL ID" required="" />
                                </div>
                                <div class="form-group col-12">
                                    <textarea class="form-control" id="" placeholder="YOUR MESSAGE" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="main-btn">
                                <input class="red_btn_submit" type="submit" value="Submit" />
                            </div>
                        </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copy_right">
        <div class="container">
            <div class="ftr_line_link">
                <ul>
                    <li><a href="{{ route('e-store') }}">Home</a></li>
                    <li><a href="{{ route('e-store.all-products') }}">Our Collections</a></li>
                    <li><a href="{{ route('contact-us') }}">Contact us</a></li>
                    @if (Helper::getCmsPages() && count(Helper::getCmsPages()) > 0)
                        @foreach (Helper::getCmsPages() as $page)
                            <li><a href="{{ route($page->slug . '.e-store.cms-page') }}">{{ $page->page_name }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <p> {!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_copywrite_text : '' !!}</p>
        </div>

    </div>


</footer>
