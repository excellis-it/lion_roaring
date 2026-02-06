@extends('ecom.layouts.master')
@section('title', ' Tracking')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('order-tracking') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Tracking</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="common-padd">
        <div class="container">
            <div class="heading_hp pe-0 pe-lg-5">
                <h2 class="text-white">Track your order</h2>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="order-id-box">
                        <form method="GET" action="{{ route('e-store.order-tracking') }}" class="track-order-form">
                            <div class="mb-3">
                                <input type="text" name="order_number" value="{{ request('order_number') }}"
                                    class="form-control" placeholder="Enter your order number (e.g. ORD-XXXXXX)" required>
                            </div>
                            <button type="submit" class="w-100 red_btn border-0"><span>Track Order <i
                                        class="fa-solid fa-arrow-right"></i></span></button>
                        </form>
                        @if (request()->filled('order_number') && !isset($order))
                            <div class="alert alert-danger mt-3 mb-0 py-2">No order found for number
                                <strong>{{ request('order_number') }}</strong>.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-7">
                    @if (isset($order))
                        @php
                            $statusSlug = optional($order->orderStatus)->slug;
                            $isFinalDelivered = in_array($statusSlug, ['delivered', 'pickup_picked_up'], true);
                            $isFinalCancelled = in_array($statusSlug, ['cancelled', 'pickup_cancelled'], true);
                            $labels = $order->is_pickup
                                ? [
                                    'pickup_pending' => 'Ordered',
                                    'pickup_processing' => 'Processing',
                                    'pickup_ready_for_pickup' => 'Ready for Pickup',
                                    'pickup_picked_up' => 'Picked Up',
                                    'pickup_cancelled' => 'Cancelled',
                                ]
                                : [
                                    'pending' => 'Ordered',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'out_for_delivery' => 'Out for Delivery',
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
                                            $cancelled = in_array(
                                                $status->slug ?? '',
                                                ['cancelled', 'pickup_cancelled'],
                                                true,
                                            );
                                            $colorClass = $cancelled
                                                ? 'btn-danger'
                                                : ($reached
                                                    ? (in_array($status->slug, ['delivered', 'pickup_picked_up'], true)
                                                        ? 'btn-success'
                                                        : ($isCurrent
                                                            ? 'btn-primary'
                                                            : 'btn-secondary'))
                                                    : 'btn-outline-secondary');
                                            $label =
                                                $labels[$status->slug] ?? ($status->name ?? ucfirst($status->slug));
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
                                @if ($deliveredAt && !$order->is_pickup)
                                    <div
                                        style="margin-top:15px;margin-bottom:5px; padding:10px 15px; border-left:4px solid #0d6efd; background:#f8f9fa; border-radius:5px; display:inline-block;">
                                        <h6 style="margin:0; font-size:14px; color:#495057;">
                                            <strong style="color:#0d6efd;">Expected Delivery Date:</strong>
                                            <span style="font-weight:bold; color:#212529;">
                                                {{ \Carbon\Carbon::parse($deliveredAt)->format('M d, Y') }}
                                            </span>
                                        </h6>
                                    </div>
                                @endif

                                <h5 class="h6 mb-1">Current Status: <span
                                        class="fw-bold">{{ $order->orderStatus->name ?? ucfirst($order->status) }}</span>
                                </h5>
                                <p class="mb-2 small text-muted">Last updated
                                    {{ $order->updated_at->format('M d, Y h:i A') }}
                                </p>
                                <ul class="list-unstyled small mb-0">
                                    <li>Placed: <strong>{{ $createdAt->format('M d, Y h:i A') }}</strong></li>
                                    @if (in_array($order->orderStatus->slug ?? '', ['cancelled', 'pickup_cancelled'], true))
                                        <li class="text-danger">Order was cancelled.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="delevry-sumry p-4 text-center text-muted" style="border:1px dashed #555;">
                            <p class="mb-0">Enter your order number to view tracking details.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>


@endsection
