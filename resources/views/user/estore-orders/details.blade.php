@extends('user.layouts.master')

@section('title')
    Order Details - {{ $order->order_number }}
@endsection

@section('content')
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
                                <h5 class="card-title mb-0">Order #{{ $order->order_number }}</h5>

                                <div>
                                    <span
                                        class="badge {{ $order->status_badge_class }} me-2">{{ ucfirst($order->status) }}</span>
                                    <span
                                        class="badge {{ $order->payment_status_badge_class }}">{{ ucfirst($order->payment_status) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
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
                                <div class="col-md-6">
                                    <h6>Warehouse:</h6>
                                    <p>{{ $order->warehouse?->name ?? 'N/A' }}, {{ $order->warehouse?->address ?? 'N/A' }}
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
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Last Updated:</strong> {{ $order->updated_at->format('M d, Y h:i A') }}</p>
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
                                            <th>Image</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->product_name }}</strong>
                                                    @if ($item->product)
                                                        <br><small class="text-muted">SKU:
                                                            {{ $item->warehouseProduct->sku ?? 'N/A' }}</small>
                                                    @endif

                                                    {{-- if item have size and color --}}
                                                    @if ($item->size)
                                                        <br><small class="text-muted">Size: {{ $item->size->size }}</small>
                                                    @endif
                                                    @if ($item->color)
                                                        <br><small class="text-muted">Color:
                                                            {{ $item->color->color_name }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->product_image)
                                                        <img src="{{ Storage::url($item->product_image) }}"
                                                            alt="{{ $item->product_name }}" class="img-thumbnail"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                                            style="width: 60px; height: 60px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td><strong>${{ number_format($item->total, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Subtotal:</th>
                                            <th>${{ number_format($order->subtotal, 2) }}</th>
                                        </tr>
                                        @if ($order->tax_amount > 0)
                                            <tr>
                                                <th colspan="4" class="text-end">Tax:</th>
                                                <th>${{ number_format($order->tax_amount, 2) }}</th>
                                            </tr>
                                        @endif
                                        @if ($order->shipping_amount > 0)
                                            <tr>
                                                <th colspan="4" class="text-end">Shipping:</th>
                                                <th>${{ number_format($order->shipping_amount, 2) }}</th>
                                            </tr>
                                        @endif
                                        @if ($order->credit_card_fee > 0)
                                            <tr>
                                                <th colspan="4" class="text-end">Credit Card Fee:</th>
                                                <th>${{ number_format($order->credit_card_fee, 2) }}</th>
                                            </tr>

                                        @endif
                                        <tr class="table-primary">
                                            <th colspan="4" class="text-end">Total Amount:</th>
                                            <th>${{ number_format($order->total_amount, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-warning w-100 mb-2"
                                onclick="openUpdateStatusModal({{ $order->id }}, '{{ $order->status }}', '{{ $order->payment_status }}', '{{ $order->notes }}')">
                                <i class="fas fa-edit"></i> Update Status
                            </button>

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
                                            <p><strong>Paid At:</strong> {{ $payment->paid_at->format('M d, Y h:i A') }}
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
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @if ($order->payment_status == 'paid')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Payment Received</h6>
                                            @if ($order->payments->where('status', 'succeeded')->first())
                                                <p class="timeline-text">
                                                    {{ $order->payments->where('status', 'succeeded')->first()->paid_at->format('M d, Y h:i A') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Order Placed</h6>
                                        <p class="timeline-text">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>

                                @if ($order->status != 'pending')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Status: {{ ucfirst($order->status) }}</h6>
                                            <p class="timeline-text">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                @endif


                            </div>
                        </div>
                    </div>
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

                        <div class="mb-3">
                            <label for="order-status" class="form-label">Order Status</label>
                            <select class="form-control" id="order-status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                    Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                </option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment-status" class="form-label">Payment Status</label>
                            <select class="form-control" id="payment-status" name="payment_status">
                                <option value="">Don't Change</option>
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid
                                </option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed
                                </option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>
                                    Refunded</option>
                            </select>
                        </div>

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

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

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
    <script>
        function openUpdateStatusModal(orderId, currentStatus, currentPaymentStatus, notes) {
            $('#order-id').val(orderId);
            $('#order-status').val(currentStatus);
            $('#payment-status').val('');
            $('#order-notes').val(notes || '');
            $('#updateStatusModal').modal('show');
        }

        $('#updateStatusForm').on('submit', function(e) {
            e.preventDefault();

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
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update order status');
                }
            });
        });

        function printOrder() {
            window.print();
        }
    </script>
@endpush
