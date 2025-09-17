@extends('user.layouts.master')

@section('title')
    E-Store Warehouse Product Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Select Warehouse (To Assign Product)</h3>
                </div>
                <div class="col-md-2 float-right">


                </div>
            </div>

            <div class="row  ms-2">
                {{-- product basic info --}}
                <div class="col-md-4">
                    <h5>Product Name : {{ $product->name }}</h5>
                    <p></p>
                </div>


            </div>

            <div>
                {{-- Grid view of warehouses with a tag  --}}
                <div class="row mt-3">
                    @foreach ($warehouses as $warehouse)
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('products.variations.warehouse', ['warehouseId' => $warehouse->id, 'productId' => $product->id]) }}"
                                class="text-decoration-none">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $warehouse->name ?? '' }}</h5>
                                        <p class="card-text">{{ $warehouse->address ?? '' }}</p>
                                    </div>

                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>


        </div>
    </div>
@endsection

@push('scripts')
@endpush
