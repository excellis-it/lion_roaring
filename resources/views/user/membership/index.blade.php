@extends('user.layouts.master')
@section('title', 'My Plan')
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
            @if ($isExpired)
                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <div>
                        Your plan expired on
                        <strong>{{ \Carbon\Carbon::parse($user_subscription->subscription_expire_date)->format('F d, Y') }}</strong>.
                        Please select a plan below to renew your account.
                    </div>
                </div>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">My Plan</h3>
                    <p class="text-muted small mb-0">Manage your plan — renew, change plans, and view benefits</p>
                </div>
                <div class="text-end d-none d-md-block">
                    <small class="text-muted">Tip: Change plans to unlock more benefits</small>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <div
                        class="card p-4 h-100 text-center {{ $user_subscription ? 'current-card' : '' }} membership-current-card">
                        <div class="mb-3 d-flex align-items-center justify-content-center">
                            <h5 class="mb-0 me-2">{{ $measurement->membership_card_title ?? 'My Current Plan' }}</h5>

                        </div>
                        <div class="my-3 text-start">
                            @if ($user_subscription)
                                @php
                                    $expireDate = \Carbon\Carbon::parse($user_subscription->subscription_expire_date);
                                    $remainingDays = now()->diffInDays($expireDate, false);
                                    $canRenew = true;
                                @endphp

                                <h4 class="mb-1">{{ $user_subscription->subscription_name }}</h4>
                                <div class="text-muted">Email:
                                    <strong>{{ auth()->user()->email ?? 'N/A' }}</strong>
                                </div>
                                @if ($user_subscription->subscription_start_date)
                                    <div class="text-muted">Started:
                                        <strong>{{ date('F j, Y', strtotime($user_subscription->subscription_start_date)) }}</strong>
                                    </div>
                                @endif
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
                                    @if ($canRenew)
                                        @php $currentTier = $tiers->firstWhere('id', $user_subscription->plan_id); @endphp
                                        <button type="button" class="btn btn-primary w-100 js-promo-checkout"
                                            data-tier-id="{{ $currentTier->id ?? '' }}"
                                            data-tier-name="{{ $currentTier->name ?? $user_subscription->subscription_name }}"
                                            data-monthly-cost="{{ $currentTier->monthly_cost ?? 0 }}"
                                            data-yearly-cost="{{ $currentTier->yearly_cost ?? 0 }}"
                                            data-tier-cost="{{ $currentTier->cost ?? $user_subscription->subscription_price }}"
                                            data-renew="1">
                                            Renew Now
                                        </button>
                                    @else
                                        @php $daysToOpen = max(0, $remainingDays - 30); @endphp
                                        <button type="button" class="btn btn-secondary w-100" disabled>Renew Now (available
                                            in
                                            {{ $daysToOpen }} days)</button>
                                    @endif
                                    <button type="button" class="btn btn-primary btn-sm mt-2 w-100" data-bs-toggle="modal"
                                        data-bs-target="#cancelMembershipModal">Cancel Plan</button>
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
                                    {{-- @if ($tier->cost == $maxCost)
                                        <div class="ribbon">Most Popular</div>
                                    @endif --}}
                                    <div class="mb-2">
                                        <h5 class="mb-1">{{ $tier->name }}</h5>
                                        <div class="text-primary fw-bold">
                                            @if (($tier->pricing_type ?? 'amount') === 'token')
                                                <span class="badge badge-price bg-light text-dark">
                                                    {{ $tier->life_force_energy_tokens }}
                                                    {{ $measurement->label ?? 'Life Force Energy' }}
                                                </span>
                                            @else
                                                <span class="badge badge-price bg-light text-dark">
                                                    ${{ number_format((float) $tier->monthly_cost, 2) }}/mo or
                                                    ${{ number_format((float) $tier->yearly_cost, 2) }}/yr
                                                </span>
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
                                    @if (!auth()->user()->hasNewRole('SUPER ADMIN'))
                                        <div class="mt-auto text-center">
                                            @if (($tier->pricing_type ?? 'amount') === 'token')
                                                <button type="button"
                                                    class="btn btn-upgrade btn-primary js-token-subscribe"
                                                    data-tier-id="{{ $tier->id }}"
                                                    data-tier-name="{{ $tier->name }}"
                                                    data-agree-description="{{ e($tier->agree_description) }}">
                                                    Subscribe to {{ $tier->name }}
                                                </button>
                                            @elseif (floatval($tier->yearly_cost) == 0 && floatval($tier->monthly_cost) == 0)
                                                <form method="POST"
                                                    action="{{ route('user.membership.upgrade', $tier->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-upgrade btn-primary">Subscribe to
                                                        {{ $tier->name }}</button>
                                                </form>
                                            @else
                                                <button type="button"
                                                    class="btn btn-upgrade btn-primary js-promo-checkout"
                                                    data-tier-id="{{ $tier->id }}"
                                                    data-tier-name="{{ $tier->name }}"
                                                    data-monthly-cost="{{ $tier->monthly_cost }}"
                                                    data-yearly-cost="{{ $tier->yearly_cost }}"
                                                    data-renew="{{ ($user_subscription && $tier->id == $user_subscription->plan_id) ? '1' : '0' }}">
                                                    {{ $user_subscription && $tier->id == $user_subscription->plan_id ? 'Renew Plan' : 'Select ' . $tier->name }}
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token agree description modal (Premium Redesign Fixed) -->
    <div class="modal fade" id="tokenAgreeModal" tabindex="-1" role="dialog" aria-labelledby="tokenAgreeModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0"
                style="border-radius: 20px; overflow: hidden; background-color: #ffffff;">
                <!-- Header with Purple Gradient -->
                <div class="modal-header border-0 p-4 pb-0 position-relative d-block"
                    style="background: linear-gradient(135deg, #6f42c1 0%, #4e2a84 100%); min-height: 110px;">
                    <button type="button" class="btn-close btn-close-white position-absolute" data-bs-dismiss="modal"
                        aria-label="Close" style="top: 20px; right: 20px; z-index: 10;"></button>
                    <div class="position-relative" style="z-index: 1;">
                        <h4 class="modal-title text-white fw-bold mb-0" id="tokenAgreeModalTitle">Tier - Agreement</h4>
                        <p class="text-white-50 mb-0 small"><i class="fa fa-file-signature me-1"></i> Terms & Conditions
                        </p>
                    </div>
                </div>

                <div class="modal-body p-4" style="margin-top: -30px; position: relative; z-index: 2;">
                    <!-- Agreement Content Card -->
                    <div class="card border-0 shadow-sm p-4"
                        style="border-radius: 15px; border: 1px solid rgba(0,0,0,0.05) !important;">
                        <div class="mb-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;"> Please
                            review and accept to subscribe.</div>
                        <div class="bg-light p-3 rounded-3"
                            style="white-space: pre-wrap; font-size: 0.9rem; color: #444; max-height: 250px; overflow-y: auto; border: 1px solid #eee;"
                            id="tokenAgreeModalBody"></div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill flex-grow-1 py-2 fw-bold text-muted border-0"
                        data-bs-dismiss="modal" style="background: #f8f9fa;">
                        Reject
                    </button>
                    <form method="POST" id="tokenAgreeForm" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm"
                            style="background: #6f42c1; border: none;">
                            Accept & Subscribe <i class="fa fa-check-circle ms-2 small"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Code Modal — Secure Payment -->
    <div class="modal fade" id="promoCodeModal" tabindex="-1" role="dialog" aria-labelledby="promoCodeModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sp-secure-modal shadow-lg border-0">
                <div class="modal-header sp-secure-header border-0 position-relative d-block">
                    <button type="button" class="btn-close btn-close-white sp-secure-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="w-100">
                        <h2 class="sp-secure-title" id="promoCodeModalTitle">Secure Payment</h2>
                        <p class="sp-secure-subtitle mb-0" id="securePaymentSubtitle">
                            <i class="fa fa-shield-alt me-1"></i> Member Plan
                        </p>
                    </div>
                </div>

                <div class="modal-body p-4 pt-3">
                    <!-- Summary Card -->
                    <div class="card border-0 shadow-sm mb-4"
                        style="border-radius: 15px; border: 1px solid rgba(0,0,0,0.05) !important;">
                        <div class="card-body p-4 text-dark">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-light p-2 me-3 d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; background-color: rgba(111, 66, 193, 0.1) !important;">
                                    <i class="fa fa-crown text-primary"
                                        style="font-size: 20px; color: #6f42c1 !important;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold" id="selectedPlan">Plan Name</h6>
                                    <span class="text-muted small" id="planTypeLabel">Member Plan</span>
                                </div>
                            </div>

                            <!-- Billing period -->
                            <p class="fw-bold small text-muted text-uppercase mb-2">Billing Period</p>
                            <div class="sp-billing-options mb-3" id="billingPeriodToggle">
                                <label class="sp-billing-option">
                                    <input type="radio" name="billing_period" value="monthly" class="billing-period-radio">
                                    <span class="sp-billing-card">
                                        <span class="sp-radio-dot" aria-hidden="true"></span>
                                        <span class="sp-billing-text">
                                            <span class="sp-billing-label">Monthly</span>
                                            <span class="sp-billing-price" id="monthlyPriceLabel">$0.00/mo</span>
                                        </span>
                                    </span>
                                </label>
                                <label class="sp-billing-option">
                                    <input type="radio" name="billing_period" value="yearly" class="billing-period-radio" checked>
                                    <span class="sp-billing-card">
                                        <span class="sp-radio-dot" aria-hidden="true"></span>
                                        <span class="sp-billing-text">
                                            <span class="sp-billing-label">Yearly</span>
                                            <span class="sp-billing-price" id="yearlyPriceLabel">$0.00/yr</span>
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="p-3 rounded-3" style="background: #fdfbff; border: 1px solid #eee;">
                                <div class="d-flex justify-content-between align-items-center mb-2 text-success"
                                    id="discountRow" style="display: none;">
                                    <span class="small"><i class="fa fa-tag me-1"></i> Discount</span>
                                    <span class="fw-bold" id="discountAmount">-$0.00</span>
                                </div>
                                <hr class="my-2 opacity-10" id="discountDivider" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">Amount To Pay</h6>
                                    <h5 class="mb-0 fw-bold text-primary" id="finalPrice"
                                        style="color: #643271 !important;">$0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="px-2">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                            <i class="fa fa-ticket-alt me-1"></i> Promo Code
                        </label>
                        <div class="input-group mb-2 border rounded-pill p-1 bg-white shadow-sm overflow-hidden">
                            <input type="text" class="form-control border-0 bg-transparent px-3" id="promoCodeInput"
                                placeholder="Enter code here..." style="box-shadow: none;">
                            <button class="btn px-4 fw-bold rounded-pill sp-apply-btn" type="button" id="applyPromoBtn">
                                Apply
                            </button>
                        </div>
                        <div id="promoMessage" class="small mt-1 ms-3"></div>
                    </div>

                    <!-- Inline Card Payment -->
                    <div class="px-2 mt-3" id="membershipCardSection" style="display:none;">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                            <i class="fa fa-credit-card me-1"></i> Card Details
                        </label>
                        <div id="membership-card-element"
                            style="border: 1px solid #dee2e6; border-radius: 8px; padding: 12px 14px; background: #fff;">
                        </div>
                        <div id="membership-card-errors" class="text-danger small mt-2"></div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill flex-grow-1 py-2 fw-bold text-muted border-0"
                        data-bs-dismiss="modal" style="background: #f8f9fa;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill flex-grow-1 py-2 fw-bold shadow-sm sp-pay-btn"
                        id="proceedToCheckout">
                        Complete Payment <i class="fa fa-lock ms-2 small"></i>
                    </button>
                </div>
                <div class="text-center pb-3">
                    <span class="text-muted" style="font-size: 11px;"><i class="fa fa-shield-alt me-1"></i> Payments are secure and encrypted.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Membership Confirmation Modal -->
    <div class="modal fade" id="cancelMembershipModal" tabindex="-1" aria-labelledby="cancelMembershipModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 p-4 pb-0 position-relative d-block"
                    style="background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%); min-height: 110px;">
                    <button type="button" class="btn-close btn-close-white position-absolute" data-bs-dismiss="modal"
                        aria-label="Close" style="top: 20px; right: 20px; z-index: 10;"></button>
                    <div class="position-relative" style="z-index: 1;">
                        <h4 class="modal-title text-white fw-bold mb-0" id="cancelMembershipModalLabel">Cancel Plan
                        </h4>
                        <p class="text-white-50 mb-0 small"><i class="fa fa-exclamation-triangle me-1"></i> This action
                            cannot be undone</p>
                    </div>
                </div>
                <div class="modal-body p-4" style="margin-top: -20px; position: relative; z-index: 2;">
                    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                        <p class="mb-2"><strong>Are you sure you want to cancel your plan?</strong></p>
                        <ul class="text-muted small mb-0">
                            <li>Your subscription will be terminated immediately.</li>
                            <li>Your account will be deactivated.</li>
                            <li>You will be logged out.</li>
                            <li>An admin will be notified of your cancellation.</li>
                            <li><strong class="text-danger">No refunds will be issued.</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill flex-grow-1 py-2 fw-bold text-muted border-0"
                        data-bs-dismiss="modal" style="background: #f8f9fa;">
                        Keep Plan
                    </button>
                    <form action="{{ route('user.membership.cancel') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold shadow-sm">
                            Yes, Cancel Plan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('frontend.membership._card-styles')

@push('styles')
    @include('partials.secure-payment-styles')
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

        .flex-grow-2 {
            flex-grow: 2;
        }

        .bg-theme-10 {
            background-color: rgba(111, 66, 193, 0.1);
        }

        .text-theme {
            color: var(--theme) !important;
        }

        .btn-theme {
            background-color: var(--theme);
            color: white;
            border: none;
        }

        .btn-theme:hover {
            background-color: var(--theme-75);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.2);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .scale-effect {
            transition: transform 0.2s ease;
        }

        .scale-effect:hover {
            transform: scale(1.02);
        }

        @media (max-width: 767px) {
            .membership-current-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
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

        // ── Stripe inline card element ──────────────────────────────
        var membershipStripe = Stripe('{{ env('STRIPE_KEY') }}');
        var membershipElements = membershipStripe.elements();
        var membershipCard = membershipElements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });
        membershipCard.mount('#membership-card-element');
        membershipCard.addEventListener('change', function(event) {
            document.getElementById('membership-card-errors').textContent = event.error ? event.error.message : '';
        });

        // ── Promo Code Modal Functionality ─────────────────────────
        var currentTierId = null;
        var currentTierName = '';
        var currentMonthlyCost = 0;
        var currentYearlyCost = 0;
        var currentBillingPeriod = 'yearly';
        var appliedPromoCode = '';
        var finalAmount = 0;
        var isRenewMode = false;

        function getBasePrice() {
            return currentBillingPeriod === 'monthly' ? currentMonthlyCost : currentYearlyCost;
        }

        function clearPromoState() {
            appliedPromoCode = '';
            $('#promoCodeInput').val('').prop('readonly', false);
            $('#promoMessage').text('').removeClass('text-success text-danger');
            $('#discountRow').hide();
            $('#discountDivider').hide();
            $('#discountAmount').text('');
            $('#applyPromoBtn').text('Apply').prop('disabled', false);
        }

        function refreshPriceDisplay() {
            var base = getBasePrice();
            if (!appliedPromoCode) {
                finalAmount = base;
                $('#finalPrice').text('$' + base.toFixed(2));
                $('#discountRow').hide();
                $('#discountDivider').hide();
            }
            updateCardSectionVisibility();
        }

        function applyPromoValidation(promoCode, options) {
            options = options || {};
            var silent = !!options.silent;
            var $btn = $('#applyPromoBtn');

            if (!promoCode) {
                if (!silent) {
                    $('#promoMessage').text('Please enter a promo code').removeClass('text-success').addClass('text-danger');
                }
                return;
            }

            if (!silent) {
                $btn.prop('disabled', true).text('Validating...');
                $('#promoMessage').text('Validating promo code...').removeClass('text-success text-danger');
            }

            $.ajax({
                url: '{{ route('user.promo-codes.validate') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    code: promoCode,
                    tier_id: currentTierId,
                    billing_period: currentBillingPeriod
                },
                success: function(response) {
                    if (response.valid) {
                        appliedPromoCode = promoCode;
                        var discount = parseFloat(response.discount);
                        finalAmount = parseFloat(response.final_price);

                        $('#discountAmount').text('-$' + discount.toFixed(2));
                        $('#finalPrice').text('$' + finalAmount.toFixed(2));
                        $('#discountRow').show();
                        $('#discountDivider').show();
                        $('#promoCodeInput').val(promoCode).prop('readonly', true);
                        $('#promoMessage').html(
                            '<i class="fa fa-check-circle me-1"></i> Promo code applied successfully!'
                        ).removeClass('text-danger').addClass('text-success');
                        updateCardSectionVisibility();
                    } else {
                        clearPromoState();
                        refreshPriceDisplay();
                        $('#promoMessage').text(response.message || 'Invalid promo code').removeClass('text-success').addClass('text-danger');
                    }
                },
                error: function(xhr) {
                    clearPromoState();
                    refreshPriceDisplay();
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to validate promo code';
                    $('#promoMessage').text(message).removeClass('text-success').addClass('text-danger');
                },
                complete: function() {
                    if (!silent) {
                        $btn.prop('disabled', false).text('Apply');
                    }
                }
            });
        }

        $('input[name="billing_period"]').on('change', function() {
            currentBillingPeriod = $(this).val();
            var existingPromo = appliedPromoCode || ($('#promoCodeInput').prop('readonly') ? $('#promoCodeInput').val().trim() : '');
            if (existingPromo) {
                applyPromoValidation(existingPromo, { silent: true });
            } else {
                clearPromoState();
                refreshPriceDisplay();
            }
        });

        function updateCardSectionVisibility() {
            var isPaidTier = currentMonthlyCost > 0 || currentYearlyCost > 0;
            if (isPaidTier) {
                $('#membershipCardSection').show();
            } else {
                $('#membershipCardSection').hide();
            }
        }

        $(document).on('click', '.js-promo-checkout', function() {
            currentTierId = $(this).data('tier-id');
            currentTierName = $(this).data('tier-name');
            currentMonthlyCost = parseFloat($(this).data('monthly-cost')) || 0;
            currentYearlyCost = parseFloat($(this).data('yearly-cost')) || 0;
            if (currentMonthlyCost <= 0 && currentYearlyCost <= 0) {
                var legacyCost = parseFloat($(this).data('tier-cost')) || 0;
                if (legacyCost > 0) {
                    currentYearlyCost = legacyCost;
                }
            }
            isRenewMode = $(this).data('renew') == 1;
            currentBillingPeriod = 'yearly';
            appliedPromoCode = '';
            finalAmount = currentYearlyCost;

            $('input[name="billing_period"][value="yearly"]').prop('checked', true);
            $('#monthlyPriceLabel').text('$' + currentMonthlyCost.toFixed(2) + '/mo');
            $('#yearlyPriceLabel').text('$' + currentYearlyCost.toFixed(2) + '/yr');
            $('#selectedPlan').text(currentTierName);
            $('#planTypeLabel').text(isRenewMode ? 'Renewal' : 'Member Plan');
            $('#securePaymentSubtitle').html('<i class="fa fa-shield-alt me-1"></i> ' + (isRenewMode ? 'Renewal' : 'Member Plan'));
            clearPromoState();
            refreshPriceDisplay();
            $('#promoCodeModalTitle').text('Secure Payment');
            $('#membership-card-errors').text('');
            $('#proceedToCheckout').prop('disabled', false);
            updateCardSectionVisibility();

            if (finalAmount <= 0) {
                $('#proceedToCheckout').html('Complete Payment <i class="fa fa-lock ms-2 small"></i>');
            } else {
                $('#proceedToCheckout').html('Complete Payment <i class="fa fa-lock ms-2 small"></i>');
            }

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
            applyPromoValidation($('#promoCodeInput').val().trim());
        });

        $('#promoCodeInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#applyPromoBtn').click();
            }
        });

        // Proceed to checkout — inline Stripe payment
        $('#proceedToCheckout').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
            $('#membership-card-errors').text('');

            function submitPayment(stripeToken) {
                $.ajax({
                    url: '{{ route('user.membership.inline-payment') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tier_id: currentTierId,
                        promo_code: appliedPromoCode || '',
                        renew: isRenewMode ? 1 : 0,
                        billing_period: currentBillingPeriod,
                        stripeToken: stripeToken || ''
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close modal and redirect with success message
                            $('#promoCodeModal').modal('hide');
                            window.location.href =
                                '{{ route('user.membership.index') }}?payment=success';
                        } else {
                            $('#membership-card-errors').text(response.message ||
                                'Payment failed. Please try again.');
                            btn.prop('disabled', false).html((isRenewMode ? 'Renew' : 'Checkout') +
                                ' <i class="fa fa-arrow-right ms-2 small"></i>');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON
                            .message : 'An error occurred. Please try again.';
                        $('#membership-card-errors').text(msg);
                        btn.prop('disabled', false).html((isRenewMode ? 'Renew' : 'Checkout') +
                            ' <i class="fa fa-arrow-right ms-2 small"></i>');
                    }
                });
            }

            var isPaidTier = currentMonthlyCost > 0 || currentYearlyCost > 0;
            if (isPaidTier) {
                membershipStripe.createToken(membershipCard).then(function(result) {
                    if (result.error) {
                        $('#membership-card-errors').text(result.error.message);
                        btn.prop('disabled', false).html((isRenewMode ? 'Renew' : 'Checkout') +
                            ' <i class="fa fa-arrow-right ms-2 small"></i>');
                    } else {
                        submitPayment(result.token.id);
                    }
                });
            } else {
                submitPayment('free_tier');
            }
        });

        // Show success toast if redirected back after inline payment
        @if (request('payment') === 'success')
            toastr.success('Payment successful! Your membership has been updated.');
        @endif

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
