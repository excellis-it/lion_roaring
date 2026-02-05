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
            <th>Status</th>
            <th>Payment</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $index => $order)
            <tr>
                <td>
                    <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
                </td>
                <td>{{ $index + 1 }}</td>
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
                    <span class=" {{ $order->status_badge_class }} p-1 rounded">
                        {{ ucfirst($order->orderStatus->name ?? '-') }}
                    </span>
                </td>
                <td>
                    <span class=" {{ $order->payment_status_badge_class }} p-1 rounded">
                        {{ ucfirst($order->payment_status) }}
                    </span>
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
                <td colspan="12" class="text-center">
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

@if ($orders->count() > 0)
    <div class="row mt-3">
        <div class="col-md-6">
            <p class="text-muted">Showing {{ $orders->count() }} orders</p>
        </div>
    </div>
@endif
