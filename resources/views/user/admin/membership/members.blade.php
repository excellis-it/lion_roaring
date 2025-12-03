@extends('admin.layouts.master')
@section('title', 'Membership Members')
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="page-header">
                <h4 class="page-title">Membership Members</h4>
                {{-- <a href="{{ route('admin.membership.index') }}" class="btn btn-secondary">Back to Plans</a> --}}
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Subscription</th>
                                <th>Price</th>
                                <th>Total Paid</th>
                                <th>Start Date</th>
                                <th>Expire Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $m)
                                <tr>
                                    <td>{{ $m->id }}</td>
                                    <td>
                                        @if ($m->user)
                                            {{ $m->user->first_name }} {{ $m->user->last_name }}<br>{{ $m->user->email }}
                                        @else
                                            <span class="text-muted">Unknown User (ID: {{ $m->user_id }})</span>
                                        @endif
                                    </td>
                                    <td>{{ $m->subscription_name }}</td>
                                    <td>{{ $m->subscription_price }}</td>
                                    <td>{{ $m->payments->sum('payment_amount') }}</td>
                                    <td>{{ $m->subscription_start_date }}</td>
                                    <td>{{ $m->subscription_expire_date }}</td>
                                    <td>
                                        @if ($m->user)
                                            <a href="{{ route('admin.membership.member.payments', $m->user_id) }}"
                                                class="btn btn-sm btn-primary">Payments</a>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>No User</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">{{ $members->links() }}</div>
                </div>
            </div>

        </div>
    </div>
@endsection
