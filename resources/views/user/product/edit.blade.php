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
            width: 7%;
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
            margin-top: .5rem;
        }

        .gallery-previews img {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 4px;
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
            <div class="row">
                <div class="col-lg-12">
                    <form id="productEditForm" action="{{ route('products.update', $product->id) }}" method="POST"
                        enctype="multipart/form-data">
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
                                        <div class="heading_box mb-5 d-flex align-items-center justify-content-between">
                                            <h3>Product Details</h3>
                                            <h3>Product Type : {{ $product->product_type }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-5">

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
                                    <div class="col-xxl-4 col-md-6 mb-2">
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
                                    <div class="col-xxl-3 col-md-5 mb-2">
                                        <div class="box_label">
                                            <label for="image"> Product Featured Image*</label>
                                            <input type="file" name="image" id="image" class="form-control"
                                                value="{{ old('image') }}" accept="image/*">
                                            <span class="text-sm ms-2 text-muted" style="font-size:12px;">(width: 300px,
                                                height: 400px, max
                                                2MB)</span>

                                            @if ($errors->has('image'))
                                                <span class="error">{{ $errors->first('image') }}</span>
                                            @endif
                                        </div>
                                        {{-- <label for="" class="ms-3 "><a class="text-link text-primary"
                                                href="{{ Storage::url($product->image?->image ?? '') }}"
                                                target="_blank">View</a></label> --}}
                                    </div>

                                    {{-- image preview --}}
                                    <div class="col-md-1 mb-2">
                                        <div class="box_label">
                                            <a href="{{ Storage::url($product->image?->image ?? '') }}" target="_blank"
                                                id="image_preview_anchor">
                                                <img style="height: 50px; width: 50px; object-fit: cover;"
                                                    id="image_preview"
                                                    src="{{ Storage::url($product->image?->image ?? '') }}"
                                                    alt="Product Image" class="img-fluid">
                                            </a>
                                        </div>
                                    </div>

                                    {{-- background_image --}}
                                    <div class="col-xxl-3 col-md-5 mb-2">
                                        <div class="box_label">
                                            <label for="background_image"> Product Banner Image</label>
                                            <input type="file" name="background_image" id="background_image"
                                                class="form-control" value="{{ old('background_image') }}"
                                                accept="image/*">
                                            <span class="text-sm ms-2 text-muted" style="font-size:12px;">(width: 1920px,
                                                height: 520px, max
                                                2MB)</span>
                                            @if ($errors->has('background_image'))
                                                <span class="error">{{ $errors->first('background_image') }}</span>
                                            @endif
                                        </div>
                                        {{-- <label for="" class="ms-3 "><a class="text-link text-primary"
                                                href="{{ Storage::url($product->background_image ?? '') }}"
                                                target="_blank">View</a></label> --}}
                                    </div>


                                    {{-- background image preview --}}
                                    <div class="col-md-1 mb-2">
                                        <div class="box_label" id="background_preview_wrapper">
                                            @if ($product->background_image)
                                                <a href="{{ Storage::url($product->background_image ?? '') }}"
                                                    target="_blank" id="background_image_preview_anchor">
                                                    <img style="height: 50px; width: 50px; object-fit: cover;"
                                                        id="background_image_preview"
                                                        src="{{ Storage::url($product->background_image ?? '') }}"
                                                        alt="Product Background Image" class="img-fluid">
                                                </a>
                                            @else
                                                {{-- // dummy placehoder image box --}}
                                                <div
                                                    style="height: 50px; width: 50px; background: #f0f0f0; border: 1px dashed #ccc;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- short_description --}}
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label for="short_description"> Product Short Description</label>
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
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label for="feature_product"> Feature Product</label>
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

                                    {{-- is_new_product --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label for="is_new_product"> New Product</label>
                                            <select name="is_new_product" id="is_new_product" class="form-control">
                                                <option value="">Select New Product</option>
                                                <option value="1"
                                                    {{ $product->is_new_product == 1 ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="0"
                                                    {{ $product->is_new_product == 0 ? 'selected' : '' }}>No
                                                </option>
                                            </select>
                                            @if ($errors->has('is_new_product'))
                                                <span class="error">{{ $errors->first('is_new_product') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <!-- <div class="col-md-2 mb-2">
                                                            </div> -->

                                    {{-- is_free --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label for="is_free" class="d-block"> Free Product</label>
                                            <div class="form-check form-switch">
                                                <label class="form-check-label" for="is_free">Mark as Free (Price becomes
                                                    0)</label>
                                                <input class="form-check-input mt-3"
                                                    style="width: 60px; height: 30px; margin-bottom:10px;" type="checkbox"
                                                    role="switch" id="is_free" name="is_free" value="1"
                                                    {{ old('is_free', $product->is_free) ? 'checked' : '' }}>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- status --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label for="status"> Status</label>
                                            <select name="status" id="status" class="form-control">

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
                                        <label class="col-form-label">Image Gallery (Drag and drop at least 1
                                            image)</label><br>

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

                                </div>

                                @if ($product->withOutMainImage && $product->withOutMainImage->count())
                                    <div class="row mb-6" id="existing-gallery-wrapper">
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
                                            {{-- <h3>Product Type : {{ $product->product_type }}</h3> --}}
                                        </div>
                                    </div>

                                    <div class="col-md-12" hidden>
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
                                    </div>




                                </div>


                                @if ($product->product_type == 'variable')
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
                                                                    @if (in_array($size->id, $productSizes->pluck('size_id')->toArray())) selected @endif>
                                                                    {{ $size->size }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif



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
                                                        <span class="text-danger"
                                                            id="other_charges.{{ $index }}.charge_name_error"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        <input step="any" type="number"
                                                            name="other_charges[{{ $index }}][charge_amount]"
                                                            class="form-control" placeholder="Charge Amount"
                                                            value="{{ $charge->charge_amount }}">
                                                        <span class="text-danger"
                                                            id="other_charges.{{ $index }}.charge_amount_error"></span>
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
                                                    <span class="text-danger"
                                                        id="other_charges.0.charge_name_error"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="box_label">
                                                    <input step="any" type="number"
                                                        name="other_charges[0][charge_amount]" class="form-control"
                                                        placeholder="Charge Amount">
                                                    <span class="text-danger"
                                                        id="other_charges.0.charge_amount_error"></span>
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
        <script>
            window.existingGalleryImageCount = {{ optional($product->withOutMainImage)->count() ?? 0 }};
        </script>
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
        <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
        </script>
        <script>
            $(document).ready(function() {
                //create a function that gets a string, converts to lowercase and then replace emptyspace with "-"
                function toSlug(str) {
                    str = str.toLowerCase().replace(/\W/g, '-').trim().split(" ");
                    if (str[str.length - 1] == " ") {
                        str[str.length - 1] = "";
                    }
                    str = str.join("-");

                    return str;
                }

                function clearSlug(slug) {
                    slug = slug.split("-");
                    if (slug[slug.length - 1] === " ") {
                        slug[slug.length - 1] = "";
                    }
                    return slug.join("-")
                }
                $('#slug').keyup(function() {
                    var title = $('#slug').val();
                    console.log(title);

                    $('#slug').val(clearSlug(toSlug(title)));
                });

            });
        </script>
        <script>
            $(document).ready(function() {
                $('.remove-image').click(function() {
                    var id = $(this).data('id');
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax({
                        url: "{{ route('products.image.delete') }}",
                        type: 'GET',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function() {
                            console.log("it Works");
                            $('#' + id).remove();
                            const $wrapper = $('#existing-gallery-wrapper');
                            const remaining = $wrapper.length ? $wrapper.find('.image-area')
                                .length : 0;
                            if (!remaining && $wrapper.length) {
                                $wrapper.remove();
                            }
                            window.existingGalleryImageCount = remaining;
                        }
                    });
                });
            });
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
                                <span id="other_charges_${otherChargeIndex}_charge_name_error" class="text-danger"></span>
                                </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="box_label">
                                <input step="any" type="number" name="other_charges[${otherChargeIndex}][charge_amount]" class="form-control" placeholder="Charge Amount">
                                <span id="other_charges_${otherChargeIndex}_charge_amount_error" class="text-danger"></span>
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
                const isFreeCheckbox = document.getElementById('is_free');
                const priceInput = document.getElementById('price');

                function togglePriceFields() {
                    if (!priceInput) return;
                    const isFree = isFreeCheckbox && isFreeCheckbox.checked;
                    if (isFree) {
                        priceInput.disabled = true;
                        priceInput.value = '0';
                        document.querySelectorAll('[name^="warehouse_products"][name$="[price]"]').forEach(inp => {
                            inp.disabled = true;
                            inp.value = inp.value || 0
                        });
                    } else {
                        priceInput.disabled = false;
                        document.querySelectorAll('[name^="warehouse_products"][name$="[price]"]').forEach(inp => {
                            inp.disabled = false;
                        });
                    }
                }
                if (isFreeCheckbox) {
                    isFreeCheckbox.addEventListener('change', togglePriceFields);
                    togglePriceFields();
                }

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

        <script>
            $(document).ready(function() {

                $('#productEditForm').on('submit', function(e) {
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
    @endpush
