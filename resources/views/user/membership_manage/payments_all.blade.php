@extends('user.layouts.master')
@section('title', 'All Membership Payments')
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="page-header">
                <h4 class="page-title">Membership Payments</h4>
                {{-- <a href="{{ route('user.membership.manage') }}" class="btn btn-secondary">Back to Plans</a> --}}
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
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
                            @foreach ($payments as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td>
                                        @if ($p->user)
                                            {{ $p->user->first_name }} {{ $p->user->last_name }}<br>{{ $p->user->email }}
                                        @else
                                            <span class="text-muted">Unknown User (ID: {{ $p->user_id }})</span>
                                        @endif
                                    </td>
                                    <td>{{ $p->userSubscription ? $p->userSubscription->subscription_name : '-' }}</td>
                                    <td>{{ $p->transaction_id }}</td>
                                    <td>{{ $p->payment_amount }}</td>
                                    <td>{{ $p->payment_method }}</td>
                                    <td>{{ $p->payment_status }}</td>
                                    <td>{{ $p->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">{{ $payments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
