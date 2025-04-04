@extends('admin.layouts.master')
@section('title')
    Role Permission - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }

        .round-btn {
            background-color: #da6f32;
            color: #ffffff;
            border-radius: 10px;
            padding: inherit;
            display: inline-block;
            margin-bottom: 6px;
        }
    </style>
@endpush
@section('head')
    {{ Auth::user()->getFirstRoleType() == 1 ? 'Admin' : '' }} Role Permission
@endsection
@section('create_button')
    <a href="{{ route('admin.roles.create') }}" id="create-admin" class="btn btn-primary" data-bs-toggle="modal"
        data-bs-target="#add_admin"> <i class="ph ph-plus"></i>Add {{ Auth::user()->getFirstRoleType() == 1 ? 'Admin' : '' }}
        Role Permission</a>
@endsection
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="main-content">
        <div class="inner_page">

            <div class="card table_sec stuff-list-table table-center-align">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="row g-1 justify-content-end">
                            {{-- <div class="col-md-8 pr-0">
                                <div class="search-field prod-search">
                                    <input type="text" name="search" id="search" placeholder="search..." required
                                        class="form-control">
                                    <a href="javascript:void(0)" class="prod-search-icon"><i
                                            class="ph ph-magnifying-glass"></i></a>
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-3 pl-0 ml-2">
                                <button class="btn btn-primary button-search" id="search-button"> <span class=""><i
                                            class="ph ph-magnifying-glass"></i></span> Search</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID (#)</th>
                                <th>{{ Auth::user()->getFirstRoleType() == 1 ? 'Admin' : '' }} Role</th>
                                <th>Permissions</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($roles) > 0)
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td> {{ $role->name }}</td>
                                        {{-- <td>
                                            @foreach ($role->permissions()->where('type', 2)->get() as $permission)
                                                <span class="round-btn">{{ $permission->name }}</span>
                                            @endforeach
                                        </td> --}}
                                        <td>
                                            @if (Auth::user()->getFirstRoleType() == 1)
                                                <button type="button" class="btn text-blue btn-view-permission"
                                                    data-permissions="{{ $role->permissions()->where('type', 2)->get() }}"
                                                    data-role-name="{{ $role->name }}">
                                                    View Permission
                                                </button>
                                            @else
                                                <button type="button" class="btn text-blue btn-view-permission"
                                                    data-permissions="{{ $role->permissions()->where('type', 1)->get() }}"
                                                    data-role-name="{{ $role->name }}">
                                                    View Permission
                                                </button>
                                            @endif

                                        </td>
                                        <td>
                                            <div class="edit-1 d-flex align-items-center justify-content-center">
                                                <a title="Edit "
                                                    href="{{ route('admin.roles.edit', Crypt::encrypt($role->id)) }}">
                                                    <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span></a>
                                                @if ($role->name == 'MEMBER' || $role->name == 'LEADER' || $role->name == 'ECCLESIA')
                                                @else
                                                    <a title="Delete "
                                                        data-route="{{ route('admin.roles.delete', Crypt::encrypt($role->id)) }}"
                                                        href="javascipt:void(0);" id="delete"> <span
                                                            class="trash-icon"><i class="ph ph-trash"></i></span></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">No data found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>



            </div>

            <div class="card card-body container role_card" style="display: none;">
                <h5 class="mt-1" id="Role_Name"></h5>
                <div class="row container mt-1" id="permissions-container">


                </div>
            </div>

        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).on('click', '#delete', function(e) {
                swal({
                        title: "Are you sure?",
                        text: "To delete this Role.",
                        type: "warning",
                        confirmButtonText: "Yes",
                        showCancelButton: true
                    })
                    .then((result) => {
                        if (result.value) {
                            window.location = $(this).data('route');
                        } else if (result.dismiss === 'cancel') {
                            swal(
                                'Cancelled',
                                'Your stay here :)',
                                'error'
                            )
                        }
                    })
            });
        </script>
        <script>
            $(document).ready(function() {
                $(".btn-view-permission").click(function(e) {
                    e.preventDefault();
                    $(".role_card").show();
                    var permissions = $(this).data('permissions');
                    var role_name = $(this).data('role-name');
                    console.log(permissions);
                    $("#Role_Name").text(role_name);

                    var col1 = $('<div class="col-6"></div>');
                    var col2 = $('<div class="col-6"></div>');

                    // Create an unordered list to hold the permissions for each column
                    var permissionsList1 = $('<ul></ul>');
                    var permissionsList2 = $('<ul></ul>');

                    // Divide the permissions list into two arrays
                    var half = Math.ceil(permissions.length / 2); // To split the list into two equal parts
                    var firstHalf = permissions.slice(0, half);
                    var secondHalf = permissions.slice(half);

                    // Add permissions to the first column
                    $.each(firstHalf, function(index, permission) {
                        var listItem = $('<li></li>').text(permission.name);
                        permissionsList1.append(listItem);
                    });

                    // Add permissions to the second column
                    $.each(secondHalf, function(index, permission) {
                        var listItem = $('<li></li>').text(permission.name);
                        permissionsList2.append(listItem);
                    });

                    // Append the lists to the respective columns
                    col1.append(permissionsList1);
                    col2.append(permissionsList2);

                    // Append the columns to the container row, replacing the content
                    $('#permissions-container').html(col1).append(col2);

                });

            });
        </script>
    @endpush
