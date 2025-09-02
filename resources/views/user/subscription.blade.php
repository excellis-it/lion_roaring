@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Member Subscription
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
                        {{-- <h1>Privacy Policy</h1> --}}
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
                        <div class="text-center w-100 mb-4">
                            <h2>Member Subscription</h2>
                        </div>
                        <div class="pricing-div">
                            <div class="row justify-content-center">
                                @if (count($plans) > 0)
                                    @foreach ($plans as $plan)
                                    <div class="col-xl-3 col-md-6 mb-3">
                                        <div class="pricing-div-box">
                                            <div class="pricing-rate d-flex justify-content-center position-relative">
                                                <h3>${{$plan->plan_price}}</h3>
                                            </div>
                                            <h4>{{$plan->plan_name}}</h4>
                                            <p>
                                                {{$plan->plan_description}}

                                            </p>
                                            <div class="month-div">

                                                <div class="pricing-rate d-flex justify-content-center">
                                                    <h5>  {{$plan->plan_validity}} Month
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="">
                                                <button class="red_btn payment-button"
                                                    data-route="{{ route('user.subscription.payment', $plan->id) }}"><span>Subscribe</span></button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-xl-12">
                                        <div class="alert alert-danger text-center">
                                            No subscription plans found.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.payment-button').click(function() {
            var route = $(this).data('route');
            window.location.href = route;
        });
    });
</script>
@endpush
