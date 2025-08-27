@extends('user.layouts.master')

@section('title')
    Warehouse Admin Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Warehouse Administrators</h3>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('warehouse-admins.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Add New Admin
                    </a>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Assigned Warehouses</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warehouseAdmins as $key => $admin)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $admin->first_name }} {{ $admin->last_name }}</td>
                                <td>{{ $admin->user_name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    @if ($admin->warehouses->count() > 0)
                                        <span class="badge bg-info">{{ $admin->warehouses->count() }} warehouses</span>
                                        <button type="button" class="btn btn-sm btn-link show-warehouses"
                                            data-bs-toggle="modal" data-bs-target="#warehouseModal{{ $admin->id }}">
                                            View
                                        </button>

                                        <!-- Modal for warehouses -->
                                        <div class="modal fade" id="warehouseModal{{ $admin->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Assigned Warehouses</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul class="list-group">
                                                            @foreach ($admin->warehouses as $warehouse)
                                                                <li class="list-group-item">{{ $warehouse->name }} -
                                                                    {{ $warehouse->address }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-warning">No warehouses</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($admin->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('warehouse-admins.edit', $admin->id) }}" class="edit_icon me-2">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" id="delete"
                                        data-route="{{ route('warehouse-admins.delete', $admin->id) }}"
                                        class="delete_icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "This will remove the warehouse admin role and all warehouse assignments from this user.",
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
                            'Operation cancelled',
                            'error'
                        )
                    }
                });
        });
    </script>
@endpush
