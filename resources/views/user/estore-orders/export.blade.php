<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Order Number</th>
            <th>Warehouse</th>
            <th>Customer</th>
            <th>Customer Email</th>
            <th>Customer Phone</th>
            <th>Items</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Order Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $index => $order)
            <tr>
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
                        <small class="text-muted">{{ $order->user->user_name ?? 'N/A' }}</small>
                    </div>
                </td>
                <td>
                    <div>
                        <i class="fas fa-envelope"></i> {{ $order->email }}
                    </div>
                </td>


                <td>
                    <div>
                        <i class="fas fa-envelope"></i> {{ $order->phone }}
                    </div>
                </td>
                <td>
                    <span class="">{{ $order->orderItems->count() }} items</span>
                </td>
                <td>
                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                </td>
                <td>
                    {{ ucfirst($order->orderStatus->name ?? '-') }}
                </td>
                <td>
                    <span class=" {{ $order->payment_status_badge_class }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td>
                    <div>
                        {{ $order->created_at->format('M d, Y') }}<br>
                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">
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
