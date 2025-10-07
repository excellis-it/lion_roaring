@extends('user.layouts.master')

@section('title')
    E-Store Warehouse Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Warehouse List</h3>
                </div>
                <div class="col-md-2 float-right">
                    @if (auth()->user()->can('Create Estore Warehouse'))
                        <a href="{{ route('ware-houses.create') }}" class="btn btn-primary w-100"><i
                                class="fa-solid fa-upload"></i>
                            Create Warehouse</a>
                    @endif

                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Warehouse Admins</th>

                            <th>Status</th>
                            @if (Auth::user()->hasRole('SUPER ADMIN'))
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wareHouses as $key => $wareHouse)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $wareHouse->name }}</td>
                                <td>{{ Str::limit($wareHouse->address, 60) }}</td>
                                <td>
                                    @foreach ($wareHouse->admins as $user)
                                        <span class="">{{ $user->full_name }} ({{ $user->email }})</span>
                                        @if (!$loop->last)
                                            ,<br>
                                        @endif
                                    @endforeach
                                </td>


                                <td>{{ $wareHouse->is_active ? 'Active' : 'Inactive' }}</td>

                                <td>
                                    <div class="d-flex">
                                        @if (auth()->user()->can('View Estore Warehouse'))
                                            <a href="{{ route('ware-houses.products.list', $wareHouse->id) }}"
                                                class="edit_icon me-2" title="View Products">
                                                <i class="fa-solid fa-box"></i>
                                            </a>
                                        @endif
                                        @if (auth()->user()->can('Edit Estore Warehouse'))
                                            <a href="{{ route('ware-houses.edit', $wareHouse->id) }}"
                                                class="edit_icon me-2">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endif
                                        @if (auth()->user()->can('Delete Estore Warehouse'))
                                            <a href="javascript:void(0)" id="delete"
                                                data-route="{{ route('ware-houses.delete', $wareHouse->id) }}"
                                                class="delete_icon">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
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
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this warehouse.",
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
@endpush
