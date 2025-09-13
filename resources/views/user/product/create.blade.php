@extends('user.layouts.master')
@section('title')
    Product - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }
    </style>
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf




                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="product-details-tab" data-bs-toggle="tab"
                                    data-bs-target="#product-details" type="button" role="tab"
                                    aria-controls="product-details" aria-selected="true">Product
                                    Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="variation-tab" data-bs-toggle="tab" data-bs-target="#variation"
                                    type="button" role="tab" aria-controls="variation"
                                    aria-selected="false">Variation</button>
                            </li>
                        </ul>
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
                                <div class="row">

                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="name"> Product Name*</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                value="{{ old('name') }}">
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
                                                <option value="">Select Category</option>
                                                @php
                                                    $renderCategoryOptions = function ($nodes, $prefix = '') use (
                                                        &$renderCategoryOptions,
                                                    ) {
                                                        foreach ($nodes as $node) {
                                                            echo '<option value="' .
                                                                $node->id .
                                                                '"' .
                                                                (old('parent_id') == $node->id ? ' selected' : '') .
                                                                '>' .
                                                                e($prefix . $node->name) .
                                                                '</option>';
                                                            if (!empty($node->children) && $node->children->count()) {
                                                                $renderCategoryOptions(
                                                                    $node->children,
                                                                    $prefix . $node->name . '->',
                                                                );
                                                            }
                                                        }
                                                    };
                                                    $topLevelCategories = $categories->whereNull('parent_id');
                                                    $renderCategoryOptions($topLevelCategories);
                                                @endphp
                                            </select>
                                            @if ($errors->has('category_id'))
                                                <span class="error">{{ $errors->first('category_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- price --}}
                                    <div class="col-md-6 mb-2" hidden>
                                        <div class="box_label">
                                            <label for="price"> Product Price*</label>
                                            <input type="text" name="price" id="price" class="form-control"
                                                value="{{ old('price') }}">
                                            @if ($errors->has('price'))
                                                <span class="error">{{ $errors->first('price') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- quantity --}}
                                    {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="quantity"> Stock Quantity*</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                        value="{{ old('quantity') }}">
                                    @if ($errors->has('quantity'))
                                        <span class="error">{{ $errors->first('quantity') }}</span>
                                    @endif
                                </div>
                            </div> --}}

                                    {{-- slug --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="slug"> Product Slug*</label>
                                            <input type="text" name="slug" id="slug" class="form-control"
                                                value="{{ old('slug') }}">
                                            @if ($errors->has('slug'))
                                                <span class="error">{{ $errors->first('slug') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- image --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="image"> Product Featured Image*</label>
                                            <input type="file" name="image" id="image" class="form-control"
                                                value="{{ old('image') }}">
                                            @if ($errors->has('image'))
                                                <span class="error">{{ $errors->first('image') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- short_description --}}
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label for="short_description"> Product Short Description*</label>
                                            <input type="text" name="short_description" id="short_description"
                                                class="form-control" value="{{ old('short_description') }}">
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
                                                placeholder="Enter Product Description">{{ old('description') }}</textarea>
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
                                                placeholder="Enter Product Specification">{{ old('specification') }}</textarea>
                                            @if ($errors->has('specification'))
                                                <span class="error">{{ $errors->first('specification') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- button_name --}}
                                    {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="button_name"> Button Name*</label>
                                    <input type="text" name="button_name" id="button_name" class="form-control"
                                        value="ADD TO CART">
                                    @if ($errors->has('button_name'))
                                        <span class="error">{{ $errors->first('button_name') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                                    {{-- affiliate_link --}}
                                    {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="affiliate_link"> Affiliate Link*</label>
                                    <input type="text" name="affiliate_link" id="affiliate_link" class="form-control"
                                        value="{{ old('affiliate_link') }}">
                                    @if ($errors->has('affiliate_link'))
                                        <span class="error">{{ $errors->first('affiliate_link') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                                    {{-- sku --}}
                                    {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="sku"> Product SKU*</label>
                                    <input type="text" name="sku" id="sku" class="form-control"
                                        value="{{ old('sku') }}">
                                    @if ($errors->has('sku'))
                                        <span class="error">{{ $errors->first('sku') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                                    {{-- feature_product --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="feature_product"> Feature Product*</label>
                                            <select name="feature_product" id="feature_product" class="form-control">
                                                <option value="">Select Feature Product</option>
                                                <option value="1"
                                                    {{ old('feature_product') == 1 ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="0"
                                                    {{ old('feature_product') == 0 ? 'selected' : '' }}>No
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
                                                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                            @if ($errors->has('status'))
                                                <span class="error">{{ $errors->first('status') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-12">
                                <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image(Drag and drop
                                    atleast 1
                                    images)<span style="color:red">*<span></label>
                                <input type="file" class="form-control dropzone" id="image-upload" name="images[]"
                                    multiple>
                                @if ($errors->has('images.*'))
                                    <div class="error" style="color:red;">
                                        {{ $errors->first('images.*') }}</div>
                                @endif
                                @if ($errors->has('images'))
                                    <div class="error" style="color:red;">
                                        {{ $errors->first('images') }}</div>
                                @endif
                            </div> --}}
                                </div>
                                {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Seo Section</h3>
                                </div>
                            </div>
                        </div> --}}
                                <div class="row">
                                    {{-- <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="meta_title">Meta Title</label>

                                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                                        value="{{ old('meta_title') }}" placeholder="">
                                    @if ($errors->has('meta_title'))
                                        <span class="error">{{ $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="type">Mete Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="5" cols="30"
                                        placeholder="">{{ old('meta_description') }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <span class="error">{{ $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
                            </div> --}}

                                    {{-- <div class="mt-3 mb-5" style="height: 10px; border-bottom: 2px solid #eee; margin: 20px 0;">
                            </div> --}}

                                    {{-- Multi Sizes --}}
                                    <div class="col-md-4 mb-2" hidden>
                                        <div class="box_label">
                                            <label>Product Sizes <small>(Auto-selected from warehouse
                                                    products)</small></label>
                                            <div id="sizes-wrapper">
                                                <div class=" mb-2">
                                                    <select multiple name="sizes[]" class="sizeSelect"
                                                        id="global-size-select">
                                                        @foreach ($sizes as $size)
                                                            <option value="{{ $size->id }}">{{ $size->size }}
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
                                            <label>Product Colors <small>(Auto-selected from warehouse
                                                    products)</small></label>
                                            <div id="colors-wrapper">
                                                <div class="mb-2">
                                                    <select multiple name="colors[]" class="colorSelect"
                                                        id="global-color-select">
                                                        @foreach ($colors as $color)
                                                            <option value="{{ $color->id }}">{{ $color->color_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row" id="other-charges-wrapper">
                            {{-- Other Charges with name, charge amount with add more button --}}

                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="box_label">
                                        <label>Other Charges</label>
                                        <div>
                                            <div class="mb-2">
                                                <input type="text" name="other_charges[0][charge_name]"
                                                    class="form-control" placeholder="Ex. Package Charge">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <div class="box_label">
                                        <div>
                                            <div class="mb-2">
                                                <input step="any" type="number"
                                                    name="other_charges[0][charge_amount]" class="form-control"
                                                    placeholder="Charge Amount">
                                            </div>
                                        </div>
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

                        </div>

                            </div>
                            <div class="tab-pane fade" id="variation" role="tabpanel" aria-labelledby="variation-tab">

                                <!-- Warehouse Products Section -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-3">
                                            <h3>Warehouse Assignment</h3>
                                        </div>
                                    </div>
                                </div>

                                <div id="warehouse-products-container">
                                    <div class="warehouse-product-entry">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <label>Warehouse <span class="text-danger">*</span></label>
                                                    <select name="warehouse_products[0][warehouse_id]"
                                                        class="form-control warehouse-id" required>
                                                        <option value="">Select Warehouse</option>
                                                        @foreach ($warehouses as $warehouse)
                                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <label>SKU <span class="text-danger">*</span></label>
                                                    <input type="text" name="warehouse_products[0][sku]"
                                                        class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <label>Price <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01"
                                                        name="warehouse_products[0][price]" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label>Size</label>
                                                    <select name="warehouse_products[0][size_id]" class="form-control">
                                                        <option value="">No Size</option>
                                                        @foreach ($sizes as $size)
                                                            <option value="{{ $size->id }}">{{ $size->size }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label>Color</label>
                                                    <select name="warehouse_products[0][color_id]" class="form-control">
                                                        <option value="">No Color</option>
                                                        @foreach ($colors as $color)
                                                            <option value="{{ $color->id }}">{{ $color->color_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label>Images <span class="text-danger">*</span></label>
                                                    <input type="file" name="warehouse_products[0][images][]"
                                                        class="form-control" multiple required>
                                                </div>
                                            </div>



                                            <div class="col-md-2 mb-2">
                                                <div class="box_label">
                                                    <label>Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" min="0"
                                                        name="warehouse_products[0][quantity]" class="form-control"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-1 mb-2 d-flex ">
                                                <button type="button" class="btn btn-danger remove-warehouse-product"><i
                                                        class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" id="add-warehouse-product">
                                            <i class="fa fa-plus"></i> Add Warehouse Product
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-3 mb-5"
                                    style="height: 10px; border-bottom: 2px solid #eee; margin: 20px 0;">
                                </div>
                            </div>

                        </div>



                        <div class="row">
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
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
            $(document).ready(function() {
                // auto set slug from name
                $('#name').on('keyup', function() {
                    var name = $(this).val();
                    var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                    $('#slug').val(slug);
                });


            });
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

                // Add event listeners to warehouse product selects
                $(document).on('change keyup',
                    '[name^="warehouse_products"][name$="[size_id]"], [name^="warehouse_products"][name$="[color_id]"], [name^="warehouse_products"][name$="[price]"]',
                    function() {
                        updateGlobalSelects();

                    });

                // Also update when removing warehouse products
                $(document).on('click', '.remove-warehouse-product', function() {
                    setTimeout(updateGlobalSelects, 100); // Slight delay to ensure DOM is updated
                });

                // Initialize when adding new warehouse products
                $('#add-warehouse-product').on('click', function() {
                    setTimeout(function() {
                        // Add change listeners to the newly added selects
                        const newRow = document.querySelector('.warehouse-product-entry:last-child');
                        newRow.querySelector('[name$="[size_id]"]').addEventListener('change',
                            updateGlobalSelects);
                        newRow.querySelector('[name$="[color_id]"]').addEventListener('change',
                            updateGlobalSelects);
                    }, 100);
                });

                // Initial update
                updateGlobalSelects();
            });
        </script>
        <script>
            $(document).ready(function() {

                // Add more other charges
                let otherChargeIndex = 1; // Start from 1 since we already have one input

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
            $(document).ready(function() {
                // Warehouse product management
                let warehouseProductIndex = 0;

                // Add new warehouse product entry
                $('#add-warehouse-product').on('click', function() {
                    warehouseProductIndex++;

                    const newEntry = `
                    <div class="warehouse-product-entry">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse_products[${warehouseProductIndex}][warehouse_id]" class="form-control warehouse-id" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="warehouse_products[${warehouseProductIndex}][sku]" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="warehouse_products[${warehouseProductIndex}][price]" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <div class="box_label">
                                    <label>Size</label>
                                    <select name="warehouse_products[${warehouseProductIndex}][size_id]" class="form-control">
                                        <option value="">No Size</option>
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <div class="box_label">
                                    <label>Color</label>
                                    <select name="warehouse_products[${warehouseProductIndex}][color_id]" class="form-control">
                                        <option value="">No Color</option>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                             <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label>Images <span class="text-danger">*</span></label>
                                            <input type="file" name="warehouse_products[${warehouseProductIndex}][images][]"
                                                class="form-control" multiple required>
                                        </div>
                                    </div>



                            <div class="col-md-2 mb-2">
                                <div class="box_label">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="warehouse_products[${warehouseProductIndex}][quantity]" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-1 mb-2 d-flex ">
                                <button type="button" class="btn btn-danger remove-warehouse-product"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                        <hr>
                    </div>
                    `;

                    $('#warehouse-products-container').append(newEntry);
                });

                // Remove warehouse product entry
                $(document).on('click', '.remove-warehouse-product', function() {
                    $(this).closest('.warehouse-product-entry').remove();
                });
            });
        </script>
    @endpush
