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
        style="background-image: url({{ asset('ecom_assets/images/banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>CART</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec">
        <div class="container">
            <div class="heading_hp mb-3">
                <h2>Shopping Cart ({{ count($carts) }} items)</h2>
            </div>

            @if (count($carts) > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <div id="cart-items-container">
                            @foreach ($carts as $item)
                                <div class="cart_item cart-item" data-cart-id="{{ $item->id }}">
                                    <div class="cart_product">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="cart_images">
                                                    @if ($item->product->main_image)
                                                        <img src="{{ Storage::url($item->product->main_image) }}"
                                                            alt="{{ $item->product->name }}" />
                                                    @else
                                                        <img src="{{ asset('ecom_assets/images/product3.jpg') }}"
                                                            alt="Product Image" />
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="cart_text">
                                                    <h4>{{ $item->product->name }}</h4>
                                                    <h6>{{ $item->size ? 'Size: ' . $item->size?->size ?? '' : '' }}
                                                        &nbsp;&nbsp;
                                                        {{ $item->color ? 'Color: ' . $item->color?->color_name ?? '' : '' }}
                                                    </h6>
                                                    {{-- <span class="">{!! \Illuminate\Support\Str::limit($item->product->description, 50) !!}</span> --}}

                                                    <ul class="wl_price">
                                                        <li>Unit Price</li>
                                                        <li class="ms-auto">${{ number_format($item->product->price, 2) }}
                                                        </li>
                                                    </ul>


                                                    @foreach ($item->product->otherCharges as $otherCharge)
                                                        <ul class="wl_price">
                                                            <li>{{ $otherCharge->charge_name }}</li>
                                                            <li class="ms-auto">
                                                                ${{ number_format($otherCharge->charge_amount, 2) }}</li>
                                                        </ul>
                                                    @endforeach


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
                                                                    <button class="cart-qty-count qty-count--minus"
                                                                        data-action="minus" type="button">-</button>
                                                                    <input class="cart-quantity product-qty" type="number"
                                                                        min="1" max="10"
                                                                        value="{{ $item['quantity'] }}"
                                                                        data-id="{{ $item['id'] }}"
                                                                        data-price="{{ $item['price'] }}">
                                                                    <button class="cart-qty-count qty-count--add"
                                                                        data-action="add" type="button">+</button>
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
                            <div class="bill_details">
                                <h4>Bill Details</h4>
                                <div class="bill_text">
                                    <ul>
                                        <li>Items Total</li>
                                        <li id="cart-total">${{ number_format($total, 2) }}</li>
                                    </ul>



                                    <hr />
                                    <div class="total_payable">
                                        <div class="total_payable_l">Total Payable</div>
                                        <div class="total_payable_r" id="final-total">
                                            ${{ number_format($total, 2) }}</div>
                                    </div>
                                </div>

                                <div class="by_con">
                                    <div class="form-group">
                                        <input type="checkbox" id="terms_agreement" required>
                                        <label for="terms_agreement">By continuing, I agree to the <a
                                                href="{{ route('terms-and-conditions') }}">Terms of use</a>
                                            & <a href="{{ route('privacy-policy') }}">Privacy Policy</a></label>
                                    </div>
                                    <a class="red_btn w-100 checkout-btn" href="{{ route('e-store.checkout') }}"
                                        style="pointer-events: none; opacity: 0.5;">
                                        <span>Proceed to Checkout</span>
                                    </a>
                                    <a class="red_btn w-100 mt-2" href="{{ route('e-store.all-products') }}">
                                        <span>Continue Shopping</span>
                                    </a>
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

            // Update cart quantity with +/- buttons
            $(document).on('click', '.cart-qty-count', function() {
                var $this = $(this);
                var $input = $this.siblings('.cart-quantity');
                var currentVal = parseInt($input.val());
                var action = $this.data('action');
                var newVal = currentVal;

                if (action === 'add') {
                    newVal = currentVal + 1;
                } else if (action === 'minus' && currentVal > 1) {
                    newVal = currentVal - 1;
                }

                if (newVal !== currentVal) {
                    $input.val(newVal);
                    updateCartItem($input);
                }
            });

            // Update cart when quantity input changes
            $(document).on('change', '.cart-quantity', function() {
                updateCartItem($(this));
            });

            function updateCartItem($input) {
                var cartId = $input.data('id');
                var quantity = parseInt($input.val());
                var price = parseFloat($input.data('price'));
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
                            // Update subtotal for this item
                            var subtotal = price * quantity;
                            $cartItem.find('.item-subtotal').text('$' + subtotal.toFixed(2));

                            // Recalculate totals
                            calculateTotals();
                            updateCartCount();
                            //  toastr.success('Cart updated successfully');
                            setTimeout(() => {
                                calculateTotals();
                            }, 1000);
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
                    var subtotalText = $(this).text().replace('$', '');
                    total += parseFloat(subtotalText);
                });

                var finalTotal = total;

                $('#cart-total').text('$' + total.toFixed(2));
                $('#final-total').text('$' + finalTotal.toFixed(2));
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
                                        calculateTotals();
                                        updateCartCount();

                                        // Check if cart is empty
                                        if ($('.cart-item').length === 0) {
                                            location.reload();
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
