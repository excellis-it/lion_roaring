@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($category['meta_description']) ? $category['meta_description'] : '' }}">
@endsection
@section('title')
    {{ isset($category['meta_title']) ? $category['meta_title'] : 'CART' }}
@endsection

@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>CHECKOUT</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="shopping_cart_sec">
        <div class="container">
            <div class="heading_hp mb-3">
                <h2>Deliver To</h2>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout_item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" placeholder="First Name">
                                    <label for="floatingInput">First Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" placeholder="Last Name">
                                    <label for="floatingInput">Last Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput"
                                        placeholder="Phone Number">
                                    <label for="floatingInput">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" placeholder="Mail ID">
                                    <label for="floatingInput">Mail ID</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput"
                                        placeholder="Address Line 1">
                                    <label for="floatingInput">Address Line 1</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput"
                                        placeholder="Address Line 2">
                                    <label for="floatingInput">Address Line 2</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" placeholder="Pincode">
                                    <label for="floatingInput">Pincode</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput"
                                        placeholder="City/District">
                                    <label for="floatingInput">City/District</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingState" placeholder="State">
                                    <label for="floatingState">State</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingCountry" placeholder="Country">
                                    <label for="floatingCountry">Country</label>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cart_right">

                        <div class="bill_details">
                            <h4>Bill Details</h4>
                            <div class="bill_text">
                                <ul>
                                    <li>Item Total</li>
                                    <li>$ 5300.00</li>
                                </ul>

                                <ul>
                                    <li>Net Item Total</li>
                                    <li>$ 5300.00</li>
                                </ul>

                                <hr />
                                <div class="total_payable">
                                    <div class="total_payable_l">Total Payable</div>
                                    <div class="total_payable_r">$ 4599.00</div>
                                </div>
                            </div>
                            <div class="by_con">
                                <div class="form-group">

                                </div>
                                <a class="red_btn w-100 text-center" href=""><span>PLACE ORDER</span></a>
                                {{-- <a class="red_btn w-100 mt-2" href="product.html"><span>Continue
                                        shopping</span></a> --}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script></script>
@endpush
