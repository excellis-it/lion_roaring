@extends('user.layouts.master')

@section('title')
    Warehouse Admin - Product Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-8">
                    <h3 class="mb-3">Manage My Products</h3>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('warehouse-admin.products.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Warehouse Inventory</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($products->count() > 0)
                            @foreach ($products as $key => $product)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="badge bg-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>
                                        @foreach (auth()->user()->warehouses as $warehouse)
                                            @php
                                                $warehouseProduct = $product
                                                    ->warehouseProducts()
                                                    ->where('warehouse_id', $warehouse->id)
                                                    ->first();
                                            @endphp
                                            @if ($warehouseProduct)
                                                <div>
                                                    <strong>{{ $warehouse->name }}:</strong>
                                                    {{ $warehouseProduct->quantity }} units
                                                </div>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('warehouse-admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-info me-2">
                                            <i class="fa-solid fa-edit"></i> Edit
                                        </a>
                                        <a href="javascript:void(0)" id="delete"
                                            data-route="{{ route('warehouse-admin.products.delete', $product->id) }}"
                                            class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i> Remove
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No products found in your warehouses</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "This will remove the product from your warehouses.",
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
                            'Your product is safe :)',
                            'error'
                        )
                    }
                })
        });
    </script>
@endpush
