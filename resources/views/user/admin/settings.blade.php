@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Settings
@endsection
@push('styles')
@endpush

@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
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

                    <div class="mb-3">
                        <label for="DONATE_TEXT" class="form-label">Donate Text</label>
                        <textarea class="form-control description" id="DONATE_TEXT" name="DONATE_TEXT">{{ old('DONATE_TEXT', $settings->DONATE_TEXT) }}</textarea>
                        @error('DONATE_TEXT')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="DONATE_BANK_TRANSFER_DETAILS" class="form-label">Donate Bank Transfer Details</label>
                        <textarea class="form-control description" id="DONATE_BANK_TRANSFER_DETAILS" name="DONATE_BANK_TRANSFER_DETAILS">{{ old('DONATE_BANK_TRANSFER_DETAILS', $settings->DONATE_BANK_TRANSFER_DETAILS) }}</textarea>
                        @error('DONATE_BANK_TRANSFER_DETAILS')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        // ClassicEditor.create(document.querySelector("#description"));
        $('.description').summernote({
            placeholder: 'Description*',
            tabsize: 2,
            height: 200
        });
    </script>
@endpush
