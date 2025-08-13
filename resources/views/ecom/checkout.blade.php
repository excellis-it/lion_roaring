@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($category['meta_description']) ? $category['meta_description'] : '' }}">
@endsection
@section('title')
    {{ isset($category['meta_title']) ? $category['meta_title'] : 'CHECKOUT' }}
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
            <form id="checkout-form">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="checkout_item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="first_name" id="first_name"
                                            placeholder="First Name" value="{{ auth()->user()->first_name }}">
                                        <label for="first_name">First Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="last_name" id="last_name"
                                            placeholder="Last Name" value="{{ auth()->user()->last_name ?? '' }}">
                                        <label for="last_name">Last Name</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="phone" id="phone"
                                            placeholder="Phone Number" value="{{ auth()->user()->phone ?? '' }}">
                                        <label for="phone">Phone Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control bg-light" name="email" id="email"
                                            placeholder="Mail ID" value="{{ auth()->user()->email }}" readonly>
                                        <label for="email">Mail ID</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="address_line_1" id="address_line_1"
                                            placeholder="Address Line 1" value="{{ auth()->user()->address ?? '' }}">
                                        <label for="address_line_1">Address Line 1</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="address_line_2" id="address_line_2"
                                            placeholder="Address Line 2" value="{{ auth()->user()->address2 ?? '' }}">
                                        <label for="address_line_2">Address Line 2</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="pincode" id="pincode"
                                            placeholder="ZIP" value="{{ auth()->user()->zip ?? '' }}">
                                        <label for="pincode">ZIP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="city" id="city"
                                            placeholder="City/District" value="{{ auth()->user()->city ?? '' }}">
                                        <label for="city">City/District</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="state" id="state"
                                            placeholder="State" value="{{ auth()->user()->state ?? '' }}">
                                        <label for="state">State</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="country" id="country"
                                            placeholder="Country" value="{{ auth()->user()->countries?->name ?? '' }}">
                                        <label for="country">Country</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="cart_right">
                            <div class="bill_details">
                                <h4>Order Method</h4>
                                <div class="bill_text mt-3">

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="order_method"
                                            id="delivery" value="0" checked>
                                        <label class="form-check-label" for="delivery">
                                            Delivery
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="order_method"
                                            id="pickup" value="1">
                                        <label class="form-check-label" for="pickup">
                                            Pickup
                                        </label>
                                    </div>



                                </div>

                            </div>
                            <div class="bill_details">
                                <h4>Bill Details</h4>
                                <div class="bill_text">
                                    @foreach ($cartItems as $item)
                                        <ul>
                                            <li>{{ $item['product_name'] }} ({{ $item['quantity'] }}x)</li>
                                            <li>${{ number_format($item['subtotal'], 2) }}</li>
                                        </ul>
                                        @if ($item['other_charges'] > 0)
                                            <ul class="text-muted small">
                                                <li style="padding-left: 15px;">• Additional charges</li>
                                                <li>${{ number_format($item['other_charges'], 2) }}</li>
                                            </ul>
                                        @endif
                                    @endforeach

                                    <hr />
                                    <div class="total_payable">
                                        <div class="total_payable_l">Total Payable</div>
                                        <div class="total_payable_r">${{ number_format($total, 2) }}</div>
                                    </div>
                                </div>
                                <div class="by_con">
                                    <button type="submit" class="red_btn w-100 text-center" id="submit-payment">
                                        <span>PLACE ORDER</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#checkout-form').on('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            let missingFields = [];
            if (!$('#first_name').val().trim()) missingFields.push('First Name');
            if (!$('#last_name').val().trim()) missingFields.push('Last Name');
            if (!$('#phone').val().trim()) missingFields.push('Phone Number');
            if (!$('#email').val().trim()) missingFields.push('Mail ID');
            if (!$('#address_line_1').val().trim()) missingFields.push('Address Line 1');
            if (!$('#pincode').val().trim()) missingFields.push('ZIP');
            if (!$('#city').val().trim()) missingFields.push('City/District');
            if (!$('#state').val().trim()) missingFields.push('State');
            if (!$('#country').val().trim()) missingFields.push('Country');

            if (missingFields.length > 0) {
                toastr.error('Please fill in the following fields: ' + missingFields.join(', '));
                return;
            }

            // Show loading
            $('#submit-payment').prop('disabled', true).find('span').text('Processing...');

            // Submit form data to create order and get Stripe checkout session
            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('e-store.process-checkout') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        // Redirect to Stripe checkout
                        window.location.href = response.checkout_url;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                        $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors)[0][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                    $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                }
            });

            setTimeout(() => {
                $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
            }, 6000);
        });
    </script>
@endpush
