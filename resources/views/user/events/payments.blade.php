@extends('user.layouts.master')
@section('title')
    Event Payments - {{ env('APP_NAME') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Payments for: {{ $event->title }}</h3>
                        <a href="{{ route('events.index') }}" class="btn btn-secondary">Back to Events</a>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Total Revenue</h5>
                                    <h3 class="text-success">${{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Pending Payments</h5>
                                    <h3 class="text-warning">${{ number_format($pendingRevenue, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Total Transactions</h5>
                                    <h3>{{ $event->payments->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($event->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->transaction_id }}</td>
                                        <td>{{ $payment->user->getFullNameAttribute() }}</td>
                                        <td>{{ $payment->user->email }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            @if ($payment->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-info">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No payments yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
