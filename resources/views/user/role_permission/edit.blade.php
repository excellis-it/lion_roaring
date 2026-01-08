@extends('user.layouts.master')
@section('title')
    Update Role - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('roles.update', Crypt::encrypt($role->id)) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Update Role </h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>Role Name</label>
                                    <input type="text" class="form-control" value="{{ $role->name }}" name="role_name"
                                        placeholder=""
                                        {{ $role->name == 'MEMBER_NON_SOVEREIGN' || $role->name == 'WAREHOUSE_ADMIN' || $role->name == 'ESTORE_USER' || $role->name == 'ECCLESIA' ? 'readonly' : '' }}>
                                    @if ($errors->has('role_name'))
                                        @error('role_name')
                                            <span class="text-danger" style="color: red !important"> {{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 mt-1">
                                <div class="box_label">
                                    <label>Is ECCLESIA?</label>
                                    <select name="is_ecclesia" id="" class="form-control" required>
                                        <option value="" disabled>
                                            Select
                                        </option>
                                        <option value="1" {{ $role->is_ecclesia == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $role->is_ecclesia == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                    @if ($errors->has('is_ecclesia'))
                                        <span class="text-danger"
                                            style="color: red !important">{{ $errors->first('is_ecclesia') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    
                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="print_btn me-2">Update</button>
                            <a href="{{ route('roles.index') }}" class="print_btn print_btn_vv">Cancel</a>
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
            $("#checkAllEdit").click(function() {
                $('input:checkbox').prop('checked', this.checked);
            });

            // Handle individual checkboxes
            $('input:checkbox').not("#checkAllEdit").click(function() {
                if (!this.checked) {
                    $("#checkAllEdit").prop('checked', false);
                }
            });

            $('.manage-cl').click(function() {
                var id = $(this).data('id');
                if ($(this).is(':checked')) {
                    $('input[data-id="' + id + '"]').prop('checked', true);
                } else {
                    $('input[data-id="' + id + '"]').prop('checked', false);
                }
            });
        });
    </script>
@endpush
