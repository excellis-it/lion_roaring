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
    <section class="banner__slider banner_sec">
        <div class="slider">
            <div class="slide">
                <a href="{{route('details')}}" tabindex="0">
                    <div class="slide__img">
                        <video autoplay="" muted="" loop="" class="video_part">
                            <source src="{{ isset($home['banner_video']) ? Storage::url($home['banner_video']) : 'https://via.placeholder.com/150' }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <!-- <img src="" alt="" class="full-image d-block d-md-none" /> -->
                        <img src="{{ isset($home['banner_image']) ? Storage::url($home['banner_image']) : 'https://via.placeholder.com/150' }}"
                            class="full-image overlay-image">
                    </div>
                </a>
                <div class="slide__content slide__content__left">
                    <div class="slide__content--headings text-left">
                        <h1 class="title">{{ $home['banner_title'] ?? 'title' }}</h1>
                        <p class="top-title"></p>
                        <!--<a class="red_btn slidebottomleft" href=""><span>get started</span></a>-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about_sec"
        style="background:url({{ asset('frontend_assets/uploads/2023/05/Mask-group-2.jpg') }}); background-repeat: no-repeat; background-size: cover;">
        <div class="v_text right_v">About Us</div>
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="img_part">
                        <div class="img1">
                            <video controls="" style="width: 100%; height: 100%;">
                                <source
                                    src="{{ isset($home['section_1_video']) ? Storage::url($home['section_1_video']) : 'https://via.placeholder.com/150' }}"
                                    type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <h6>{{ $home['section_1_title'] ?? 'title' }}</h6>
                        <h2> {{ $home['section_1_sub_title'] ?? 'title' }}</h2>
                        <p style="font-weight: 400;">
                            <strong>{{ $home['section_1_description'] ?? 'description' }}</strong>
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
                                                <h4 class="flex-fixed">{{ $home['section_2_right_title'] ?? 'title' }}</h4>
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
    </section>
    @if (count($our_governances) > 0)
        <section class="key_feature_sec">
            <div class="v_text right_v feature_v"></div>
            <div class="key_bg">
                <img src="{{ asset('frontend_assets/images/line.png') }}" alt="">
            </div>
            <div class="container">
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-lg-8">
                        <div class="about_text heading_hp text-center">
                            <h2>{{ $home['section_3_title'] ?? 'title' }}</h2>
                            <h6> {{ $home['section_3_description'] ?? 'descripiton' }} </h6>
                        </div>
                    </div>
                </div>
                <div class="slid_bh">
                    @if (count($our_governances) > 0)
                        @foreach ($our_governances as $key => $our_governance)
                            <div class="padding_k">
                                <div class="{{($key % 2 == 0 ) ? 'bounce_1' : 'bounce_2' }}">
                                    <div class="{{($key % 2 == 0 ) ? 'one_cli' : 'one_cli1' }}">
                                        <div class="one_cli_nh">
                                            <img src="{{ asset('frontend_assets/images/before_n.png') }}" alt="">
                                        </div>
                                        <div class="clild_box">
                                            <div class="clild_sec">
                                                <img src="{{ Storage::url($our_governance->image) ?? 'https://via.placeholder.com/150' }}"
                                                    alt="">
                                                <h4>{{ $our_governance->name }}</h4>

                                            </div>
                                            <a href="{{ route('our-governance', $our_governance->slug) }}"
                                                class="ellipss_right" tabindex="-1">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </a>
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
            <div class="v_text left_v">Service</div>
            <div class="container">
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-lg-6">
                        <div class="about_text heading_hp text-center text_white">
                            <h6></h6>
                            <h2>{{ $home['section_4_title'] ?? 'title' }}</h2>

                            <h4>  {{ $home['section_4_description'] ?? 'description' }}</h4>
                        </div>
                    </div>
                </div>
                <div class="row g-0 align-items-center justify-content-center row-cols-1 row-cols-lg-3 row-cols-md-2">
                    @foreach ($our_organizations as $our_organization)
                        <div class="col" data-aos="fade-up" data-aos-duration="1000">
                            <div class="tow_box_j">
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
                                                <strong>{{ $our_organization->description }}</strong>
                                            </p>
                                            <a href="{{ route('our-organization', $our_organization->slug) }}"
                                                class="ellipss"><i class="fa-solid fa-ellipsis"></i></a>
                                        </div>
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
            <div class="v_text left_v">TESTIMONIES</div>
            <div class="container">
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-lg-6">
                        <div class="about_text heading_hp text-center text_white">
                            <h6></h6>
                            <h2>{{ $home['section_5_title'] ?? 'title' }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-xl-8 col-lg-10">
                        <div class="testimonial_slider">
                            @foreach ($testimonials as $key => $item)
                                <div class="client">
                                    <div class="testimonial_box {{ $key % 2 == 0 ? '' : 'testimonial_box_2' }}">
                                        <div class="client_img">
                                            <img src="{{ Storage::url($item->image) }}" alt="">
                                        </div>
                                        <div class="client-text">
                                            <h2>{{ $item->name ?? 'N/A' }}<span>{{ $item->address ?? 'N/A' }}</span></h2>
                                            <div class="srlt" id="">
                                                {!! $item->description !!}
                                            </div>
                                            <div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (count($galleries) > 0)
        <section class="gallery_sec margin_27">
            <div class="gallery_slider">
                @foreach ($galleries as $galary)
                    <div class="gallery_box" style="width: 100%; display: inline-block;">
                        <img src="{{ Storage::url($galary->image) }}" alt="">
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
@endpush
