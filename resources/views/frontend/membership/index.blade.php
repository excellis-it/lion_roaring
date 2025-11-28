@extends('frontend.layouts.master')
@section('title', env('APP_NAME') . ' - Membership')
@section('content')
    <section class="py-5 ">
        <div class="container">
            <div class="row mt-5">

                <div class="mt-5">

                </div>

                @foreach ($tiers as $tier)
                    <div class="col-lg-4 col-md-4 mb-4">
                        <div class="card h-100 shadow-sm p-4">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title mb-0">{{ $tier->name }}</h4>
                                    <div class="text-primary fw-bold">{{ $tier->cost }} {{ $measurement->label ?? '' }}
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
                                        <a href="{{ route('user.membership.index') }}"
                                            class="red_btn w-100"><span>Become a Member</span></a>
                                    @else
                                        <a href="#" class="red_btn w-100"><span>Login to Join</span></a>
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
