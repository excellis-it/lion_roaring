@extends('user.layouts.master')
@section('title')
    Product Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }
    </style>
    <style>
        .image-area {
            position: relative;
            width: 15%;
            background: #333;
        }

        .image-area img {
            max-width: 100%;
            height: auto;
        }

        .remove-image {
            display: none;
            position: absolute;
            top: -10px;
            right: -10px;
            border-radius: 10em;
            padding: 2px 6px 3px;
            text-decoration: none;
            font: 700 21px/20px sans-serif;
            background: #555;
            border: 3px solid #fff;
            color: #FFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5), inset 0 2px 4px rgba(0, 0, 0, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            -webkit-transition: background 0.5s;
            transition: background 0.5s;
        }

        .remove-image:hover {
            background: #E54E4E;
            padding: 3px 7px 5px;
            top: -11px;
            right: -11px;
        }

        .remove-image:active {
            background: #E54E4E;
            top: -10px;
            right: -11px;
        }

        .remove-warehouse-product {
            max-height: 60px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('products.index') }}" class="print_btn print_btn_vv float-end mb-3">Back to
                        Products</a>
                </div>
                <h5>Product Name : <strong>{{ $product->name }}</strong></h5>
                <h5> Product Variations for Warehouse : <strong>{{ $wareHouse->name }}</strong></h5>

            </div>

            <div class="row">
                {{-- search product variations by color to select --}}
                <div class="col-md-4 mt-3 mb-3">
                    <form
                        action="{{ route('products.variations.warehouse', ['warehouseId' => $wareHouse->id, 'productId' => $product->id]) }}"
                        method="GET">
                        <label for="">Select Colors To Get Variations</label>
                        <div class="input-group mb-3">

                            <select name="color_id" class="form-control" multiple>
                                <option value="">-- Select Colors --</option>
                                @foreach ($product_have_colors as $color)
                                    <option value="{{ $color->id }}"
                                        {{ in_array($color->id, (array) request('color_id')) ? 'selected' : '' }}>
                                        {{ $color->color_name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" type="submit">Select</button>
                        </div>
                    </form>
                </div>

            </div>


            <!--  Row 1 -->
            <div class="row" hidden>
                <div class="col-lg-12">
                    <form action="{{ route('products.variations.update') }}" method="POST" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">


                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="heading_box mb-3">
                                    <h3>Product Variations</h3>
                                </div>
                            </div>
                        </div>

                        <div id="variation-products-container">
                            @if ($product_variations->count() > 0)
                                @foreach ($product_variations as $index => $variation)
                                    <div class="variation-product-entry" data-id="{{ $variation->id }}">
                                        <input type="hidden" name="variation_products[{{ $index }}][id]"
                                            value="{{ $variation->id }}">
                                        <div class="row">


                                            <div class="col-md-2 mb-2">
                                                <div class="box_label">
                                                    <label>SKU <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        name="variation_products[{{ $index }}][sku]"
                                                        class="form-control" value="{{ $variation->sku }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-2 mb-2">
                                                <div class="box_label">
                                                    <label>Price <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01"
                                                        name="variation_products[{{ $index }}][price]"
                                                        class="form-control" value="{{ $variation->price }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-1 mb-2">
                                                <div class="box_label">
                                                    <label>Color</label>
                                                    <input type="hidden"
                                                        name="variation_products[{{ $index }}][color_id]"
                                                        class="form-control" value="{{ $variation->color_id }}">
                                                    <input type="text"
                                                        name="variation_products[{{ $index }}][color]"
                                                        class="form-control"
                                                        value="{{ $variation->colorDetail->color_name ?? '' }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-1 mb-2">
                                                <div class="box_label">
                                                    <label>Size</label>
                                                    <input type="hidden"
                                                        name="variation_products[{{ $index }}][size_id]"
                                                        class="form-control" value="{{ $variation->size_id }}">
                                                    <input type="text"
                                                        name="variation_products[{{ $index }}][size]"
                                                        class="form-control"
                                                        value="{{ $variation->sizeDetail->size ?? '' }}" readonly>

                                                </div>
                                            </div>





                                            <div class="col-md-2 mb-2">
                                                <div class="box_label">
                                                    <label>Available Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" min="0"
                                                        name="variation_products[{{ $index }}][available_quantity]"
                                                        class="form-control" value="{{ $variation->allocated_qty }}"
                                                        readonly>
                                                </div>
                                            </div>



                                            {{-- Images --}}


                                            <div class="col-md-3 mb-2 d-flex flex-wrap align-items-start">
                                                <div class="d-flex flex-wrap">
                                                    @if ($variation->images && $variation->images->count() > 0)
                                                        @foreach ($variation->images as $image)
                                                            <div class="image-area m-1 position-relative"
                                                                id="{{ $image->id }}"
                                                                style="width:80px; height:80px; overflow:hidden; border-radius:4px; background:#fff;">
                                                                <img src="{{ Storage::url($image->image_path) }}"
                                                                    alt="Variation Image"
                                                                    style="width:100%; height:100%; object-fit:cover; display:block;">

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="image-area m-1 d-flex align-items-center justify-content-center"
                                                            style="width:80px; height:80px; background:#f8f9fa; border:1px dashed #e9ecef; color:#6c757d; border-radius:4px;">
                                                            <small>No images</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-1 mb-2">
                                                <div class="box_label">
                                                    <label>Set Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" min="0"
                                                        name="variation_products[{{ $index }}][quantity]"
                                                        class="form-control" value="{{ $variation->quantity }}">
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                    </div>
                                @endforeach
                            @else
                                <h4 class="text-center">No variations available</h4>
                            @endif
                        </div>

                        {{-- <div class="row mb-4">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="add-warehouse-product">
                                    <i class="fa fa-plus"></i> Add Warehouse Product
                                </button>
                            </div>
                        </div> --}}


                        <div class="mt-3 mb-5" style="height: 10px; border-bottom: 2px solid #eee; margin: 20px 0;">
                        </div>

                        <div class="row">
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('products.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <!-- Choices.js -->
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
        </script>
        <script>
            ClassicEditor.create(document.querySelector("#description"));
            ClassicEditor.create(document.querySelector("#specification"));
        </script>
    @endpush
