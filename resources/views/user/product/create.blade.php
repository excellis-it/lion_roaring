@extends('user.layouts.master')
@section('title')
    Product - {{ env('APP_NAME') }}
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
                                                <input class="form-check-input mt-3" style="width: 60px; height: 30px;"
                                                    type="checkbox" role="switch" id="is_free" name="is_free"
                                                    value="1" {{ old('is_free') ? 'checked' : '' }}>

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
                                        <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image Gallery
                                            (Drag and
                                            drop
                                            atleast 1
                                            images)*</label>
                                        <br><span class="text-sm ms-2 text-muted">(width: 300px, height: 400px, max
                                            2MB)</span>
                                        <input type="file" class="form-control dropzone" id="image-upload"
                                            name="images[]" multiple accept="image/*">
                                        <span class="text-danger" id="images_error"></span>


                                        <!-- Gallery previews -->
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

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label for="sku"> Product SKU*</label>
                                                    <input type="text" name="sku" id="sku"
                                                        class="form-control" value="{{ old('sku') }}">
                                                    @if ($errors->has('sku'))
                                                        <span class="error">{{ $errors->first('sku') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <div class="box_label">
                                                    <label for="price"> Price*</label>
                                                    <input type="number" step="any" name="price" id="price"
                                                        class="form-control" value="{{ old('price') }}">
                                                    @if ($errors->has('price'))
                                                        <span class="error">{{ $errors->first('price') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
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
                            toastr.success('Product created successfully!');

                            // Redirect or reset form if needed
                            form[0].reset();
                            window.location.href = "{{ route('products.index') }}"; // optional
                        },
                        error: function(xhr) {
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

                $('#name').on('keyup change', function() {
                    let name = $(this).val();

                    if (name.length > 0) {
                        $.ajax({
                            url: '{{ route('products.slug.check') }}', // route to check slug
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: name
                            },
                            success: function(response) {
                                if (response.slug) {
                                    $('#slug').val(response.slug);
                                    $('#slug-feedback').text(''); // clear error
                                } else if (response.error) {
                                    $('#slug-feedback').text(response.error);
                                }
                            }
                        });
                    } else {
                        $('#slug').val('');
                    }
                });


                function togglePriceFields() {
                    const isFree = $('#is_free').is(':checked');
                    if (isFree) {
                        $('#price').prop('readonly', true).val('0');
                        $('#sale_price').prop('readonly', true).val('');
                    } else {
                        $('#price').prop('readonly', false);
                        $('#sale_price').prop('readonly', false);
                    }
                }
                $('#is_free').on('change', togglePriceFields);
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
            // productCreateForm validate before submit
            (function() {
                function addClientError($el, message) {
                    $el.addClass('is-invalid');
                    // if there's already a client error next to element update it, otherwise append
                    if ($el.next('.client-error').length) {
                        $el.next('.client-error').text(message);
                    } else {
                        $el.after('<span class="error client-error" style="color:red;display:block;margin-top:4px;">' +
                            message + '</span>');
                    }
                }

                function clearClientErrors($form) {
                    $form.find('.client-error').remove();
                    $form.find('.is-invalid').removeClass('is-invalid');
                }

                async function validateImageFile(file, maxBytes, reqW, reqH, $input, label) {
                    return new Promise(function(resolve) {
                        if (!file) {
                            resolve(false);
                            return;
                        }
                        if (maxBytes && file.size > maxBytes) {
                            addClientError($input, label + ' must be under ' + (maxBytes / 1024 / 1024).toFixed(
                                2) + 'MB.');
                            resolve(false);
                            return;
                        }
                        if (!reqW && !reqH) {
                            resolve(true);
                            return;
                        }
                        var url = URL.createObjectURL(file);
                        var img = new Image();
                        var timedOut = false;
                        var timer = setTimeout(function() {
                            timedOut = true;
                            URL.revokeObjectURL(url);
                            addClientError($input, label + ' validation timed out.');
                            resolve(false);
                        }, 5000);

                        function finish(ok, msg) {
                            if (timedOut) return;
                            clearTimeout(timer);
                            URL.revokeObjectURL(url);
                            if (!ok && msg) addClientError($input, msg);
                            resolve(!!ok);
                        }

                        img.onload = function() {
                            var w = this.naturalWidth || this.width;
                            var h = this.naturalHeight || this.height;
                            if ((reqW && w !== reqW) || (reqH && h !== reqH)) {
                                finish(false, label + ' must be ' + reqW + 'x' + reqH + '. Uploaded ' + w +
                                    'x' + h + '.');
                            } else {
                                finish(true);
                            }
                        };

                        img.onerror = function() {
                            finish(false, label + ' is not a valid image.');
                        };

                        img.src = url;
                    });
                }

                $('#productCreateForm').on('submit', async function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    clearClientErrors($form);

                    var errors = [];

                    // Helpers
                    var val = function(selector) {
                        return $.trim($(selector).val() || '');
                    };

                    // Basic required fields
                    if (!val('#name')) {
                        addClientError($('#name'), 'Product name is required.');
                        errors.push('#name');
                    }

                    if (!val('#category_id')) {
                        addClientError($('#category_id'), 'Category is required.');
                        errors.push('#category_id');
                    }

                    if (!val('#slug')) {
                        addClientError($('#slug'), 'Product slug is required.');
                        errors.push('#slug');
                    }

                    if (!$('#image').val()) {
                        addClientError($('#image'), 'Image is required.');
                        errors.push('#image');
                    }

                    if (!val('#description')) {
                        addClientError($('#description'), 'Description is required.');
                        errors.push('#description');
                    }

                    if (!val('#specification')) {
                        addClientError($('#specification'), 'Specification is required.');
                        errors.push('#specification');
                    }

                    var featuredInput = $('#image')[0];
                    // if (featuredInput && featuredInput.files && featuredInput.files.length) {
                    //     var okFeatured = await validateImageFile(featuredInput.files[0], 2 * 1024 * 1024, 300,
                    //         400, $('#image'), 'Featured image');
                    //     if (!okFeatured) errors.push('#image');
                    // }

                    var backgroundInput = $('#background_image')[0];
                    // if (backgroundInput && backgroundInput.files && backgroundInput.files.length) {
                    //     var okBackground = await validateImageFile(backgroundInput.files[0], 2 * 1024 * 1024,
                    //         1920, 520, $('#background_image'), 'Banner image');
                    //     if (!okBackground) errors.push('#background_image');
                    // }

                    var galleryInput = $('#image-upload')[0];
                    if (!galleryInput || (galleryInput.files && galleryInput.files.length === 0)) {
                        addClientError($('#image-upload'), 'Please upload at least one gallery image.');
                        errors.push('#image-upload');
                    }

                    // else {
                    //     for (var i = 0; i < galleryInput.files.length; i++) {
                    //         var okGallery = await validateImageFile(galleryInput.files[i], 2 * 1024 * 1024, 300,
                    //             400, $('#image-upload'), 'Gallery image');
                    //         if (!okGallery) {
                    //             errors.push('#image-upload');
                    //             break;
                    //         }
                    //     }
                    // }

                    // Product type specific checks
                    var productType = $('input[name="product_type"]:checked').val() || 'simple';
                    if (productType === 'simple') {
                        if (!val('#sku')) {
                            addClientError($('#sku'), 'SKU is required for simple products.');
                            errors.push('#sku');
                        }

                        // Product SKU field should not accept special characters as first character.
                        var skuValue = val('#sku');
                        if (skuValue && !/^[a-zA-Z0-9]/.test(skuValue)) {
                            addClientError($('#sku'), 'SKU must start with a letter or number.');
                            errors.push('#sku');
                        }

                        // price is optional if marked free; otherwise required
                        var isFree = $('#is_free').is(':checked');
                        if (!isFree && (val('#price') === '' || isNaN(Number(val('#price'))) || Number(val(
                                '#price')) < 0)) {
                            addClientError($('#price'), 'Valid price is required (or mark product as Free).');
                            errors.push('#price');
                        }

                        // if set sale_price then should not negetive and not greater than price
                        var salePriceVal = val('#sale_price');
                        var priceVal = val('#price');
                        if (salePriceVal) {
                            if (isNaN(Number(salePriceVal)) || Number(salePriceVal) < 0) {
                                addClientError($('#sale_price'), 'Sale Price cannot be negative.');
                                errors.push('#sale_price');
                            } else if (priceVal && !isNaN(Number(priceVal)) && Number(salePriceVal) > Number(
                                    priceVal)) {
                                addClientError($('#sale_price'), 'Sale Price cannot be greater than Price.');
                                errors.push('#sale_price');
                            }
                        }


                        if (val('#quantity') === '' || isNaN(Number(val('#quantity'))) || parseInt(val(
                                '#quantity')) < 0) {
                            addClientError($('#quantity'), 'Valid stock quantity is required.');
                            errors.push('#quantity');
                        }
                    } else if (productType === 'variable') {
                        // sizes select may be enhanced by Choices.js; check underlying select value
                        var sizesVal = $('#global-size-select').val() || [];
                        if (!Array.isArray(sizesVal)) {
                            sizesVal = [sizesVal];
                        }
                        if (sizesVal.length === 0 || (sizesVal.length === 1 && sizesVal[0] === '')) {
                            addClientError($('#global-size-select'),
                                'Please select at least one size for variable products.');
                            errors.push('#global-size-select');
                        }
                    }

                    // If errors found, prevent submit and focus first invalid field
                    if (errors.length) {
                        var firstSel = errors[0];
                        var $first = $(firstSel);
                        if ($first.length) {
                            $('html, body').animate({
                                scrollTop: $first.offset().top - 100
                            }, 300, function() {
                                $first.focus();
                            });
                        }
                        return;
                    }

                    $('#productCreateForm').off('submit');
                    $form.submit();
                });
            })();
        </script> --}}

        <script>
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
        </script>
    @endpush
