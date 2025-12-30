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
            <div class="row mt-4">
                @php $maxCost = $tiers->max('cost'); @endphp
                @foreach ($tiers as $tier)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm tier-card position-relative">
                            @if ($tier->cost == $maxCost)
                                <div class="ribbon">Most Popular</div>
                            @endif
                            <div class="card-body card-content-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title mb-0">{{ $tier->name }}</h4>
                                    <div class="text-primary fw-bold">
                                        @if (($tier->pricing_type ?? 'amount') === 'token')
                                            <span
                                                class="badge badge-price bg-light text-dark">{{ $tier->life_force_energy_tokens }}
                                                {{ $measurement->label ?? 'Life Force Energy' }}</span>
                                        @else
                                            <span
                                                class="badge badge-price bg-light text-dark">${{ number_format((float) $tier->cost, 2) }}</span>
                                        @endif
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
                                        @if (($tier->pricing_type ?? 'amount') === 'token')
                                            <button type="button" class="btn red_btn btn-upgrade w-100 js-token-subscribe"
                                                data-tier-id="{{ $tier->id }}" data-tier-name="{{ $tier->name }}"
                                                data-agree-description="{{ e($tier->agree_description) }}">
                                                <span>Become a Member</span>
                                            </button>
                                        @else
                                            <a href="{{ route('user.membership.checkout', $tier->id) }}"
                                                class="btn red_btn btn-upgrade w-100"><span>Become a Member</span></a>
                                        @endif
                                    @else
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#loginModal"
                                            class="btn red_btn btn-upgrade w-100"><span>Login to Join</span></a>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>


    </section>

    <!-- Token agree description modal -->
    <div class="modal fade" id="tokenAgreeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
                    <button type="button" class="btn btn-sm red_btn" data-bs-dismiss="modal"><span>Reject</span></button>
                    <form method="POST" id="tokenAgreeForm" action="#">
                        @csrf
                        <button type="submit" class="btn btn-sm red_btn"><span>Accept</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('frontend.membership._card-styles')

@push('scripts')
    <script>
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
    </script>
@endpush
