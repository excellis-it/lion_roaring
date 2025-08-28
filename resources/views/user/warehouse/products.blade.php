@extends('user.layouts.master')

@section('title')
    Warehouse Products Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-8">
                    <h3 class="mb-3">Products in Warehouse: {{ $wareHouse->name }}</h3>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('ware-houses.index') }}" class="btn btn-secondary me-2">
                        <i class="fa-solid fa-arrow-left"></i> Back to Warehouses
                    </a>
                    <a href="{{ route('ware-houses.products.add', $wareHouse->id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Add Product Stock
                    </a>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Size</th>

                            <th>Stock Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($warehouseProducts->count() > 0)
                            @foreach ($warehouseProducts as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        @if ($item->color)
                                            <span class="badge" style="background-color: {{ $item->color->color }}">
                                                {{ $item->color->color_name }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $item->size ? $item->size->size : 'N/A' }}</td>

                                    <td>{{ $item->quantity }}</td>
                                    <td class="d-flex">
                                        <a href="{{ route('ware-houses.products.edit', [$wareHouse->id, $item->id]) }}"
                                            class="edit_icon me-2">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" id="delete"
                                            data-route="{{ route('ware-houses.products.delete', [$wareHouse->id, $item->id]) }}"
                                            class="delete_icon">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No products found in this warehouse</td>
                            </tr>
                        @endif
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
                    text: "To remove this product from the warehouse.",
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
