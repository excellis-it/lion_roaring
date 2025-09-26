@extends('ecom.layouts.master')
@section('title', ' Tracking')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('order-tracking') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2> Tracking</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="common-padd">
        <div class="container">
            <div class="heading_hp pe-0 pe-lg-5">
                <h2 class="text-white">Track you order</h2>
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
                            $progression = ['pending', 'processing', 'shipped', 'delivered'];
                            $labels = [
                                'pending' => 'Ordered',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ];
                            $currentStatus = $order->status;
                            if ($currentStatus === 'cancelled') {
                                $timeline = ['pending', 'cancelled'];
                            } else {
                                $timeline = $progression;
                            }
                            $statusIndex = array_search($currentStatus, $timeline);
                            if ($statusIndex === false) {
                                $timeline[] = $currentStatus;
                                $statusIndex = array_search($currentStatus, $timeline);
                            }
                        @endphp
                        <div class="delevry-sumry p-3 shadow">
                            <div class="info-del mb-3">
                                <h4 class="mb-1">Order #{{ $order->order_number }}</h4>
                                <p class="mb-0 small text-muted">Placed {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="d-position mb-4">
                                <ul class="list-unstyled d-flex flex-wrap gap-3" style="row-gap:1.5rem;">
                                    @foreach ($timeline as $idx => $st)
                                        @php
                                            $reached = $idx <= $statusIndex;
                                            $isCurrent = $idx === $statusIndex;
                                            $cancelled = $st === 'cancelled';
                                            $colorClass = $cancelled
                                                ? 'btn-danger'
                                                : ($reached
                                                    ? ($st === 'delivered'
                                                        ? 'btn-success'
                                                        : ($isCurrent
                                                            ? 'btn-primary'
                                                            : 'btn-secondary'))
                                                    : 'btn-outline-secondary');
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
                                                {{ $labels[$st] ?? ucfirst($st) }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="d-details">
                                <h5 class="h6 mb-1">Current Status: <span
                                        class="fw-bold">{{ $labels[$currentStatus] ?? ucfirst($currentStatus) }}</span></h5>
                                <p class="mb-2 small text-muted">Last updated
                                    {{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                <ul class="list-unstyled small mb-0">
                                    <li>Total: <strong>${{ number_format($order->total_amount, 2) }}</strong></li>
                                    <li>Payment: <span
                                            class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($order->payment_status) }}</span>
                                    </li>
                                </ul>
                                @if ($currentStatus === 'cancelled')
                                    <p class="text-danger small mt-2 mb-0">This order was cancelled.</p>
                                @endif
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
