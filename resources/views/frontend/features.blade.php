@extends('frontend.layouts.master')
@section('meta_title')
    <meta name="description" content="{{ $organization_center['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $organization_center['meta_keywords'] ?? '' }}">
    <meta name="title" content="{{ $organization_center['meta_title'] ?? '' }}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{ $organization_center['meta_title'] ?? $organization_center['name'] }}
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ $organization_center['banner_image'] ? Storage::url($organization_center['banner_image']) : '' }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{ $organization_center['name'] ?? '' }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about_sec">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="">
                        <div class="img1">
                            <img src="{{ Storage::url($organization_center->image) ?? 'https://via.placeholder.com/150' }}"
                                alt="">
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <h6>{{ $organization_center['name'] ?? 'title' }}</h6>
                        <p style="font-weight: 400;">
                            <strong>{!! $organization_center['description'] ?? '' !!}</strong></p>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
@endpush
