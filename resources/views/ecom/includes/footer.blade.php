@php
    use App\Helpers\Helper;
@endphp

<footer class="footer_sec">
    <div class="container-fluid position-relative z-1">
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="left_logo left_logo_top me-0 me-xl-5">
                    {{-- <div class="ftr_logo mb-3">
                        <img src="{{ Helper::getFooterCms() ? Storage::url(Helper::getFooterCms()->footer_logo) : asset('ecom_assets/images/logo.png') }}"
                            alt="logo" />
                    </div> --}}
                    <div class="ftr_logo_sec">
                        <div class="d-flex align-items-center">
                            <div>
                                <a class="ftr_logo">
                                    <img src="{{ Helper::getFooterCms() ? Storage::url(Helper::getFooterCms()->footer_logo) : asset('ecom_assets/images/estore_logo.png') }}"
                                        alt="logo" />
                                    {{-- <img src="{{ asset('ecom_assets/images/estore_logo.png') }}" alt="logo" /> --}}
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

                </div>
            </div>

            <div class="col-lg-6">
                <div class="news-letter" id="News-letter" style="background-color:#202d4d;">
                    <div class="news ">
                        <div class="container position-relative z-1">
                            <h2 class="news-heading text-left">Latest News</h2>
                            <p class="des how-de">Get the Latest news about digital Marketing to Your Pocket, drop your
                            </p>
                            <form action="{{ route('e-store.newsletter') }}" method="post" id="submit-newsletter-home">
                                @csrf
                                <input type="email" required placeholder="Enter your email address"
                                    name="newsletter_email" id="newsletter_email_home" />
                                <button type="submit" class="bt">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copy_right">
        <div class="container-fluid">
            <div class="copy_right_text">
                <div class="ftr_line_link">
                    <ul>
                        <li><a href="{{ route('e-store') }}">Home</a></li>
                        {{-- <li><a href="{{ route('e-store.all-products') }}">Our Collections</a></li> --}}
                        <li>
                            <a href="{{ Helper::getPDFAttribute() ?? 'javascript:void(0);' }}" target="_blank">Article
                                of
                                Agreement</a>
                        </li>
                        <li><a href="{{ route('e-store.order-tracking') }}">Track Order</a></li>
                        <li><a href="{{ route('e-store.contact') }}">Contact us</a></li>
                        {{-- <li><a href="{{ route('privacy-policy.e-store.cms-page') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('terms-and-condition.e-store.cms-page') }}">Terms and Conditions</a></li> --}}
                        <li><a href="{{ route('e-store.cms-page', ['slug' => 'privacy-policy']) }}">Privacy Policy</a></li>
                        <li><a href="{{ route('terms-and-conditions') }}">Terms and Conditions</a></li>

                    </ul>
                </div>
                <!-- <p> {!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_copywrite_text : '' !!}</p> -->
                <div class="left_ali left_logo">
                    <ul>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_facebook_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-facebook"></i></a></li>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_instagram_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_twitter_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-twitter"></i></a></li>
                        <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_youtube_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-youtube"></i></a></li>

                        <li><span class="badge bg-primary ms-3 mt-2"> <i class="fa fa-globe"></i>
                                {{ Helper::getVisitorCountryName() }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <p class="mt-3" style="border-top:1px solid #ccc; padding:20px; 20px 0px 20px"> {!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_copywrite_text : '' !!}</p>




    </div>
</footer>
