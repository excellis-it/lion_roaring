@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Settings
@endsection
@push('styles')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Site Settings</h3>
                    <p class="text-muted small mb-0">Manage general site configuration</p>
                </div>
            </div>

            <div class="container mt-3">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label d-block">Update is in Progress</label>
                    <div class="form-check form-switch custom-switch">
                        <input class="form-check-input" type="checkbox" id="siteUpdateToggle"
                            {{ isset($settings->SITE_UPDATE) && $settings->SITE_UPDATE == 1 ? 'checked' : '' }}
                            style="cursor: pointer; width: 40px; height: 20px;">
                        <label class="form-check-label ms-2" for="siteUpdateToggle" id="toggleLabel">
                            {{ isset($settings->SITE_UPDATE) && $settings->SITE_UPDATE == 1 ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                    {{-- <small class="text-muted">Toggle the site status between Active (1) and Inactive (0).</small> --}}
                </div>

                <form action="{{ route('user.admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="SITE_NAME" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="SITE_NAME" name="SITE_NAME"
                                value="{{ old('SITE_NAME', $settings->SITE_NAME) }}" required>
                            @error('SITE_NAME')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 mt-2">
                            <label for="SITE_NAME" class="form-label"></label>
                            <input type="file" class="form-control" id="SITE_LOGO" name="SITE_LOGO">
                            <small class="form-text text-muted">Current Logo: {{ $settings->SITE_LOGO }}</small>
                            <label for="SITE_LOGO" class="form-label">Site Logo</label><br>
                            <img src="{{ asset($settings->SITE_LOGO) }}" class="mb-3" alt=""
                                style="height: 100px" />
                            @error('SITE_LOGO')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="PANEL_WATERMARK_LOGO" class="form-label">PMA Panel Watermark Logo</label><br>
                            @if ($settings->PANEL_WATERMARK_LOGO)
                                <img src="{{ asset($settings->PANEL_WATERMARK_LOGO) }}" class="mb-2" alt="Watermark Logo"
                                    style="height: 100px" />
                            @else
                                <p class="text-muted">No watermark logo uploaded</p>
                            @endif

                            <input type="file" class="form-control" id="PANEL_WATERMARK_LOGO"
                                name="PANEL_WATERMARK_LOGO">
                            <small class="form-text text-muted">Upload a watermark logo for the admin panel (Max 2MB,
                                formats:
                                jpeg, png, jpg, gif, svg)</small>
                            @if ($settings->PANEL_WATERMARK_LOGO)
                                <br><small class="form-text text-muted">Current:
                                    {{ $settings->PANEL_WATERMARK_LOGO }}</small>
                            @endif
                            @error('PANEL_WATERMARK_LOGO')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="PMA_PANEL_LOGO" class="form-label">PMA Panel Logo</label><br>
                            @if ($settings->PMA_PANEL_LOGO)
                                <img src="{{ asset($settings->PMA_PANEL_LOGO) }}" class="mb-2" alt="PMA Panel Logo"
                                    style="height: 100px" />
                            @else
                                <p class="text-muted">No PMA panel logo uploaded</p>
                            @endif

                            <input type="file" class="form-control" id="PMA_PANEL_LOGO" name="PMA_PANEL_LOGO">
                            <small class="form-text text-muted">Upload a logo for the PMA panel (Max 2MB, formats:
                                jpeg, png, jpg, gif, svg)</small>
                            @if ($settings->PMA_PANEL_LOGO)
                                <br><small class="form-text text-muted">Current: {{ $settings->PMA_PANEL_LOGO }}</small>
                            @endif
                            @error('PMA_PANEL_LOGO')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="SITE_CONTACT_EMAIL" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="SITE_CONTACT_EMAIL" name="SITE_CONTACT_EMAIL"
                                value="{{ old('SITE_CONTACT_EMAIL', $settings->SITE_CONTACT_EMAIL) }}" required>
                            @error('SITE_CONTACT_EMAIL')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="SITE_CONTACT_PHONE" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" id="SITE_CONTACT_PHONE" name="SITE_CONTACT_PHONE"
                                value="{{ old('SITE_CONTACT_PHONE', $settings->SITE_CONTACT_PHONE) }}" required>
                            @error('SITE_CONTACT_PHONE')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="DONATE_TEXT" class="form-label">Donate Text</label>
                            <textarea class="form-control description" id="DONATE_TEXT" name="DONATE_TEXT">{{ old('DONATE_TEXT', $settings->DONATE_TEXT) }}</textarea>
                            @error('DONATE_TEXT')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="DONATE_BANK_TRANSFER_DETAILS" class="form-label">Donate Bank Transfer
                                Details</label>
                            <textarea class="form-control description" id="DONATE_BANK_TRANSFER_DETAILS" name="DONATE_BANK_TRANSFER_DETAILS">{{ old('DONATE_BANK_TRANSFER_DETAILS', $settings->DONATE_BANK_TRANSFER_DETAILS) }}</textarea>
                            @error('DONATE_BANK_TRANSFER_DETAILS')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
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

        $('#siteUpdateToggle').on('change', function() {
            let isChecked = $(this).is(':checked');
            let status = isChecked ? 1 : 0;
            let label = $('#toggleLabel');

            $.ajax({
                url: "{{ route('user.admin.settings.toggle-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                beforeSend: function() {
                    $('#siteUpdateToggle').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status) {
                        label.text(status == 1 ? 'Active' : 'Inactive');

                        // Instant update for the header tag
                        if (status == 1) {
                            $('#siteUpdateBlink').show();
                        } else {
                            $('#siteUpdateBlink').hide();
                        }

                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        $('#siteUpdateToggle').prop('checked', !isChecked);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while updating site status.';
                    if (xhr.status === 403) {
                        errorMessage = 'You do not have permission to perform this action.';
                    }
                    toastr.error(errorMessage);
                    $('#siteUpdateToggle').prop('checked', !isChecked);
                },
                complete: function() {
                    $('#siteUpdateToggle').prop('disabled', false);
                }
            });
        });
    </script>
@endpush
