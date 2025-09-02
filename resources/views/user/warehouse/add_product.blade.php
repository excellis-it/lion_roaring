@extends('user.layouts.master')

@section('title')
    Add Product to Warehouse
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-4">Add Product to Warehouse: {{ $wareHouse->name }}</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ware-houses.products.store', $wareHouse->id) }}" method="POST"
                        id="add-product-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="product_id">Select Product <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="sku">SKU*</label>
                                    <input type="text" name="sku" id="sku" class="form-control"
                                        value="{{ old('sku') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="color_id">Color</label>
                                    <select name="color_id" id="color_id" class="form-control">
                                        <option value="">No Color</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="size_id">Size</label>
                                    <select name="size_id" id="size_id" class="form-control">
                                        <option value="">No Size</option>

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" class="form-control"
                                        value="{{ old('price') }}" required>
                                    @if ($errors->has('price'))
                                        <span class="error">{{ $errors->first('price') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="quantity">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="quantity" id="quantity" class="form-control"
                                        value="{{ old('quantity', 1) }}" required>
                                    @if ($errors->has('quantity'))
                                        <span class="error">{{ $errors->first('quantity') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-4">
                            <button type="submit" class="print_btn me-2">Add Product</button>
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
            $("#add-product-form").on("submit", function(e) {
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });

        // on change product get product's size and colors
        $("#product_id").on("change", function() {
            var productId = $(this).val();
            if (productId) {
                $.ajax({
                    url: "{{ route('ware-houses.products.getDetails') }}",
                    method: "GET",
                    data: {
                        id: productId
                    },
                    success: function(response) {
                        if (response.status) {
                            // Populate size and color dropdowns
                            var sizeSelect = $("#size_id");
                            var colorSelect = $("#color_id");
                            sizeSelect.empty();
                            colorSelect.empty();

                            $.each(response.data.sizes, function(index, size) {
                                sizeSelect.append($("<option>").val(size.size.id).text(size.size
                                    .size));
                            });
                            $.each(response.data.colors, function(index, color) {
                                colorSelect.append($("<option>").val(color.color.id).text(color
                                    .color.color_name));
                            });
                        }
                    }
                });
            }
        });
    </script>
@endpush
