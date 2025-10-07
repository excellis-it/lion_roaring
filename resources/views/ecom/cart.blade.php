@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($category['meta_description']) ? $category['meta_description'] : '' }}">
@endsection
@section('title')
    {{ isset($category['meta_title']) ? $category['meta_title'] : 'CART' }}
@endsection

@push('styles')
    <style>
        .qty-input .qty-count--minus,
        .qty-input .qty-count--add {
            border: 1px solid #181818;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            font-size: 22px;
            background-color: transparent;
        }
    </style>
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('cart') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>CART</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec add-to-cart-sec">
        <div class="container">
            <div class="heading_hp mb-3">
                {{-- <h2>Shopping Cart ({{ count($carts) }} items)</h2> --}}
            </div>

            @if (count($carts) > 0)
                @if (isset($hasChanges) && $hasChanges)
                    <div class="alert alert-warning">
                        Some items in your cart were sold out. Please review before
                        proceeding to checkout.
                    </div>
                @endif
                <div class="row">
                    <div class="col-lg-8">
                        <div id="cart-items-container">
                            @foreach ($carts as $item)
                                <div class="cart_item cart-item" data-cart-id="{{ $item->id }}">
                                    <div class="cart_product">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="cart_images">
                                                    @if (isset($item->warehouseProduct) && $item->warehouseProduct?->images->first())
                                                        <img src="{{ Storage::url($item->warehouseProduct?->images->first()->image_path) }}"
                                                            alt="{{ $item->product->name ?? '' }}" />
                                                    @elseif (isset($item->product->warehouseProduct) && $item->product->warehouseProduct)
                                                        <img src="{{ Storage::url($item->product->main_image) }}"
                                                            alt="{{ $item->product->name ?? '' }}" />
                                                    @else
                                                        <img src="{{ asset('ecom_assets/images/main-bg-pattern.png') }}"
                                                            alt="Product Image" />
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="cart_text">
                                                    <h4>{{ $item->product->name ?? '' }}</h4>
                                                    <h6>SKU: {{ $item->warehouseProduct->sku ?? '' }}</h6>
                                                    <!-- <h6>{{ $item->size ? 'Size: ' . $item->size?->size ?? '' : '' }}
                                                            &nbsp;&nbsp;
                                                            {{ $item->color ? 'Color: ' . $item->color?->color_name ?? '' : '' }}
                                                        </h6> -->
                                                    {{-- <span class="">{!! \Illuminate\Support\Str::limit($item->product->description, 50) !!}</span> --}}

                                                    <ul class="wl_price mb-1">
                                                        <li>Unit Price</li>
                                                        <li class="ms-auto">
                                                            @php
                                                                $displayPrice =
                                                                    $item->price ??
                                                                    ($item->warehouseProduct->price ?? 0);
                                                            @endphp
                                                            @if (isset($item->meta['price_changed']) && $item->meta['price_changed'])
                                                                <span style="text-decoration: none;"
                                                                    class="fw-bold text-dark">{{ number_format($item->meta['current_price'], 2) }}</span>
                                                                <span
                                                                    class="text-decoration-line-through text-muted">{{ number_format($item->meta['original_price'], 2) }}</span>
                                                            @else
                                                                {{ number_format($displayPrice, 2) }}
                                                            @endif
                                                        </li>
                                                    </ul>

                                                    @if (isset($item->meta['price_changed']) && $item->meta['price_changed'])
                                                        <div class="text-warning small mb-2">Price updated</div>
                                                    @endif
                                                    @if (isset($item->meta['out_of_stock']) && $item->meta['out_of_stock'])
                                                        <div class="text-danger small mb-2">This item is currently out of
                                                            stock.</div>
                                                    @endif

                                                    <!-- Display other charges if any -->
                                                    @if (isset($item->product->otherCharges) && $item->product->otherCharges->count() > 0)
                                                        @foreach ($item->product->otherCharges as $otherCharge)
                                                            <ul class="wl_price">
                                                                <li>{{ $otherCharge->charge_name }}</li>
                                                                <li class="ms-auto">
                                                                    ${{ number_format($otherCharge->charge_amount, 2) }}
                                                                </li>
                                                            </ul>
                                                        @endforeach
                                                    @endif



                                                    <div class="d-flex justify-content-between final_price">
                                                        <div class="left_p_text">
                                                            <h4>Subtotal</h4>
                                                        </div>
                                                        <div class="right_p_text">
                                                            <h4 class="item-subtotal">
                                                                ${{ number_format($item->subtotal, 2) }}</h4>
                                                        </div>
                                                    </div>

                                                    <div class="row justify-content-between align-items-center mt-2 mb-3">
                                                        <div class="col-md-8">
                                                            <div class="qty d-flex align-items-center">
                                                                <span>Qty</span>
                                                                <div class="qty-input mx-2" style="border: none;">
                                                                    @if (isset($item->meta['out_of_stock']) && $item->meta['out_of_stock'])
                                                                        <input class="cart-quantity product-qty"
                                                                            type="number" value="0" disabled>
                                                                    @else
                                                                        <button class="cart-qty-count qty-count--minus"
                                                                            data-action="minus" type="button">-</button>
                                                                        <input class="cart-quantity product-qty"
                                                                            type="number" min="1"
                                                                            max="{{ $item->warehouseProduct->quantity ?? 0 }}"
                                                                            value="{{ $item['quantity'] }}"
                                                                            data-id="{{ $item['id'] }}"
                                                                            data-price="{{ $item['price'] }}">
                                                                        <button class="cart-qty-count qty-count--add"
                                                                            data-action="add" type="button">+</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 text-end">
                                                            <div class="d-flex align-items-center justify-content-end">
                                                                <div>
                                                                    <a href="javascript:void(0);"
                                                                        class="edit_lens remove_lens cart-remove-from-cart"
                                                                        data-id="{{ $item['id'] }}">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('e-store.all-products') }}" class="red_btn">
                                <span>Continue Shopping</span>
                            </a>
                            <a href="javascript:void(0);" class="red_btn cart-clear-cart ms-2">
                                <span>Clear Cart</span>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="cart_right">
                            <!-- Promo Code Section -->
                            <div class="bill_details mb-3">
                                <h4>Promo Code</h4>
                                <div class="promo-code-section">
                                    @if (isset($appliedPromoCode) && $appliedPromoCode)
                                        <div class="applied-promo" id="applied-promo-section">
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

                            <div class="bill_details">
                                <h4>Bill Details</h4>
                                <div class="bill_text">
                                    <ul>
                                        <li>Items Total</li>
                                        <li id="cart-total">${{ number_format($total, 2) }}</li>
                                    </ul>

                                    @if (isset($appliedPromoCode) && $appliedPromoCode && $promoDiscount > 0)
                                        <ul class="text-success">
                                            <li>Promo Discount ({{ $appliedPromoCode }})</li>
                                            <li id="promo-discount">-${{ number_format($promoDiscount, 2) }}</li>
                                        </ul>
                                    @endif

                                    <div class="total_payable">
                                        <div class="total_payable_l">Total Payable</div>
                                        <div class="total_payable_r" id="final-total">
                                            ${{ number_format($total - ($promoDiscount ?? 0), 2) }}</div>
                                    </div>
                                </div>

                                <div class="by_con">
                                    <div class="form-group">
                                        <input type="checkbox" id="terms_agreement" required>
                                        <label for="terms_agreement">By continuing, I agree to the <a
                                                href="{{ route('terms-and-conditions') }}">Terms of use</a>
                                            & <a href="{{ route('privacy-policy') }}">Privacy Policy</a></label>
                                    </div>

                                    {{-- // if not auth then login button  --}}
                                    @if (Auth::check())
                                        <a class="red_btn w-100 checkout-btn text-center"
                                            href="{{ route('e-store.checkout') }}"
                                            style="pointer-events: none; opacity: 0.5;">
                                            <span>Proceed to Checkout</span>
                                        </a>
                                    @else
                                        <a class="red_btn w-100 mt-2 text-center" href="javascript:void(0);"
                                            data-bs-toggle="modal" data-bs-target="#loginModalEstore">
                                            <span>Login to Checkout</span>
                                        </a>
                                    @endif


                                    {{-- <a class="red_btn w-100 mt-2 text-center" href="{{ route('e-store.all-products') }}">
                                        <span>Continue Shopping</span>
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="empty-cart text-center py-5">
                            <i class="fa-solid fa-shopping-cart"
                                style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <h3>Your cart is empty</h3>
                            <p class="mb-4">Looks like you haven't added any items to your cart yet.</p>
                            <a href="{{ route('e-store.all-products') }}" class="red_btn">
                                <span>Start Shopping</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Enable/disable checkout button based on terms agreement
            $('#terms_agreement').change(function() {
                if ($(this).is(':checked')) {
                    $('.checkout-btn').css({
                        'pointer-events': 'auto',
                        'opacity': '1'
                    });
                } else {
                    $('.checkout-btn').css({
                        'pointer-events': 'none',
                        'opacity': '0.5'
                    });
                }
            });

            $(document).on('click', '.cart-qty-count', function() {
                var $this = $(this);
                var $input = $this.siblings('.cart-quantity');
                var currentVal = parseInt($input.val());
                var action = $this.data('action');
                var minVal = parseInt($input.attr('min')) || 1;
                var maxVal = parseInt($input.attr('max')) || 9999;
                var newVal = currentVal;

                // Handle add button click
                if (action === 'add') {
                    // Don't exceed max value
                    newVal = Math.min(currentVal + 1, maxVal);

                    // Disable add button if at max
                    if (newVal >= maxVal) {
                        $this.attr('disabled', true);
                        toastr.warning('No more stock available on that item');
                    }

                    // Enable minus button if above min
                    if (newVal > minVal) {
                        $this.siblings('.qty-count--minus').attr('disabled', false);
                    }
                }
                // Handle minus button click
                else if (action === 'minus') {
                    // Don't go below min value
                    newVal = Math.max(currentVal - 1, minVal);

                    // Disable minus button if at min
                    if (newVal <= minVal) {
                        $this.attr('disabled', true);
                    }

                    // Enable add button if below max
                    if (newVal < maxVal) {
                        $this.siblings('.qty-count--add').attr('disabled', false);
                    }
                }

                // Only update if value has changed
                if (newVal !== currentVal) {
                    $input.val(newVal);
                    updateCartItem($input);
                }
            });
            // Also update the change handler to respect max values
            $(document).on('change', '.cart-quantity', function() {
                var $this = $(this);
                var currentVal = parseInt($this.val());
                var minVal = parseInt($this.attr('min')) || 1;
                var maxVal = parseInt($this.attr('max')) || 9999;

                // Enforce min/max constraints
                if (isNaN(currentVal) || currentVal < minVal) {
                    $this.val(minVal);
                    $this.siblings('.qty-count--minus').attr('disabled', true);
                    $this.siblings('.qty-count--add').attr('disabled', false);
                } else if (currentVal > maxVal) {
                    $this.val(maxVal);
                    $this.siblings('.qty-count--add').attr('disabled', true);
                    $this.siblings('.qty-count--minus').attr('disabled', false);
                    //  toastr.warning('No more stock available');
                } else {
                    $this.siblings('.qty-count--minus').attr('disabled', currentVal <= minVal);
                    $this.siblings('.qty-count--add').attr('disabled', currentVal >= maxVal);
                }

                updateCartItem($this);
            });

            function updateCartItem($input) {
                var cartId = $input.data('id');
                var quantity = parseInt($input.val());
                var $cartItem = $input.closest('.cart-item');

                if (quantity < 1) {
                    quantity = 1;
                    $input.val(1);
                }

                $.ajax({
                    url: window.cartRoutes.updateCart,
                    type: 'POST',
                    data: {
                        id: cartId,
                        quantity: quantity,
                        _token: window.csrfToken
                    },
                    success: function(response) {
                        if (response.status) {
                            // Reload the page to get updated calculations from server
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update cart');
                    }
                });
            }

            function calculateTotals() {
                var total = 0;
                $('.item-subtotal').each(function() {
                    var subtotalText = $(this).text().replace('$', '').replace(',', '');
                    total += parseFloat(subtotalText) || 0;
                });

                $('#cart-total').text('$' + total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#final-total').text('$' + total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            // Remove item from cart with SweetAlert2
            $(document).on('click', '.cart-remove-from-cart', function() {
                var cartId = $(this).data('id');
                var $cartItem = $(this).closest('.cart-item');

                Swal.fire({
                    title: 'Remove Item?',
                    text: 'Are you sure you want to remove this item from your cart?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: window.cartRoutes.removeFromCart,
                            type: 'POST',
                            data: {
                                id: cartId,
                                _token: window.csrfToken
                            },
                            success: function(response) {
                                if (response.status) {
                                    $cartItem.fadeOut(300, function() {
                                        $(this).remove();

                                        // Check if cart is empty
                                        if ($('.cart-item').length === 0) {
                                            location.reload();
                                        } else {
                                            calculateTotals();
                                            updateCartCount();
                                        }
                                    });

                                    Swal.fire({
                                        title: 'Removed!',
                                        text: 'Item has been removed from your cart.',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('Failed to remove item from cart');
                            }
                        });
                    }
                });
            });

            // Clear entire cart with SweetAlert2
            $(document).on('click', '.cart-clear-cart', function() {
                Swal.fire({
                    title: 'Clear Cart?',
                    text: 'Are you sure you want to clear your entire cart? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, clear cart!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: window.cartRoutes.clearCart,
                            type: 'POST',
                            data: {
                                _token: window.csrfToken
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: 'Cart Cleared!',
                                        text: 'Your cart has been cleared successfully.',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('Failed to clear cart');
                            }
                        });
                    }
                });
            });

            // Apply promo code
            $('#apply-promo-btn').on('click', function() {
                const promoCode = $('#promo-code-input').val().trim();

                if (!promoCode) {
                    $('#promo-message').html(
                    '<small class="text-danger">Please enter a promo code</small>');
                    return;
                }

                $('#apply-promo-btn').prop('disabled', true).text('Applying...');
                $('#promo-message').empty();

                $.ajax({
                    url: '{{ route('e-store.apply-promo-code') }}',
                    type: 'POST',
                    data: {
                        promo_code: promoCode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            // Reload page to update totals
                            location.reload();
                        } else {
                            $('#promo-message').html('<small class="text-danger">' + response
                                .message + '</small>');
                        }
                    },
                    error: function() {
                        $('#promo-message').html(
                            '<small class="text-danger">Error applying promo code</small>');
                    },
                    complete: function() {
                        $('#apply-promo-btn').prop('disabled', false).text('Apply');
                    }
                });
            });

            // Remove promo code
            $('#remove-promo-btn').on('click', function() {
                $.ajax({
                    url: '{{ route('e-store.remove-promo-code') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('Error removing promo code');
                    }
                });
            });

            // Allow Enter key to apply promo code
            $('#promo-code-input').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#apply-promo-btn').click();
                }
            });

            function updateCartCount() {
                $.ajax({
                    url: window.cartRoutes.cartCount,
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            $('.cart_count').text(response.cartCount);
                        }
                    }
                });
            }
        });
    </script>
@endpush
