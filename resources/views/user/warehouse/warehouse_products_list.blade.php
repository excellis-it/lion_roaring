@extends('user.layouts.master')

@section('title')
    E-Store Warehouse Products List
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3"><span style="color: #f6bc41; font-weight:600;">{{$wareHouse->name ?? '-'}}</span> Products Stocks</h3>
                </div>
                <div class="col-md-2 float-right">
                    <a href="{{ route('ware-houses.index') }}" class="btn btn-primary w-100"><i
                            class="fa-solid fa-arrow-left"></i>
                        Back</a>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>SR.</th>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Global Stock Quantity</th>
                            <th>Warehouse Stock Quantity</th>




                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warehouseProducts as $key => $warehouseProduct)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $warehouseProduct->sku ?? '' }}</td>
                                <td>{{ $warehouseProduct->product?->name ?? '-' }}</td>
                                <td>{{ $warehouseProduct->colorDetail?->color_name ?? '-' }}</td>
                                <td>{{ $warehouseProduct->sizeDetail?->size ?? '-' }}</td>
                                <td>{{ $warehouseProduct->price ?? '' }}</td>
                                <td>{{ $warehouseProduct->stock_quantity ?? '' }}</td>
                                <td>{{ $warehouseProduct->warehouse_quantity ?? '' }}</td>




                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush
