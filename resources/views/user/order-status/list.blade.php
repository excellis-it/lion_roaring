@extends('user.layouts.master')
@section('title')
    Order Status List - {{ env('APP_NAME') }}
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

                                <div class="row mb-3">
                                    <div class="col-md-10">
                                        <h3 class="mb-3">Order Status List</h3>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Order Status'))
                                            <a href="{{ route('order-status.create') }}" class="btn btn-primary w-100"><i
                                                    class="fa-solid fa-plus"></i> Create Status</a>
                                        @endif
                                    </div>
                                </div>
                                {{-- <div class="row justify-content-end">
                                    <div class="col-lg-4">
                                        <div class="search-field">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required="" class="form-control rounded_search">
                                            <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                <th>Name</th>
                                                {{-- <th>Slug</th> --}}
                                                <th>Sort Order</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($statuses as $key => $status)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $status->name }}</td>
                                                    {{-- <td>{{ $status->slug }}</td> --}}
                                                    <td>{{ $status->sort_order }}</td>
                                                    <td>{{ $status->is_active ? 'Active' : 'Inactive' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            @if (auth()->user()->can('Edit Order Status'))
                                                                <a href="{{ route('order-status.edit', $status->id) }}"
                                                                    class="edit_icon me-2">
                                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                                </a>
                                                            @endif

                                                            {{-- Delete only if status is NOT Pending, Delivered, Cancelled --}}
                                                            @if (auth()->user()->can('Delete Order Status') && !in_array($status->slug, ['pending', 'delivered', 'cancelled']))
                                                                <a href="javascript:void(0)" class="delete_status"
                                                                    data-route="{{ route('order-status.destroy', $status->id) }}">
                                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No data found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>

                                <hr>

                                <div class="row mb-2 mt-4">
                                    <div class="col-md-10">
                                        <h3 class="mb-3">Pickup Order Status List</h3>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Order Status'))
                                            <a href="{{ route('order-status.create', ['type' => 'pickup']) }}"
                                                class="btn btn-primary w-100"><i class="fa-solid fa-plus"></i> Create
                                                Pickup Status</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                <th>Name</th>
                                                {{-- <th>Slug</th> --}}
                                                <th>Sort Order</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pickupStatuses as $key => $status)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $status->name }}</td>
                                                    {{-- <td>{{ $status->slug }}</td> --}}
                                                    <td>{{ $status->sort_order }}</td>
                                                    <td>{{ $status->is_active ? 'Active' : 'Inactive' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            @if (auth()->user()->can('Edit Order Status'))
                                                                <a href="{{ route('order-status.edit', $status->id) }}"
                                                                    class="edit_icon me-2">
                                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                                </a>
                                                            @endif

                                                            @if (auth()->user()->can('Delete Order Status') &&
                                                                    !in_array($status->slug, ['pickup_pending', 'pickup_picked_up', 'pickup_cancelled']))
                                                                <a href="javascript:void(0)" class="delete_status"
                                                                    data-route="{{ route('order-status.destroy', $status->id) }}">
                                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No data found</td>
                                                </tr>
                                            @endforelse
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
        $(document).on('click', '.delete_status', function() {
            var route = $(this).data('route');

            swal({
                title: "Are you sure?",
                text: "You are about to delete this order status.",
                icon: "warning",
                buttons: ["Cancel", "Yes, delete it!"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // use a form submit for DELETE method
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': route
                    });
                    var token = $('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    });
                    var hiddenMethod = $('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    });
                    form.append(token, hiddenMethod).appendTo('body').submit();
                }
            });
        });
    </script>
@endpush
