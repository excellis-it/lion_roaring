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
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf

                        {{-- <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="product-details-tab" data-bs-toggle="tab"
                                    data-bs-target="#product-details" type="button" role="tab"
                                    aria-controls="product-details" aria-selected="true">Product
                                    Details</button>
                            </li>

                        </ul> --}}
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="product-details" role="tabpanel"
                                aria-labelledby="product-details-tab">

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Product Details</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-5">

                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="name"> Product Name*</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                value="{{ $product->name }}" placeholder="">
                                            @if ($errors->has('name'))
                                                <span class="error">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- category_id --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="category_id"> Category*</label>
                                            <select name="category_id" id="category_id" class="form-control">
                                                <option value="">Select Parent Category</option>
                                                @php
                                                    $renderCategoryOptions = function (
                                                        $nodes,
                                                        $prefix = '',
                                                        $selectedParentId = null,
                                                    ) use (&$renderCategoryOptions) {
                                                        foreach ($nodes as $node) {
                                                            echo '<option value="' .
                                                                $node->id .
                                                                '"' .
                                                                ($selectedParentId == $node->id ? ' selected' : '') .
                                                                '>' .
                                                                e($prefix . $node->name) .
                                                                '</option>';
                                                            if (!empty($node->children) && $node->children->count()) {
                                                                $renderCategoryOptions(
                                                                    $node->children,
                                                                    $prefix . $node->name . '->',
                                                                    $selectedParentId,
                                                                );
                                                            }
                                                        }
                                                    };
                                                    $topLevelCategories = $categories->whereNull('parent_id');
                                                    $renderCategoryOptions(
                                                        $topLevelCategories,
                                                        '',
                                                        $product->category_id,
                                                    );
                                                @endphp
                                            </select>
                                            @if ($errors->has('category_id'))
                                                <span class="error">{{ $errors->first('category_id') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    {{-- slug --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="slug"> Product Slug*</label>
                                            <input type="text" name="slug" id="slug" class="form-control"
                                                value="{{ $product->slug }}">
                                            @if ($errors->has('slug'))
                                                <span class="error">{{ $errors->first('slug') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- image --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="image"> Product Featured Image</label>
                                            <input type="file" name="image" id="image" class="form-control"
                                                value="{{ old('image') }}">

                                            @if ($errors->has('image'))
                                                <span class="error">{{ $errors->first('image') }}</span>
                                            @endif
                                        </div>
                                        <label for="" class="ms-3 "><a class="text-link text-primary"
                                                href="{{ Storage::url($product->image?->image ?? '') }}"
                                                target="_blank">View</a></label>
                                    </div>

                                    {{-- short_description --}}
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label for="short_description"> Product Short Description*</label>
                                            <input type="text" name="short_description" id="short_description"
                                                class="form-control" value="{{ $product->short_description }}">
                                            @if ($errors->has('short_description'))
                                                <span class="error">{{ $errors->first('short_description') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- description --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="description"> Product Description*</label>
                                            <textarea name="description" id="description" class="form-control" rows="5" cols="30"
                                                placeholder="Enter Product Description">{{ $product['description'] }}</textarea>
                                            @if ($errors->has('description'))
                                                <span class="error">{{ $errors->first('description') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- specification --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="specification"> Product Specification*</label>
                                            <textarea name="specification" id="specification" class="form-control" rows="5" cols="30"
                                                placeholder="Enter Product Specification">{{ $product['specification'] }}</textarea>
                                            @if ($errors->has('specification'))
                                                <span class="error">{{ $errors->first('specification') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- feature_product --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="feature_product"> Feature Product*</label>
                                            <select name="feature_product" id="feature_product" class="form-control">
                                                <option value="">Select Feature Product</option>
                                                <option value="1"
                                                    {{ $product->feature_product == 1 ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="0"
                                                    {{ $product->feature_product == 0 ? 'selected' : '' }}>No
                                                </option>
                                            </select>

                                            @if ($errors->has('feature_product'))
                                                <span class="error">{{ $errors->first('feature_product') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- status --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="status"> Status*</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            @if ($errors->has('status'))
                                                <span class="error">{{ $errors->first('status') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image(Drag and
                                            drop
                                            atleast 1
                                            images)</label>
                                        <input type="file" class="form-control dropzone" id="image-upload"
                                            name="images[]" multiple>
                                        @if ($errors->has('images.*'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('images.*') }}</div>
                                        @endif
                                        @if ($errors->has('images'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('images') }}</div>
                                        @endif
                                    </div>


                                </div>

                                @if ($product->withOutMainImage)
                                    <div class="row mb-6">
                                        <label for="inputConfirmPassword2" class="col-form-label">Image Preview</label>

                                        @foreach ($product->withOutMainImage as $image)
                                            <div class="image-area m-4" id="{{ $image->id }}">
                                                <img src="{{ Storage::url($image->image) }}" alt="Preview">
                                                <a class="remove-image" href="javascript:void(0);"
                                                    data-id="{{ $image->id }}" style="display: inline;">&#215;</a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif



                                <div class="row mb-5">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-3">
                                            <h3>Product Type : {{ $product->product_type }}</h3>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="product_type"
                                                id="simple_product" value="simple"
                                                {{ $product->product_type == 'simple' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="simple_product">Simple
                                                Product</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="product_type"
                                                id="variable_product" value="variable"
                                                {{ $product->product_type == 'variable' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="variable_product">Variable
                                                Product</label>
                                        </div>
                                    </div> --}}




                                </div>

                                <div id="simple-product-section"
                                    style="{{ $product->product_type == 'simple' ? '' : 'display:none;' }}">
                                    <div class="row mb-5">
                                        <div class="col-md-12">
                                            <div class="heading_box mb-3">
                                                <h3>Simple Product Details</h3>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="box_label">
                                                <label for="simple_sku"> Product SKU</label>
                                                <input type="text" name="sku" id="simple_sku"
                                                    class="form-control" value="{{ $product->sku }}">
                                                @if ($errors->has('sku'))
                                                    <span class="error">{{ $errors->first('sku') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="box_label">
                                                <label for="simple_price"> Product Price</label>
                                                <input type="text" name="price" id="simple_price"
                                                    class="form-control" value="{{ $product->price }}">
                                                @if ($errors->has('price'))
                                                    <span class="error">{{ $errors->first('price') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="box_label">
                                                <label for="simple_quantity"> Stock Quantity</label>
                                                <input type="number" name="quantity" id="simple_quantity"
                                                    class="form-control" value="{{ $product->quantity }}">
                                                @if ($errors->has('quantity'))
                                                    <span class="error">{{ $errors->first('quantity') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div id="variable-product-section"
                                    style="{{ $product->product_type == 'variable' ? '' : 'display:none;' }}">
                                    <div class="row mb-5">
                                        <div class="col-md-12">
                                            <div class="heading_box mb-3">
                                                <h3>Variable Product Details</h3>
                                            </div>
                                        </div>

                                        {{-- Multi Sizes --}}
                                        <div class="col-md-4 mb-2">
                                            <div class="box_label">
                                                <label>Product Sizes </label>
                                                <div id="sizes-wrapper">
                                                    <div class=" mb-2">
                                                        <select multiple name="sizes[]" class="sizeSelect"
                                                            id="global-size-select">
                                                            @foreach ($sizes as $size)
                                                                <option value="{{ $size->id }}"
                                                                    {{ $product->sizeIds()->contains($size->id) ? 'selected' : '' }}>
                                                                    {{ $size->size }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Multi Colors --}}
                                        <div class="col-md-4 mb-2" hidden>
                                            <div class="box_label">
                                                <label>Product Colors</label>
                                                <div id="colors-wrapper">
                                                    <div class="mb-2">
                                                        <select multiple name="colors[]" class="colorSelect"
                                                            id="global-color-select">
                                                            @foreach ($colors as $color)
                                                                <option value="{{ $color->id }}"
                                                                    {{ $product->colorIds()->contains($color->id) ? 'selected' : '' }}>
                                                                    {{ $color->color_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Other Charges Section -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-3">
                                            <h3>Other Charges</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="other-charges-wrapper">
                                    @if ($product->otherCharges->count() > 0)
                                        @foreach ($product->otherCharges as $index => $charge)
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        @if ($index == 0)
                                                            <label>Other Charges</label>
                                                        @endif
                                                        <input type="text"
                                                            name="other_charges[{{ $index }}][charge_name]"
                                                            class="form-control" placeholder="Ex. Package Charge"
                                                            value="{{ $charge->charge_name }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        <input step="any" type="number"
                                                            name="other_charges[{{ $index }}][charge_amount]"
                                                            class="form-control" placeholder="Charge Amount"
                                                            value="{{ $charge->charge_amount }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        <div class="mb-2 mt-1">
                                                            @if ($index == 0)
                                                                <button type="button"
                                                                    class="btn btn-primary add-more-other-charge">+</button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn btn-danger text-danger remove-other-charge">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <label>Other Charges</label>
                                                    <input type="text" name="other_charges[0][charge_name]"
                                                        class="form-control" placeholder="Ex. Package Charge">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <input step="any" type="number"
                                                        name="other_charges[0][charge_amount]" class="form-control"
                                                        placeholder="Charge Amount">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <div class="mb-2 mt-1">
                                                        <button type="button"
                                                            class="btn btn-primary add-more-other-charge">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

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

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Initialize Choices.js for global selects
                const globalSizeSelect = new Choices("#global-size-select", {
                    removeItemButton: true,
                    searchPlaceholderValue: "Type to search...",
                    closeDropdownOnSelect: 'auto',
                    placeholderValue: "Select size",
                });

                const globalColorSelect = new Choices("#global-color-select", {
                    removeItemButton: true,
                    searchPlaceholderValue: "Type to search...",
                    closeDropdownOnSelect: 'auto',
                    placeholderValue: "Select color",
                });

                // Function to update global selects based on warehouse products
                function updateGlobalSelects() {
                    const selectedSizeIds = new Set();
                    const selectedColorIds = new Set();

                    // Collect all selected sizes and colors from warehouse products
                    document.querySelectorAll('[name^="warehouse_products"][name$="[size_id]"]').forEach(sizeSelect => {
                        if (sizeSelect.value) {
                            selectedSizeIds.add(sizeSelect.value);
                        }
                    });

                    document.querySelectorAll('[name^="warehouse_products"][name$="[color_id]"]').forEach(
                        colorSelect => {
                            if (colorSelect.value) {
                                selectedColorIds.add(colorSelect.value);
                            }
                        });

                    // Update global size select
                    globalSizeSelect.removeActiveItems();
                    selectedSizeIds.forEach(sizeId => {
                        globalSizeSelect.setChoiceByValue(sizeId);
                    });

                    // Update global color select
                    globalColorSelect.removeActiveItems();
                    selectedColorIds.forEach(colorId => {
                        globalColorSelect.setChoiceByValue(colorId);
                    });

                    // update id=price of first warehouse sets price
                    const firstWarehousePriceInput = document.querySelector(
                        '[name^="warehouse_products"][name$="[price]"]');
                    //  console.log(firstWarehousePriceInput);

                    if (firstWarehousePriceInput) {

                        const price = firstWarehousePriceInput.value;
                        $("#price").val(price);

                    }
                }


            });
        </script>

        <script>
            $(document).ready(function() {

                // Add more other charges
                let otherChargeIndex =
                    {{ $product->otherCharges->count() + 1 }}; // Start from last index


                $('.add-more-other-charge').on('click', function() {
                    const newChargeHtml = `
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="box_label">
                                <input type="text" name="other_charges[${otherChargeIndex}][charge_name]" class="form-control" placeholder="Ex. Shipping Charge">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="box_label">
                                <input step="any" type="number" name="other_charges[${otherChargeIndex}][charge_amount]" class="form-control" placeholder="Charge Amount">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="box_label">
                                <div class="mb-2 mt-1">
                                    <button type="button" class="btn btn-danger text-danger remove-other-charge"><i class="fas fa-close"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    $('#other-charges-wrapper').append(newChargeHtml);
                    otherChargeIndex++;
                });

                // Remove other charge
                $(document).on('click', '.remove-other-charge', function() {
                    $(this).closest('.row').remove();
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const simpleProductRadio = document.getElementById('simple_product');
                const variableProductRadio = document.getElementById('variable_product');
                const simpleProductSection = document.getElementById('simple-product-section');
                const variableProductSection = document.getElementById('variable-product-section');

                simpleProductRadio.addEventListener('change', function() {
                    simpleProductSection.style.display = 'block';
                    variableProductSection.style.display = 'none';
                });

                variableProductRadio.addEventListener('change', function() {
                    simpleProductSection.style.display = 'none';
                    variableProductSection.style.display = 'block';
                });
            });
        </script>
    @endpush
