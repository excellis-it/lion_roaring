@extends('user.layouts.master')

@section('title')
    Edit Warehouse Product
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-4">Edit Product in Warehouse: {{ $wareHouse->name }}</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ware-houses.products.update', [$wareHouse->id, $warehouseProduct->id]) }}"
                        method="POST" id="edit-product-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="product_id">Select Product <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $warehouseProduct->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="color_id">Color (Optional)</label>
                                    <select name="color_id" id="color_id" class="form-control">
                                        <option value="">No Color</option>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ $warehouseProduct->color_id == $color->id ? 'selected' : '' }}>
                                                {{ $color->color_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="size_id">Size (Optional)</label>
                                    <select name="size_id" id="size_id" class="form-control">
                                        <option value="">No Size</option>
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}"
                                                {{ $warehouseProduct->size_id == $size->id ? 'selected' : '' }}>
                                                {{ $size->size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="tax_rate">Tax Rate (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="tax_rate" id="tax_rate"
                                        class="form-control" value="{{ old('tax_rate', $warehouseProduct->tax_rate) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="quantity" id="quantity" class="form-control"
                                        value="{{ old('quantity', $warehouseProduct->quantity) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-4">
                            <button type="submit" class="print_btn me-2">Update Product</button>
                            <a href="{{ route('ware-houses.products', $wareHouse->id) }}"
                                class="print_btn print_btn_vv">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#edit-product-form").on("submit", function(e) {
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
@endpush
