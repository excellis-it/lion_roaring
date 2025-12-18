@extends('user.layouts.master')
@section('title', 'Membership Members')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h3 class="mb-3">Membership Members</h3>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle bg-white color_body_text">
                    <thead class="color_head">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Subscription</th>
                            <th>Method</th>
                            <th>Price</th>
                            <th>Total Paid</th>
                            <th>Start Date</th>
                            <th>Expire Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $m)
                            <tr>
                                <td>{{ $m->id }}</td>
                                <td>
                                    @if ($m->user)
                                        <div>{{ $m->user->first_name }} {{ $m->user->last_name }}</div>
                                        <small class="text-muted">{{ $m->user->email }}</small>
                                    @else
                                        <span class="text-muted">Unknown User (ID: {{ $m->user_id }})</span>
                                    @endif
                                </td>
                                <td>{{ $m->subscription_name }}</td>
                                <td>
                                    @if (($m->subscription_method ?? 'amount') === 'token')
                                        <span class="badge bg-info">Token</span>
                                    @else
                                        <span class="badge bg-success">Amount</span>
                                    @endif
                                </td>
                                <td>
                                    @if (($m->subscription_method ?? 'amount') === 'token')
                                        {{ $m->life_force_energy_tokens ?? $m->subscription_price }}
                                        {{ $measurement->label ?? 'Life Force Energy' }}
                                    @else
                                        ${{ number_format((float) $m->subscription_price, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if (($m->subscription_method ?? 'amount') === 'token')
                                        -
                                    @else
                                        ${{ number_format((float) $m->payments->sum('payment_amount'), 2) }}
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($m->subscription_start_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($m->subscription_expire_date)->format('M d, Y') }}</td>
                                <td>
                                    @if ($m->user)
                                        <a href="{{ route('user.membership.member.payments', $m->user_id) }}"
                                            class="btn btn-sm btn-primary">View Payments</a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>No User</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No members found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <div class="d-flex justify-content-center">
                                    {!! $members->links() !!}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
