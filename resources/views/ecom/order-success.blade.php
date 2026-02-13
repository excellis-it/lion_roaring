@extends('ecom.layouts.master')
@section('title', 'Order Success')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('order-success') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Order Success</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="order-success-card text-center">
                        <div class="success-icon mb-4">
                            <i class="fa-solid fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="mb-3">Thank you for your order!</h2>
                        <p class="mb-4">Your order has been successfully placed and is being processed.</p>

                        <div class="order-details bg-light p-4 rounded mb-4">
                            <h4>Order Details</h4>
                            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                            <p><strong>Payment Status:</strong>
                                <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                            </p>
                            @if (!$orderHasDigitalProduct)
                                <p><strong>Order Status:</strong>
                                    <span class="badge bg-primary">{{ ucfirst($order->orderStatus->name ?? '-') }}</span>
                                </p>
                            @endif
                            @php
                                $statusSlug = optional($order->orderStatus)->slug;
                                $isFinalCancelled = in_array($statusSlug, ['cancelled', 'pickup_cancelled'], true);
                            @endphp
                            @if (!$orderHasDigitalProduct && $order->expected_delivery_date && !$order->is_pickup && !$isFinalCancelled)
                                <p><strong>Expected Delivery Date:</strong>
                                    {{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('M d, Y') }}
                                </p>
                            @endif
                        </div>

                        @if ($orderHasDigitalProduct)
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mt-4 text-start">
                                <div class="me-3">
                                    <i class="fa-solid fa-cloud-download-alt fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Digital Product Ready</h6>
                                    <p class="mb-0 small">Please go to your <a href="{{ route('e-store.my-orders') }}"
                                            class="fw-bold text-decoration-none">My Orders Page</a> to access and download
                                        your product.</p>
                                </div>
                            </div>
                        @endif

                        <div class="order-items mb-4">
                            <h5>Items Ordered</h5>
                            @foreach ($order->orderItems as $item)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        <small class="text-muted">(Qty: {{ $item->quantity }})</small>
                                    </div>
                                    <div>${{ number_format($item->total, 2) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('e-store.my-orders') }}" class="red_btn me-2">
                                <span>View My Orders</span>
                            </a>
                            <a href="{{ route('e-store') }}" class="red_btn">
                                <span>Continue Shopping</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
