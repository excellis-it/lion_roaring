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
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('checkout') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Checkout</h2>
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
                <input type="hidden" name="credit_card_percentage"
                    value="{{ $estoreSettings ? $estoreSettings->credit_card_percentage : 0 }}" id="credit_card_percentage">

                {{-- Existing Order Details --}}
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
                                            placeholder="Address Line 1"
                                            value="{{ auth()->user()->location_address ?? '' }}">
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
                                        <input type="text" class="form-control bg-light" name="pincode" id="pincode"
                                            placeholder="ZIP" value="{{ auth()->user()->location_zip ?? '' }}" readonly>
                                        <label for="pincode">ZIP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control " name="city" id="city"
                                            placeholder="City/District"
                                            value="{{ auth()->user()->defaultDeliveryAddress?->city ?? '' }}">
                                        <label for="city">City/District</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control bg-light" name="state" id="state"
                                            placeholder="State" value="{{ auth()->user()->location_state ?? '' }}"
                                            readonly>
                                        <label for="state">State</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control bg-light" name="country"
                                            id="country" placeholder="Country"
                                            value="{{ auth()->user()->location_country ?? '' }}" readonly>
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
                                        <input class="form-check-input order-method-radio" type="radio"
                                            name="order_method" id="delivery" value="0" checked>
                                        <label class="form-check-label" for="delivery">Delivery</label>
                                    </div>
                                    @if ($estoreSettings && $estoreSettings->is_pickup_available)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input order-method-radio" type="radio"
                                                name="order_method" id="pickup" value="1">
                                            <label class="form-check-label" for="pickup">Pickup</label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="bill_details">
                                <h4>Bill Details</h4>
                                <div class="bill_text">
                                    {{-- Cart items --}}
                                    @foreach ($cartItems as $item)
                                        <ul>
                                            <li>{{ $item['product_name'] }} ({{ $item['quantity'] }}x)</li>
                                            @php
                                                $new_subtotal = $item['price'] * $item['quantity'];
                                            @endphp
                                            <li>${{ number_format($new_subtotal, 2) }}</li>
                                        </ul>
                                        @if ($item['other_charges'] > 0)
                                            <ul class="text-muted small">
                                                <li style="padding-left: 15px;">• Additional charges</li>
                                                <li>${{ number_format($item['other_charges'], 2) }}</li>
                                            </ul>
                                        @endif
                                    @endforeach

                                    <hr />

                                    <ul>
                                        <li>Subtotal</li>
                                        <li id="subtotal-amount" data-value="{{ $subtotal }}">
                                            ${{ number_format($subtotal, 2) }}
                                        </li>
                                    </ul>

                                    @if (isset($appliedPromoCode) && $appliedPromoCode && $promoDiscount > 0)
                                        <ul class="text-success">
                                            <li>Promo Discount ({{ $appliedPromoCode }})</li>
                                            <li id="promo-discount-amount" data-value="{{ $promoDiscount }}">
                                                -${{ number_format($promoDiscount, 2) }}
                                            </li>
                                        </ul>
                                    @endif

                                    @if (
                                        $estoreSettings &&
                                            ($estoreSettings->shipping_cost > 0 ||
                                                (is_array($estoreSettings->shipping_rules) && count($estoreSettings->shipping_rules) > 0)))
                                        <ul id="shipping-cost-row"
                                            style="{{ request('order_method') == 1 && $estoreSettings->is_pickup_available ? 'display: none;' : '' }}">
                                            <li>Shipping Cost</li>
                                            <li id="shipping-amount" data-value="{{ $shippingCost }}">
                                                ${{ number_format($shippingCost, 2) }}
                                            </li>
                                        </ul>
                                    @endif

                                    @if (
                                        $estoreSettings &&
                                            ($estoreSettings->delivery_cost > 0 ||
                                                (is_array($estoreSettings->shipping_rules) && count($estoreSettings->shipping_rules) > 0)))
                                        <ul id="delivery-cost-row"
                                            style="{{ request('order_method') == 1 && $estoreSettings->is_pickup_available ? 'display: none;' : '' }}">
                                            <li>Handling Cost</li>
                                            <li id="delivery-amount" data-value="{{ $deliveryCost }}">
                                                ${{ number_format($deliveryCost, 2) }}
                                            </li>
                                        </ul>
                                    @endif

                                    @if ($estoreSettings && $estoreSettings->tax_percentage > 0)
                                        <ul>
                                            <li>Tax ({{ $estoreSettings->tax_percentage }}%)</li>
                                            <li id="tax-amount" data-value="{{ $taxAmount }}">
                                                ${{ number_format($taxAmount, 2) }}
                                            </li>
                                        </ul>
                                    @endif

                                    <!-- Credit Card Fee Row (hidden initially) -->
                                    <ul id="credit-card-fee-row" style="display: none;">
                                        <li>Credit Card Fee (<span
                                                id="cc-percent">{{ $estoreSettings->credit_card_percentage ?? 0 }}</span>%)
                                        </li>
                                        <li id="credit-card-fee">$0.00</li>
                                    </ul>

                                    <div class="total_payable">
                                        <div class="total_payable_l">Total Payable</div>
                                        <div class="total_payable_r" id="total-amount" data-base="{{ $total }}">
                                            ${{ number_format($total, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Method (credit/debit + card fields) --}}
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="cart_right p-4 border rounded shadow-sm bg-white">
                            <h5 class="mb-3 fw-bold">Payment Method</h5>

                            <!-- Payment Type -->
                            <div class="mb-3">
                                <label class="form-label d-block">Choose Payment Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                        id="credit_card" value="credit" checked>
                                    <label class="form-check-label" for="credit_card">Credit Card</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                        id="debit_card" value="debit">
                                    <label class="form-check-label" for="debit_card">Debit Card</label>
                                </div>
                            </div>

                            <!-- Stripe Card Element -->
                            <div id="card-element" class="form-control p-3"></div>
                            <div id="card-errors" class="text-danger mt-2"></div>

                            <!-- Submit -->
                            <div class="col-md-12 mt-3 text-end">
                                <button type="submit" class="red_btn text-center border-0" id="submit-payment">
                                    <span>PLACE ORDER</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const paymentRadios = document.querySelectorAll("input[name='payment_type']");
            const orderMethodRadios = document.querySelectorAll(".order-method-radio");

            const creditCardFeeRow = document.getElementById("credit-card-fee-row");
            const ccFeeElement = document.getElementById("credit-card-fee");
            const totalAmountElement = document.getElementById("total-amount");

            const creditCardPercentage = parseFloat(document.getElementById("credit_card_percentage").value) || 0;

            // Store base costs
            const costs = {
                subtotal: {{ $subtotal }},
                promoDiscount: {{ $promoDiscount ?? 0 }},
                tax: {{ $taxAmount }},
                isPickupAvailable: {{ $estoreSettings && $estoreSettings->is_pickup_available ? 'true' : 'false' }}
            };

            // Shipping rules from server
            const shippingRules = {!! json_encode($estoreSettings->shipping_rules ?? []) !!};
            const totalItems = {{ collect($cartItems)->sum('quantity') }};

            function findShippingForQty(qty) {
                if (!Array.isArray(shippingRules) || shippingRules.length === 0) {
                    // fallback to server-provided values present in the page when no rules
                    return {
                        shipping: {{ $shippingCost }},
                        delivery: {{ $deliveryCost }}
                    };
                }

                // sort by min_qty asc
                shippingRules.sort((a, b) => (a.min_qty || 0) - (b.min_qty || 0));
                for (let i = 0; i < shippingRules.length; i++) {
                    const r = shippingRules[i];
                    const min = parseInt(r.min_qty || 0, 10);
                    const max = (r.max_qty === null || r.max_qty === undefined || r.max_qty === '') ? null :
                        parseInt(r.max_qty, 10);
                    if (qty >= min && (max === null || qty <= max)) {
                        return {
                            shipping: parseFloat(r.shipping_cost || 0),
                            delivery: parseFloat(r.delivery_cost || 0)
                        };
                    }
                }

                return {
                    shipping: {{ $shippingCost }},
                    delivery: {{ $deliveryCost }}
                };
            }

            function calculateTotal() {
                const isPickup = document.querySelector(".order-method-radio:checked").value == "1";
                let shippingInfo = {
                    shipping: 0,
                    delivery: 0
                };

                if (!isPickup) {
                    shippingInfo = findShippingForQty(totalItems);
                    document.getElementById("shipping-cost-row")?.style.setProperty("display", "flex");
                    document.getElementById("delivery-cost-row")?.style.setProperty("display", "flex");

                    document.getElementById('shipping-amount').textContent = `$${shippingInfo.shipping.toFixed(2)}`;
                    document.getElementById('shipping-amount').setAttribute('data-value', shippingInfo.shipping);

                    document.getElementById('delivery-amount').textContent = `$${shippingInfo.delivery.toFixed(2)}`;
                    document.getElementById('delivery-amount').setAttribute('data-value', shippingInfo.delivery);
                } else {
                    document.getElementById("shipping-cost-row")?.style.setProperty("display", "none");
                    document.getElementById("delivery-cost-row")?.style.setProperty("display", "none");
                }

                const baseTotal = costs.subtotal - costs.promoDiscount + costs.tax + (isPickup ? 0 : (shippingInfo
                    .shipping + shippingInfo.delivery));

                let finalTotal = baseTotal;

                if (document.getElementById("credit_card").checked && creditCardPercentage > 0) {
                    const fee = (baseTotal * creditCardPercentage) / 100;
                    ccFeeElement.textContent = `$${fee.toFixed(2)}`;
                    creditCardFeeRow.style.display = "flex";
                    finalTotal += fee;
                } else {
                    creditCardFeeRow.style.display = "none";
                }

                totalAmountElement.textContent = `$${finalTotal.toFixed(2)}`;
            }

            // Attach listeners
            paymentRadios.forEach(radio => radio.addEventListener("change", calculateTotal));
            orderMethodRadios.forEach(radio => radio.addEventListener("change", calculateTotal));

            // Initial run
            calculateTotal();
        });
    </script>

    <script>
        const stripe = Stripe("{{ env('STRIPE_KEY') }}"); // Your Stripe Publishable Key
        const elements = stripe.elements();

        // Stripe Card Element
        const card = elements.create('card', {
            hidePostalCode: true
        });
        card.mount('#card-element');

        // Handle card errors
        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        $('#checkout-form').on('submit', async function(e) {
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

            $('#submit-payment').prop('disabled', true).find('span').text('Processing...');

            // Create payment method in Stripe
            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    name: $('#first_name').val() + ' ' + $('#last_name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    address: {
                        line1: $('#address_line_1').val(),
                        line2: $('#address_line_2').val(),
                        city: $('#city').val(),
                        state: $('#state').val(),
                        postal_code: $('#pincode').val(),
                        // country: $('#country').val().toUpperCase(), // must be ISO code (e.g. "FR")
                    }
                }
            });

            if (error) {
                $('#card-errors').text(error.message);
                $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                return;
            }

            // ✅ Correct way to detect debit/credit card
            const selectedType = $('input[name="payment_type"]:checked').val(); // "credit" or "debit"
            const detectedType = paymentMethod.card.funding; // will be "credit", "debit", or "prepaid"

            if (detectedType && selectedType !== detectedType) {
                toastr.error(
                    `This card is a ${detectedType} card, but you selected ${selectedType}. Please choose the correct type.`
                );
                $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                return;
            }

            // Send to backend for payment processing
            const formData = new FormData(this);
            formData.append('payment_method_id', paymentMethod.id);

            $.ajax({
                url: '{{ route('e-store.process-checkout') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        window.location.href = response.checkout_url;

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                    $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong!';
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON?.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                    $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                }
            });
        });
    </script>
@endpush
