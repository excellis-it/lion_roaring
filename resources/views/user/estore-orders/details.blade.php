@extends('user.layouts.master')

@section('title')
    Order Details - {{ $order->order_number }}
@endsection
@push('styles')
    {{-- Custom Timeline CSS --}}
    <style>
        .timeline {
            position: relative;
        }

        .timeline-item {
            position: relative;
            text-align: center;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 26%;
            right: -62%;
            width: 100%;
            height: 4px;
            background: #7851a9;
            z-index: 0;
        }

        .timeline-circle {
            position: relative;
            z-index: 2;
        }

        @media (max-width:767px) {

            .timeline-circle {
                width: 30px !important;
                height: 30px !important;
            }

            .timeline-item .small {
                font-size: 12px !important;
            }

            .timeline-item:not(:last-child)::after {
                top: 21%;
                height: 2px;
            }
        }

        @media (max-width:576px) {
            .timeline {
                padding-left: 0px !important;
                flex-direction: column;
                align-items: self-start !important;
            }

            .timeline-item {
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .timeline-item .small {
                margin-bottom: 0px;
            }

            .timeline-item:not(:last-child)::after {
                top: 21%;
                height: 74px;
                width: 2px;
                left: 14px;
            }
        }
    </style>
@endpush
@section('content')
    @php
        $hasDigitalProduct = $order->orderItems->contains(function ($i) {
            return optional($i->product)->product_type === 'digital';
        });
    @endphp
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-3 font-size-18">Order Details</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">

                                <li class="breadcrumb-item"><a href="{{ route('user.store-orders.list') }}">Orders</a></li>
                                <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                            </ol>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Order Info -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-none mb-0 h5">Order #{{ $order->order_number }}</p>

                                <div>
                                    @php
                                        $statusSlug = optional($order->orderStatus)->slug;
                                        $isFinalDelivered = in_array(
                                            $statusSlug,
                                            ['delivered', 'pickup_picked_up'],
                                            true,
                                        );
                                        $isFinalCancelled = in_array(
                                            $statusSlug,
                                            ['cancelled', 'pickup_cancelled'],
                                            true,
                                        );
                                    @endphp
                                    {{-- <span
                                        class="badge {{ $order->status_badge_class }} me-2">{{ ucfirst($order->status) }}</span>
                                    <span
                                        class="badge {{ $order->payment_status_badge_class }}">{{ ucfirst($order->payment_status) }}</span> --}}
                                    {{-- add invoice button --}}

                                    @if ($order->is_pickup == 1)
                                        <h5 class="bg-dark text-white p-2 rounded">Order Type: Pickup Order</h5>
                                    @endif

                                    @if ($isFinalDelivered && $order->payment_status == 'paid')
                                        <a href="{{ route('user.store-orders.invoice', $order->id) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> Download Invoice
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            @php
                                $estoreSettingsGlobal = \App\Models\EstoreSetting::first();
                                $cancelHoursGlobal = $estoreSettingsGlobal->cancel_within_hours ?? 24;
                                $allowedAdminSlugs = $order->is_pickup
                                    ? ['pickup_pending', 'pickup_processing']
                                    : ['pending', 'processing'];
                                // precise minute-based check so the window closes exactly at X hours
                                $elapsedMinutesAdmin = (int) $order->created_at->diffInMinutes(now());
                                $isWithinCancelWindowAdmin = $elapsedMinutesAdmin < $cancelHoursGlobal * 60;
                                $minutesLeftAdmin = max(0, $cancelHoursGlobal * 60 - $elapsedMinutesAdmin);
                                $hoursLeftAdmin = (int) floor($minutesLeftAdmin / 60);
                                $minutesRemAdmin = $minutesLeftAdmin % 60;
                            @endphp

                            @if (in_array(optional($order->orderStatus)->slug, $allowedAdminSlugs, true) && $isWithinCancelWindowAdmin)
                                <div class="alert alert-warning" style="background-color: #fff3cd; border-color: #ffeeba;">
                                    <strong>Warning:</strong> This order is still within the {{ $cancelHoursGlobal }}-hour
                                    cancellation window and may be canceled by the customer.
                                    <div class="small mt-1">Time left: @if ($hoursLeftAdmin > 0)
                                            {{ $hoursLeftAdmin }}h
                                        @endif{{ $minutesRemAdmin }}m</div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Customer Information</h6>
                                    <p><strong>Name:</strong> {{ $order->full_name }}</p>
                                    <p><strong>Email:</strong> {{ $order->email }}</p>
                                    <p><strong>Phone:</strong> {{ $order->phone }}</p>
                                    @if ($order->user)
                                        <p><strong>Username:</strong> {{ $order->user->user_name }}</p>
                                    @endif
                                </div>

                                @if ($order->is_pickup == 1)
                                    <div class="col-md-6">
                                        <h6>Warehouse - Pickup Location:</h6>
                                        <p>{{ $order->warehouse_name ?? 'N/A' }}, {{ $order->warehouse_address ?? '' }}
                                        </p>

                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <h6>Warehouse:</h6>
                                        <p>{{ $order->warehouse_name ?? 'N/A' }}, {{ $order->warehouse_address ?? '' }}
                                        </p>
                                        <h6>Shipping Address</h6>
                                        <address>
                                            {{ $order->address_line_1 }}<br>
                                            @if ($order->address_line_2)
                                                {{ $order->address_line_2 }}<br>
                                            @endif
                                            {{ $order->city }}, {{ $order->state }} {{ $order->pincode }}<br>
                                            {{ $order->country }}
                                        </address>
                                    </div>
                                @endif
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p><strong>Order Date:</strong>
                                        {{ \Carbon\Carbon::parse($order->created_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Last Updated:</strong>
                                        {{ \Carbon\Carbon::parse($order->updated_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>

                            @if ($order->notes)
                                <div class="mt-3">
                                    <h6>Notes</h6>
                                    <p class="text-muted">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $item)
                                            @php
                                                $price = $item->price ?? 0;
                                                $quantity = $item->quantity ?? 0;
                                                $total = $price * $quantity;
                                                $charges = $item->other_charges
                                                    ? json_decode($item->other_charges, true)
                                                    : [];
                                            @endphp

                                            {{-- Product Row --}}
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->product_name ?? 'N/A' }}</strong>

                                                    @if (!empty($item->warehouseProduct?->sku))
                                                        <br><small class="text-muted">SKU:
                                                            {{ $item->warehouseProduct->sku }}</small>
                                                    @endif

                                                    @if (!empty($item->size))
                                                        <br><small class="text-muted">Size: {{ $item->size }}</small>
                                                    @endif

                                                    @if (!empty($item->color))
                                                        <br><small class="text-muted">Color: {{ $item->color }}</small>
                                                    @endif
                                                </td>
                                                <td>${{ number_format($price, 2) }}</td>
                                                <td>{{ $quantity }}</td>
                                                <td><strong>${{ number_format($total, 2) }}</strong></td>
                                            </tr>

                                            {{-- Other Charges per Product --}}
                                            @if (!empty($charges))
                                                @foreach ($charges as $charge)
                                                    <tr class="table-secondary">
                                                        <td style="padding-left: 20px;">•
                                                            {{ $charge['charge_name'] ?? 'Other' }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>${{ number_format($charge['charge_amount'] ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Subtotal:</th>
                                            <th>${{ number_format($order->subtotal ?? 0, 2) }}</th>
                                        </tr>

                                        {{-- Promo Code / Discount --}}
                                        @if (!empty($order->promo_discount) && $order->promo_discount > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">
                                                    Promo Discount
                                                    @if (!empty($order->promo_code))
                                                        (Code: {{ $order->promo_code }})
                                                    @endif
                                                </th>
                                                <th>- ${{ number_format($order->promo_discount, 2) }}</th>
                                            </tr>
                                        @endif

                                        @if (!empty($order->tax_amount) && $order->tax_amount > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Tax:</th>
                                                <th>${{ number_format($order->tax_amount, 2) }}</th>
                                            </tr>
                                        @endif

                                        @if (!empty($order->shipping_amount) && $order->shipping_amount > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Shipping:</th>
                                                <th>${{ number_format($order->shipping_amount, 2) }}</th>
                                            </tr>
                                        @endif

                                        @if (!empty($order->handling_amount) && $order->handling_amount > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Handling:</th>
                                                <th>${{ number_format($order->handling_amount, 2) }}</th>
                                            </tr>
                                        @endif

                                        @if (!empty($order->credit_card_fee) && $order->credit_card_fee > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Credit Card Fee:</th>
                                                <th>${{ number_format($order->credit_card_fee, 2) }}</th>
                                            </tr>
                                        @endif

                                        <tr class="table-primary">
                                            <th colspan="3" class="text-end">Total Amount:</th>
                                            <th>${{ number_format($order->total_amount ?? 0, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>



                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        @php
                            if ($order->is_pickup == 1) {
                                $labels = [
                                    'pickup_pending' => 'Ordered',
                                    'pickup_processing' => 'Processing',
                                    'pickup_ready_for_pickup' => 'Ready for Pickup',
                                    'pickup_picked_up' => 'Picked Up',
                                    'pickup_cancelled' => 'Cancelled',
                                ];
                            } else {
                                $labels = [
                                    'pending' => 'Ordered',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'out_for_delivery' => 'Out for Delivery',
                                    'delivered' => 'Delivered',
                                    'cancelled' => 'Cancelled',
                                ];
                            }
                        @endphp
                        @if (!$hasDigitalProduct)
                            <div class="card-body">
                                {{-- Order Info --}}
                                <div class="mb-4">
                                    <h4 class="mb-1 fw-bold">Order Tracking</h4>
                                    <p class="mb-0 text-muted">Order <strong>#{{ $order->order_number }}</strong></p>
                                </div>

                                {{-- Timeline --}}
                                <div class="timeline d-flex justify-content-between align-items-center position-relative mb-4"
                                    style="gap:1rem;">
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
                                                ? 'bg-danger'
                                                : ($reached
                                                    ? (in_array($status->slug, ['delivered', 'pickup_picked_up'], true)
                                                        ? 'bg-success'
                                                        : ($isCurrent
                                                            ? 'bg-primary'
                                                            : 'bg-secondary'))
                                                    : 'bg-light');
                                            $label =
                                                $labels[$status->slug] ?? ($status->name ?? ucfirst($status->slug));
                                        @endphp

                                        <div class="timeline-item text-center position-relative flex-fill">
                                            {{-- Circle --}}
                                            <div class="timeline-circle {{ $reached ? 'text-white' : 'text-muted' }} {{ $cancelled ? 'bg-danger' : ($reached ? (in_array($status->slug, ['delivered', 'pickup_picked_up'], true) ? 'bg-success' : ($isCurrent ? 'bg-primary' : 'bg-secondary')) : 'bg-light') }}"
                                                style="width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:auto; font-size:1.2rem; background-color:#ccc !important;">
                                                @if ($reached)
                                                    <i class="fa-solid fa-check"></i>
                                                @else
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                @endif
                                            </div>

                                            {{-- Connecting line --}}
                                            @if (!$loop->last)
                                                <div class="timeline-line position-absolute top-50 start-50 translate-middle"
                                                    style="height:4px; width:100%; background: #dee2e6; z-index:-1;"></div>
                                            @endif

                                            {{-- Label --}}
                                            <p
                                                class="mt-2 small fw-semibold {{ $isCurrent ? 'text-primary' : 'text-muted' }}">
                                                {{ $label }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Expected Delivery Date --}}
                                @if (!empty($order->expected_delivery_date) && !$order->is_pickup && !$isFinalCancelled)
                                    <div class="text-center mb-3">
                                        <span class="badge bg-info text-dark p-2">
                                            <i class="fa-solid fa-calendar-day me-1"></i>
                                            Expected Delivery:
                                            {{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Current Status --}}
                                <div class="border-top pt-3 mt-3 text-center">
                                    <h6 class="mb-1">Current Status: <span
                                            class="fw-bold">{{ $order->orderStatus->name ?? ucfirst($order->status) }}</span>
                                    </h6>
                                    <p class="mb-0 small text-muted">Last updated:
                                        {{ \Carbon\Carbon::parse($order->updated_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>



                </div>

                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        {{-- @dd($order->payment_status, $order->status) --}}
                        <div class="card-body">
                            @if ($order->payment_status === 'paid' && $isFinalCancelled)
                                @if (auth()->user()->can('Edit Estore Orders') || auth()->user()->isWarehouseAdmin())
                                    <button type="button" class="btn btn-danger w-100 mb-2"
                                        onclick="processRefund({{ $order->id }})">
                                        <i class="fas fa-undo"></i> Refund
                                    </button>
                                @endif

                                @if ($order->notes)
                                    <div class="alert alert-warning mt-2">
                                        <strong>Cancelled Reason:</strong> {{ $order->notes }}
                                    </div>
                                @endif
                            @else
                                @if (
                                    (auth()->user()->can('Edit Estore Orders') || auth()->user()->isWarehouseAdmin()) &&
                                        !$isFinalDelivered &&
                                        !$isFinalCancelled &&
                                        !$hasDigitalProduct)
                                    <button type="button" class="btn btn-warning w-100 mb-2"
                                        onclick="openUpdateStatusModal({{ $order->id }}, '{{ $order->status }}', '{{ $order->payment_status }}', '{{ $order->notes }}', '{{ $order->expected_delivery_date ? date('Y-m-d', strtotime($order->expected_delivery_date)) : '' }}', '{{ $order->is_pickup ? 1 : 0 }}')">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                @endif
                            @endif


                            <a href="{{ route('user.store-orders.list') }}" class="btn btn-secondary w-100 mb-2">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>

                            {{-- <button type="button" class="btn btn-info w-100 mb-2" onclick="printOrder()">
                        <i class="fas fa-print"></i> Print Order
                    </button> --}}
                        </div>
                    </div>

                    <!-- Payment Information -->
                    @if ($order->payments->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($order->payments as $payment)
                                    <div class="border p-3 rounded mb-2">
                                        <p><strong>Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                                        <p><strong>Status:</strong>
                                            <span
                                                class=" {{ $payment->status == 'succeeded' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </p>
                                        <p><strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}</p>
                                        @if ($payment->paid_at)
                                            <p><strong>Paid At:</strong>
                                                {{ \Carbon\Carbon::parse($payment->paid_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                                            </p>
                                        @endif
                                        @if ($payment->stripe_payment_intent_id)
                                            <p><strong>Payment ID:</strong>
                                                <small class="text-muted">{{ $payment->stripe_payment_intent_id }}</small>
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Order Timeline -->

                </div>
            </div>

        </div>
    </div>
    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <input type="hidden" id="order-id" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" id="current-status" name="current_status" value="{{ $order->status }}">

                        <!-- Cancellation Window Warning -->
                        <div class="alert alert-warning" id="cancel-window-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Cancellation Window Active:</strong> This order is within the cancellation period.
                            You can only cancel the order at this time. Other status changes will be available after the
                            cancellation window expires.
                        </div>

                        <div class="mb-3">
                            <label for="order-status" class="form-label">Order Status</label>
                            <select class="form-control" id="order-status" name="status" required>

                                @foreach ($order_status as $status)
                                    <option value="{{ $status->id }}"
                                        {{ $order->status == $status->id ? 'selected' : '' }}>
                                        {{ ucfirst($status->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 " id="expected-delivery-wrapper">
                            <label for="expected-delivery-date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" id="expected-delivery-date"
                                value="{{ $order->expected_delivery_date ? date('Y-m-d', strtotime($order->expected_delivery_date)) : '' }}"
                                name="expected_delivery_date">
                        </div>
                        {{-- @dd($order->payment_status) --}}
                        {{-- <div class="mb-3">
                            <label for="payment-status" class="form-label">Payment Status</label>
                            <select class="form-control" id="payment-status" name="payment_status">
                                <option value="">Don't Change</option>
                                <option value="pending"
                                    {{ trim(strtolower($order->payment_status)) == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="paid"
                                    {{ trim(strtolower($order->payment_status)) == 'paid' ? 'selected' : '' }}>
                                    Paid
                                </option>
                                <option value="failed"
                                    {{ trim(strtolower($order->payment_status)) == 'failed' ? 'selected' : '' }}>
                                    Failed
                                </option>
                                <option value="refunded"
                                    {{ trim(strtolower($order->payment_status)) == 'refunded' ? 'selected' : '' }}>
                                    Refunded
                                </option>
                            </select>
                        </div> --}}


                        <div class="mb-3">
                            <label for="order-notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="order-notes" name="notes" rows="3"
                                placeholder="Add any notes about this status change...">{{ $order->notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        /* .timeline::before {
                                                                                                                                                    content: '';
                                                                                                                                                    position: absolute;
                                                                                                                                                    left: 15px;
                                                                                                                                                    top: 0;
                                                                                                                                                    bottom: 0;
                                                                                                                                                    width: 2px;
                                                                                                                                                    background: #e9ecef;
                                                                                                                                                } */

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content {
            padding-left: 20px;
        }

        .timeline-title {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-text {
            margin-bottom: 0;
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function processRefund(orderId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you really want to refund this order?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, refund it!",
                cancelButtonText: "No, cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.store-orders.refund', ':id') }}".replace(':id', orderId),
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Refunded!",
                                text: response.message,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 2000);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON?.message || "Failed to process refund.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }
    </script>
    <script>
        const STATUS_SEQUENCE = [
            @foreach ($order_status as $status)
                '{{ $status->id }}',
            @endforeach
        ];
        const CANCELLED_STATUS_ID =
            {{ $order_status->firstWhere('slug', $order->is_pickup ? 'pickup_cancelled' : 'cancelled')?->id ?? 'null' }};
        const FINAL_STATUS_ID =
            {{ $order_status->firstWhere('slug', $order->is_pickup ? 'pickup_picked_up' : 'delivered')?->id ?? 'null' }};

        // Cancellation window check
        const IS_WITHIN_CANCEL_WINDOW = {{ $isWithinCancelWindowAdmin ? 'true' : 'false' }};
        const ALLOWED_CANCEL_SLUGS = @json($allowedAdminSlugs);

        function openUpdateStatusModal(orderId, currentStatus, currentPaymentStatus, notes, date, isPickup) {
            isPickup = parseInt(isPickup) === 1 ? 1 : 0;
            if ((FINAL_STATUS_ID && parseInt(currentStatus) === parseInt(FINAL_STATUS_ID)) ||
                (CANCELLED_STATUS_ID && parseInt(currentStatus) === parseInt(CANCELLED_STATUS_ID))) {
                toastr.warning('This order status is final and cannot be changed.');
                return;
            }
            $('#order-id').val(orderId);
            $('#current-status').val(currentStatus);

            // Show/hide cancellation window warning
            if (IS_WITHIN_CANCEL_WINDOW) {
                $('#cancel-window-warning').show();
            } else {
                $('#cancel-window-warning').hide();
            }

            // If within cancellation window, only allow cancellation
            if (IS_WITHIN_CANCEL_WINDOW) {
                $('#order-status option').each(function() {
                    const optionValue = $(this).val();
                    // Disable all options except cancelled status
                    if (parseInt(optionValue) !== parseInt(CANCELLED_STATUS_ID)) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                // Normal behavior: disable previous steps only
                $('#order-status option').each(function() {
                    $(this).prop('disabled', false);
                    const optionValue = $(this).val();
                    const optionIndex = STATUS_SEQUENCE.indexOf(optionValue);
                    const currentIndex = STATUS_SEQUENCE.indexOf(currentStatus);
                    const isPreviousStep = optionIndex !== -1 && optionIndex < currentIndex && optionValue !==
                        currentStatus;
                    $(this).prop('disabled', isPreviousStep);
                });
            }

            $('#order-status').val(currentStatus);
            $('#order-notes').val(notes || '');
            $('#updateStatusModal').modal('show');
            $('#expected-delivery-date').val(date);
            // Show/hide expected delivery based on pickup flag or if status is cancelled
            toggleExpectedDeliveryVisibility(isPickup, currentStatus);
        }

        function toggleExpectedDeliveryVisibility(isPickup, statusId) {
            const wrapper = $('#expected-delivery-wrapper');
            const input = $('#expected-delivery-date');
            if (isPickup === 1 || (CANCELLED_STATUS_ID && parseInt(statusId) === parseInt(CANCELLED_STATUS_ID))) {
                wrapper.hide();
                input.prop('disabled', true).val('');
            } else {
                wrapper.show();
                input.prop('disabled', false);
            }
        }

        // Re-evaluate when status selector changes
        $(document).on('change', '#order-status', function() {
            const selected = $(this).val();
            // We don't have isPickup here; read from current order DOM if present
            const isPickupKnown = {{ $order->is_pickup ? 1 : 0 }};
            toggleExpectedDeliveryVisibility(isPickupKnown, selected);
        });

        $('#updateStatusForm').on('submit', function(e) {
            e.preventDefault();

            const currentStatus = $('#current-status').val();
            const targetStatus = $('#order-status').val();
            const currentIndex = STATUS_SEQUENCE.indexOf(currentStatus);
            const targetIndex = STATUS_SEQUENCE.indexOf(targetStatus);

            if ((FINAL_STATUS_ID && parseInt(currentStatus) === parseInt(FINAL_STATUS_ID)) ||
                (CANCELLED_STATUS_ID && parseInt(currentStatus) === parseInt(CANCELLED_STATUS_ID))) {
                toastr.warning('Finalized orders cannot be updated.');
                return;
            }

            if (targetIndex !== -1 && currentIndex !== -1 && targetIndex < currentIndex) {
                toastr.warning('Cannot revert an order to a previous status.');
                return;
            }

            // Client-side required check for expected delivery
            const isPickupKnown = {{ $order->is_pickup ? 1 : 0 }};
            if (parseInt(isPickupKnown) !== 1 && (!CANCELLED_STATUS_ID || parseInt(targetStatus) !== parseInt(
                    CANCELLED_STATUS_ID))) {
                const dateVal = $('#expected-delivery-date').val();
                if (!dateVal) {
                    toastr.error(
                        'Please provide an expected delivery date unless the order is cancelled or pickup.');
                    return;
                }
            }

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('user.store-orders.update-status') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#updateStatusModal').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(response.message || 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An unexpected error occurred. Please try again.';

                    // Laravel Validation Errors
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            messages.forEach(function(message) {
                                toastr.error(message);
                            });
                        });
                        return;
                    }

                    // Custom message from controller
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    toastr.error(errorMsg);
                }
            });

        });

        function printOrder() {
            window.print();
        }
    </script>
@endpush
