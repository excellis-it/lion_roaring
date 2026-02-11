<div class="table-responsive">
    <table class="table align-middle bg-white color_body_text">
        <thead class="color_head">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Subscription</th>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Promo Code</th>
                <th>Discount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $p)
                <tr>
                    <td>{{ $payments->firstItem() + $loop->index }}</td>
                    <td>
                        @if ($p->user)
                            <div>{{ $p->user->first_name }} {{ $p->user->last_name }}</div>
                            <small class="text-muted">{{ $p->user->email }}</small>
                        @else
                            <span class="text-muted">Unknown User (ID: {{ $p->user_id }})</span>
                        @endif
                    </td>
                    <td>{{ $p->userSubscription ? $p->userSubscription->subscription_name : '-' }}</td>
                    <td><small>{{ $p->transaction_id }}</small></td>
                    <td>${{ number_format($p->payment_amount, 2) }}</td>
                    <td>
                        @if ($p->promo_code)
                            <span class="badge"
                                style="background: #28a745; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px;">
                                <i class="fa fa-tag"></i> {{ $p->promo_code }}
                            </span>
                        @else
                            <span class="text-muted" style="font-size: 12px;">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($p->discount_amount && $p->discount_amount > 0)
                            <span style="color: #28a745; font-weight: 600;">
                                -${{ number_format($p->discount_amount, 2) }}
                            </span>
                        @else
                            <span class="text-muted" style="font-size: 12px;">$0.00</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge"
                            style="background: #6c757d; color: white; padding: 5px 10px; border-radius: 4px; font-size: 11px;">
                            {{ $p->payment_method }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $p->payment_status == 'Success' ? 'bg-success' : 'bg-danger' }}"
                            style="padding: 5px 10px; border-radius: 4px; font-size: 11px;">
                            {{ $p->payment_status }}
                        </span>
                    </td>
                    <td>
                        <div>{{ $p->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $p->created_at->format('H:i') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="fa fa-inbox" style="font-size: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                        <div>No payments found</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .color_head {
        background: #f8f9fa;
        font-weight: 600;
    }

    .color_head th {
        padding: 12px 8px;
        border-bottom: 2px solid #dee2e6;
        font-size: 13px;
        text-transform: uppercase;
        color: #495057;
    }

    .table tbody tr {
        border-bottom: 1px solid #dee2e6;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table tbody td {
        padding: 12px 8px;
        vertical-align: middle;
    }
</style>
