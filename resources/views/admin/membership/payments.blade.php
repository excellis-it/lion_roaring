@extends('admin.layouts.master')
@section('title', 'Payment History')
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="page-header">
                <h4 class="page-title">Payment History for {{ $user->first_name }} {{ $user->last_name }}</h4>
                {{-- <a href="{{ route('admin.membership.members') }}" class="btn btn-secondary">Back to Members</a> --}}
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
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
                </div>
            </div>
        </div>
    </div>
@endsection
