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
                        <a href="{{ route('events.index') }}" class="btn btn-secondary">Back to Events</a>
                    </div>

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
@endsection
