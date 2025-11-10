{{-- {{ dd(session()->all()) }} --}}
@extends('frontend.layouts.master')
@section('meta_title')
    <meta name="description" content="{{ $home['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $home['meta_keywords'] ?? '' }}">
    <meta name="title" content="{{ $home['meta_title'] ?? '' }}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{ $home['meta_title'] ?? 'Home' }}
@endsection
@push('styles')
@endpush

@section('content')

    @php
        $currentCode = strtoupper(\App\Helpers\Helper::getVisitorCountryCode());
        $countries = \App\Helpers\Helper::getCountries();
        // show popup only if session key not set for this IP
        $ip = request()->ip();
        $sessionKey = 'visitor_country_flag_code_' . $ip;
        $showPopup = !session()->has($sessionKey);
    @endphp

    <!--Flag Popup -->
    <div class="popup-overlay" id="popupOverlay" style="{{ $showPopup ? '' : 'display:none;' }}">
        <div class="popup-box flag-popup-box">
            <h4>Select Your Country</h4>
            <div class="flag-grid">
                <!-- Flags directly in HTML -->
                @foreach ($countries as $c)
                    <img src="{{ asset('frontend_assets/images/flags/' . strtolower($c->code) . '.png') }}"
                        alt="{{ $c->name }}" title="{{ $c->name }}"
                        onclick="selectFlag('{{ strtolower($c->code) }}')">
                @endforeach

            </div>
            <button class="btn btn-danger popup-btn" onclick="closePopup()">Close</button>
        </div>
    </div>



    <section class="banner__slider banner_sec" style="background-image: url('{{asset('frontend_assets/images/bg-wrap.jpg')}}');">
        <div class="slider">
            <div class="slide">
                <a href="{{ route('details') }}" tabindex="0">
                    <div class="slide__img">
                        <!-- <video autoplay="" muted="" loop="" class="video_part" playsInline>
                            <source
                                src="{{ isset($home['section_1_video']) ? Storage::url($home['section_1_video']) : 'https://via.placeholder.com/150' }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video> -->
                        <!-- <img src="" alt="" class="full-image d-block d-md-none" /> -->
                        <!-- <img src="{{ isset($home['banner_image']) ? Storage::url($home['banner_image']) : 'https://via.placeholder.com/150' }}"
                            class="full-image overlay-image"> -->
                            
                        <img src="{{asset('frontend_assets/images/banner_img.png')}}" class="full-image overlay-image">
                    </div>
                </a>
                <div class="slide__content slide__content__left">
                    <div class="slide__content--headings text-left">
                        <h1 class="title">{{ $home['banner_title'] ?? 'title' }}</h1>
                        <!-- <p class="top-title"></p> -->
                        <!--<a class="red_btn slidebottomleft" ><span>get started</span></a>-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about_sec">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="four_image">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-right" data-aos-duration="800">
                                <img src="{{asset('frontend_assets/images/abt_one.png')}}" class="about_four_ii mb-3">
                                <img src="{{asset('frontend_assets/images/abt_one1.png')}}" class="about_four_ii mb-3">
                            </div>
                            <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="1600">
                                <img src="{{asset('frontend_assets/images/abt_one2.png')}}" class="about_four_ii mb-3 mt-5">
                                <img src="{{asset('frontend_assets/images/abt_one3.jpg')}}" class="about_four_ii mb-3">
                            </div>
                        </div>
                    </div>                       
                </div>
                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp">
                        <h6>{{ $home['section_1_title'] ?? 'title' }}</h6>
                        <h2 class="text-start"> {{ $home['section_1_sub_title'] ?? 'title' }}</h2>
                        <p style="font-weight: 400;">
                            <strong>{!! $home['section_1_description'] ?? 'description' !!}</strong>
                        </p>
                    </div>
                    <a class="red_btn" data-animation-in="fadeInUp" href="{{ route('about-us') }}"><span>read
                            more</span></a>
                </div>
            </div>
        </div>
    </section>
    <section class="after_about after_about_hm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="book">
                        <div id="pages" class="pages">
                            <div class="page first_page text-center">
                                    <img src="{{asset('frontend_assets/images/banner_img.png')}}" alt="" class="page_logo">
                                <div class="about_text heading_hp">
                                    <h2>{{ $home['section_3_title'] ?? 'title' }}</h2>
                                    <p> {!! $home['section_3_description'] ?? 'descripiton' !!} </p>
                                </div>
                                <div class="design_page"></div>
                            </div>
                        @if (count($our_governances) > 0)
                        @foreach ($our_governances as $key => $our_governance)
                            <div class="page">
                                <img src="{{ isset($our_governance->image) ? Storage::url($our_governance->image) : 'https://via.placeholder.com/150' }}"
                                                alt="">
                                <h4 class="flex-fixed">{{ $our_governance->name }}</h4>
                                @php
                                    $description = $our_governance->description ?? 'description';
                                    $firstPart = Str::limit(strip_tags($description), 1200, '');
                                    $restPart = Str::after($description, $firstPart);
                                @endphp
                                <p>{!! $firstPart ?? '' !!}</p>
                                <div class="design_page"></div>
                            </div>
                            <div class="page">
                                <p>{!! $restPart ?? '' !!}</p>
                                <div class="design_page_right"></div>
                            </div>
                                @endforeach
                        @endif
                        <div class="page"></div>
                            <!-- <div class="page">
                                <img src="{{ isset($home['section_2_right_image']) ? Storage::url($home['section_2_right_image']) : 'https://via.placeholder.com/150' }}"
                                                alt="">
                                <h4 class="flex-fixed">{{ $home['section_2_right_title'] ?? 'title' }}</h4>
                            </div>
                            <div class="page">
                                <h4 class="flex-fixed">{{ $home['section_2_right_title'] ?? 'title' }}</h4>
                                <p>{!! $home['section_2_right_description'] ?? 'description' !!}</p>
                            </div>
                            <div class="page"></div>
                            <div class="page"></div>
                            <div class="page"></div>
                            <div class="page"></div>
                            <div class="page"></div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
   

    @if (count($our_organizations) > 0)
        <section class="real_solution_sec">
            <div class="container">
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-lg-12">
                        <div class="about_text heading_hp text-center">
                            <h6></h6>
                            <h2>{{ $home['section_4_title'] ?? 'title' }}</h2>

                            <h4> {!! $home['section_4_description'] ?? 'description' !!}</h4>
                        </div>
                    </div>
                </div>
                <div class="">
                    @foreach ($our_organizations as $our_organization)
                        
                            <div class="article card-4 mb-5">
                                <div class="card-body">
                                    <div class="card-corner">
                                        <a href="single-3.html" class="arrow-box">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M13.75 6.75L19.25 12L13.75 17.25" stroke="#0E0E0F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M19 12H4.75" stroke="#0E0E0F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </a>
                                        <div class="curve-one"></div>
                                        <div class="curve-two"></div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-lg-7">
                                        <div class="position-relative card-img hover-effect-1" style="position: relative; overflow: hidden; border-radius: 16px; cursor: pointer;">
                                            <div class="card-img-top">
                                                <a href="{{ route('service', $our_organization->slug) }}">
                                                    <img src="{{ Storage::url($our_organization->image) }}" alt="">
                                                </a>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="col-lg-5">
                                        <h4>
                                            <a href="{{ route('service', $our_organization->slug) }}">{{ $our_organization->name }}</a>
                                        </h4>
                                        <p class="word-litmit card-text" style="font-weight: 400;">
                                               {!! $our_organization->description !!}
                                            </p>
                                            </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (count($testimonials) > 0)
        <section class="testimonial_sec">
            <div class="tp-testimonial-area tp-testimonial-bg position-relative">
                <div class="tp-testimonial-global">
                    <img alt="" class="global-img" style="color:transparent" src="{{ asset('frontend_assets/images/global.png') }}">
                    <img alt="" class="overlay-img" style="color:transparent" src="{{ asset('frontend_assets/images/overlay.png') }}">
                </div>
            </div>
            
            
            <div class="container">
                <div class="row align-items-center justify-content-center mb-5 mx-0">
                    <div class="col-lg-6 px-0">
                        <div class="about_text heading_hp text-center text_white position-relative">
                            <h6></h6>
                            <h2>{{ $home['section_5_title'] ?? 'title' }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial_slider">
                @foreach ($testimonials as $key => $item)
                    <div class="client">
                        <div class="testimonial_box testimonial_box_1">
                            <div class="d-flex">
                                <div class="client_img">
                                    <img src="{{ Storage::url($item->image) }}" alt="">
                                </div>
                                <h2>{{ $item->name ?? 'N/A' }}<span>{{ $item->address ?? 'N/A' }}</span></h2>
                            </div>                            
                            <div class="client-text">                                
                                <div class="srlt" id="">
                                    {!! $item->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_2">
                            <div class="d-flex">
                                <div class="client_img">
                                    <img src="{{ Storage::url($item->image) }}" alt="">
                                </div>
                                <h2>{{ $item->name ?? 'N/A' }}<span>{{ $item->address ?? 'N/A' }}</span></h2>
                            </div>                            
                            <div class="client-text">                                
                                <div class="srlt" id="">
                                    {!! $item->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_3">
                            <div class="d-flex">
                                <div class="client_img">
                                    <img src="{{ Storage::url($item->image) }}" alt="">
                                </div>
                                <h2>{{ $item->name ?? 'N/A' }}<span>{{ $item->address ?? 'N/A' }}</span></h2>
                            </div>                            
                            <div class="client-text">                                
                                <div class="srlt" id="">
                                    {!! $item->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_4">
                            <div class="d-flex">
                                <div class="client_img">
                                    <img src="{{ Storage::url($item->image) }}" alt="">
                                </div>
                                <h2>{{ $item->name ?? 'N/A' }}<span>{{ $item->address ?? 'N/A' }}</span></h2>
                            </div>                            
                            <div class="client-text">                                
                                <div class="srlt" id="">
                                    {!! $item->description !!}
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
            
        </section>
    @endif

    <!-- @if (count($galleries) > 0)
        <section class="gallery_sec margin_27">
            <div class="gallery_slider">
                @foreach ($galleries as $galary)
                    <div class="gallery_box" style="width: 100%; display: inline-block;">
                        <img src="{{ Storage::url($galary->image) }}" alt="">
                    </div>
                @endforeach
            </div>
        </section>
    @endif -->
@endsection

@push('scripts')
@endpush
