<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>#</th>
            <th>Order Number</th>
            <th>Warehouse</th>
            <th>Customer </th>
            <th>Contact</th>
            <th>Items</th>
            <th>Total Amount</th>
            <th>Type</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Expected Date</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $index => $order)
            @php
                $hasDigitalItems = $order->orderItems->contains(function ($i) {
                    return optional($i->product)->product_type === 'digital';
                });
            @endphp
            <tr>
                <td>
                    <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
                </td>
                <td>{{ ($orders->firstItem() ?? 0) + $index }}</td>
                <td>
                    <strong>{{ $order->order_number }}</strong>
                </td>
                <td>
                    <strong>{{ $order->warehouse?->name ?? '' }}</strong>
                </td>
                <td>
                    <div>
                        <strong>{{ $order->full_name }}</strong><br>
                        <small class="text-muted">{{ $order->user->user_name ?? '-' }}</small>
                    </div>
                </td>
                <td>
                    <div>
                        <i class="fas fa-envelope"></i> {{ $order->email }}<br>
                        <i class="fas fa-phone"></i> {{ $order->phone }}
                    </div>
                </td>
                <td>
                    <span class="">{{ $order->orderItems->count() }} items</span>
                </td>
                <td>
                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                </td>
                <td>
                    @if ($hasDigitalItems)
                        <span class="badge bg-success">Digital</span>
                    @else
                        @if ($order->is_pickup)
                            <span class="badge bg-info">Pickup</span>
                        @else
                            <span class="badge bg-primary">Delivery</span>
                        @endif
                    @endif
                </td>
                <td>
                    @if (!$hasDigitalItems)
                        <span class=" {{ $order->status_badge_class }} p-1 rounded">
                            {{ ucfirst($order->orderStatus->name ?? '-') }}
                        </span>
                    @endif
                </td>
                <td>
                    <span class=" {{ $order->payment_status_badge_class }} p-1 rounded">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td>
                    @php
                        $statusSlug = optional($order->orderStatus)->slug;
                        $isCancelled = in_array($statusSlug, ['cancelled', 'pickup_cancelled'], true);
                        $isDelivered = in_array($statusSlug, ['delivered', 'pickup_picked_up'], true);
                        $isRefunded = $order->payment_status === 'refunded';
                    @endphp

                    @if ($isRefunded)
                        <div class="text-secondary small">
                            <strong>Refunded:</strong><br>
                            {{ \Carbon\Carbon::parse($order->updated_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                        </div>
                    @elseif ($isDelivered)
                        <div class="text-success small">
                            <strong>Delivered:</strong><br>
                            {{ \Carbon\Carbon::parse($order->updated_at)->timezone(auth()->user()->time_zone)->format('M d, Y h:i A') }}
                        </div>
                    @elseif ($isCancelled)
                        <div class="text-danger small">
                            <strong>Refund by:</strong><br>
                            {{ \Carbon\Carbon::parse($order->updated_at)->addDays($max_refundable_days)->format('M d, Y') }}
                        </div>
                    @elseif (!$order->is_pickup && $order->expected_delivery_date)
                        <div class="text-info small">
                            <strong>Expected:</strong><br>
                            {{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('M d, Y') }}
                        </div>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <div>
                        {{ \Carbon\Carbon::parse($order->created_at)->timezone(auth()->user()->time_zone)->format('M d, Y') }}<br>
                        <small
                            class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->timezone(auth()->user()->time_zone)->format('h:i A') }}</small>
                    </div>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        @if (auth()->user()->can('View Estore Orders') || auth()->user()->isWarehouseAdmin())
                            <a href="{{ route('user.store-orders.details', $order->id) }}" class="btn btn-sm btn-info"
                                title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endif
                        @php
                            $finalSlugs = ['delivered', 'cancelled', 'pickup_picked_up', 'pickup_cancelled'];
                            $isFinalStatus = in_array(optional($order->orderStatus)->slug, $finalSlugs, true);
                        @endphp
                        {{-- @if (auth()->user()->can('Edit Estore Orders') && !$isFinalStatus)
                            <button type="button" class="btn btn-sm btn-warning"
                                onclick="openUpdateStatusModal({{ $order->id }}, '{{ $order->status }}', '{{ $order->payment_status }}', '{{ $order->notes }}', '{{ $order->expected_delivery_date ? date('Y-m-d', strtotime($order->expected_delivery_date)) : '' }}', '{{ $order->is_pickup ? 1 : 0 }}')"
                                title="Update Status">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif --}}
                        {{-- <button type="button" class="btn btn-sm btn-danger" onclick="deleteOrder({{ $order->id }})"
                            title="Delete Order">
                            <i class="fas fa-trash"></i>
                        </button> --}}
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-center">
                    <div class="py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No orders found</h5>
                        <p class="text-muted">No orders match your current filters.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($orders->total() > 0)
    <div class="row mt-3 align-items-center">
        <div class="col-md-6">
            <p class="text-muted">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of
                {{ $orders->total() }} orders</p>
        </div>
        <div class="col-md-6 text-end">
            {!! $orders->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>
    </div>
@endif
