@extends('user.layouts.master')
@section('title', 'Membership Expired')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border p-4 text-center">
            <h3 class="mb-3">Membership Expired</h3>
            <p class="text-muted mb-2">Your membership expired on
                <strong>{{ $user_subscription ? \Carbon\Carbon::parse($user_subscription->subscription_expire_date)->format('F d, Y') : 'N/A' }}</strong>.
            </p>
            <p class="mb-4" style="color: #c0392b; font-weight: 600;">
                If you do not renew your membership, your account will be deactivated.
            </p>

            <div class="d-flex justify-content-center mb-3">
                <form method="POST" action="{{ route('user.membership.renew') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">Renew Membership</button>
                </form>
            </div>

            <div class="text-muted small">Need help? Contact support.</div>
        </div>
    </div>
@endsection
