@extends('user.layouts.master')

@section('title')
    Edit Warehouse Administrator
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-4">Edit Warehouse Administrator: {{ $warehouseAdmin->first_name }}
                        {{ $warehouseAdmin->last_name }}</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('warehouse-admins.update', $warehouseAdmin->id) }}" method="POST"
                        id="edit-admin-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" class="form-control"
                                        value="{{ old('first_name', $warehouseAdmin->first_name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" class="form-control"
                                        value="{{ old('last_name', $warehouseAdmin->last_name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="user_name">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="user_name" id="user_name" class="form-control"
                                        value="{{ old('user_name', $warehouseAdmin->user_name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email', $warehouseAdmin->email) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone', $warehouseAdmin->phone) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1"
                                            {{ old('status', $warehouseAdmin->status) == 1 ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="0"
                                            {{ old('status', $warehouseAdmin->status) == 0 ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="password">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                    <small class="text-muted">Leave blank to keep current password</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="">
                                    <label class="mb-3">Assign Warehouses <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            @foreach ($warehouses as $warehouse)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="warehouses[]"
                                                            value="{{ $warehouse->id }}"
                                                            id="warehouse{{ $warehouse->id }}"
                                                            {{ in_array($warehouse->id, old('warehouses', $assignedWarehouseIds)) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="warehouse{{ $warehouse->id }}">
                                                            {{ $warehouse->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-4">
                            <button type="submit" class="print_btn me-2">Update Admin</button>
                            <a href="{{ route('warehouse-admins.index') }}" class="print_btn print_btn_vv">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#edit-admin-form").on("submit", function(e) {
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
    <script>
        // bs 5 alert
        var alertList = document.querySelectorAll('.alert');
        alertList.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
        });
    </script>
@endpush
