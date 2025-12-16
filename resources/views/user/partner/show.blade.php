@extends('user.layouts.master')
@section('title')
    Member Details View - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .info-item {
            transition: all 0.3s ease;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #643271;
        }

        .info-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(100, 50, 113, 0.15);
        }

        .page-header {
            border-bottom: 2px solid #f0f0f0;
        }

        .page-title {
            color: #643271;
            font-weight: 600;
        }

        .back-btn {
            background: linear-gradient(135deg, #643271 0%, #8b4c9e 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(100, 50, 113, 0.3);
        }

        .profile-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #643271 0%, #8b4c9e 100%);
            padding: 30px 20px 80px;
            text-align: center;
        }

        .profile-image-wrapper {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            background: white;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-body {
            margin-top: -90px;
        }

        .profile-name {
            color: #fff;
            font-weight: 600;
        }

        .profile-email {
            color: #fff !important;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .status-inactive {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .info-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #643271 0%, #8b4c9e 100%);
            color: white;
            border: none;
            border-radius: 16px 16px 0 0;
            padding: 20px;
        }

        .card-header-title {
            font-weight: 600;
        }

        .card-body-padding {
            padding: 25px;
        }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #2c3e50;
        }

        .signature-container {
            border: 2px dashed #643271;
            border-radius: 12px;
            background: linear-gradient(to bottom, #ffffff 0%, #f9f9f9 100%);
            padding: 25px;
            box-shadow: inset 0 2px 8px rgba(100, 50, 113, 0.05);
            text-align: center;
        }

        .signature-image {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .signature-info {
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .signature-info i {
            color: #643271;
        }

        .no-signature {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            background: #f8f9fa;
            padding: 50px;
            text-align: center;
        }

        .no-signature-icon {
            font-size: 50px;
            color: #dee2e6;
            margin-bottom: 15px;
        }

        .no-signature-text {
            font-size: 16px;
            font-weight: 500;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            {{-- Header Section --}}
            <div class="member-details-header d-flex justify-content-between align-items-center mb-4 pb-3 page-header">
                <div>
                    <h3 class="mb-1 page-title">
                        <i class="fa fa-user-circle"></i> Member Details
                    </h3>
                    <p class="text-muted small mb-0">Complete profile information</p>
                </div>
                <a href="{{ route('partners.index') }}" class="btn btn-sm back-btn">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row">
                {{-- Left Column - Profile Picture --}}
                <div class="col-lg-4 mb-4">
                    <div class="card profile-card">
                        <div class="profile-header">
                            <div class="profile-image-wrapper">
                                <img src="{{ $partner->profile_picture ? Storage::url($partner->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                    alt="Profile Picture" class="profile-image">
                            </div>
                        </div>
                        <div class="card-body text-center profile-body">
                            <h4 class="mb-1 profile-name">{{ $partner->full_name }}</h4>
                            <p class="mb-3 profile-email">
                                <i class="fa fa-envelope"></i> {{ $partner->email }}
                            </p>
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                @if ($partner->status == 1)
                                    <span class="badge status-badge status-active">
                                        <i class="fa fa-check-circle"></i> Active
                                    </span>
                                @else
                                    <span class="badge status-badge status-inactive">
                                        <i class="fa fa-times-circle"></i> Inactive
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fa fa-calendar-alt"></i> Joined on {{ $partner->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Details --}}
                <div class="col-lg-8">
                    {{-- Contact Information Card --}}
                    <div class="card mb-4 info-card">
                        <div class="card-header card-header-gradient">
                            <h5 class="mb-0 card-header-title">
                                <i class="fa fa-address-card"></i> Contact Information
                            </h5>
                        </div>
                        <div class="card-body card-body-padding">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1 info-label">
                                            <i class="fa fa-phone"></i> Phone Number
                                        </small>
                                        <strong class="info-value">{{ $partner->phone ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1 info-label">
                                            <i class="fa fa-globe"></i> Country
                                        </small>
                                        <strong class="info-value">{{ $partner->countries->name ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1 info-label">
                                            <i class="fa fa-map-marker-alt"></i> State
                                        </small>
                                        <strong class="info-value">{{ $partner->states->name ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1 info-label">
                                            <i class="fa fa-city"></i> City
                                        </small>
                                        <strong class="info-value">{{ $partner->city ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1 info-label">
                                            <i class="fa fa-mail-bulk"></i> Zip Code
                                        </small>
                                        <strong class="info-value">{{ $partner->zip ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="info-item">
                                        <small class="text-muted d-block mb-1 info-label">
                                            <i class="fa fa-home"></i> Address Line 1
                                        </small>
                                        <strong class="info-value">{{ $partner->address ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                @if ($partner->address2)
                                    <div class="col-md-12 mb-3">
                                        <div class="info-item">
                                            <small class="text-muted d-block mb-1 info-label">
                                                <i class="fa fa-home"></i> Address Line 2
                                            </small>
                                            <strong class="info-value">{{ $partner->address2 }}</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- E-Signature Card --}}
                    <div class="card info-card">
                        <div class="card-header card-header-gradient">
                            <h5 class="mb-0 card-header-title">
                                <i class="fa fa-signature"></i> E-Signature
                            </h5>
                        </div>
                        <div class="card-body card-body-padding">
                            @if ($partner->signature)
                                <div class="signature-container">
                                    <img src="{{ $partner->signature }}" alt="Member Signature" class="signature-image">
                                    <div class="signature-info">
                                        <i class="fa fa-info-circle"></i>
                                        <small class="text-muted">Signed on
                                            {{ $partner->created_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="no-signature">
                                    <i class="fa fa-pen-fancy no-signature-icon"></i>
                                    <p class="text-muted mb-0 no-signature-text">No signature on file</p>
                                    <small class="text-muted">Signature was not provided during registration</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
