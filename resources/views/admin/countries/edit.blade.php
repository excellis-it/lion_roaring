@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit Country
@endsection
@push('styles')
@endpush
@section('head')
    Edit Country
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Country Details</h4>
                    </div>
                    <form action="{{ route('admin-countries.update', $country->id) }}" method="post"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Name*</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name', $country->name) }}" placeholder="Country Name" required>
                                        @error('name')
                                            <div class="error" style="color:red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>ISO Code</label>
                                        <input type="text" class="form-control" name="code"
                                            value="{{ old('code', $country->code) }}" placeholder="e.g., US, IN, GB">
                                        @error('code')
                                            <div class="error" style="color:red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Flag Image</label>
                                        <input type="file" class="form-control" name="flag_image" accept="image/*">
                                        @if ($country->flag_image)
                                            <small class="text-muted d-block mt-1">Current: <img
                                                    src="{{ asset('storage/' . $country->flag_image) }}" alt="flag"
                                                    width="24" height="16"
                                                    style="object-fit:cover;border:1px solid #eee;"></small>
                                        @endif
                                        @error('flag_image')
                                            <div class="error" style="color:red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="statusSwitchEdit" name="status" value="1"
                                                {{ old('status', $country->status) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusSwitchEdit">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Languages (Optional)</label>
                                        <select class="form-control select2" name="languages[]" multiple="multiple"
                                            id="languageSelect">
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->id }}"
                                                    {{ $country->languages->contains($language->id) ? 'selected' : '' }}>
                                                    {{ $language->name }} ({{ $language->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select one or more languages for this country
                                            (optional)</small>
                                        @error('languages')
                                            <div class="error" style="color:red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Update Country</button>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for language selection
            $('#languageSelect').select2({
                placeholder: "Select languages (optional)",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
