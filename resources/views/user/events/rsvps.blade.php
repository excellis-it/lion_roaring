@extends('user.layouts.master')
@section('title')
    Event RSVPs - {{ env('APP_NAME') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>RSVPs for: {{ $event->title }}</h3>
                        <div class="d-flex align-items-center gap-2">

                            <a href="{{ route('events.index') }}" class="btn btn-secondary"><span
                                    class="fa fa-arrow-left"></span> Back to Events</a>

                            @if ((int) auth()->id() === (int) $event->user_id)
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#notifyRsvpModal">
                                    Send Notifications to All RSVPs
                                </button>
                            @endif
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Total RSVPs</h5>
                                    <h3>{{ $event->rsvps->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Confirmed</h5>
                                    <h3 class="text-success">{{ $event->confirmedRsvps->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Pending</h5>
                                    <h3 class="text-warning">{{ $event->rsvps()->where('status', 'pending')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Cancelled</h5>
                                    <h3 class="text-danger">{{ $event->rsvps()->where('status', 'cancelled')->count() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>RSVP Date</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($event->rsvps as $rsvp)
                                    <tr>
                                        <td>{{ $rsvp->user->getFullNameAttribute() }}</td>
                                        <td>{{ $rsvp->user->email }}</td>
                                        <td>{{ $rsvp->rsvp_date->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @if ($rsvp->status === 'confirmed')
                                                <span class="badge bg-success">Confirmed</span>
                                            @elseif($rsvp->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($rsvp->payment)
                                                @if ($rsvp->payment->status === 'completed')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($rsvp->payment->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger">{{ ucfirst($rsvp->payment->status) }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Free Event</span>
                                            @endif
                                        </td>
                                        <td>{{ $rsvp->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No RSVPs yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ((int) auth()->id() === (int) $event->user_id)
        <div class="modal fade" id="notifyRsvpModal" tabindex="-1" aria-labelledby="notifyRsvpModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notifyRsvpModalLabel">Update Event & Notify RSVPs</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('events.notify-rsvps', $event->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="notifyStart" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="notifyStart" name="start"
                                    value="{{ old('start', optional($event->start)->format('Y-m-d\\TH:i')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="notifyEnd" class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="notifyEnd" name="end"
                                    value="{{ old('end', optional($event->end)->format('Y-m-d\\TH:i')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="notifyEventLink" class="form-label">Event Link</label>
                                <input type="url" class="form-control" id="notifyEventLink" name="event_link"
                                    placeholder="https://example.com/meeting-link"
                                    value="{{ old('event_link', $event->getDecryptedLink()) }}">
                                <small class="text-muted">Can be changed at any time.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send Notification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@if ((int) auth()->id() === (int) $event->user_id && $errors->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('notifyRsvpModal');
                if (modal) {
                    new bootstrap.Modal(modal).show();
                }
            });
        </script>
    @endpush
@endif
