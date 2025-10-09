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
    </style>
@endpush
@section('content')
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
                                    <div class="col-md-4 mb-2">
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
                                    <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label for="image"> Product Featured Image*</label>
                                            <input type="file" name="image" id="image" class="form-control"
                                                value="{{ old('image') }}" accept="image/*">
                                            <span class="text-sm ms-2 text-muted">(width: 300px, height: 400px, max
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
                                                <img style="height: 80px; width: 80px; object-fit: cover;"
                                                    id="image_preview"
                                                    src="{{ Storage::url($product->image?->image ?? '') }}"
                                                    alt="Product Image" class="img-fluid">
                                            </a>
                                        </div>
                                    </div>

                                    {{-- background_image --}}
                                    <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label for="background_image"> Product Banner Image</label>
                                            <input type="file" name="background_image" id="background_image"
                                                class="form-control" value="{{ old('background_image') }}"
                                                accept="image/*">
                                            <span class="text-sm ms-2 text-muted">(width: 1920px, height: 520px, max
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
                                    <div class="col-md-2 mb-2">
                                        <div class="box_label" id="background_preview_wrapper">
                                            @if ($product->background_image)
                                                <a href="{{ Storage::url($product->background_image ?? '') }}"
                                                    target="_blank" id="background_image_preview_anchor">
                                                    <img style="height: 80px; width: 80px; object-fit: cover;"
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
                                                <input class="form-check-input mt-3" style="width: 60px; height: 30px; margin-bottom:10px;"
                                                    type="checkbox" role="switch" id="is_free" name="is_free"
                                                    value="1"
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
                                        <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image(Drag and
                                            drop atleast 1 images)*</label><br>
                                        <span class="text-sm ms-2 text-muted">(width: 300px, height: 400px, max 2MB)</span>
                                        <input type="file" class="form-control dropzone" id="image-upload"
                                            name="images[]" multiple accept="image/*">
                                        @if ($errors->has('images.*'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('images.*') }}</div>
                                        @endif
                                        @if ($errors->has('images'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('images') }}</div>
                                        @endif

                                        <!-- Gallery previews for newly selected files -->
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
        <script>
            window.existingGalleryImageCount = {{ optional($product->withOutMainImage)->count() ?? 0 }};
        </script>
        <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
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
            // Real-time image previews for featured, banner, and gallery inputs (edit form)
            (function() {
                function readSingleImage(input, previewSelector, anchorSelector, wrapperForCreateIfMissing) {
                    if (!input.files || !input.files[0]) {
                        return;
                    }
                    const file = input.files[0];
                    if (!file.type.startsWith('image/')) {
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const src = e.target.result;
                        const $preview = $(previewSelector);
                        if ($preview.length) {
                            $preview.attr('src', src);
                            if ($(anchorSelector).length) {
                                $(anchorSelector).attr('href', src);
                            } else {
                                // if anchor not present but img inside anchor originally, try closest anchor
                                $preview.closest('a').attr('href', src);
                            }
                        } else {
                            // create preview img if not present (use small size consistent with existing markup)
                            const $img = $('<img/>', {
                                id: previewSelector.replace('#', ''),
                                src: src,
                                style: 'height:80px;width:80px;object-fit:cover;',
                                class: 'img-fluid'
                            });
                            if (wrapperForCreateIfMissing && $(wrapperForCreateIfMissing).length) {
                                $(wrapperForCreateIfMissing).append($img);
                            } else {
                                $(input).closest('.box_label').append($img);
                            }
                        }
                    };
                    reader.readAsDataURL(file);
                }

                function readMultipleImages(input, containerSelector) {
                    const $container = $(containerSelector);
                    $container.empty();
                    const files = input.files || [];
                    if (!files.length) {
                        $container.hide();
                        return;
                    }
                    Array.from(files).forEach(function(file) {
                        if (!file.type.startsWith('image/')) return;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = $('<img/>', {
                                src: e.target.result,
                                alt: file.name
                            });
                            $container.append(img);
                        };
                        reader.readAsDataURL(file);
                    });
                    $container.show();
                }

                $(function() {
                    $('#image').attr('accept', 'image/*').on('change', function() {
                        readSingleImage(this, '#image_preview', '#image_preview_anchor', null);
                    });

                    $('#background_image').attr('accept', 'image/*').on('change', function() {
                        // ensure wrapper exists for background preview when previously missing
                        if (!$('#background_image_preview').length && $('#background_preview_wrapper')
                            .length) {
                            // create anchor+img structure to match existing preview pattern
                            const $anchor = $('<a/>', {
                                id: 'background_image_preview_anchor',
                                target: '_blank'
                            });
                            const $img = $('<img/>', {
                                id: 'background_image_preview',
                                style: 'height:80px;width:80px;object-fit:cover;',
                                class: 'img-fluid',
                                alt: 'Product Background Image'
                            });
                            $anchor.append($img);
                            $('#background_preview_wrapper').empty().append($anchor);
                        }
                        readSingleImage(this, '#background_image_preview',
                            '#background_image_preview_anchor', '#background_preview_wrapper');
                    });

                    $('#image-upload').attr('accept', 'image/*').on('change', function() {
                        readMultipleImages(this, '#gallery-previews');
                    });
                });
            })();
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

        <!-- Client-side validation for productEditForm -->
        <script>
            (function() {
                // number of existing gallery images (rendered on server)
                var existingImagesCount = {{ $product->withOutMainImage->count() ?? 0 }};

                function addClientError($el, message) {
                    $el.addClass('is-invalid');
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

                $('#productEditForm').on('submit', async function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    clearClientErrors($form);

                    var errors = [];
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

                    // if (!val('#short_description')) {
                    //     addClientError($('#short_description'), 'Short description is required.');
                    //     errors.push('#short_description');
                    // }

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
                    var galleryHasFiles = galleryInput && galleryInput.files && galleryInput.files.length > 0;
                    var existingImagesCount = (typeof window.existingGalleryImageCount !== 'undefined') ?
                        window.existingGalleryImageCount :
                        $('#existing-gallery-wrapper .image-area').length;
                    if (!galleryHasFiles && (!existingImagesCount || existingImagesCount === 0)) {
                        addClientError($('#image-upload'),
                            'Please have at least one gallery image (existing or new).');
                        errors.push('#image-upload');
                    }

                    // else if (galleryHasFiles) {
                    //     for (var i = 0; i < galleryInput.files.length; i++) {
                    //         var okGallery = await validateImageFile(galleryInput.files[i], 2 * 1024 * 1024, 300,
                    //             400, $('#image-upload'), 'Gallery image');
                    //         if (!okGallery) {
                    //             errors.push('#image-upload');
                    //             break;
                    //         }
                    //     }
                    // }

                    // Product type specific checks (only when fields exist)
                    var productType = ($('input[name="product_type"]:checked').val() || '').toLowerCase() ||
                        '{{ $product->product_type }}';
                    if (productType === 'simple') {
                        if ($('#sku').length && !val('#sku')) {
                            addClientError($('#sku'), 'SKU is required for simple products.');
                            errors.push('#sku');
                        }
                        var skuValue = val('#sku');
                        if (skuValue && !/^[a-zA-Z0-9]/.test(skuValue)) {
                            addClientError($('#sku'), 'SKU must start with a letter or number.');
                            errors.push('#sku');
                        }
                        if ($('#price').length) {
                            var isFree = $('#is_free').is(':checked');
                            var priceVal = val('#price');
                            if (!isFree && (priceVal === '' || isNaN(Number(priceVal)) || Number(
                                        parsepriceVal) <
                                    0)) {
                                addClientError($('#price'),
                                    'Valid price is required (or mark product as Free).');
                                errors.push('#price');
                            }
                        }
                        // if set sale_price then should not negetive and not greater than price
                        if ($('#sale_price').length && val('#sale_price')) {
                            var salePriceVal = val('#sale_price');
                            var mainPriceVal = val('#price');
                            if (isNaN(Number(salePriceVal)) || Number(salePriceVal) < 0) {
                                addClientError($('#sale_price'), 'Sale price cannot be negative.');
                                errors.push('#sale_price');
                            } else if (mainPriceVal && !isNaN(Number(mainPriceVal)) && Number(salePriceVal) >
                                Number(
                                    mainPriceVal)) {
                                addClientError($('#sale_price'),
                                    'Sale price cannot be greater than the main price.');
                                errors.push('#sale_price');
                            }
                        }
                        if ($('#quantity').length && (val('#quantity') === '' || isNaN(Number(val(
                                    '#quantity'))) ||
                                Number(val('#quantity')) < 0)) {
                            addClientError($('#quantity'), 'Valid stock quantity is required.');
                            errors.push('#quantity');
                        }
                    } else if (productType === 'variable') {
                        if ($('#global-size-select').length) {
                            var sizesVal = $('#global-size-select').val() || [];
                            if (!Array.isArray(sizesVal)) sizesVal = [sizesVal];
                            if (sizesVal.length === 0 || (sizesVal.length === 1 && sizesVal[0] === '')) {
                                addClientError($('#global-size-select'),
                                    'Please select at least one size for variable products.');
                                errors.push('#global-size-select');
                            }
                        }
                    }

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
                    $('#productEditForm').off('submit');
                    $form.submit();
                });
            })();
        </script>
    @endpush
