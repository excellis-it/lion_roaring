@extends('user.layouts.master')
@section('title', 'Membership Expired')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border p-4 text-center">
            <h3 class="mb-3">Membership Expired</h3>
            <p class="text-muted mb-4">Your membership expired on
                <strong>{{ $user_subscription ? $user_subscription->subscription_expire_date : 'N/A' }}</strong>. To
                reactivate your access to the user panel, please renew your membership.</p>

            <div class="d-flex justify-content-center mb-3">
                <form method="POST" action="{{ route('user.membership.renew') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">Renew Membership</button>
                </form>
            </div>

            <div class="text-muted small">If you believe this is an error, contact support.</div>
        </div>
    </div>
@endsection
