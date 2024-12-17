@extends('admin.layouts.master')
@section('title')
    Admin - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }

        .eye-btn-1 {
            top: 39px;
            right: 24px;
        }
    </style>
@endpush
@section('head')
    Admin
@endsection
@section('create_button')
    @if (auth()->user()->can('Create Admin List'))
        <a href="javascript:void(0)" id="create-admin" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#add_admin">Add
            Admin</a>
    @endif
@endsection

@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div id="add_admin" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin Information</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.store') }}" method="POST" id="createForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <div class="profile-img-wrap edit-img">
                                    <img class="inline-block" alt="admin"
                                        src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}">
                                    <div class="fileupload btn">
                                        <span class="btn-text">upload</span>
                                        <input class="upload" type="file" name="profile_picture"
                                            id="profile_picture">
                                    </div>
                                </div> --}}
                                <div class="row">
                                    {{-- user_name --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>User Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="user_name" id="user_name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="first_name" id="first_name">
                                        </div>
                                    </div>
                                    {{-- middle_name --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" name="middle_name" id="middle_name">
                                        </div>
                                    </div>
                                    {{-- last_name --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="last_name" id="last_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="email" id="email">
                                        </div>
                                    </div>
                                    {{-- phone --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="phone" id="phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password<span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password">
                                    <span class="eye-btn-1" id="eye-button-1">
                                        <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password<span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password"
                                        id="confirm_password">
                                    <span class="eye-btn-1" id="eye-button-2">
                                        <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="edit_admin" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin Information</h5>
                    <button type="button" class="edit_close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.update') }}" method="POST" id="editForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="hidden_id" name="id" value="">
                                <div class="row">
                                    {{-- user_name --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>User Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="edit_user_name"
                                                id="edit_user_name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="edit_first_name"
                                                id="edit_first_name">
                                        </div>
                                    </div>
                                    {{-- middle_name --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" name="edit_middle_name"
                                                id="edit_middle_name">
                                        </div>
                                    </div>
                                    {{-- last name --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="edit_last_name"
                                                id="edit_last_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="edit_email"
                                                id="edit_email">
                                        </div>
                                    </div>
                                    {{-- phone --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="edit_phone"
                                                id="edit_phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="inner_page">

            <div class="card table_sec stuff-list-table">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="row g-1 justify-content-end">

                            {{-- <div class="col-md-3 pl-0 ml-2">
                                <button class="btn btn-primary button-search" id="search-button"> <span class=""><i
                                            class="ph ph-magnifying-glass"></i></span> Search</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="contacts-data">
                    <table id="example" class="dd table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    User Name
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>
                                    Phone
                                </th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td>{{ $admin->user_name }}</td>
                                    <td>{{ $admin->full_name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>{{ $admin->phone }}</td>
                                    <td>{{ date('d M Y', strtotime($admin->created_at)) }}</td>
                                    <td align="center">
                                        <div class="edit-1 d-flex align-items-center justify-content-center">
                                            @if (auth()->user()->can('Edit Admin List'))
                                                <a class="edit-admins edit-icon" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edit_admin" data-id="{{ $admin->id }}"
                                                    data-route="{{ route('admin.edit', $admin->id) }}"> <span
                                                        class="edit-icon"><i class="ph ph-pencil-simple"></i></span></a>
                                            @endif
                                            @if (auth()->user()->can('Delete Admin List'))
                                                <a href="{{ route('admin.delete', $admin->id) }}"
                                                    onclick="return confirm('Are you sure to delete this admin?')"> <span
                                                        class="trash-icon"><i class="ph ph-trash"></i></span></a>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- create-admin --}}
    <script>
        $(document).ready(function() {
            $('#create-admin').on('click', function() {
                $('#add_admin').modal('show');
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            $('.edit-admins').on('click', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                var img_url = $('#img-' + id).data('url');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    dataType: 'JSON',
                    success: async function(data) {
                        try {
                            //    open modal
                            await $('#edit_admin').modal('show');
                            await $('#hidden_id').val(data.admin.id);
                            await $('#edit_user_name').val(data.admin.user_name);
                            await $('#edit_first_name').val(data.admin.first_name);
                            await $('#edit_middle_name').val(data.admin.middle_name);
                            await $('#edit_last_name').val(data.admin.last_name);
                            await $('#edit_email').val(data.admin.email);
                            await $('#edit_phone').val(data.admin.phone);
                            await $('#loading').removeClass('loading');
                            await $('#loading-content').removeClass('loading-content');
                        } catch (error) {
                            console.log(error);
                        }
                    }
                });
            });
        });
    </script>

    {{-- createForm  submit --}}
    <script>
        $(document).ready(function() {
            $('#createForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var type = form.attr('method');
                var data = new FormData(form[0]);
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    url: url,
                    type: type,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            $('#add_admin').modal('hide');
                            location.reload();

                        } else {
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            toastr.error(data.message);
                        }
                    },
                    error: function(data) {
                        // validation error
                        if (data.status == 422) {
                            var errors = data.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                        }
                    }
                });
            });
        });
    </script>

    {{-- editForm  submit --}}
    <script>
        $(document).ready(function() {
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var type = form.attr('method');
                var data = new FormData(form[0]);
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    url: url,
                    type: type,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            $('#edit_admin').modal('hide');
                            location.reload();
                        } else {
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            toastr.error(data.message);
                        }
                    },
                    error: function(data) {
                        // validation error
                        if (data.status == 422) {
                            var errors = data.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                        }
                    }
                });
            });
        });
    </script>
    {{-- close --}}
    <script>
        $(document).ready(function() {
            $('.edit_close').on('click', function() {
                $('#edit_admin').modal('hide');
            });

            $('.close').on('click', function() {
                $('#add_admin').modal('hide');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#eye-button-1').click(function() {
                $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-2').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
        });
    </script>
@endpush
