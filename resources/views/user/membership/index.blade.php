@extends('user.layouts.master')
@section('title', 'My Membership')
@section('content')
    @php use App\Helpers\Helper; @endphp


    <div class="container-fluid">
        @php
            $currentMethod = $user_subscription->subscription_method ?? 'amount';
            $currentPrice = isset($user_subscription->subscription_price)
                ? floatval($user_subscription->subscription_price)
                : 0;
        @endphp
        <div class="bg_white_border py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">My Membership</h3>
                    <p class="text-muted small mb-0">Manage your plan — renew, upgrade and view benefits</p>
                </div>
                <div class="text-end d-none d-md-block">
                    <small class="text-muted">Tip: Upgrade to unlock more benefits</small>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <div
                        class="card p-4 h-100 text-center {{ $user_subscription ? 'current-card' : '' }} membership-current-card">
                        <div class="mb-3 d-flex align-items-center justify-content-center">
                            <h5 class="mb-0 me-2">My Current Membership</h5>

                        </div>
                        <div class="my-3 text-start">
                            @if ($user_subscription)
                                @php
                                    $expireDate = \Carbon\Carbon::parse($user_subscription->subscription_expire_date);
                                    $remainingDays = now()->diffInDays($expireDate, false);
                                    $canRenew = ($remainingDays <= 30 && $remainingDays >= 0) || $expireDate->isPast();
                                @endphp

                                <h4 class="mb-1">{{ $user_subscription->subscription_name }}</h4>
                                <div class="text-muted">Valid until:
                                    <strong>{{ date('F j, Y', strtotime($user_subscription->subscription_expire_date)) }}</strong>
                                </div>
                                <div class="text-muted">Remaining:
                                    <strong>
                                        @if ($expireDate->isPast())
                                            Expired
                                        @else
                                            {{ $remainingDays }} days
                                        @endif
                                    </strong>
                                </div>
                                {{-- <div class="mt-3"><strong class="text-primary">
                                        @if (($user_subscription->subscription_method ?? 'amount') === 'token')
                                            {{ $user_subscription->life_force_energy_tokens ?? $user_subscription->subscription_price }}
                                            {{ $measurement->label ?? 'Life Force Energy' }}
                                        @else
                                            ${{ number_format((float) $user_subscription->subscription_price, 2) }}
                                        @endif
                                    </strong></div> --}}

                                {{-- Progress bar and renew action --}}
                                {{-- @php
                                    $start = \Carbon\Carbon::parse(
                                        $user_subscription->subscription_start_date ?? now(),
                                    );
                                    $end = \Carbon\Carbon::parse($user_subscription->subscription_expire_date ?? now());
                                    $totalDays = max(1, $start->diffInDays($end));
                                    $elapsed = max(0, $totalDays - max(0, $remainingDays));
                                    $percent = (int) round(($elapsed / $totalDays) * 100);
                                @endphp
                                <div class="progress mb-2" style="height:8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted d-block mb-2">Membership progress: {{ $percent }}%</small> --}}

                                <div class="mt-3">
                                    <form action="{{ route('user.membership.renew') }}" method="POST">
                                        @csrf
                                        @if ($canRenew)
                                            <button type="submit" class="btn btn-primary">Renew</button>
                                        @else
                                            @php $daysToOpen = max(0, $remainingDays - 30); @endphp
                                            <button type="button" class="btn btn-secondary" disabled>Renew (available in
                                                {{ $daysToOpen }} days)</button>
                                            {{-- <small class="d-block text-muted mt-1">Renewals open 30 days before
                                                expiry.</small> --}}
                                        @endif
                                    </form>
                                </div>
                            @else
                                <p class="text-danger">No active subscription</p>
                                {{-- <a href="{{ route('user.membership.index') }}" class="btn btn-success">View Plans</a> --}}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        @php
                            $maxCost = $tiers->max('cost');
                        @endphp
                        @foreach ($tiers as $tier)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 p-4 tier-card position-relative">
                                    @if ($tier->cost == $maxCost)
                                        <div class="ribbon">Most Popular</div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">{{ $tier->name }}</h5>
                                        <div class="text-primary fw-bold">
                                            @if (($tier->pricing_type ?? 'amount') === 'token')
                                                <span class="badge badge-price bg-light text-dark">
                                                    {{ $tier->life_force_energy_tokens }}
                                                    {{ $measurement->label ?? 'Life Force Energy' }}
                                                </span>
                                            @else
                                                <span
                                                    class="badge badge-price bg-light text-dark">${{ number_format((float) $tier->cost, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 text-dark">{{ $tier->description }}</div>
                                    <ul class="mb-3 list-unstyled">
                                        @foreach ($tier->benefits as $b)
                                            <li class="mb-2"><i
                                                    class="fa fa-check text-success me-2"></i>{{ $b->benefit }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-auto text-center">
                                        @if ($user_subscription)
                                            @if ($tier->id == $user_subscription->plan_id)
                                                <span class="btn btn-sm btn-outline-primary disabled">Current Plan</span>
                                            @elseif (($tier->pricing_type ?? 'amount') === 'token')
                                                <button type="button"
                                                    class="btn btn-upgrade btn-primary js-token-subscribe"
                                                    data-tier-id="{{ $tier->id }}"
                                                    data-tier-name="{{ $tier->name }}"
                                                    data-agree-description="{{ e($tier->agree_description) }}">
                                                    Subscribe to {{ $tier->name }}
                                                </button>
                                            @else
                                                @if (($currentMethod ?? 'amount') === 'amount' && floatval($tier->cost) > $currentPrice)
                                                    <button type="button"
                                                        class="btn btn-upgrade btn-primary js-promo-checkout"
                                                        data-tier-id="{{ $tier->id }}"
                                                        data-tier-name="{{ $tier->name }}"
                                                        data-tier-cost="{{ $tier->cost }}">Upgrade to
                                                        {{ $tier->name }}</button>
                                                @else
                                                    <span class="btn btn-sm btn-outline-primary disabled"></span>
                                                @endif
                                            @endif
                                        @else
                                            @if (($tier->pricing_type ?? 'amount') === 'token')
                                                <button type="button" class="btn btn-primary js-token-subscribe"
                                                    data-tier-id="{{ $tier->id }}"
                                                    data-tier-name="{{ $tier->name }}"
                                                    data-agree-description="{{ e($tier->agree_description) }}">
                                                    Subscribe to {{ $tier->name }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-primary js-promo-checkout"
                                                    data-tier-id="{{ $tier->id }}"
                                                    data-tier-name="{{ $tier->name }}"
                                                    data-tier-cost="{{ $tier->cost }}">Subscribe to
                                                    {{ $tier->name }}</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token agree description modal -->
    <div class="modal fade" id="tokenAgreeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenAgreeModalTitle">Tier - Agreement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 text-muted small">Please review and accept to subscribe.</div>
                    <div class="border rounded p-3" style="white-space: pre-wrap;" id="tokenAgreeModalBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Reject</button>
                    <form method="POST" id="tokenAgreeForm">
                        @csrf
                        <button type="submit" class="btn btn-primary">Accept</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Code Modal -->
    <div class="modal fade" id="promoCodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoCodeModalTitle">Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 id="selectedPlan"></h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Original Price:</span>
                            <strong id="originalPrice"></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 text-success" id="discountRow"
                            style="display: none !important;">
                            <span>Discount:</span>
                            <strong id="discountAmount"></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span><strong>Final Price:</strong></span>
                            <strong class="text-primary" id="finalPrice"></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="promoCodeInput" class="form-label">Promo Code (Optional)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promoCodeInput"
                                placeholder="Enter promo code">
                            <button class="btn btn-outline-secondary" type="button" id="applyPromoBtn">Apply</button>
                        </div>
                        <div class="form-text" id="promoMessage"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="proceedToCheckout">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('frontend.membership._card-styles')

@push('styles')
    <style>
        .benefits-list {
            max-height: 120px;
            overflow: hidden;
        }

        .benefits-list.collapsed {
            max-height: 120px;
        }

        .benefits-list.expanded {
            max-height: none;
        }

        /* Pricing UI tweaks */
        .card h5 {
            font-size: 1.1rem;
        }

        .badge-price {
            font-size: 1rem;
            padding: 8px 12px;
        }

        .current-card {
            border: 2px solid var(--theme);
            box-shadow: 0 6px 20px rgba(100, 50, 113, 0.06);
        }

        .membership-current-card {
            background: linear-gradient(90deg, var(--theme-50), var(--theme-25));
        }

        .badge-active {
            display: inline-block;
            padding: .25rem .6rem;
            border-radius: .6rem;
            background: var(--theme);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(100, 50, 113, 0.18);
            animation: badgePulse 2.2s infinite;
            font-size: 0.75rem;
        }

        @keyframes badgePulse {
            0% {
                transform: scale(1);
                box-shadow: 0 6px 18px rgba(100, 50, 113, 0.18);
            }

            50% {
                transform: scale(1.04);
                box-shadow: 0 12px 30px rgba(100, 50, 113, 0.22);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 6px 18px rgba(100, 50, 113, 0.18);
            }
        }

        .membership-current-card {
            box-shadow: 0 18px 40px rgba(100, 50, 113, 0.12);
            border-top: 6px solid var(--theme);
            border-bottom: 4px solid var(--theme-25);
        }

        /* Tier card visuals */
        .tier-card {
            transition: transform .18s ease, box-shadow .18s ease;
            border-radius: 10px;
        }

        .tier-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .ribbon {
            position: absolute;
            left: -10px;
            top: 12px;
            background: var(--theme);
            color: #fff;
            padding: 6px 10px;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 4px;
        }

        .btn-upgrade {
            width: 100%;
        }

        @media (max-width: 767px) {
            .membership-current-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Existing functionality
        $(document).on('click', '.toggle-benefits', function(e) {
            e.preventDefault();
            var el = $(this).prev('.benefits-list');
            el.toggleClass('expanded');
            $(this).text(el.hasClass('expanded') ? 'Show less' : 'Show more');
        });

        $(document).on('click', '.js-token-subscribe', function() {
            var tierId = $(this).data('tier-id');
            var tierName = $(this).data('tier-name');
            var agree = $(this).data('agree-description') || '';

            $('#tokenAgreeModalTitle').text(tierName + ' Tier - Agreement');
            $('#tokenAgreeModalBody').text(agree);
            $('#tokenAgreeForm').attr('action', '{{ url('user/membership/token-subscribe') }}/' + tierId);

            if (window.bootstrap && bootstrap.Modal) {
                var modal = new bootstrap.Modal(document.getElementById('tokenAgreeModal'));
                modal.show();
            } else {
                $('#tokenAgreeModal').modal('show');
            }
        });

        // Promo Code Modal Functionality
        var currentTierId = null;
        var currentTierName = '';
        var currentTierCost = 0;
        var appliedPromoCode = '';
        var finalAmount = 0;

        $(document).on('click', '.js-promo-checkout', function() {
            currentTierId = $(this).data('tier-id');
            currentTierName = $(this).data('tier-name');
            currentTierCost = parseFloat($(this).data('tier-cost'));
            appliedPromoCode = '';
            finalAmount = currentTierCost;

            // Reset modal
            $('#promoCodeModalTitle').text('Checkout - ' + currentTierName);
            $('#selectedPlan').text(currentTierName + ' Membership');
            $('#originalPrice').text('$' + currentTierCost.toFixed(2));
            $('#finalPrice').text('$' + currentTierCost.toFixed(2));
            $('#discountRow').hide();
            $('#discountAmount').text('');
            $('#promoCodeInput').val('');
            $('#promoMessage').text('').removeClass('text-success text-danger');

            // Show modal
            if (window.bootstrap && bootstrap.Modal) {
                var modal = new bootstrap.Modal(document.getElementById('promoCodeModal'));
                modal.show();
            } else {
                $('#promoCodeModal').modal('show');
            }
        });

        // Apply promo code
        $('#applyPromoBtn').on('click', function() {
            var promoCode = $('#promoCodeInput').val().trim();

            if (!promoCode) {
                $('#promoMessage').text('Please enter a promo code').removeClass('text-success').addClass(
                    'text-danger');
                return;
            }

            // Show loading
            $(this).prop('disabled', true).text('Validating...');
            $('#promoMessage').text('Validating promo code...').removeClass('text-success text-danger');

            // AJAX validation
            $.ajax({
                url: '{{ route('user.promo-codes.validate') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    code: promoCode,
                    tier_id: currentTierId
                },
                success: function(response) {
                    if (response.valid) {
                        appliedPromoCode = promoCode;
                        var discount = parseFloat(response.discount);
                        finalAmount = parseFloat(response.final_price);

                        // Update UI
                        $('#discountAmount').text('-$' + discount.toFixed(2));
                        $('#finalPrice').text('$' + finalAmount.toFixed(2));
                        $('#discountRow').show();
                        $('#promoMessage').text('✓ Promo code applied successfully!').removeClass(
                            'text-danger').addClass('text-success');
                        $('#promoCodeInput').prop('readonly', true);
                        $('#applyPromoBtn').text('Applied').addClass('btn-success').removeClass(
                            'btn-outline-secondary');
                    } else {
                        $('#promoMessage').text(response.message || 'Invalid promo code').removeClass(
                            'text-success').addClass('text-danger');
                    }
                },
                error: function(xhr) {
                    var message = 'Failed to validate promo code';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $('#promoMessage').text(message).removeClass('text-success').addClass(
                    'text-danger');
                },
                complete: function() {
                    $('#applyPromoBtn').prop('disabled', false).text('Apply');
                }
            });
        });

        // Allow Enter key to apply promo code
        $('#promoCodeInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#applyPromoBtn').click();
            }
        });

        // Proceed to checkout
        $('#proceedToCheckout').on('click', function() {
            var checkoutUrl = '{{ url('user/membership/checkout') }}/' + currentTierId;

            if (appliedPromoCode) {
                checkoutUrl += '?promo_code=' + encodeURIComponent(appliedPromoCode);
            }

            window.location.href = checkoutUrl;
        });

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
