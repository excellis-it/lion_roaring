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

        .choices__list--dropdown.is-active {
            z-index: 999999;
        }

        .color-variation-group {
            border: 2px solid #d9d9d9;
            border-radius: 10px;
            background: #fafafa;
        }

        /* Inner variation rows */
        .color-variation-group .variation-product-entry {
            border-bottom: 1px dashed #e3e3e3;
        }

        .color-variation-group .variation-product-entry:last-of-type {
            border-bottom: 0;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            @if ($product->product_type != 'simple')
                <div class="row">
                    <div class="col-lg-12">
                        <form id="generate-variations-form" action="{{ route('products.generate.variations') }}"
                            method="POST" enctype="multipart/form-data">
                            @method('POST')
                            @csrf

                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="heading_box mb-3">
                                        <h3>Generate Product Variations</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="row multi-generate-variation">
                                <div class="col-md-3 mb-2">
                                    <div class="box_label">
                                        <label>Select Color*</label>
                                        <div id="colors-wrapper">
                                            <div class="mb-2">
                                                <select name="colors[]" class="form-control" id="generate-color-select">
                                                    <option value="">-- Select Color --</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}">
                                                            {{ $color->color_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="box_label">
                                        <label>Select Sizes*</label>
                                        <div id="sizes-wrapper">
                                            <div class=" mb-2">
                                                <select multiple name="sizes[]" class="sizeSelect"
                                                    id="generate-size-select">
                                                    @foreach ($productSizes as $itemSize)
                                                        <option value="{{ $itemSize->size->id }}">
                                                            {{ $itemSize->size->size }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" id="generate-variations-btn">
                                            <i class="fa fa-plus"></i> Generate Variations
                                        </button>
                                    </div>
                                </div>

                            </div>


                        </form>
                    </div>
                </div>
            @endif

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('products.variations.update') }}" method="POST" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">


                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="heading_box mb-3">
                                    <h3>Product {{ $product->product_type != 'simple' ? 'Variations' : 'Stock' }}</h3>
                                    <h3>Product Name : {{ $product->name }}</h3>
                                </div>
                            </div>
                        </div>

                        <div id="variation-products-container">
                            @if ($product_variations->count() > 0)
                                @php
                                    $groupedVariations = $product_variations->groupBy('color_id');
                                    $index = 0;
                                @endphp

                                @foreach ($groupedVariations as $colorId => $colorGroup)
                                    @php
                                        $first = $colorGroup->first();
                                        $canDelete = $colorGroup->count() > 1;
                                    @endphp
                                    <div class="color-variation-group mb-4 p-3">
                                        @if ($product->product_type != 'simple')
                                            <h3 class="h3 mb-3">Color : {{ $first->colorDetail->color_name ?? '' }}</h3>
                                            <div class="d-flex justify-content-between align-items-start mb-3">

                                                <div class="w-25">
                                                    <label class="small fw-semibold mb-1">Images
                                                        ({{ $first->colorDetail->color_name ?? '' }})
                                                    </label>
                                                    <input type="file"
                                                        name="variation_products[{{ $index }}][images][]"
                                                        class="form-control" multiple accept="image/*">
                                                    <small class="text-muted d-block mt-1">Upload images once per
                                                        color.</small>
                                                </div>
                                            </div>
                                        @else
                                            {{-- // images without color variation --}}

                                            <div class="d-flex justify-content-between align-items-start mb-3">

                                                <div class="w-25">
                                                    <label class="small fw-semibold mb-1">Images

                                                    </label>
                                                    <input type="file"
                                                        name="variation_products[{{ $index }}][images][]"
                                                        class="form-control" multiple accept="image/*">
                                                    <small class="text-muted d-block mt-1">Upload images </small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="d-flex flex-wrap mb-3">
                                            @if ($first->images && $first->images->count())
                                                @foreach ($first->images as $image)
                                                    <div class="image-area m-1 position-relative" id="{{ $image->id }}"
                                                        style="width:80px; height:80px; overflow:hidden; border-radius:4px; background:#fff;">
                                                        <img src="{{ Storage::url($image->image_path) }}"
                                                            alt="Variation Image"
                                                            style="width:100%; height:100%; object-fit:cover;">
                                                        <button type="button" class="remove-image btn btn-sm"
                                                            data-id="{{ $image->id }}" title="Remove image"
                                                            style="position:absolute; top:4px; right:4px; display:flex; align-items:center; justify-content:center; width:26px; height:26px; padding:0; border-radius:50%;">
                                                            <i class="fa fa-times" style="font-size:12px;"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="image-area m-1 d-flex align-items-center justify-content-center"
                                                    style="width:80px; height:80px; background:#f8f9fa; border:1px dashed #e9ecef; color:#6c757d; border-radius:4px;">
                                                    <small>No images</small>
                                                </div>
                                            @endif
                                        </div>

                                        @foreach ($colorGroup as $variation)
                                            <div class="variation-product-entry py-2" data-id="{{ $variation->id }}">
                                                <input type="hidden" name="variation_products[{{ $index }}][id]"
                                                    value="{{ $variation->id }}">
                                                <input type="hidden"
                                                    name="variation_products[{{ $index }}][color_id]"
                                                    value="{{ $variation->color_id }}">

                                                <div class="row align-items-end g-2">
                                                    <div class="col-md-2">
                                                        <label class="small fw-semibold">SKU <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            name="variation_products[{{ $index }}][sku]"
                                                            class="form-control" value="{{ $variation->sku }}">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="small fw-semibold">Price <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" step="0.01"
                                                            name="variation_products[{{ $index }}][price]"
                                                            class="form-control" value="{{ $variation->price }}"
                                                            {{ $product->is_free == 1 ? 'readonly' : '' }}>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="small fw-semibold">Sale Price (If Any)</label>
                                                        <input type="number" step="0.01"
                                                            name="variation_products[{{ $index }}][sale_price]"
                                                            class="form-control" value="{{ $variation->sale_price }}"
                                                            {{ $product->is_free == 1 ? 'readonly' : '' }}>
                                                    </div>

                                                    <div class="col-md-1"
                                                        {{ $product->product_type == 'simple' ? 'hidden' : '' }}>
                                                        <label class="small fw-semibold">Color</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $variation->colorDetail->color_name ?? '' }}"
                                                            readonly>
                                                    </div>

                                                    <div class="col-md-2"
                                                        {{ $product->product_type == 'simple' ? 'hidden' : '' }}>
                                                        <label class="small fw-semibold">Size</label>
                                                        <input type="hidden"
                                                            name="variation_products[{{ $index }}][size_id]"
                                                            value="{{ $variation->size_id }}">
                                                        <input type="text" class="form-control"
                                                            value="{{ $variation->sizeDetail->size ?? '' }}" readonly>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="small fw-semibold">Global Stock Qty <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" min="0"
                                                            name="variation_products[{{ $index }}][stock_quantity]"
                                                            class="form-control"
                                                            value="{{ $variation->available_quantity }}">
                                                    </div>

                                                    @if ($product->product_type != 'simple' && $canDelete)
                                                        <div class="col-md-1 text-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger remove-variation-product">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @php $index++; @endphp
                                        @endforeach
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
    <script>
        $(document).ready(function() {
            $('.remove-image').click(function() {
                var id = $(this).data('id');
                var token = $("meta[name='csrf-token']").attr("content");
                $.ajax({
                    url: "{{ route('products.variation.image.delete') }}",
                    type: 'POST',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function() {
                        console.log("it Works");
                        $('#' + id).remove();
                    }
                });
            });


        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Choices.js for global selects
            const globalSizeSelect = new Choices("#generate-size-select", {
                removeItemButton: true,
                searchPlaceholderValue: "Type to search...",
                closeDropdownOnSelect: 'auto',
                placeholderValue: "Select size",
            });




        });
    </script>
    <script>
        // remove-variation-product
        $(document).on('click', '.remove-variation-product', function() {
            var entry = $(this).closest('.variation-product-entry');
            var variationId = entry.data('id');

            if (variationId) {
                // Send AJAX request to delete the variation from the database
                $.ajax({
                    url: "{{ route('products.variation.delete') }}",
                    data: {
                        id: variationId
                    },
                    type: 'POST',
                    success: function(response) {
                        // On success, remove the entry from the DOM
                        entry.remove();
                        toastr.success('Variation deleted successfully.');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting the variation.');
                    }
                });
            } else {
                // If no ID, just remove the entry from the DOM
                entry.remove();
            }
        });

        // After existing success callback where entry.remove();
        if (!group.find('.variation-product-entry').length) {
            group.remove();
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#generate-variations-form').on('submit', function(e) {
                const color = $('#generate-color-select').val();
                const sizes = $('#generate-size-select').val();
                if (!color) {
                    e.preventDefault();
                    toastr.error('Please select a color.');
                    return;
                }
                if (!sizes || !sizes.length) {
                    e.preventDefault();
                    toastr.error('Please select at least one size.');
                }
            });
        });
    </script>
@endpush
