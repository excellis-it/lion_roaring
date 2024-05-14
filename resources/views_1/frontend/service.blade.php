@extends('frontend.layouts.master')
@section('meta_title')
    <meta name="description" content="{{ $our_organization['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $our_organization['meta_keywords'] ?? '' }}">
    <meta name="title" content="{{ $our_organization['meta_title'] ?? '' }}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{ $our_organization['meta_title'] ?? $our_organization['name'] }}
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
                        <h1>{{ $our_organization['name'] ?? 'title' }}</h1>
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
                            <img src="{{ Storage::url($our_organization->image) ?? 'https://via.placeholder.com/150' }}"
                                alt="">
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <h6>{{ $our_organization['name'] ?? 'title' }}</h6>
                        <p style="font-weight: 400;">
                            <strong>{{ $our_organization['description'] ?? 'description' }}</strong>
                        </p>
                    </div>
                </div>
                @if (count($services) > 0)
                    <div class="col-xl-12 col-lg-12 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                        <div class="about_text heading_hp text_white">
                            @foreach ($services as $key => $value)
                                <div class="{{ $key % 2 == 0 ? 'right_img_single' : 'left_img_single' }}">
                                    <img src="{{ Storage::url($value->image) }}" alt="">
                                </div>
                                {!! $value->content !!}
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </section>
@endsection

@push('scripts')
@endpush
