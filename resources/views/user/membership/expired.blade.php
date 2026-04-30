@extends('user.layouts.master')
@section('title', 'Account Expired')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border p-4 text-center">
            <h3 class="mb-3">Account Expired</h3>
            <p class="text-muted mb-2">Your account expired on
                <strong>{{ $user_subscription ? \Carbon\Carbon::parse($user_subscription->subscription_expire_date)->format('F d, Y') : 'N/A' }}</strong>.
            </p>
            <p class="mb-4" style="color: #c0392b; font-weight: 600;">
                If you do not renew, your account will be deactivated.
            </p>

            <div class="d-flex justify-content-center mb-3">
                <form method="POST" action="{{ route('user.membership.renew') }}" id="renewForm">
                    @csrf

                    {{-- Promo Code --}}
                    <div class="mb-3" style="max-width: 360px; margin: 0 auto;">
                        <div class="input-group">
                            <input type="text" id="promo_code_input" class="form-control"
                                placeholder="Promo code (optional)">
                            <button type="button" class="btn btn-outline-secondary" id="applyPromoBtn">Apply</button>
                        </div>
                        <div id="promo_message" class="mt-1 small"></div>
                        <input type="hidden" name="promo_code" id="promo_code_hidden">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Renew Now</button>
                </form>
            </div>

            <div class="text-muted small">Need help? Contact support.</div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('applyPromoBtn').addEventListener('click', function () {
        var code = document.getElementById('promo_code_input').value.trim();
        var msgEl = document.getElementById('promo_message');
        var hiddenEl = document.getElementById('promo_code_hidden');

        if (!code) {
            msgEl.innerHTML = '<span class="text-danger">Please enter a promo code.</span>';
            return;
        }

        fetch('{{ route('user.membership.apply-promo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                promo_code: code,
                tier_id: {{ $user_subscription->plan_id ?? 'null' }}
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hiddenEl.value = code;
                msgEl.innerHTML = '<span class="text-success">' + data.message + '</span>';
            } else {
                hiddenEl.value = '';
                msgEl.innerHTML = '<span class="text-danger">' + data.message + '</span>';
            }
        })
        .catch(() => {
            msgEl.innerHTML = '<span class="text-danger">Could not validate promo code.</span>';
        });
    });
</script>
@endpush
