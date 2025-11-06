@extends('user.layouts.master')
@section('title')
    Private Collaboration Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border p-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <h3 class="mb-3">Private Collaboration Details</h3>
                        </div>
                        <div class="col-lg-2">
                            <a href="{{ route('private-collaborations.index') }}" class="btn btn-primary w-100">
                                < Back</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="card-title">{{ $collaboration->title }}</h6>
                                                <p class="card-text"><strong>Start Time:</strong>
                                                    {{ $collaboration->start_time ? date('d M, Y h:i A', strtotime($collaboration->start_time)) : 'N/A' }}
                                                </p>
                                                <p class="card-text"><strong>End Time:</strong>
                                                    {{ $collaboration->end_time ? date('d M, Y h:i A', strtotime($collaboration->end_time)) : 'N/A' }}
                                                </p>


                                                @if ($isCreator || $hasAccepted)
                                                    <p class="card-text"><strong>Meeting Link:</strong>
                                                        @if ($collaboration->meeting_link)
                                                            <a href="{{ $collaboration->meeting_link }}" target="_blank">
                                                                {{ $collaboration->meeting_link }}
                                                            </a>
                                                            <br>
                                                            <br>
                                                            @if ($collaboration->is_zoom && $isCreator)
                                                                <span class="">Zoom - You are Host</span>
                                                            @elseif($collaboration->is_zoom)
                                                                <span class="">Zoom Meeting</span>
                                                            @else
                                                                <span class="">External Link</span>
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="card-text">
                                                        <strong>Meeting Link:</strong>
                                                        <span class="text-muted">Accept invitation to view link</span>
                                                    </p>
                                                @endif
                                                <hr>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <p class="card-text"><strong>Created By:</strong>
                                                    {{ $collaboration->user ? $collaboration->user->full_name : 'N/A' }}
                                                </p>
                                                <p class="card-text"><strong>Your Status:</strong>
                                                    @if ($isCreator)
                                                        <span class=" bg-success">Creator</span>
                                                    @elseif($hasAccepted)
                                                        <span class=" bg-primary">Accepted</span>
                                                    @else
                                                        <span class=" bg-warning">Pending Invitation</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Description:</h5>
                                    <p>{!! $collaboration->description !!}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Show invited users list if creator --}}
                        @if ($isCreator)
                            <div class="col-md-12 mt-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Invited Users:</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Accepted At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($collaboration->invitations as $invitation)
                                                        <tr>
                                                            <td>{{ $invitation->user->full_name }}</td>
                                                            <td>{{ $invitation->user->email }}</td>
                                                            <td>
                                                                @if ($invitation->status == 'accepted')
                                                                    <span class=" bg-success">Accepted</span>
                                                                @elseif($invitation->status == 'declined')
                                                                    <span class=" bg-danger">Declined</span>
                                                                @else
                                                                    <span class=" bg-warning">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $invitation->accepted_at ? date('d M, Y h:i A', strtotime($invitation->accepted_at)) : '-' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">No invitations sent</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
