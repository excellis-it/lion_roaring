@extends('user.layouts.master')
@section('title', 'My Membership')
@section('content')
    @php use App\Helpers\Helper; @endphp
    <div class="container-fluid">
        @php $currentPrice = isset($user_subscription->subscription_price) ? floatval($user_subscription->subscription_price) : 0; @endphp
        <div class="bg_white_border py-4">
            <h3>My Membership</h3>
            <p class="text-muted">Manage your current plan, renew or upgrade to a higher tier.</p>
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <div class="card p-4 h-100 text-center {{ $user_subscription ? 'current-card' : '' }}">
                        <div class="mb-3">
                            <h5 class="mb-0">My Current Membership</h5>
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
                                <div class="mt-3"><strong>{{ $user_subscription->subscription_price }}
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
                        @foreach ($tiers as $tier)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">{{ $tier->name }}</h5>
                                        <div class="text-primary fw-bold">{{ $tier->cost }} {{ $measurement->label ?? '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 text-dark">{{ $tier->description }}</div>
                                    <ul class="mb-3">
                                        @foreach ($tier->benefits as $b)
                                            <li>{{ $b->benefit }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-auto text-center">
                                        @if ($user_subscription)
                                            @if ($tier->cost > $currentPrice)
                                                <a href="{{ route('user.membership.checkout', $tier->id) }}"
                                                    class="btn btn-primary">Upgrade to {{ $tier->name }}</a>
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
            border: 2px solid #0d6efd;
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
