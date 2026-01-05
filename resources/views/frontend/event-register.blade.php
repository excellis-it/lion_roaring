@extends('frontend.layouts.master')
@section('title')
    Event Registration - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .event-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .event-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .badge-paid {
            background: #28a745;
            color: white;
        }

        .badge-free {
            background: #17a2b8;
            color: white;
        }

        .register-btn {
            background: #7851a9;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .register-btn:hover {
            background: #5f3d8a;
        }
    </style>
@endpush
@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('frontend_assets/uploads/2023/07/inner_banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>Event Registration</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="login-sec">
        <div class="container">
            <div class="container-fluid">
                <div class="bg_white_border">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="event-card">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <div class="mb-4">
                                    <span class="event-badge {{ $event->type === 'paid' ? 'badge-paid' : 'badge-free' }}">
                                        {{ $event->type === 'paid' ? 'Paid Event' : 'Free Event' }}
                                    </span>
                                </div>

                                <h2 class="mb-3">{{ $event->title }}</h2>

                                <div class="mb-4">
                                    <p><strong>Description:</strong></p>
                                    <p>{{ $event->description }}</p>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <p><strong>Start:</strong> {{ $event->start->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>End:</strong> {{ $event->end->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p><strong>Host:</strong> {{ $event->user->getFullNameAttribute() }}</p>
                                    <p><strong>Country:</strong> {{ $event->country->name ?? 'N/A' }}</p>
                                </div>

                                @if ($event->type === 'paid')
                                    <div class="mb-4">
                                        <h4>Price: ${{ number_format($event->price, 2) }} USD</h4>
                                    </div>
                                @endif

                                @if ($event->capacity)
                                    <div class="mb-4">
                                        <p><strong>Available Spots:</strong>
                                            {{ $event->availableSpots() }} / {{ $event->capacity }}
                                        </p>
                                    </div>
                                @endif

                                @guest
                                    <div class="alert alert-info text-center">
                                        <p class="mb-3">You need to be logged in to register for this event.</p>
                                        <button type="button" class="register-btn" data-bs-toggle="modal"
                                            data-bs-target="#loginModal" onclick="setEventRedirect()">
                                            Login to Register Event
                                        </button>
                                        <p class="mt-3 mb-0">Don't have an account?
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#registerModalFirst" onclick="setEventRedirect()">Create one
                                                here</a>
                                        </p>
                                    </div>
                                @else
                                    @if ($userRsvp)
                                        <div class="alert alert-info">
                                            <strong>Status:</strong> You have already registered for this event.
                                            <br>
                                            <strong>RSVP Status:</strong> {{ ucfirst($userRsvp->status) }}
                                            @if ($userRsvp->status === 'confirmed')
                                                <br>
                                                <a href="{{ route('event.access', $event->id) }}" class="btn btn-primary mt-2">
                                                    Access Event
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        @if ($event->hasCapacity())
                                            <form id="register-form" method="POST"
                                                action="{{ route('event.register.submit', $event->id) }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                                </div>

                                                <button type="submit" class="register-btn">
                                                    {{ $event->type === 'paid' ? 'Proceed to Payment' : 'Register for Free' }}
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-warning">
                                                This event is currently full.
                                            </div>
                                        @endif
                                    @endif
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Set redirect URL in session storage for post-login redirect
        function setEventRedirect() {
            sessionStorage.setItem('post_login_redirect', '{{ url()->current() }}');
        }

        // Check if there's a redirect URL after login and redirect
        $(document).ready(function() {
            @auth
            // Clear redirect if user is already logged in
            sessionStorage.removeItem('post_login_redirect');
        @endauth

        @if ($event->type === 'paid')
            const stripe = Stripe('{{ config('services.stripe.key') }}');
        @endif

        $('#register-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.redirect_to_stripe && response.session_id) {
                        // Redirect to Stripe Checkout
                        stripe.redirectToCheckout({
                            sessionId: response.session_id
                        }).then(function(result) {
                            if (result.error) {
                                alert(result.error.message);
                            }
                        });
                    } else {
                        // Free event - show success and redirect
                        alert(response.message);
                        window.location.href = '{{ route('event.access', $event->id) }}';
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'An error occurred');
                }
            });
        });
        });
    </script>
@endpush
