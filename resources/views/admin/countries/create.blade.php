@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Country
@endsection
@push('styles')
@endpush
@section('head')
    Create Country
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('admin-countries.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>Country Details</h4>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Name*</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" placeholder="Country Name" required>
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
                                            value="{{ old('code') }}" placeholder="e.g., US, IN, GB">
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
                                                id="statusSwitchNew" name="status" value="1" checked>
                                            <label class="form-check-label" for="statusSwitchNew">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Create Country</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
