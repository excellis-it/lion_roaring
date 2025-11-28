@extends('user.layouts.master')
@section('title', 'My Membership')
@section('content')
    @php use App\Helpers\Helper; @endphp


    <div class="container-fluid">
        @php $currentPrice = isset($user_subscription->subscription_price) ? floatval($user_subscription->subscription_price) : 0; @endphp
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
                            @if ($user_subscription)
                                <span class="badge-active">Active</span>
                            @endif
                        </div>
                        <div class="my-3">
                            @if ($user_subscription)
                                <h4 class="mb-1">{{ $user_subscription->subscription_name }}</h4>
                                <div class="text-muted">Valid until:
                                    <strong>{{ $user_subscription->subscription_expire_date }}</strong>
                                </div>
                                <div class="text-muted">Remaining:
                                    <strong>{{ Helper::expireTo($user_subscription->subscription_expire_date) }}
                                        days</strong>
                                </div>
                                <div class="mt-3"><strong
                                        class="text-primary">{{ $user_subscription->subscription_price }}
                                        {{ $measurement->label ?? '' }}</strong></div>
                                <div class="mt-3">
                                    <form action="{{ route('user.membership.renew') }}" method="POST"
                                        style="display:inline">
                                        @csrf
                                        <button class="btn btn-primary">Renew</button>
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
                                    {{-- @if ($tier->cost == $maxCost)
                                        <div class="ribbon">Most Popular</div>
                                    @endif --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">{{ $tier->name }}</h5>
                                        <div class="text-primary fw-bold">
                                            <span class="badge badge-price bg-light text-dark">{{ $tier->cost }}
                                                {{ $measurement->label ?? '' }}</span>
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
                                            @if ($tier->cost > $currentPrice)
                                                <a href="{{ route('user.membership.checkout', $tier->id) }}"
                                                    class="btn btn-upgrade btn-primary">Upgrade to {{ $tier->name }}</a>
                                            @elseif ($tier->id == $user_subscription->plan_id)
                                                <span class="btn btn-sm btn-outline-primary disabled">Current Plan</span>
                                            @else
                                                <span class="btn btn-sm btn-outline-primary disabled">Lower Tier</span>
                                            @endif
                                        @else
                                            <a href="{{ route('user.membership.checkout', $tier->id) }}"
                                                class="btn btn-primary">Subscribe to {{ $tier->name }}</a>
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
    </style>
@endpush

@push('scripts')
    <script>
        $(document).on('click', '.toggle-benefits', function(e) {
            e.preventDefault();
            var el = $(this).prev('.benefits-list');
            el.toggleClass('expanded');
            $(this).text(el.hasClass('expanded') ? 'Show less' : 'Show more');
        });
    </script>
@endpush
