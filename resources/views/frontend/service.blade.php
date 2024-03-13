@extends('frontend.layouts.master')
@section('meta_title')
    <meta name="description" content="{{ $service['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $service['meta_keywords'] ?? '' }}">
    <meta name="title" content="{{ $service['meta_title'] ?? '' }}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{ $service['meta_title'] ?? $service['name'] }}
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('frontend_assets/uploads/2023/07/inner_banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{ $service['name'] ?? 'title' }}</h1>
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
                            <img src="{{ Storage::url($service->image) ?? 'https://via.placeholder.com/150' }}"
                                alt="">
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <h6>{{ $service['name'] ?? 'title' }}</h6>
                        <p style="font-weight: 400;">
                            <strong>{{ $service['description'] ?? 'description' }}</strong></p>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
@endpush
