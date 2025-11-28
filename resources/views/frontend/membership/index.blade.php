@extends('frontend.layouts.master')
@section('title', env('APP_NAME') . ' - Membership')
@section('content')

    <section class="inner_banner_sec"
        style="background-image: url({{ asset('frontend_assets/uploads/2023/07/inner_banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>Membership</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="py-5 login-sec">
        <div class="container">
            <div class="row mt-5">

                <div class="mt-5">

                </div>

                @php $maxCost = $tiers->max('cost'); @endphp
                @foreach ($tiers as $tier)
                    <div class="col-lg-4 col-md-4 mb-4">
                        <div class="card h-100 shadow-sm p-4 tier-card position-relative">
                            @if ($tier->cost == $maxCost)
                                <div class="ribbon">Most Popular</div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title mb-0">{{ $tier->name }}</h4>
                                    <div class="text-primary fw-bold">
                                        <span class="badge badge-price bg-light text-dark">{{ $tier->cost }}
                                            {{ $measurement->label ?? '' }}</span>
                                    </div>
                                </div>
                                <p class="text-muted mb-3">{{ $tier->description }}</p>
                                <ul class="list-unstyled mb-4">
                                    @foreach ($tier->benefits as $b)
                                        <li class="mb-2"><i class="fa fa-check text-success me-2"></i>{{ $b->benefit }}
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-auto text-center">
                                    @auth
                                        <a href="{{ route('user.membership.checkout', $tier->id) }}"
                                            class="btn btn-upgrade w-100">Become a Member</a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Login to Join</a>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>


    </section>
@endsection

@include('frontend.membership._card-styles')
