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
                        <h2>Digital Checkout</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="shopping_cart_sec">
        <div class="container">
            <div class="heading_hp mb-3">
                <h2 id="change-text">Billing Details</h2>
            </div>
            <form id="checkout-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="credit_card_percentage"
                    value="{{ $estoreSettings ? $estoreSettings->credit_card_percentage : 0 }}" id="credit_card_percentage">

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
                                        <input type="text" class="form-control bg-light" name="state"
                                            id="state" placeholder="State"
                                            value="{{ auth()->user()->location_state ?? '' }}" readonly>
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
                            @if ($total > 0)
                                <div class="bill_details mb-3">
                                    <h4>Promo Code</h4>
                                    <div class="promo-code-section">
                                        @if (isset($appliedPromoCode) && $appliedPromoCode)
                                            <div class="applied-promo" id="applied-promo-section"
                                                data-code="{{ $appliedPromoCode }}"
                                                data-discount="{{ $promoDiscount ?? 0 }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-success">
                                                        <i class="fa-solid fa-check"></i> {{ $appliedPromoCode }}
                                                    </span>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        id="remove-promo-btn">
                                                        Remove
                                                    </button>
                                                </div>
                                                <small class="text-success">Discount:
                                                    ${{ number_format($promoDiscount ?? 0, 2) }}</small>
                                            </div>
                                        @else
                                            <div class="promo-input-section" id="promo-input-section">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="promo-code-input"
                                                        placeholder="Enter promo code">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="apply-promo-btn">
                                                        Apply
                                                    </button>
                                                </div>
                                                <div id="promo-message" class="mt-2"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="bill_details">
                                <h4>Order Summary</h4>
                                <div class="bill_text mt-3">
                                    {{-- Product Details --}}
                                    <ul>
                                        <li>{{ $product->name }} (1x)</li>
                                        <li>${{ number_format($product->price, 2) }}</li>
                                    </ul>

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
                                            <li id="promo-discount-amount" data-discount="{{ $promoDiscount }}">
                                                -${{ number_format($promoDiscount, 2) }}</li>
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
                <div class="row mt-4" id="payment-method-container" style="{{ $total > 0 ? '' : 'display: none;' }}">
                    <div class="col-lg-12">
                        <div class="cart_right p-4 border rounded shadow-sm bg-white">
                            <h5 class="mb-3 fw-bold">Payment Method</h5>

                            <!-- Payment Type -->
                            <div class="mb-3">
                                <label class="form-label d-block">Choose Payment Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                        id="credit_card" value="credit" {{ $total > 0 ? 'checked' : '' }}>
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
                        </div>
                    </div>
                </div>

                {{-- Terms and Submit --}}
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="cart_right p-4 border rounded shadow-sm bg-white">
                            <!-- Terms and Conditions -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms_agreement"
                                        id="terms_agreement" value="1" required>
                                    <label class="form-check-label" for="terms_agreement">
                                        By continuing, I agree to the <a
                                            href="{{ route('e-store.cms-page', 'terms-and-conditions') }}"
                                            target="_blank">Terms and Conditions</a>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-md-12 mt-3  d-flex align-item-center justify-content-end gap-2">
                                <button type="submit" class="red_btn text-center border-0" id="submit-payment">
                                    <span id="submit-text">{{ $total > 0 ? 'PLACE ORDER' : 'GET IT FREE' }}</span>
                                </button>
                                {{-- add a cancel to back to product page --}}
                                <a href="{{ route('e-store.product-details', $product->slug) }}"
                                    class="red_btn text-center border-0 "><span>Cancel</span></a>
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

            const creditCardFeeRow = document.getElementById("credit-card-fee-row");
            const ccFeeElement = document.getElementById("credit-card-fee");
            const totalAmountElement = document.getElementById("total-amount");

            const creditCardPercentage = parseFloat(document.getElementById("credit_card_percentage").value) || 0;

            // Store base costs
            const costs = {
                subtotal: {{ $subtotal }},
                original_tax: {{ ($product->price * ($estoreSettings->tax_percentage ?? 0)) / 100 }},
                tax_percent: {{ $estoreSettings->tax_percentage ?? 0 }}
            };


            function calculateTotal() {
                const subtotal = costs.subtotal;
                const promoDiscount = parseFloat($('#promo-discount-amount').data('discount')) || 0;

                const taxableAmount = Math.max(subtotal - promoDiscount, 0);
                const taxAmount = (taxableAmount * costs.tax_percent) / 100;

                // Update UI for tax
                const taxElement = document.getElementById("tax-amount");
                if (taxElement) {
                    taxElement.textContent = `$${taxAmount.toFixed(2)}`;
                }

                const baseTotal = taxableAmount + taxAmount;

                let finalTotal = baseTotal;

                if (baseTotal > 0) {
                    if (document.getElementById("credit_card").checked && creditCardPercentage > 0) {
                        const fee = (baseTotal * creditCardPercentage) / 100;
                        ccFeeElement.textContent = `$${fee.toFixed(2)}`;
                        creditCardFeeRow.style.display = "flex";
                        finalTotal += fee;
                    } else {
                        creditCardFeeRow.style.display = "none";
                    }
                    document.getElementById("payment-method-container").style.display = "block";
                    document.getElementById("submit-text").textContent = "PLACE ORDER";
                } else {
                    creditCardFeeRow.style.display = "none";
                    document.getElementById("payment-method-container").style.display = "none";
                    document.getElementById("submit-text").textContent = "GET IT FREE";
                }

                totalAmountElement.textContent = `$${finalTotal.toFixed(2)}`;
                totalAmountElement.setAttribute('data-current-total', finalTotal);
            }

            // Promo Code Handlers
            $(document).on('click', '#apply-promo-btn', function() {
                const promoCode = $('#promo-code-input').val().trim();
                if (!promoCode) {
                    toastr.error('Please enter a promo code');
                    return;
                }

                $('#apply-promo-btn').prop('disabled', true).text('Applying...');

                $.ajax({
                    url: '{{ route('e-store.digital.apply-promo-code') }}',
                    method: 'POST',
                    data: {
                        promo_code: promoCode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            location.reload(); // Simplest way to refresh all totals and UI
                        } else {
                            toastr.error(response.message);
                            $('#apply-promo-btn').prop('disabled', false).text('Apply');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to apply promo code');
                        $('#apply-promo-btn').prop('disabled', false).text('Apply');
                    }
                });
            });

            $(document).on('click', '#remove-promo-btn', function() {
                $.ajax({
                    url: '{{ route('e-store.digital.remove-promo-code') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    }
                });
            });

            // Attach listeners
            paymentRadios.forEach(radio => radio.addEventListener("change", calculateTotal));

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

            if (!$('#terms_agreement').is(':checked')) {
                toastr.error('You must agree to the Terms and Conditions');
                return;
            }

            if (missingFields.length > 0) {
                toastr.error('Please fill in the following fields: ' + missingFields.join(', '));
                return;
            }

            const totalAmountElement = document.getElementById("total-amount");
            const currentTotal = parseFloat(totalAmountElement.getAttribute('data-current-total')) || 0;
            let paymentMethodId = null;

            if (currentTotal > 0) {
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

                if (detectedType && (selectedType === 'credit' && detectedType === 'debit' || selectedType ===
                        'debit' && detectedType === 'credit')) {
                    toastr.error(
                        `This card is a ${detectedType} card, but you selected ${selectedType}. Please choose the correct type.`
                    );
                    $('#submit-payment').prop('disabled', false).find('span').text('PLACE ORDER');
                    return;
                }

                paymentMethodId = paymentMethod.id;
            } else {
                $('#submit-payment').prop('disabled', true).find('span').text('Placing Order...');
            }

            // Send to backend for payment processing
            const formData = new FormData(this);
            if (paymentMethodId) {
                formData.append('payment_method_id', paymentMethodId);
            } else {
                formData.append('payment_type', 'free');
                formData.append('payment_method_id', 'free');
            }

            $.ajax({
                url: '{{ route('e-store.process-digital-checkout') }}',
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
