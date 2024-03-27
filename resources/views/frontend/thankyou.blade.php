@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - DONATION
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
                        <h1>DONATION</h1>
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
                        <div class="asp-thank-you-page-wrap">
                            <div class="asp-thank-you-page-msg-wrap">
                                <p class="asp-thank-you-page-msg1">Thank you for your payment.</p>
                                <hr>
                                <div class="asp-thank-you-page-total-amount">Total Amount: ${{Session::get('donation_amount')}}</div><br>
                                <div class="asp-thank-you-page-txn-id">Transaction ID: {{Session::get('transaction_id')}}</div>
                                <style>
                                    .asp-thank-you-page-msg-wrap {
                                        background: #dff0d8;
                                        border: 1px solid #C9DEC1;
                                        margin: 10px 0px;
                                        padding: 15px;
                                    }
                                </style>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
