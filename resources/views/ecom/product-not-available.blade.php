@extends('ecom.layouts.master')
@section('title', 'Product Not Available')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/slider-bg.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Product Not Available</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec">
        <div class="container">
            <div class="row justify-content-center">
                <h1>Product Not Available at Your Area</h1>
                <p>We're sorry, but the product you're looking for is not available at this time</p>
            </div>
        </div>
    </section>

@endsection
