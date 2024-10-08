@extends('frontend.layouts.master')
@section('meta_title')
    <meta name="description" content="{{ $principleAndBusiness['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $principleAndBusiness['meta_keywords'] ?? '' }}">
    <meta name="title" content="{{ $principleAndBusiness['meta_title'] ?? '' }}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{ $principleAndBusiness['meta_title'] ?? 'Principle and Business Model' }}
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ isset($principleAndBusiness['banner_image']) ? Storage::url($principleAndBusiness['banner_image']) : '' }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{ $principleAndBusiness['banner_title'] ?? 'title' }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="login-sec">
        <div class="container">
            <div class="row align-items-center justify-content-center">



                <div class="col-xl-10 col-lg-12 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                    <div class="lion_aducation">
                        @if ($principle_images->count() > 0)
                            <div class="row">
                                @foreach ($principle_images as $item)
                                            <img src="{{ isset($item->image) ? Storage::url($item->image) : '' }}" alt="">
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="about_text heading_hp">
                        {!! $principleAndBusiness['description'] ?? 'Description' !!}
                        {!! $principleAndBusiness['description1'] ?? 'Description' !!}
                        {!! $principleAndBusiness['description2'] ?? 'Description' !!}
                        {!! $principleAndBusiness['description3'] ?? 'Description' !!}
                        {!! $principleAndBusiness['description4'] ?? 'Description' !!}
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection

@push('scripts')
@endpush
