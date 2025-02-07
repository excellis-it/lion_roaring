@extends('user.layouts.master')
@section('title')
    Role Permission List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="row justify-content-between">
                                    <div class="col-md-2">
                                        <h3 class="mb-3">Role Permission List</h3>
                                    </div>

                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('ecclesias.index') }}" class="btn btn-primary me-3">+
                                            House Of Ecclesia</a>

                                        <a href="{{ route('roles.create') }}" class="btn btn-primary ">+ Add
                                            Role</a>
                                    </div>

                                </div>
                                <div class="row justify-content-end">
                                    {{-- <div class="col-lg-4">
                                                <div class="search-field">
                                                    <input type="text" name="search" id="search"
                                                        placeholder="search..." required=""
                                                        class="form-control rounded_search">
                                                    <button class="submit_search" id="search-button"> <span
                                                            class=""><i class="fa fa-search"></i></span></button>
                                                </div>
                                            </div> --}}
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                <th>Role</th>
                                                <th>Is ECCLESIA</th>
                                                <th>Permission</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($roles) > 0)
                                                @foreach ($roles as $key => $role)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td> {{ $role->name }}</td>
                                                        <td>{{ $role->is_ecclesia == 1 ? 'ECCLESIA' : '' }}</td>
                                                        {{-- <td>
                                                            @foreach ($role->permissions()->where('type', 1)->get() as $permission)
                                                                <span class="round-btn">{{ $permission->name }}</span>
                                                            @endforeach
                                                        </td> --}}
                                                        <td>

                                                            <button type="button" class="btn text-blue btn-view-permission"
                                                                data-permissions="{{ $role->permissions()->where('type', 1)->get() }}"
                                                                data-role-name="{{ $role->name }}">
                                                                View Permission
                                                            </button>


                                                        </td>
                                                        <td>

                                                            <div class="d-flex">
                                                                <a href="{{ route('roles.edit', Crypt::encrypt($role->id)) }}"
                                                                    class="edit_icon me-2">
                                                                    <i class="ti ti-edit"></i>
                                                                </a>
                                                                {{-- @if ($role->name == 'MEMBER' || $role->name == 'LEADER' || $role->name == 'ECCLESIA')
                                                                @else --}}
                                                                @if ($role->name != 'MEMBER')
                                                                    <a href="javascript:void(0);"
                                                                        data-route="{{ route('roles.delete', Crypt::encrypt($role->id)) }}"
                                                                        class="delete_icon" id="delete">
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                @endif
                                                                {{-- @endif --}}

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


                                <div class="card card-body container role_card" style="display: none;">
                                    <h5 class="mt-1" id="Role_Name"></h5>
                                    <div class="row container mt-1" id="permissions-container">


                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
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

                var col1 = $('<div class="col-4"></div>');
                var col2 = $('<div class="col-4"></div>');

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
