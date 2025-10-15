@extends('ecom.layouts.master')
@section('title', 'My Orders')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('my-orders') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>My Orders</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec">
        <div class="container">
            <div class="heading_hp mb-3">
                <h2>Order History</h2>
            </div>

            @if ($orders->count() > 0)
                <div class="row">
                    <div class="col-12">
                        @foreach ($orders as $order)
                            <div class="order-card mb-4 p-4 border rounded shadow">
                                <div class="row">
                                    <!--<div class="col-md-3">-->

                                    <!--</div>-->
                                    <div class="col-md-12">
                                        <div class="order-header d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5>Order #{{ $order->order_number }}</h5>
                                                <p class="text-muted mb-0">Placed on
                                                    {{ $order->created_at->format('M d, Y') }}</p>
                                                <p class="text-muted mb-0">Total:
                                                    ${{ number_format($order->total_amount, 2) }}</p>

                                                <div class="text-start mt-2">
                                                    <span
                                                        class="badge bg-{{ $order->status == 4 ? 'success' : ($order->status == 5 ? 'danger' : 'primary') }} mb-1">
                                                        {{ ucfirst($order->orderStatus->name ?? '-') }}
                                                    </span>
                                                    <span
                                                        class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($order->payment_status) }}
                                                    </span>
                                                </div>
                                            </div>





                                            <div class="order-actions text-end">
                                                <a href="{{ route('e-store.order-details', $order->id) }}"
                                                    class="red_btn mb-2">
                                                    <span>View Details</span>
                                                </a>
                                                {{-- @if ($order->status == 'delivered')
                                                <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                                                    Download Invoice
                                                </a>
                                            @endif --}}
                                            </div>
                                        </div>



                                        <div class="order-items">
                                            @foreach ($order->orderItems->take(2) as $item)
                                                <div class="single-item-box d-flex align-items-center mb-2">
                                                    <div class="item-image me-3">
                                                        @if ($item->product_image)
                                                            <img src="{{ Storage::url($item->product_image) }}"
                                                                alt="{{ $item->product_name }}">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                style="width: 60px; height: 60px;">
                                                                <i class="fa-solid fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                        <p class="text-muted">Qty: {{ $item->quantity }} ×
                                                            ${{ number_format($item->price, 2) }}</p>

                                                        <!--<button class="order-btn">Buy it Again</button>-->
                                                        <!--<button class="order-btn">View your item</button>-->
                                                        <!--<button class="order-btn">Track package</button>-->
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($order->orderItems->count() > 2)
                                                <small class="text-muted">and {{ $order->orderItems->count() - 2 }} more
                                                    items</small>
                                            @endif
                                        </div>


                                    </div>

                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="empty-orders text-center py-5">
                            <i class="fa-solid fa-shopping-bag"
                                style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <h3>No orders yet</h3>
                            <p class="mb-4">You haven't placed any orders yet.</p>
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
