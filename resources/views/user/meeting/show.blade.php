@extends('user.layouts.master')
@section('title')
    Meeting - {{ env('APP_NAME') }}
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
                            <h3 class="mb-3 float-left">Meeting Details</h3>
                        </div>
                        <div class="col-lg-2">
                            <a href="{{ route('meetings.index') }}" class="btn btn-primary w-100">
                                < Back</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-md-6">
                                            <h6 class="card-title">{{ $meeting->title }}</h6>
                                            <p class="card-text"><strong>Meeting Start Time:</strong>
                                                {{ $meeting->start_time ? date('d M, Y', strtotime($meeting->start_time)) : 'N/A' }} </p>
                                            <p class="card-text"><strong>Meeting End Time:</strong>
                                                {{ $meeting->end_time ? date('d M, Y', strtotime($meeting->end_time)) : 'N/A' }} </p>
                                            <p class="card-text"><strong>Meeting Link:</strong>
                                                {{ $meeting->meeting_link ? $meeting->meeting_link : 'N/A' }}</p>
                                            <hr>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <p class="card-text"><strong>Meeting posted By:</strong>
                                                {{ $meeting->user ? $meeting->user->full_name : 'N/A' }}</p>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Meeting Description:</h5>
                                    <p>{!! $meeting->description !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
