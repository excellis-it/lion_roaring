@extends('frontend.layouts.master')
@section('meta_title')
    <meta name="description" content="{{ $our_governance['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $our_governance['meta_keywords'] ?? '' }}">
    <meta name="title" content="{{ $our_governance['meta_title'] ?? '' }}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{ $our_governance['meta_title'] ?? $our_governance['name'] }}
@endsection
@push('styles')
    <style>
        .governance-profile {
            padding: 80px 0;
            background: #fff;
            border-radius: 0 0 30px 30px;
        }

        .governance-profile .profile-image img {
            width: 100%;
            max-width: 350px;
            border-radius: 10px;
            object-fit: cover;
        }

        .governance-profile .profile-name {
            font-weight: 700;
            color: transparent;
            text-transform: uppercase;
            font-size: 25px;
            -webkit-text-stroke-width: 1px;
            -webkit-text-stroke-color: #000;
            stroke: #000;
            letter-spacing: 3px;
            margin-top: 15px;
            margin-bottom: 0;
        }

        .governance-profile .profile-description {
            font-size: 16px;
            color: #000;
            line-height: 30px;
        }

        .governance-profile .profile-description p {
            margin-bottom: 18px;
            text-align: justify;
            font-size: 16px !important;
            font-family: inherit !important;
            line-height: 30px !important;
            color: #000 !important;
        }

        .governance-profile .profile-description span {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: normal !important;
        }

        .governance-profile .profile-description o\:p {
            display: none;
        }
    </style>
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ $our_governance['banner_image'] ? Storage::url($our_governance['banner_image']) : '' }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{ $our_governance['name'] ?? 'title' }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="governance-profile">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-5 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="text-center">
                        <div class="profile-image">
                            <img src="{{ Storage::url($our_governance->image) ?? 'https://via.placeholder.com/150' }}"
                                alt="{{ $our_governance['name'] ?? '' }}">
                        </div>
                        <h6 class="profile-name">{{ $our_governance['name'] ?? 'title' }}</h6>
                    </div>
                </div>

                <div class="col-lg-8 col-md-7" data-aos="fade-up" data-aos-duration="1000">
                    <div class="profile-description">
                        @php
                            $description = $our_governance['description'] ?? '';
                            // Strip Word/Office specific tags
                            $description = preg_replace('/<o:p[^>]*>.*?<\/o:p>/is', '', $description);
                            // Remove MsoNormal class
                            $description = str_replace('class="MsoNormal"', '', $description);
                            // Strip inline style attributes to let CSS handle styling
                            $description = preg_replace('/\sstyle="[^"]*"/i', '', $description);
                        @endphp
                        {!! $description !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
