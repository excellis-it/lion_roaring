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



    <section class="banner__slider banner_sec">
        <div class="slider">
            <div class="slide">
                <a href="{{ route('details') }}" tabindex="0">
                    <div class="slide__img">
                        <video autoplay="" muted="" loop="" class="video_part" playsInline>
                            <source
                                src="{{ isset($home['banner_video']) ? Storage::url($home['banner_video']) : 'https://via.placeholder.com/150' }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
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
        <div class="v_text right_v">About Us</div>
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <h6>{{ $home['section_1_title'] ?? 'title' }}</h6>
                        <h2 class="text-start"> {{ $home['section_1_sub_title'] ?? 'title' }}</h2>
                        </div>
                    <div class="img_part">
                        <div class="img1">
                            <video autoplay muted loop style="width: 100%; height: 100%;">
                                <source
                                    src="{{ isset($home['section_1_video']) ? Storage::url($home['section_1_video']) : 'https://via.placeholder.com/150' }}"
                                    type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
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
    <!-- <section class="after_about after_about_hm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="abt-box-1">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="after_abt first_abt first_abt_1">
                                    <div class="row align-items-center justify-content-center flex-column">
                                        <div class="col-md-4">
                                            <div class="img_abt flex-fixed">
                                                <img src="{{ isset($home['section_2_left_image']) ? Storage::url($home['section_2_left_image']) : 'https://via.placeholder.com/150' }}"
                                                    alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="abt_text_white">
                                                <h4 class="flex-fixed">{{ $home['section_2_left_title'] ?? 'title' }}</h4>
                                                <div class="srl" id="srl_1">
                                                    <p><strong>
                                                            {!! $home['section_2_left_description'] ?? 'description' !!}
                                                        </strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="after_abt">
                                    <div class="row align-items-center justify-content-center flex-column">
                                        <div class="col-md-4">
                                            <div class="img_abt flex-fixed">
                                                <img src="{{ isset($home['section_2_right_image']) ? Storage::url($home['section_2_right_image']) : 'https://via.placeholder.com/150' }}"
                                                    alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="abt_text_white">
                                                <h4 class="flex-fixed">{{ $home['section_2_right_title'] ?? 'title' }}
                                                </h4>
                                                <div class="srl" id="srl_1">
                                                    <p><strong>
                                                            {!! $home['section_2_right_description'] ?? 'description' !!}
                                                        </strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    @if (count($our_governances) > 0)
        <section class="key_feature_sec">
            <div class="container">
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-lg-12">
                        <div class="about_text heading_hp text-center">
                            <h2>{{ $home['section_3_title'] ?? 'title' }}</h2>
                            <h6> {!! $home['section_3_description'] ?? 'descripiton' !!} </h6>
                        </div>
                    </div>
                </div>
                <div class="slid_bh">
                    @if (count($our_governances) > 0)
                        @foreach ($our_governances as $key => $our_governance)
                            <div class="padding_k">
                                <div class="{{ $key % 2 == 0 ? 'bounce_1' : 'bounce_2' }}">
                                    <div class="{{ $key % 2 == 0 ? 'one_cli' : 'one_cli1' }}">
                                        
                                        <div class="article card-5">
                                            <div class="post-link">
                                                <div class="hover-effect-1" style="">
                                                    <div class="position-relative card-img-top thumbnail">
                                                        <a href="{{ route('our-governance', $our_governance->slug) }}">
                                                            <img src="{{ asset('frontend_assets/images/d.png') }}" alt="" class="cover-image">
                                                        </a>
                                                        <a href="#" class="badge bg-1 fs-8">Director</a>
                                                    </div>
                                                </div>
                                                <div class="card-corner @@corner-bg-color no-border">
                                                    <a href="{{ route('our-governance', $our_governance->slug) }}" class="arrow-box">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M13.75 6.75L19.25 12L13.75 17.25" stroke="#0E0E0F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M19 12H4.75" stroke="#0E0E0F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </a>
                                                    <div class="curve-one"></div>
                                                    <div class="curve-two"></div>
                                                </div>
                                            </div>
                                                <div class="card-body">
                                                    <a href="{{ route('our-governance', $our_governance->slug) }}" class="hover-underline">
                                                        <h6 class="card-title mb-0">{{ $our_governance->name }}</h6>
                                                    </a>
                                                </div>
                                            
                                        </div>



                                    </div>
                                </div>


                                



                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </section>
    @endif


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
                <div class="row align-items-center justify-content-center row-cols-1 row-cols-lg-3 row-cols-md-2">
                    @foreach ($our_organizations as $our_organization)
                        <div class="col" data-aos="fade-up" data-aos-duration="1000">
                            <!-- <div class="tow_box_j">
                                <div class="row align-items-center justify-content-center">
                                    <div class="col-lg-12">
                                        <div class="solution_img">
                                            <a href="{{ route('service', $our_organization->slug) }}">
                                                <img src="{{ Storage::url($our_organization->image) }}" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="box_solution box_blue">
                                            <h4><a
                                                    href="{{ route('service', $our_organization->slug) }}">{{ $our_organization->name }}</a>
                                            </h4>
                                            <p class="word-litmit" style="font-weight: 400;">
                                                <strong>{!! $our_organization->description !!}</strong>
                                            </p>
                                            <a href="{{ route('our-organization', $our_organization->slug) }}"
                                                class="ellipss"><i class="fa-solid fa-ellipsis"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div> -->


                            <div class="article card-4">
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
                                    <div class="d-flex flex-column">
                                        <div class="position-relative card-img hover-effect-1" style="position: relative; overflow: hidden; border-radius: 16px; cursor: pointer;">
                                            <div class="card-img-top">
                                                <a href="{{ route('service', $our_organization->slug) }}">
                                                    <img src="{{ Storage::url($our_organization->image) }}" alt="">
                                                </a>
                                            </div>
                                        </div>
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
