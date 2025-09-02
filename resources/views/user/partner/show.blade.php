@extends('user.layouts.master')
@section('title')
    Member Details View - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border p-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">View Details</h3>
                        <a href="{{ route('partners.index') }}" class="btn btn-primary">
                            &lt; Back
                        </a>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Member Name: {{ $partner->full_name }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="{{ $partner->profile_picture ? Storage::url($partner->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                        class="img-fluid rounded mb-3" alt="Partner Logo">
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-4">
                                        <h5>Contact Information</h5>
                                        <p><strong>Email:</strong> {{ $partner->email }}</p>
                                        <p><strong>Phone:</strong> {{ $partner->phone }}</p>
                                        <p><strong>Country:</strong>
                                            {{ $partner->countries ? $partner->countries->name : 'N/A' }}</p>
                                        <p><strong>State:</strong> {{ $partner->states ? $partner->states->name : 'N/A' }}
                                        </p>
                                        <p><strong>City:</strong> {{ $partner->city ? $partner->city : 'N/A' }}</p>
                                        <p><strong>Address:</strong> {{ $partner->address ? $partner->address : 'N/A' }}</p>
                                        <p><strong>Address 2:</strong>
                                            {{ $partner->address2 ? $partner->address2 : 'N/A' }}</p>
                                        <p><strong>Zip Code:</strong> {{ $partner->zip ? $partner->zip : 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <h5>Additional Information</h5>
                                        <p><strong>Joined On:</strong> {{ $partner->created_at->format('d M Y') }}</p>
                                        <p><strong>Status:</strong> {{ $partner->status == 1 ? 'Active' : 'Inactive' }}</p>
                                    </div>
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
