@extends('user.layouts.master')
@section('title', 'All Membership Payments')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h3 class="mb-3">All Membership Payments</h3>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle bg-white color_body_text">
                    <thead class="color_head">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Subscription</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
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
                                <td>{{ $p->payment_method }}</td>
                                <td>
                                    <span class="badge {{ $p->payment_status == 'Success' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $p->payment_status }}
                                    </span>
                                </td>
                                <td>{{ $p->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8">
                                <div class="d-flex justify-content-center">
                                    {!! $payments->links() !!}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
