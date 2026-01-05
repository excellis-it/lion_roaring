@extends('user.layouts.master')
@section('title')
    Event Access - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .access-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .access-link {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px dashed #7851a9;
            margin: 20px 0;
        }

        .join-btn {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .join-btn:hover {
            background: #218838;
            color: white;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="access-card">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="mb-4">
                            <span class="badge bg-success">Registered</span>
                        </div>

                        <h2 class="mb-3">{{ $event->title }}</h2>

                        <div class="mb-4">
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
                        </div>

                        <div class="access-link">
                            <h5>Your Event Access Link</h5>
                            <p class="mb-2">
                                <strong>Link:</strong>
                                <a href="{{ $event->access_link }}" target="_blank" id="access-link">
                                    {{ $event->access_link }}
                                </a>
                            </p>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyAccessLink()">
                                <i class="ti ti-copy"></i> Copy Link
                            </button>
                        </div>

                        <div class="mb-4">
                            <h5>Event Details</h5>
                            <p><strong>RSVP Status:</strong> <span class="badge bg-success">Confirmed</span></p>
                            <p><strong>Registered On:</strong> {{ $rsvp->created_at->format('M d, Y h:i A') }}</p>
                            @if ($event->type === 'paid')
                                <p><strong>Payment:</strong> <span class="badge bg-success">Completed</span></p>
                            @endif
                        </div>

                        <div class="text-center">
                            <a href="{{ $event->access_link }}" class="join-btn" target="_blank">
                                Join Event
                            </a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <button class="btn btn-outline-danger" onclick="cancelRsvp()">
                                Cancel Registration
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyAccessLink() {
            const link = document.getElementById('access-link').href;
            navigator.clipboard.writeText(link).then(() => {
                alert('Link copied to clipboard!');
            });
        }

        function cancelRsvp() {
            if (confirm('Are you sure you want to cancel your registration?')) {
                $.ajax({
                    url: '{{ route('event.rsvp.cancel', $event->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        window.location.href = '{{ route('events.index') }}';
                    },
                    error: function(xhr) {
                        alert('An error occurred');
                    }
                });
            }
        }
    </script>
@endpush
