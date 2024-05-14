@extends('admin.layouts.master')
@section('title')
    All Admin Details - {{env('APP_NAME')}}
@endsection


@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="page-wrapper">

        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Admin Information</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_admin"><i
                                class="fa fa-plus"></i> Add Admin</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-0">Admin Details</h4>
                            </div>
                            
                        </div>
                    </div>

                    <hr />
                    <div class="table-responsive">
                        <table id="example" class="dd table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>

                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $admin)
                                
                                <tr>
                                    <td style="display:none;"><a href="{{Storage::url($admin->profile_picture)}}" target="_blank" >
                                        <img src="{{Storage::url($admin->profile_picture)}}" class="rounded-circle shadow" width="130px" height="130px" alt="" id="img-{{ $admin->id }}" data-url="{{ Storage::url($admin->profile_picture) }}"></a></td>
                                    <td>{{ $admin->name}}</td>
                                    <td>{{ $admin->email}}</td>
                                    <td>{{ date('d M Y',strtotime($admin->created_at)) }}</td>
                                    <td align="center">
                                        <a class="edit-admins" href="#" data-bs-toggle="modal"
                                                data-bs-target="#edit_admin" data-id="{{ $admin->id }}"
                                                data-route="{{ route('admin.edit', $admin->id) }}"><i
                                                    class="fas fa-edit"></i></a> &nbsp;&nbsp;
                                                    
                                        <a href="{{route('admin.delete', $admin->id)}}" onclick="return confirm('Are you sure to delete this admin?')"><i class="fas fa-trash"></i></a>
                                        
                                    </td>
                                </tr>
                               
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

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
                            <form action="{{ route('admin.store') }}" method="POST" id="createForm"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile-img-wrap edit-img">
                                            <img class="inline-block" alt="admin"
                                                src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}">
                                            <div class="fileupload btn">
                                                <span class="btn-text">upload</span>
                                                <input class="upload" type="file" name="profile_picture"
                                                    id="profile_picture">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name"
                                                        id="name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="email"
                                                        id="email">
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
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                id="confirm_password">
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
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.update') }}" method="POST" id="editForm"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile-img-wrap edit-img">
                                            <div class="show-image"></div>
                                            <img class="inline-block" alt="admin"
                                                src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}">
                                            <div class="fileupload btn">
                                                <span class="btn-text">upload</span>
                                                <input class="upload" type="file" name="profile_picture"
                                                    id="profile_picture">
                                            </div>
                                        </div>
                                        <input type="hidden" id="hidden_id" name="id" value="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="edit_name"
                                                        id="edit_name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="edit_email"
                                                        id="edit_email">
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

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            jQuery.validator.addMethod("emailExt", function(value, element, param) {
                return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
            }, 'Your E-mail is wrong');

            $("#createForm").validate({
                rules: {
                    name: "required",
                    email: {
                        required: true,
                        email: true,
                        emailExt: true,
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    confirm_password: {
                        required: true,
                        minlength: 8,
                        equalTo: "#password"
                    },

                },

            });

            $("#editForm").validate({
                rules: {
                    edit_name: "required",
                    edit_email: {
                        required: true,
                        email: true,
                        emailExt: true,
                    },
                },

            });

            
        });
    </script>
    <script>
        $(document).ready(function() {

            $('.edit-admins').on('click', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                var img_url = $('#img-'+id).data('url');
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
                            console.log(data);
                            await $('#hidden_id').val(data.admin.id);
                            await $('#edit_name').val(data.admin.name);
                            await $('#edit_email').val(data.admin.email);
                            await $('.show-image').html('<img src="'+img_url+'" class="inline-block" alt="admin">');
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


@endpush
