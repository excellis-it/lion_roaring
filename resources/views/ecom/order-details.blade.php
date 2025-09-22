@extends('ecom.layouts.master')
@section('title', 'Order Details')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/slider-bg.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Order Details</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="order-details-card">
                        <!-- Order Header -->
                        <div class="order-header bg-light p-4 rounded mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Order #{{ $order->order_number }}</h4>
                                    <p class="mb-0">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span
                                        class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'primary') }} mb-1">
                                        {{ ucfirst($order->status) }}
                                    </span><br>
                                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        Payment {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($order->is_pickup == 1)
                            <div class="alert alert-info">
                                This order is marked for <strong>Pickup</strong>.
                            </div>
                            <!-- Pickup Address -->
                            <div class="shipping-address mb-4">
                                <h5>Pickup Address</h5>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>{{ $order->warehouse_name }}</strong></p>
                                    <p class="mb-1">{{ $order->warehouse_address }}</p>

                                </div>
                            </div>
                        @else
                            <!-- Shipping Address -->
                            <div class="shipping-address mb-4">
                                <h5>Shipping Address</h5>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>{{ $order->full_name }}</strong></p>
                                    <p class="mb-1">{{ $order->email }}</p>
                                    <p class="mb-1">{{ $order->phone }}</p>
                                    <p class="mb-0">{{ $order->full_address }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Order Items -->
                        <div class="order-items">
                            <h5>Items Ordered</h5>
                            @foreach ($order->orderItems as $item)
                                <div class="item-card border rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            @if ($item->warehouseProduct?->images->first())
                                                <img src="{{ Storage::url($item->warehouseProduct?->images->first()->image_path) }}"
                                                    alt="{{ $item->product->name }}" class="img-fluid rounded" />
                                            @elseif ($item->product_image)
                                                <img src="{{ Storage::url($item->product_image) }}"
                                                    alt="{{ $item->product_name }}" class="img-fluid rounded">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="fa-solid fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ $item->product_name }}</h6>
                                            <p class="text-muted mb-0">Quantity: {{ $item->quantity }}</p>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="mb-0">${{ number_format($item->price, 2) }}</p>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <p class="mb-0"><strong>${{ number_format($item->total, 2) }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <!-- Order Summary -->
                        <div class="bill_details">
                            <h4>Order Summary</h4>
                            <div class="bill_text">
                                <ul>
                                    <li>Subtotal</li>
                                    <li>${{ number_format($order->subtotal, 2) }}</li>
                                </ul>
                                {{-- <ul>
                                    <li>Tax</li>
                                    <li>${{ number_format($order->tax_amount, 2) }}</li>
                                </ul>
                                <ul>
                                    <li>Shipping</li>
                                    <li>${{ number_format($order->shipping_amount, 2) }}</li>
                                </ul> --}}
                                <hr />
                                <div class="total_payable">
                                    <div class="total_payable_l">Total</div>
                                    <div class="total_payable_r">${{ number_format($order->total_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        @if ($order->payments->count() > 0)
                            <div class="payment-info mt-4">
                                <h5>Payment Information</h5>
                                @foreach ($order->payments as $payment)
                                    <div class="bg-light p-3 rounded mb-2">
                                        <p class="mb-1"><strong>Method:</strong> {{ ucfirst($payment->payment_method) }}
                                        </p>
                                        <p class="mb-1"><strong>Status:</strong>
                                            <span
                                                class="badge bg-{{ $payment->status == 'succeeded' ? 'success' : 'warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </p>
                                        @if ($payment->paid_at)
                                            <p class="mb-0"><strong>Paid on:</strong>
                                                {{ $payment->paid_at->format('M d, Y \a\t h:i A') }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Actions Order Cancellation button with modal confirm with note area and cancel button -->
                        <div class="order-cancellation mt-4">
                            @if (
                                $order->status != 'cancelled' &&
                                    $order->status != 'delivered' &&
                                    $order->created_at->diffInDays(now()) <= optional($estoreSettings)->refund_max_days)
                                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#cancelOrderModal">
                                    Cancel Order
                                </button>

                                <!-- Cancel Order Modal -->
                                <div class="modal fade" id="cancelOrderModal" tabindex="-1"
                                    aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order
                                                    #{{ $order->order_number }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('e-store.cancel-order') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="cancellation_reason" class="form-label">Reason for
                                                            Cancellation (optional):</label>
                                                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3"></textarea>
                                                    </div>
                                                    <p>Are you sure you want to cancel this order?</p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="confirmCancellation" required>
                                                        <label class="form-check-label" for="confirmCancellation">
                                                            I confirm the cancellation of this order.
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($order->status == 'cancelled')
                            <div class="alert alert-danger mt-3">

                                @if ($order->refund_status === true)
                                    Your order has been cancelled and refunded.
                                @else
                                    Your order has been cancelled. Refund is being processed.
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="order-actions mt-4">
                            <a href="{{ route('e-store.my-orders') }}" class="red_btn w-100 mb-2">
                                <span>Back to Orders</span>
                            </a>
                            {{-- @if ($order->status == 'delivered')
                                <a href="#" class="btn btn-outline-secondary w-100">
                                    Download Invoice
                                </a>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
