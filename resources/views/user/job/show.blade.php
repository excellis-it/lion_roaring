@extends('user.layouts.master')
@section('title')
    Job - {{ env('APP_NAME') }}
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
                            <h3 class="mb-3 float-left">Job Details</h3>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('jobs.index') }}" class="btn btn-primary w-100">
                                < Back</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-md-6">
                                            <h4 class="card-title"><b>{{ $job->job_title }}</b></h4>
                                            <p class="card-subtitle mb-2 text-muted">
                                                {{ $job->job_type ? $job->job_type : 'N/A' }}</p>
                                            <p class="card-text"><strong>Location:</strong>
                                                {{ $job->job_location ? $job->job_location : 'N/A' }}</p>
                                            <p class="card-text"><strong>Salary:</strong>
                                                {{ $job->job_salary ? $job->currency . ' ' . $job->job_salary .'/' : '0'.'/' }} {{ $job->list_of_values ? $job->list_of_values : 'N/A' }}</p>
                                            <p class="card-text"><strong>Experience:</strong>
                                                {{ $job->job_experience ? ($job->job_experience > 1 ? $job->job_experience . ' years' : $job->job_experience . ' year') : 'N/A' }}
                                            </p>
                                            {{-- contact_person --}}
                                            <p class="card-text"><strong>Contact Person:</strong>
                                                {{ $job->contact_person ? $job->contact_person : 'N/A' }}</p>
                                            {{-- contact_email --}}
                                            <p class="card-text"><strong>Contact Email:</strong>
                                                {{ $job->contact_email ? $job->contact_email : 'N/A' }}</p>
                                            {{-- contact_phone --}}
                                            <hr>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <p class="card-text"><strong>Job posted By:</strong>
                                                {{ $job->user ? $job->user->full_name : 'N/A' }}</p>
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <p class="card-text"><strong>Job Post Date:</strong>
                                                {{ date('d M, Y', strtotime($job->created_at)) }} 
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Job Description:</h5>
                                    <p>{!! $job->job_description !!}</p>
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
