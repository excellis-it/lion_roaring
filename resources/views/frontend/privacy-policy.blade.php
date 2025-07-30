@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{$privacy_policy['text'] ?? 'Privacy Policy'}}
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{asset('frontend_assets/uploads/2023/07/inner_banner.jpg')}}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{$privacy_policy['text'] ?? 'Privacy Policy'}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="login-sec">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-10 col-lg-12 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp">
                        {!!  $privacy_policy['description'] ?? 'No centers available' !!}
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection

@push('scripts')
@endpush
