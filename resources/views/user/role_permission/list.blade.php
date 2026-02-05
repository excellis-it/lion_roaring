@extends('user.layouts.master')
@section('title')
    {{ App\Helpers\Helper::getMenuName('role_permission', 'Role Permission') }} List - {{ env('APP_NAME') }}
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
                            <div class="col-lg-12">

                                <div class="row justify-content-between">
                                    <div class="col-lg-8">
                                        <h3 class="mb-3">
                                            {{ App\Helpers\Helper::getMenuName('role_permission', 'Role Permission') }} List
                                        </h3>
                                    </div>

                                    <div class="col-lg-4 text-end">
                                        <a href="{{ route('ecclesias.index') }}" class="btn btn-primary me-3">+
                                            House Of Ecclesia</a>

                                        <a href="{{ route('roles.create') }}" class="btn btn-primary ">+ Add
                                            {{ App\Helpers\Helper::getMenuName('role_permission', 'Role Permission') }}</a>
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
                                                <th>{{ App\Helpers\Helper::getMenuName('role_permission', 'Role Permission') }}
                                                </th>
                                                <th>Is ECCLESIA</th>
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

                                                        <td>

                                                            <div class="d-flex">
                                                                <a href="{{ route('roles.edit', Crypt::encrypt($role->id)) }}"
                                                                    class="edit_icon me-2">
                                                                    <i class="ti ti-edit"></i>
                                                                </a>
                                                                {{-- @if ($role->name == 'MEMBER' || $role->name == 'LEADER' || $role->name == 'ECCLESIA')
                                                                @else --}}
                                                                @if (
                                                                    $role->name != 'MEMBER_NON_SOVEREIGN' &&
                                                                        $role->name != 'WAREHOUSE_ADMIN' &&
                                                                        $role->name != 'ESTORE_USER' &&
                                                                        $role->name != 'ECCLESIA')
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
            e.preventDefault();
            var deleteUrl = $(this).data('route');

            swal({
                    title: "Are you sure?",
                    text: "To delete this {{ App\Helpers\Helper::getMenuName('role_permission', 'Role Permission') }}.",
                    type: "warning",
                    confirmButtonText: "Yes, delete it!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                })
                .then((result) => {
                    if (result.value) {
                        // Make AJAX call to delete
                        $.ajax({
                            url: deleteUrl,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: 'Deleted!',
                                        text: response.message,
                                        type: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    swal({
                                        title: 'Error!',
                                        text: response.message,
                                        type: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                swal({
                                    title: 'Error!',
                                    text: 'An error occurred while deleting the role.',
                                    type: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your role is safe :)',
                            'info'
                        )
                    }
                })
        });
    </script>
@endpush
