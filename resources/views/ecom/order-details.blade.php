@extends('ecom.layouts.master')
@section('title', 'Order Details')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('order-details') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
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
                    <div class="order-details-card p-4">
                        <!-- Order Header -->
                        <div class="order-header bg-light rounded mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Order #{{ $order->order_number }}</h4>
                                    <p class="mb-0">Placed on
                                        {{ \Carbon\Carbon::parse($order->created_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                                    </p>
                                    @if ($order->status == 4 && $order->payment_status == 'paid')
                                        <a href="{{ route('user.store-orders.invoice', $order->id) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> Download Invoice
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-6 text-end">
                                    <span
                                        class="badge bg-{{ $order->status == 4 ? 'success' : ($order->status == 5 ? 'danger' : 'primary') }} mb-1">
                                        {{ ucfirst($order->orderStatus->name ?? '-') }}
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
                                <div class="bg-light rounded">
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
                            @php
                                $all_other_charges = [];
                                $subtotal = 0;
                            @endphp
                            @foreach ($order->orderItems as $item)
                                @php
                                    $charges = $item->other_charges ? json_decode($item->other_charges, true) : [];
                                    $otherChargesTotal = 0;

                                    if (!empty($charges)) {
                                        foreach ($charges as $charge) {
                                            $otherChargesTotal += floatval($charge['charge_amount'] ?? 0);
                                        }
                                    }

                                    $all_other_charges[] = [
                                        'product_name' => $item->product_name,
                                        'quantity' => $item->quantity,
                                        'subtotal' => $item->price * $item->quantity,
                                        'other_charges' => $otherChargesTotal,
                                    ];
                                    $subtotal += $item->total;
                                @endphp
                                <div class="item-card border rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            @if ($item->product_image)
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
                                        @php
                                            // Calculate total for this item
                                            $itemTotal = $item->price * $item->quantity;
                                        @endphp
                                        <div class="col-md-2 text-end">
                                            <p class="mb-0"><strong>${{ number_format($itemTotal, 2) }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary p-4">
                        <!-- Order Summary -->
                        <div class="bill_details">
                            <h4>Order Summary</h4>
                            <div class="bill_text">

                                {{-- <ul class="list-unstyled mb-2 d-flex justify-content-between">
                                    <li>Subtotal</li>
                                    <li>${{ number_format($order->subtotal, 2) }}</li>
                                </ul> --}}

                                @foreach ($all_other_charges as $item)
                                    <ul class="list-unstyled mb-1 d-flex justify-content-between">
                                        <li>{{ $item['product_name'] }} ({{ $item['quantity'] }}x)</li>

                                        <li>${{ number_format($item['subtotal'], 2) }}</li>
                                    </ul>

                                    @if ($item['other_charges'] > 0)
                                        <ul class="list-unstyled mb-1 d-flex justify-content-between text-muted small">
                                            <li style="padding-left: 15px;">• Additional charges</li>
                                            <li>${{ number_format($item['other_charges'], 2) }}</li>
                                        </ul>
                                    @endif
                                @endforeach

                                <hr />

                                <ul>
                                    <li>Subtotal</li>
                                    <li id="subtotal-amount">
                                        ${{ number_format($subtotal, 2) }}
                                    </li>
                                </ul>

                                @if ($order->promo_discount && $order->promo_discount > 0)
                                    <ul class="list-unstyled mb-2 d-flex justify-content-between">
                                        <li>
                                            Promo Discount
                                            @if ($order->promo_code)
                                                (Code: {{ $order->promo_code }})
                                            @endif
                                        </li>
                                        <li>- ${{ number_format($order->promo_discount, 2) }}</li>
                                    </ul>
                                @endif

                                @if ($order->tax_amount && $order->tax_amount > 0)
                                    <ul class="list-unstyled mb-2 d-flex justify-content-between">
                                        <li>Tax</li>
                                        <li>${{ number_format($order->tax_amount, 2) }}</li>
                                    </ul>
                                @endif

                                @if ($order->shipping_amount && $order->shipping_amount > 0)
                                    <ul class="list-unstyled mb-2 d-flex justify-content-between">
                                        <li>Shipping</li>
                                        <li>${{ number_format($order->shipping_amount, 2) }}</li>
                                    </ul>
                                @endif


                                @if ($order->shipping_amount && $order->credit_card_fee > 0)
                                    <ul class="list-unstyled mb-2 d-flex justify-content-between">
                                        <li>Credit Card Fee:</li>
                                        <li>${{ number_format($order->credit_card_fee, 2) }}</li>
                                    </ul>
                                @endif




                                <div class="total_payable mt-3 border-top pt-2 d-flex justify-content-between fw-bold">
                                    <div>Total</div>
                                    <div>${{ number_format($order->total_amount, 2) }}</div>
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
                                                {{ \Carbon\Carbon::parse($payment->paid_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Actions Order Cancellation button with modal confirm with note area and cancel button -->
                        <div class="order-cancellation mt-4">

                            @if ($order->status == 3)
                                <button type="button" class="btn btn-outline-dark w-100" disabled>
                                    Order that has been shipped can't be cancelled.
                                </button>
                            @endif

                            @if (
                                $order->status != 3 &&
                                    $order->status != 4 &&
                                    $order->status != 5 &&
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

                        @if ($order->status == 5)
                            <div class="alert alert-danger mt-3">

                                @if ($payment->status == 'refunded')
                                    Your order has been cancelled and refunded.
                                @else
                                    Your order has been cancelled. Refund is being processed.
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="order-actions mt-2">
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

            <div class="row mt-3">
                <div class="col-md-8">
                    @php
                        $labels = [
                            // optional overrides for label text; otherwise use $status->name
                            'pending' => 'Ordered',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ];
                    @endphp

                    <div class="delevry-sumry shadow p-3">
                        <div class="info-del mb-3">
                            <h4 class="mb-1">Order Tracking</h4>
                            <p class="mb-0">Order <strong>#{{ $order->order_number }}</strong></p>
                        </div>

                        <div class="d-position mb-4">
                            <ul class="list-unstyled d-flex flex-wrap gap-3" style="row-gap:1.5rem;">
                                @foreach ($timelineStatuses as $idx => $status)
                                    @php
                                        $reached = $idx <= $statusIndex;
                                        $isCurrent = $idx === $statusIndex;
                                        $cancelled = ($status->slug ?? '') === 'cancelled';
                                        $colorClass = $cancelled
                                            ? 'btn-danger'
                                            : ($reached
                                                ? ($status->slug === 'delivered'
                                                    ? 'btn-success'
                                                    : ($isCurrent
                                                        ? 'btn-primary'
                                                        : 'btn-secondary'))
                                                : 'btn-outline-secondary');
                                        $label = $labels[$status->slug] ?? ($status->name ?? ucfirst($status->slug));
                                    @endphp

                                    <li class="text-center" style="min-width:90px;">
                                        <span
                                            class="btn {{ $colorClass }} rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:42px;height:42px;">
                                            @if ($reached)
                                                <i class="fa-solid fa-check"></i>
                                            @else
                                                <i class="fa-solid fa-ellipsis"></i>
                                            @endif
                                        </span>
                                        <p
                                            class="mb-0 mt-2 small fw-semibold {{ $isCurrent ? 'text-primary' : 'text-muted' }}">
                                            {{ $label }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @php
                            $createdAt = $order->created_at;
                            // if you have a status history table use that; otherwise approximate
                            $deliveredAt = $order->expected_delivery_date ?? null;
                        @endphp

                        <div class="d-details">
                            @if ($deliveredAt && $order->status != 5 && $order->status != 4)
                                <div
                                    style="margin-top:15px;margin-bottom:5px; padding:10px 15px; border-left:4px solid #0d6efd; background:#f8f9fa; border-radius:5px; display:inline-block;">
                                    <h6 style="margin:0; font-size:14px; color:#495057;">
                                        <strong style="color:#0d6efd;">Expected Delivery Date:</strong>
                                        <span style="font-weight:bold; color:#212529;">
                                            {{ \Carbon\Carbon::parse($deliveredAt)->timezone(auth()->user()->time_zone)->format('M d, Y') }}
                                        </span>
                                    </h6>
                                </div>
                            @endif

                            <h5 class="h6 mb-1">Current Status: <span
                                    class="fw-bold">{{ $order->orderStatus->name ?? ucfirst($order->status) }}</span>
                            </h5>
                            <p class="mb-2 small text-muted">Last updated
                                {{ \Carbon\Carbon::parse($order->updated_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                            </p>
                            <ul class="list-unstyled small mb-0">
                                {{-- {{ auth()->user()->time_zone }} <br>
                                {{ \Carbon\Carbon::parse($createdAt)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }} --}}
                                <li>Placed:
                                    <strong>{{ \Carbon\Carbon::parse($createdAt)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}</strong>
                                </li>

                                @if (($order->orderStatus->slug ?? $order->status) === 'cancelled')
                                    <li class="text-danger">Order was cancelled.</li>
                                @endif
                            </ul>
                        </div>
                    </div>



                </div>

            </div>

        </div>
    </section>
@endsection
