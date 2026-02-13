@extends('user.layouts.master')
@section('title')
    Product - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }

        .choices__list--dropdown.is-active {
            z-index: 999999;
        }

        /* Added preview styling */
        .image-preview {
            margin-top: .5rem;
        }

        .image-preview img {
            max-width: 220px;
            max-height: 160px;
            object-fit: cover;
            border: 1px solid #ddd;
            padding: 4px;
            border-radius: 4px;
        }

        .gallery-previews {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 0.5rem;
        }

        .gallery-previews img {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 4px;
        }
    </style>
    <style>
        .preview-image {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .preview-image img {
            width: 100px;
            height: 130px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .remove-image {
            position: absolute;
            top: -6px;
            right: -6px;
            background: red;
            color: white;
            border-radius: 50%;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        .invalid-feedback {
            display: block;
            color: red;
        }

        .text-danger {
            color: red !important;
        }
    </style>
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <form id="productCreateForm" action="{{ route('products.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

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
                                                value="{{ old('name') }}" />
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
                                                            // only render active categories
                                                            if (!$node->status) {
                                                                continue;
                                                            }

                                                            echo '<option value="' .
                                                                $node->id .
                                                                '"' .
                                                                (old('category_id') == $node->id ? ' selected' : '') .
                                                                '>' .
                                                                e($prefix . $node->name) .
                                                                '</option>';

                                                            // only recurse into active children
                                                            $children = $node->children->filter(function ($c) {
                                                                return $c->status;
                                                            });

                                                            if ($children->count()) {
                                                                $renderCategoryOptions(
                                                                    $children,
                                                                    $prefix . $node->name . '->',
                                                                );
                                                            }
                                                        }
                                                    };

                                                    $topLevelCategories = $categories
                                                        ->whereNull('parent_id')
                                                        ->filter(function ($c) {
                                                            return $c->status;
                                                        });

                                                    $renderCategoryOptions($topLevelCategories);
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
                                                value="{{ old('image') }}" accept="image/*">
                                            <span class="text-sm ms-2 text-muted">(width: 300px, height: 400px, max
                                                2MB)</span>
                                            @if ($errors->has('image'))
                                                <span class="error">{{ $errors->first('image') }}</span>
                                            @endif

                                            <!-- Preview for featured image -->
                                            <div class="image-preview" id="image-preview-container" style="display:none;">
                                                <img id="image-preview" src="#" alt="Featured preview" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- image --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="image"> Product Banner Image</label>
                                            <input type="file" name="background_image" id="background_image"
                                                class="form-control" value="{{ old('background_image') }}" accept="image/*">
                                            <span class="text-sm ms-2 text-muted">(width: 1920px, height: 520px, max
                                                2MB)</span>
                                            @if ($errors->has('background_image'))
                                                <span class="error">{{ $errors->first('background_image') }}</span>
                                            @endif

                                            <!-- Preview for banner image -->
                                            <div class="image-preview" id="background-image-preview-container"
                                                style="display:none;">
                                                <img id="background-image-preview" src="#" alt="Banner preview" />
                                            </div>

                                        </div>
                                    </div>

                                   

                                    {{-- short_description --}}
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label for="short_description"> Product Short Description</label>
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

                                    {{-- feature_product --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label for="feature_product"> Feature Product</label>
                                            <select name="feature_product" id="feature_product" class="form-control">

                                                <option value="1"
                                                    {{ old('feature_product') == 1 ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="0"
                                                    {{ old('feature_product') == 0 ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                            @if ($errors->has('feature_product'))
                                                <span class="error">{{ $errors->first('feature_product') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    {{-- is_new_product --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label for="is_new_product"> Is New Product</label>
                                            <select name="is_new_product" id="is_new_product" class="form-control">

                                                <option value="1" {{ old('is_new_product') == 1 ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="0" {{ old('is_new_product') == 0 ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                            @if ($errors->has('is_new_product'))
                                                <span class="error">{{ $errors->first('is_new_product') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-2 mb-2">
                                    </div>

                                    {{-- is_free --}}
                                    <div class="col-md-2 mb-2">
                                        <div class="box_label">
                                            <label for="is_free" class=""> Free Product</label>
                                            <div class="form-check form-switch mt-3">
                                                <label class="form-check-label" for="is_free">Mark as Free (Price becomes
                                                    0)</label>
                                                <input class="form-check-input mt-3 mb-3"
                                                    style="width: 60px; height: 30px;" type="checkbox" role="switch"
                                                    id="is_free" name="is_free" value="1"
                                                    {{ old('is_free') ? 'checked' : '' }}>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- status --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="status"> Status</label>
                                            <select name="status" id="status" class="form-control">

                                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0" {{ old('status', '1') == '0' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            @if ($errors->has('status'))
                                                <span class="error">{{ $errors->first('status') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- multiple images --}}
                                    <div class="col-md-12">
                                        <label class="col-form-label">Image Gallery (Drag and drop at least 1
                                            image) </label><br>

                                        <!-- Hidden native file input (keeps form submit working) -->
                                        <input type="file" class="form-control" id="image-upload" name="images[]"
                                            multiple accept="image/*" style="display:none;">

                                        <!-- Dropzone area -->
                                        <div id="dropzone-area" class="dropzone dz-clickable">
                                            <div class="dz-message-content" style="text-align:center;">

                                            </div>
                                        </div>
                                        <span class="text-sm ms-2 text-muted">(width: 300px, height: 400px, max
                                            2MB)</span>
                                        <span class="text-danger" id="images_error"></span>

                                        <!-- Previews grid -->
                                        <div id="gallery-previews" class="gallery-previews" style="display:none;"></div>
                                    </div>






                                    <div class="row mb-5 mt-5">
                                        <div class="col-md-12">
                                            <div class="heading_box mb-3">
                                                <h3>Product Type</h3>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="product_type"
                                                    id="simple_product" value="simple"
                                                    {{ old('product_type', 'simple') == 'simple' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="simple_product">Simple
                                                    Product</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="product_type"
                                                    id="variable_product" value="variable"
                                                    {{ old('product_type', 'simple') == 'variable' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="variable_product">Variable
                                                    Product</label>
                                            </div>
                                        </div>




                                    </div>

                                    <div id="simple-product-section"
                                        style="{{ old('product_type', 'simple') == 'simple' ? '' : 'display:none;' }}">
                                        <div class="row mb-5">
                                            <div class="col-md-12">
                                                <div class="heading_box mb-3">
                                                    <h3>Simple Product Details</h3>
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label for="sku"> Product SKU*</label>
                                                    <input type="text" name="sku" id="sku"
                                                        class="form-control" value="{{ old('sku') }}">
                                                    @if ($errors->has('sku'))
                                                        <span class="error">{{ $errors->first('sku') }}</span>
                                                    @endif
                                                </div>
                                            </div> --}}

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label for="use_market_price"
                                                        style="position: relative; top: 0; left: 0;">
                                                        Select Market Price</label>
                                                    <div class="form-check form-switch mt-1">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="use_market_price" id="use_market_price" value="1"
                                                            {{ old('use_market_price') ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            style="position: relative; top: 0;"
                                                            for="use_market_price">Enable</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2 market-price-fields" style="display:none;">
                                                <div class="box_label">
                                                    <label for="market_material_id"> Material <span
                                                            class="text-danger">*</span></label>
                                                    <select name="market_material_id" id="market_material_id"
                                                        class="form-control">
                                                        <option value="">Select Material</option>
                                                        @foreach ($marketMaterials as $material)
                                                            <option value="{{ $material->id }}"
                                                                {{ old('market_material_id') == $material->id ? 'selected' : '' }}>
                                                                {{ $material->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('market_material_id'))
                                                        <span
                                                            class="error">{{ $errors->first('market_material_id') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2 market-price-fields" style="display:none;">
                                                <div class="box_label">
                                                    <label for="market_grams"> Quantity <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" step="any" name="market_grams"
                                                        id="market_grams" class="form-control"
                                                        value="{{ old('market_grams') }}" min="0.01">
                                                    @if ($errors->has('market_grams'))
                                                        <span class="error">{{ $errors->first('market_grams') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-2 mb-2 market-price-fields" style="display:none;">
                                                <div class="box_label">
                                                    <label for="market_unit"> Unit <span
                                                            class="text-danger">*</span></label>
                                                    <select name="market_unit" id="market_unit" class="form-control">
                                                        <option value="g"
                                                            {{ old('market_unit') == 'g' ? 'selected' : '' }}>Gram (g)
                                                        </option>
                                                        <option value="oz"
                                                            {{ old('market_unit') == 'oz' ? 'selected' : '' }}>Ounce (oz)
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('market_unit'))
                                                        <span class="error">{{ $errors->first('market_unit') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2" id="price-field">
                                                <div class="box_label">
                                                    <label for="price"> Price*</label>
                                                    <input type="number" step="any" name="price" id="price"
                                                        class="form-control" value="{{ old('price') }}">
                                                    @if ($errors->has('price'))
                                                        <span class="error">{{ $errors->first('price') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2" id="sale-price-field">
                                                <div class="box_label">
                                                    <label for="sale_price"> Sale Price</label>
                                                    <input type="number" step="any" name="sale_price"
                                                        id="sale_price" class="form-control"
                                                        value="{{ old('sale_price') }}" min="0.00">
                                                    @if ($errors->has('sale_price'))
                                                        <span class="error">{{ $errors->first('sale_price') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label for="quantity"> Stock Quantity*</label>
                                                    <input type="number" name="quantity" id="quantity"
                                                        class="form-control" value="{{ old('quantity') }}">
                                                    @if ($errors->has('quantity'))
                                                        <span class="error">{{ $errors->first('quantity') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div id="variable-product-section"
                                        style="{{ old('product_type', 'simple') == 'variable' ? '' : 'display:none;' }}">
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
                                                                    <option value="{{ $size->id }}">
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
                                                                    <option value="{{ $color->id }}">
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


                                    <div class="row" id="other-charges-wrapper">
                                        {{-- Other Charges with name, charge amount with add more button --}}

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_box mb-3">
                                                    <h3>Other Charges</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <label>Other Charges</label>
                                                    <div>
                                                        <div class="mb-2">
                                                            <input type="text" name="other_charges[0][charge_name]"
                                                                class="form-control" placeholder="Ex. Package Charge">
                                                            {{-- showing error message --}}
                                                            <span class="text-danger"
                                                                id="other_charges.0.charge_name_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <div>
                                                        <div class="mb-2">
                                                            <input step="any" type="number"
                                                                name="other_charges[0][charge_amount]"
                                                                class="form-control" placeholder="Charge Amount"
                                                                min="0.00">
                                                            {{-- showing error message --}}
                                                            <span class="text-danger"
                                                                id="other_charges.0.charge_amount_error"></span>
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
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <!-- Choices.js -->
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            Dropzone.autoDiscover = false;

            // PARAMETERS
            var MAX_FILES = 8;
            var MAX_FILESIZE_MB = 12; // adjust if you want

            // Custom preview template
            var previewTemplate = [
                '<div class="dz-preview dz-file-preview">',
                '<div class="dz-image"><img data-dz-thumbnail /></div>',
                '<div class="dz-details">',
                '<div class="dz-filename"><span data-dz-name></span></div>',
                '<div class="dz-size" data-dz-size></div>',
                '</div>',
                '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>',
                '<div class="dz-success-mark">✔</div>',
                '<a class="dz-remove" href="javascript:undefined;" data-dz-remove>Remove</a>',
                '</div>'
            ].join('');


            var text_button = `
      <i class="fas fa-upload dz-message-icon" style="font-size:48px; color:#4caf50; margin-bottom:8px;"></i>
      <div class="dz-message-title" style="font-weight:bold; font-size:16px; color:#333;">Drag & drop images here</div>
      <div class="dz-message-sub" style="font-size:14px; color:#666;">or click to select</div>
    `;

            var myDropzone = new Dropzone("#dropzone-area", {
                url: "#", // we fake upload to get progress UI
                autoProcessQueue: false, // no real network upload
                uploadMultiple: false,
                parallelUploads: 8,
                maxFilesize: MAX_FILESIZE_MB,
                maxFiles: MAX_FILES,
                acceptedFiles: "image/*",
                addRemoveLinks: false, // we use custom remove element in template
                previewsContainer: "#gallery-previews", // put previews into the grid below
                clickable: "#dropzone-area", // drop area is clickable
                previewTemplate: previewTemplate,
                dictDefaultMessage: text_button,
                dictMaxFilesExceeded: "You can only upload up to " + MAX_FILES + " images.",
                init: function() {
                    var dz = this;

                    // When a file added: if too many, remove and show error; else start fake upload
                    dz.on("addedfile", function(file) {
                        // enforce max files (Dropzone handles but we also sync nicely)
                        if (dz.files.length > MAX_FILES) {
                            dz.removeFile(file);
                            showError('Maximum ' + MAX_FILES + ' images allowed.');
                            return;
                        }
                        clearError();
                        syncFilesToInput();
                        // start fake upload so user sees progress + success tick
                        fakeUpload(file);
                        // show previews container
                        document.getElementById('gallery-previews').style.display = dz.files.length ?
                            'grid' : 'none';
                    });

                    dz.on("removedfile", function(file) {
                        syncFilesToInput();
                        // hide previews if none
                        document.getElementById('gallery-previews').style.display = dz.files.length ?
                            'grid' : 'none';
                    });

                    // When user clicks the dropzone area the native file picker is opened by Dropzone.
                    // We DO NOT add another click handler (that caused double-picker before).

                    // Native input fallback: if user uses your input (e.g. from other UI), add to Dropzone
                    var nativeInput = document.getElementById('image-upload');
                    nativeInput.addEventListener('change', function() {
                        if (nativeInput.files && nativeInput.files.length) {
                            Array.from(nativeInput.files).forEach(function(file) {
                                // make sure we don't add duplicates or exceed limit
                                var already = dz.files.some(f => f.name === file.name && f.size ===
                                    file.size);
                                if (!already) {
                                    if (dz.files.length >= MAX_FILES) {
                                        showError('Maximum ' + MAX_FILES + ' images allowed.');
                                    } else {
                                        dz.addFile(file);
                                    }
                                }
                            });
                        }
                        syncFilesToInput();
                    });

                    // Hook remove link inside preview (works for our template)
                    // Dropzone's 'data-dz-remove' handles it automatically
                }
            });

            // Helper: sync Dropzone files -> native input using DataTransfer
            function syncFilesToInput() {
                var input = document.getElementById('image-upload');
                var dt = new DataTransfer();
                myDropzone.files.forEach(function(file) {
                    try {
                        dt.items.add(file);
                    } catch (e) {
                        // if DataTransfer fails, ignore (very old browsers)
                    }
                });
                input.files = dt.files;
            }

            // Error / clear
            function showError(msg) {
                var el = document.getElementById('images_error');
                el.textContent = msg;
            }

            function clearError() {
                document.getElementById('images_error').textContent = '';
            }


            // --- Fake upload to show progress + success tick ---
            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 80,
                bytesPerStep = 100000;

            function fakeUpload(file) {
                var dz = myDropzone;
                var totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                for (let step = 0; step < totalSteps; step++) {
                    let duration = timeBetweenSteps * (step + 1);
                    setTimeout(function() {
                        // progress (0..100)
                        var progress = 100 * (step + 1) / totalSteps;
                        file.upload = {
                            progress: progress,
                            total: file.size,
                            bytesSent: (step + 1) * file.size / totalSteps
                        };
                        dz.emit('uploadprogress', file, progress, file.upload.bytesSent);

                        if (progress >= 100) {
                            file.status = Dropzone.SUCCESS;
                            dz.emit("success", file, 'success', null);
                            dz.emit("complete", file);
                            // ensure sync (in case)
                            syncFilesToInput();
                        }
                    }, duration);
                }
            }
        </script>



        {{-- <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
        </script> --}}
        <script>
            ClassicEditor.create(document.querySelector("#description"));
            ClassicEditor.create(document.querySelector("#specification"));
        </script>

        <script>
            $(document).ready(function() {

                $('#productCreateForm').on('submit', function(e) {
                    e.preventDefault();

                    let form = $(this);
                    let url = form.attr('action');
                    let method = form.attr('method');

                    // Create FormData to support file uploads
                    let formData = new FormData(this);

                    // Clear previous error states
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                    $('.error-summary').remove(); // clear any old summary
                    $("#loading").addClass("loading");
                    $("#loading-content").addClass("loading-content");

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        processData: false, // important for file uploads
                        contentType: false, // important for file uploads
                        beforeSend: function() {
                            // Optional: disable button to prevent multiple clicks
                            form.find('button[type=submit]').prop('disabled', true);
                        },
                        success: function(response) {
                            $("#loading").removeClass("loading");
                            $("#loading-content").removeClass("loading-content");
                            toastr.success('Product created successfully!');
                            // Redirect or reset form if needed
                            form[0].reset();
                            window.location.href = "{{ route('products.index') }}"; // optional
                        },
                        error: function(xhr) {
                            $("#loading").removeClass("loading");
                            $("#loading-content").removeClass("loading-content");
                            form.find('button[type=submit]').prop('disabled', false);

                            // Clear previous states
                            form.find('.is-invalid').removeClass('is-invalid');
                            form.find('.invalid-feedback').remove();
                            // Clear any old "*_error" spans (like other_charges_0_charge_name_error or images_error)
                            $('[id$="_error"]').text('');

                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                let firstInvalidEl = null;

                                Object.keys(errors).forEach(function(field) {
                                    let messages = errors[field]; // array

                                    // --- Resolve selector name (map images.* -> images[] etc.)
                                    let selectorName;
                                    if (field === 'images' || field.startsWith('images.') ||
                                        field.startsWith('images[')) {
                                        selectorName = 'images[]';
                                    } else if (field.includes('.')) {
                                        // other_charges.0.charge_name -> other_charges[0][charge_name]
                                        selectorName = field.replace(/\.(\d+)/g, '[$1]')
                                            .replace(/\./g, '][');
                                    } else {
                                        selectorName = field;
                                    }

                                    // Build selectors to try
                                    const selectorsToTry = [
                                        `[name="${selectorName}"]`,
                                    ];
                                    if (!selectorName.includes('[') && field.endsWith(
                                            's')) {
                                        selectorsToTry.push(`[name="${selectorName}[]"]`);
                                    }
                                    if (selectorName.indexOf('[') !== -1) {
                                        const prefix = selectorName.split('[').slice(0, 2)
                                            .join('[') + '[';
                                        selectorsToTry.push(`[name^="${prefix}"]`);
                                    } else {
                                        selectorsToTry.push(`[name^="${selectorName}"]`);
                                    }

                                    // Try to find input using selectors
                                    let $input = $();
                                    for (let sel of selectorsToTry) {
                                        $input = form.find(sel);
                                        if ($input.length) break;
                                    }

                                    // Special explicit error span for images - prefer this if present
                                    // You used <span id="images_error"></span>
                                    const explicitImageSpan = (field.startsWith('images')) ?
                                        $('#images_error') : $();
                                    if (explicitImageSpan && explicitImageSpan.length) {
                                        explicitImageSpan.text(messages.join(' '));
                                        if (!firstInvalidEl) firstInvalidEl =
                                            explicitImageSpan;
                                        return; // done with this field
                                    }

                                    // Also check general "_error" span naming (field + '_error'), escape dots for ID
                                    const errorSpanId = field + '_error';
                                    const escapedId = errorSpanId.replace(
                                        /([:.#[\],/\\$*+?^(){}|-])/g, "\\$1");
                                    const $errorSpan = $(`#${escapedId}`);

                                    if ($input.length) {
                                        // Mark invalid
                                        $input.addClass('is-invalid');

                                        // Decide placement
                                        const tag = $input.prop('tagName').toLowerCase();
                                        const type = ($input.attr('type') || '')
                                            .toLowerCase();

                                        // If input is file/select/textarea -> place message below the visible wrapper
                                        if (type === 'file' || tag === 'select' || tag ===
                                            'textarea') {
                                            const $wrapper = $input.closest('.box_label');
                                            const messageHtml =
                                                `<div class="invalid-feedback d-block">${messages.join('<br>')}</div>`;

                                            if ($wrapper.length) {
                                                // append inside wrapper (after label/input block)
                                                $wrapper.append(messageHtml);
                                            } else {
                                                // IMPORTANT: for file inputs that have no .box_label, use after() (not append)
                                                $input.after(messageHtml);
                                            }
                                        } else {
                                            // For text/number inputs put the error after the last matched input element
                                            $input.last().after(
                                                `<div class="invalid-feedback d-block">${messages.join('<br>')}</div>`
                                            );
                                        }

                                        if (!firstInvalidEl) firstInvalidEl = $input
                                            .first();

                                    } else if ($errorSpan.length) {
                                        // Put message into explicit span if present
                                        $errorSpan.text(messages.join(' '));
                                        if (!firstInvalidEl) firstInvalidEl = $errorSpan;
                                    } else {
                                        // final fallback: summary area at top
                                        if ($('.error-summary').length === 0) {
                                            form.prepend(
                                                '<div class="error-summary alert alert-danger mt-2"></div>'
                                            );
                                        }
                                        $('.error-summary').append(
                                            `<div>${messages.join('<br>')}</div>`);
                                        if (!firstInvalidEl) firstInvalidEl = $(
                                            '.error-summary').first();
                                    }
                                });

                                // Scroll to first invalid item
                                if (firstInvalidEl && firstInvalidEl.length) {
                                    $('html, body').animate({
                                        scrollTop: firstInvalidEl.offset().top - 100
                                    }, 250);
                                    try {
                                        firstInvalidEl.focus();
                                    } catch (e) {}
                                }

                            } else {
                                toastr.error('Something went wrong. Please try again.');
                                console.error(xhr.responseText);
                            }
                        }



                    });
                });

            });
        </script>

        <script>
            $(document).ready(function() {
                // auto set slug from name
                // $('#name').on('keyup', function() {
                //     var name = $(this).val();
                //     var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                //     $('#slug').val(slug);
                // });

                function debounce(func, wait) {
                    let timeout;
                    return function() {
                        const context = this,
                            args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }

                $('#slug, #name').on('keyup change', debounce(function() {
                    let slug = $(this).val().trim();

                    if (slug.length > 0) {
                        $.ajax({
                            url: '{{ route('products.slug.check') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: slug
                            },
                            success: function(response) {
                                if (response.slug) {
                                    $('#slug').val(response.slug);
                                    $('#slug-feedback').text('');
                                } else if (response.error) {
                                    $('#slug-feedback').text(response.error);
                                }
                            }
                        });
                    }
                }, 500)); // wait 500ms after last key press





                function togglePriceFields() {
                    const isFree = $('#is_free').is(':checked');
                    const useMarket = $('#use_market_price').is(':checked');

                    if (useMarket) {
                        $('#price-field').hide();
                        $('#sale-price-field').hide();
                        $('.market-price-fields').show();
                        $('#price').prop('readonly', true).val('');
                        $('#sale_price').prop('readonly', true).val('');
                        $('#is_free').prop('checked', false).prop('disabled', true);
                    } else {
                        $('#price-field').show();
                        $('#sale-price-field').show();
                        $('.market-price-fields').hide();
                        $('#is_free').prop('disabled', false);

                        if (isFree) {
                            $('#price').prop('readonly', true).val('0');
                            $('#sale_price').prop('readonly', true).val('');
                        } else {
                            $('#price').prop('readonly', false);
                            $('#sale_price').prop('readonly', false);
                        }
                    }
                }
                $('#is_free').on('change', togglePriceFields);
                $('#use_market_price').on('change', togglePriceFields);
                togglePriceFields();


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
                                <span class="text-danger" id="other_charges.${otherChargeIndex}.charge_name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="box_label">
                                <input step="any" type="number" name="other_charges[${otherChargeIndex}][charge_amount]" class="form-control" placeholder="Charge Amount" min="0.00">
                                <span class="text-danger" id="other_charges.${otherChargeIndex}.charge_amount_error"></span>
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

        {{-- <script>
            (function() {
                function readSingleImage(input, previewImgEl, containerEl) {
                    if (input.files && input.files[0]) {
                        const file = input.files[0];
                        if (!file.type.startsWith('image/')) {
                            containerEl.hide();
                            previewImgEl.attr('src', '#');
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImgEl.attr('src', e.target.result);
                            containerEl.show();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewImgEl.attr('src', '#');
                        containerEl.hide();
                    }
                }

                function readMultipleImages(input, containerSelector) {
                    const $container = $(containerSelector);
                    $container.empty();
                    const files = input.files || [];
                    if (!files.length) {
                        $container.hide();
                        return;
                    }

                    Array.from(files).forEach(function(file, index) {
                        if (!file.type.startsWith('image/')) return;

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const $imgWrapper = $('<div/>', {
                                class: 'preview-image',
                                'data-index': index
                            });

                            const $img = $('<img/>', {
                                src: e.target.result,
                                alt: file.name
                            });

                            const $removeBtn = $('<span/>', {
                                class: 'remove-image',
                                text: '×'
                            });

                            // Remove image from preview and input
                            $removeBtn.on('click', function() {
                                $imgWrapper.remove();
                                // Update the input.files by creating a new DataTransfer
                                const dt = new DataTransfer();
                                Array.from(input.files)
                                    .filter((f, i) => i !== index)
                                    .forEach(f => dt.items.add(f));
                                input.files = dt.files;

                                if (!input.files.length) $container.hide();
                            });

                            $imgWrapper.append($img).append($removeBtn);
                            $container.append($imgWrapper);
                        };
                        reader.readAsDataURL(file);
                    });

                    $container.show();
                }

                $(function() {
                    const $featuredInput = $('#image');
                    const $featuredPreview = $('#image-preview');
                    const $featuredContainer = $('#image-preview-container');

                    const $bgInput = $('#background_image');
                    const $bgPreview = $('#background-image-preview');
                    const $bgContainer = $('#background-image-preview-container');

                    const $galleryInput = $('#image-upload');
                    const $galleryContainer = $('#gallery-previews');

                    $featuredInput.on('change', function() {
                        readSingleImage(this, $featuredPreview, $featuredContainer);
                    });

                    $bgInput.on('change', function() {
                        readSingleImage(this, $bgPreview, $bgContainer);
                    });

                    $galleryInput.on('change', function() {
                        readMultipleImages(this, $galleryContainer);
                    });
                });
            })();
        </script> --}}
    @endpush
