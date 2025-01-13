@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Settings
@endsection
@push('styles')
@endpush
@section('head')
    Site Settings
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="container mt-3">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="SITE_NAME" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="SITE_NAME" name="SITE_NAME"
                            value="{{ old('SITE_NAME', $settings->SITE_NAME) }}" required>
                        @error('SITE_NAME')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="SITE_LOGO" class="form-label">Site Logo</label><br>
                        <img src="{{ asset($settings->SITE_LOGO) }}" class="" alt="" style="height: 100px" />

                        <input type="file" class="form-control" id="SITE_LOGO" name="SITE_LOGO">
                        <small class="form-text text-muted">Current Logo: {{ $settings->SITE_LOGO }}</small>
                        @error('SITE_LOGO')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="SITE_CONTACT_EMAIL" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="SITE_CONTACT_EMAIL" name="SITE_CONTACT_EMAIL"
                            value="{{ old('SITE_CONTACT_EMAIL', $settings->SITE_CONTACT_EMAIL) }}" required>
                        @error('SITE_CONTACT_EMAIL')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="SITE_CONTACT_PHONE" class="form-label">Contact Phone</label>
                        <input type="text" class="form-control" id="SITE_CONTACT_PHONE" name="SITE_CONTACT_PHONE"
                            value="{{ old('SITE_CONTACT_PHONE', $settings->SITE_CONTACT_PHONE) }}" required>
                        @error('SITE_CONTACT_PHONE')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </form>

            </div>
        </div>
    </div>
@endsection
