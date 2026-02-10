@extends('user.layouts.master')
@section('title')
    Product Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
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

        @media (max-width: 575.98px) {
            .bg_white_border.products-page-form .form-group {
                width: 100%;
            }

            .print_btn {
                padding: 5px 16px;
            }

            .form-check-input {
                margin-left: 0em;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border products-page-form">
            @if ($product->product_type != 'simple')
                <div class="row card card-body">
                    <div class="col-lg-12">
                        <form id="generate-variations-form" action="{{ route('products.generate.variations') }}" method="POST"
                            enctype="multipart/form-data">
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
                                                <select name="sizes[]" class="form-control" id="generate-size-select">
                                                    <option value="">-- Select Size --</option>
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

                                <div class="col-md-4 mb-4" hidden>
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

            <!-- <hr> -->

            <!--  Row 1 -->
            <div class="row card card-body mt-4">
                <div class="col-lg-12">
                    <form action="{{ route('products.variations.update') }}" method="POST" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">


                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="heading_box mb-3">
                                    <h3>{{ $product->product_type != 'simple' ? 'Variation' : 'Simple' }} Product </h3>
                                    <h3>Product Name : {{ $product->name }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="variation-file" id="variation-products-container">
                            @if ($product_variations->count() > 0)
                                @php
                                    $groupedVariations = $product_variations->groupBy('color_id');
                                    $index = 0;
                                @endphp

                                @foreach ($groupedVariations as $colorId => $colorGroup)
                                    @php
                                        $first = $colorGroup->first();
                                        // $canDelete = $colorGroup->count() > 1;
                                    @endphp
                                    <div class="color-variation-group mb-4 p-3" data-index="{{ $index }}"
                                        data-color-id="{{ $colorId }}">
                                        @if ($product->product_type != 'simple')
                                            <h3>Color: {{ $first->colorDetail->color_name ?? '' }}</h3>



                                            <label class="small fw-semibold mb-1">Images
                                                ({{ $first->colorDetail->color_name ?? '' }})
                                            </label>
                                            <!-- hidden native input -->
                                            <input type="file" id="image-upload-{{ $index }}"
                                                name="variation_products[{{ $index }}][images][]"
                                                class="form-control image-upload" multiple accept="image/*"
                                                style="display:none;">
                                            <small class="text-muted d-block mt-1">Upload images once per
                                                color. (width: 300px, height: 400px, max 2MB)</small>
                                            <!-- Dropzone visual area -->
                                            <div id="dropzone-{{ $index }}" class="dropzone dz-clickable mb-3"
                                                style="border:2px dashed #4caf50; padding:40px; text-align:center; cursor:pointer;">
                                            </div>
                                            <span class="text-danger images-error"
                                                id="images_error_{{ $index }}"></span>
                                            <!-- previews container -->
                                            <div id="gallery-previews-{{ $index }}" class="gallery-previews mt-2"
                                                style="display:none; grid-template-columns: repeat(auto-fill, 80px); gap:10px;">
                                            </div>
                                        @endif

                                        @if ($product->product_type != 'simple')
                                            <div class="d-flex flex-wrap mb-3">
                                                @if ($first->images && $first->images->count())
                                                    @foreach ($first->images as $image)
                                                        <div class="image-area m-1 position-relative"
                                                            id="{{ $image->id }}"
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
                                        @endif

                                        @if ($product->product_type != 'simple')
                                            <!-- Group toolbar: bulk delete and bulk apply -->
                                            <div class="d-flex flex-wrap align-items-end gap-2 mb-3 justify-content-end">

                                                <div class="bulk-apply-form d-flex flex-wrap align-items-end gap-2">

                                                    <div class="form-group">
                                                        <label class="small fw-semibold mb-1">Price</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm bulk-price"
                                                            placeholder="0.00"
                                                            {{ $product->is_free == 1 ? 'readonly' : '' }}>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="small fw-semibold mb-1">Sale Price</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm bulk-sale-price"
                                                            placeholder="0.00"
                                                            {{ $product->is_free == 1 ? 'readonly' : '' }}>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="small fw-semibold mb-1">Global Stock</label>
                                                        <input type="number" min="0"
                                                            class="form-control form-control-sm bulk-stock"
                                                            placeholder="0">
                                                    </div>
                                                    <div class="d-flex gap-2 ">
                                                        <button type="button" class="print_btn btn-group-apply"
                                                            data-scope="checked">
                                                            Apply to Checked
                                                        </button>
                                                        <button type="button" class="print_btn btn-group-apply"
                                                            data-scope="group">
                                                            Apply to All in Color
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="ms-4">

                                                </div>
                                                <button type="button" class="print_btn ms-5 btn-group-delete-selected">
                                                    <i class="fa fa-trash"></i> Delete Selected
                                                </button>
                                                <button type="button" class="print_btn btn-group-delete-all">
                                                    <i class="fa fa-trash-alt"></i> Delete All (Color)
                                                </button>
                                            </div>
                                        @endif

                                        @foreach ($colorGroup as $variation)
                                            <div class="variation-product-entry py-2" data-id="{{ $variation->id }}">
                                                <input type="hidden" name="variation_products[{{ $index }}][id]"
                                                    value="{{ $variation->id }}">
                                                <input type="hidden"
                                                    name="variation_products[{{ $index }}][color_id]"
                                                    value="{{ $variation->color_id }}">
                                                <div class="row align-items-end g-2">
                                                    @if ($product->product_type != 'simple')
                                                        <div class="col-xxl-1 col-lg-1 col-md-1">
                                                            <label class="small fw-semibold"></label>
                                                            <div>
                                                                <input type="checkbox"
                                                                    class="form-check-input variation-select">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-xxl-4 col-lg-4 col-md-6">
                                                        <label class="small fw-semibold">SKU <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            name="variation_products[{{ $index }}][sku]"
                                                            class="form-control" value="{{ $variation->sku }}">
                                                    </div>
                                                    <div class="col-xxl-1 col-lg-1 col-md-6">
                                                        <label
                                                            class="small fw-semibold">{{ $product->is_market_priced == 1 ? 'Market Price' : 'Price' }}<span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" step="0.01"
                                                            name="variation_products[{{ $index }}][price]"
                                                            class="form-control" value="{{ $variation->price }}"
                                                            {{ $product->is_free == 1 || $product->is_market_priced == 1 ? 'readonly' : '' }}>
                                                    </div>
                                                    <div class="col-xxl-2 col-lg-2 col-md-6"
                                                        {{ $product->is_market_priced == 1 ? 'hidden' : '' }}>
                                                        <label class="small fw-semibold">Sale Price (If Any)</label>
                                                        <input type="number" step="0.01"
                                                            name="variation_products[{{ $index }}][sale_price]"
                                                            class="form-control" value="{{ $variation->sale_price }}"
                                                            {{ $product->is_free == 1 ? 'readonly' : '' }}>
                                                    </div>
                                                    <div class="col-xxl-1 col-lg-1 col-md-6"
                                                        {{ $product->product_type == 'simple' ? 'hidden' : '' }}>
                                                        <label class="small fw-semibold">Color</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $variation->colorDetail->color_name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-xxl-1 col-lg-1 col-md-6"
                                                        {{ $product->product_type == 'simple' ? 'hidden' : '' }}>
                                                        <label class="small fw-semibold">Size</label>
                                                        <input type="hidden"
                                                            name="variation_products[{{ $index }}][size_id]"
                                                            value="{{ $variation->size_id }}">
                                                        <input type="text" class="form-control"
                                                            value="{{ $variation->sizeDetail->size ?? '' }}" readonly>
                                                    </div>
                                                    <div class="col-xxl-1 col-lg-1 col-md-6">
                                                        <label class="small fw-semibold">Global Stock Qty <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" min="0"
                                                            name="variation_products[{{ $index }}][stock_quantity]"
                                                            class="form-control"
                                                            value="{{ $variation->available_quantity }}">
                                                    </div>
                                                    @if ($product->product_type != 'simple')
                                                        <div class="col-xxl-1 col-lg-1 col-md-6 text-end">
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
    <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
    <!-- Choices.js -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        document.addEventListener('DOMContentLoaded', function() {
            const MAX_FILES = 8;
            const MAX_FILESIZE_MB = 12;

            const groups = document.querySelectorAll('.color-variation-group');

            groups.forEach(group => {
                const index = group.dataset.index;
                const dropzoneEl = document.getElementById('dropzone-' + index);
                const previewsEl = document.getElementById('gallery-previews-' + index);
                const inputEl = document.getElementById('image-upload-' + index);
                const errorEl = document.getElementById('images_error_' + index);

                var text_button = `
      <i class="fas fa-upload dz-message-icon" style="font-size:48px; color:#4caf50; margin-bottom:8px;"></i>
      <div class="dz-message-title" style="font-weight:bold; font-size:16px; color:#333;">Drag & drop images here</div>
      <div class="dz-message-sub" style="font-size:14px; color:#666;">or click to select</div>
    `;

                if (!dropzoneEl) return;

                const dz = new Dropzone(dropzoneEl, {
                    url: "#",
                    autoProcessQueue: false,
                    uploadMultiple: false,
                    parallelUploads: MAX_FILES,
                    maxFiles: MAX_FILES,
                    maxFilesize: MAX_FILESIZE_MB,
                    acceptedFiles: "image/*", // ONLY allow images
                    previewsContainer: previewsEl,
                    clickable: dropzoneEl,
                    previewTemplate: `
                <div class="dz-preview dz-file-preview">
                    <div class="dz-image"><img data-dz-thumbnail /></div>
                    <div class="dz-details">
                        <div class="dz-filename"><span data-dz-name></span></div>
                        <div class="dz-size" data-dz-size></div>
                    </div>
                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                    <div class="dz-success-mark">✔</div>
                    <a class="dz-remove" href="javascript:undefined;" data-dz-remove>Remove</a>
                </div>
            `,
                    dictDefaultMessage: text_button,
                });

                dz.on("addedfile", file => {
                    // Extra validation for image MIME type
                    if (!file.type.startsWith('image/')) {
                        dz.removeFile(file);
                        if (errorEl) errorEl.textContent = 'Only image files are allowed.';
                        return;
                    }

                    if (dz.files.length > MAX_FILES) {
                        dz.removeFile(file);
                        if (errorEl) errorEl.textContent = 'Maximum ' + MAX_FILES +
                            ' images allowed.';
                        return;
                    }

                    if (errorEl) errorEl.textContent = '';

                    // Fake upload to show progress
                    const totalSteps = 20;
                    let step = 0;

                    const interval = setInterval(() => {
                        step++;
                        const progress = (step / totalSteps) * 100;
                        file.upload = {
                            progress: progress,
                            total: file.size,
                            bytesSent: file.size * (step / totalSteps)
                        };
                        dz.emit('uploadprogress', file, progress, file.upload.bytesSent);

                        if (step >= totalSteps) {
                            clearInterval(interval);
                            file.status = Dropzone.SUCCESS;
                            dz.emit("success", file);
                            dz.emit("complete", file);
                            syncFiles();
                        }
                    }, 50);

                    previewsEl.style.display = dz.files.length ? 'grid' : 'none';
                });

                dz.on('removedfile', () => {
                    syncFiles();
                    previewsEl.style.display = dz.files.length ? 'grid' : 'none';
                });

                function syncFiles() {
                    if (!inputEl) return;
                    const dt = new DataTransfer();
                    dz.files.forEach(f => dt.items.add(f));
                    inputEl.files = dt.files;
                }

                // fallback for native input
                if (inputEl) {
                    inputEl.addEventListener('change', function() {
                        Array.from(inputEl.files).forEach(f => {
                            if (!dz.files.some(existing => existing.name === f.name &&
                                    existing.size === f.size)) {
                                if (f.type.startsWith('image/')) dz.addFile(f);
                            }
                        });
                        syncFiles();
                    });
                }

                // Remove existing images (optional)
                // group.querySelectorAll('.remove-image').forEach(btn => {
                //     btn.addEventListener('click', function() {
                //         const container = this.closest('.image-area');
                //         container.remove();
                //     });
                // });
            });
        });
    </script>


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

                // Show confirmation dialog
                swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel"
                }).then(function(result) {
                    if (result.value) {
                        // Proceed with AJAX delete
                        $.ajax({
                            url: "{{ route('products.variation.image.delete') }}",
                            type: 'POST',
                            data: {
                                "id": id,
                                "_token": token,
                            },
                            success: function() {
                                $('#' + id).remove();
                                swal(
                                    'Deleted!',
                                    'The image has been deleted.',
                                    'success'
                                );
                            },
                            error: function() {
                                swal(
                                    'Error!',
                                    'Something went wrong while deleting.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });


        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Choices.js for global selects
            // const globalSizeSelect = new Choices("#generate-size-select", {
            //     removeItemButton: true,
            //     searchPlaceholderValue: "Type to search...",
            //     closeDropdownOnSelect: 'auto',
            //     placeholderValue: "Select size",
            // });




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

            $('#generate-size-select').on('change', function() {
                $('#generate-variations-form').trigger('submit');
            });
        });
    </script>
    <script>
        (function() {
            const $form = $('form[action="{{ route('products.variations.update') }}"]');

            function addClientError($el, message) {
                $el.addClass('is-invalid');
                if ($el.next('.client-error').length) {
                    $el.next('.client-error').text(message);
                } else {
                    $el.after('<span class="error client-error" style="color:red;display:block;margin-top:4px;">' +
                        message + '</span>');
                }
            }

            function clearClientErrors() {
                $form.find('.client-error').remove();
                $form.find('.is-invalid').removeClass('is-invalid');
            }

            function validateNumber($el, allowZero) {
                const val = $.trim($el.val());
                if (val === '') return false;
                const num = Number(val);
                if (Number.isNaN(num)) return false;
                if (!allowZero && num <= 0) return false;
                if (allowZero && num < 0) return false;
                return true;
            }

            function validateSalePrice($price, $sale) {
                const priceVal = Number($.trim($price.val() || '0'));
                const saleVal = Number($.trim($sale.val() || '0'));
                if ($.trim($sale.val()) === '') return true;
                if (Number.isNaN(saleVal) || saleVal < 0) return false;
                return saleVal <= priceVal;
            }

            function validateImageFile(file, $input, label) {
                return new Promise(function(resolve) {
                    if (file.size > 2 * 1024 * 1024) {
                        addClientError($input, label + ' must be under 2MB.');
                        return resolve(false);
                    }
                    const url = URL.createObjectURL(file);
                    const img = new Image();
                    let timedOut = false;
                    const timer = setTimeout(function() {
                        timedOut = true;
                        URL.revokeObjectURL(url);
                        addClientError($input, label + ' validation timed out.');
                        resolve(false);
                    }, 5000);

                    img.onload = function() {
                        if (timedOut) return;
                        clearTimeout(timer);
                        if (img.naturalWidth !== 300 || img.naturalHeight !== 400) {
                            addClientError($input, label + ' must be 300x400. Uploaded ' + img
                                .naturalWidth + 'x' + img.naturalHeight + '.');
                            URL.revokeObjectURL(url);
                            return resolve(false);
                        }
                        URL.revokeObjectURL(url);
                        resolve(true);
                    };
                    img.onerror = function() {
                        if (timedOut) return;
                        clearTimeout(timer);
                        addClientError($input, label + ' is not a valid image.');
                        URL.revokeObjectURL(url);
                        resolve(false);
                    };
                    img.src = url;
                });
            }

            $form.on('submit', async function(e) {
                e.preventDefault();
                clearClientErrors();
                const errors = [];

                const priceInputs = $form.find('input[name$="[price]"]');
                const saleInputs = $form.find('input[name$="[sale_price]"]');
                const stockInputs = $form.find('input[name$="[stock_quantity]"]');
                const skuInputs = $form.find('input[name$="[sku]"]');
                const imageInputs = $form.find(
                    'input[type="file"][name^="variation_products"][name$="[images][]"]');

                skuInputs.each(function() {
                    if (!$.trim($(this).val())) {
                        addClientError($(this), 'SKU is required.');
                        errors.push(this);
                    }
                });

                priceInputs.each(function(idx) {
                    const $price = $(this);
                    if (!validateNumber($price, true)) {
                        addClientError($price, 'Price must be a positive number.');
                        errors.push(this);
                    }
                    const $sale = $(saleInputs[idx]);
                    if ($sale.length && $.trim($sale.val()) !== '' && !validateSalePrice($price,
                            $sale)) {
                        addClientError($sale,
                            'Sale price must be non-negative and not exceed price.');
                        errors.push($sale[0]);
                    }
                });

                stockInputs.each(function() {
                    const $stock = $(this);
                    if (!validateNumber($stock, true)) {
                        addClientError($stock, 'Stock quantity must be zero or greater.');
                        errors.push(this);
                    }
                });

                // const imagePromises = [];
                // imageInputs.each(function() {
                //     const input = this;
                //     if (input.files && input.files.length) {
                //         for (let i = 0; i < input.files.length; i++) {
                //             imagePromises.push(
                //                 validateImageFile(input.files[i], $(input), 'Variation image')
                //                 .then(ok => {
                //                     if (!ok) errors.push(input);
                //                 })
                //             );
                //         }
                //     }
                // });
                // if (imagePromises.length) await Promise.all(imagePromises);

                $('.color-variation-group').each(function() {
                    const $group = $(this);
                    const $fileInput = $group.find(
                            'input[type="file"][name^="variation_products"][name$="[images][]"]')
                        .first();
                    if (!$fileInput.length) {
                        return;
                    }
                    const hasExistingImages = $group.find('.image-area img').length > 0;
                    const hasNewImages = $fileInput[0].files && $fileInput[0].files.length > 0;
                    if (!hasExistingImages && !hasNewImages) {
                        const label = $.trim($group.find('h3').first().text()) || 'this variation';
                        addClientError($fileInput, 'Add at least one image for ' + label + '.');
                        errors.push($fileInput[0]);
                    }
                });

                if (errors.length) {
                    const $first = $(errors[0]);
                    $('html, body').animate({
                        scrollTop: $first.offset().top - 100
                    }, 300, function() {
                        $first.focus();
                    });
                    return;
                }

                $form.off('submit');
                $form.trigger('submit');
            });
        })();
    </script>

    <script>
        // Bulk actions: delete selected, delete all in color, bulk apply values
        (function() {
            function getSelectedVariationIds($group) {
                var ids = [];
                $group.find('.variation-select:checked').each(function() {
                    var id = $(this).closest('.variation-product-entry').data('id');
                    if (id) ids.push(id);
                });
                return ids;
            }

            function gatherBulkFields($group) {
                const data = {};
                const sku = $.trim($group.find('.bulk-sku').val());
                const price = $.trim($group.find('.bulk-price').val());
                const sale = $.trim($group.find('.bulk-sale-price').val());
                const stock = $.trim($group.find('.bulk-stock').val());

                if (sku !== '') data.sku = sku;
                if (price !== '') data.price = price;
                if (sale !== '') data.sale_price = sale;
                if (stock !== '') data.stock_quantity = stock;

                return data;
            }

            $(document).on('click', '.btn-group-delete-selected', function() {
                const $group = $(this).closest('.color-variation-group');
                const ids = getSelectedVariationIds($group);
                if (!ids.length) {
                    toastr.error('Select at least one variation.');
                    return;
                }
                swal({
                    title: "Are you sure?",
                    text: "Selected variations will be deleted.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete",
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('products.variations.bulk-delete') }}",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                product_id: "{{ $product->id }}",
                                variation_ids: ids
                            },
                            success: function() {
                                // Remove from DOM
                                ids.forEach(function(id) {
                                    $group.find(
                                        '.variation-product-entry[data-id="' +
                                        id + '"]').remove();
                                });
                                if (!$group.find('.variation-product-entry').length) $group
                                    .remove();
                                toastr.success('Selected variations deleted.');
                            },
                            error: function() {
                                toastr.error('Failed to delete selected variations.');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-group-delete-all', function() {
                const $group = $(this).closest('.color-variation-group');
                const colorId = $group.data('color-id');
                if (!colorId) {
                    toastr.error('Invalid color group.');
                    return;
                }
                swal({
                    title: "Delete all variations for this color?",
                    text: "This will remove all variations in this color group.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Delete All",
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('products.variations.bulk-delete') }}",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                product_id: "{{ $product->id }}",
                                color_id: colorId
                            },
                            success: function() {
                                $group.remove();
                                toastr.success(
                                    'All variations for this color were deleted.');
                            },
                            error: function() {
                                toastr.error('Failed to delete color variations.');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-group-apply', function() {
                const $btn = $(this);
                const scope = $btn.data('scope'); // 'checked' or 'group'
                const $group = $btn.closest('.color-variation-group');
                const colorId = $group.data('color-id');
                const fields = gatherBulkFields($group);

                if ($.isEmptyObject(fields)) {
                    toastr.error('Enter at least one field to apply.');
                    return;
                }

                if (fields.price !== undefined && fields.sale_price !== undefined) {
                    const price = parseFloat(fields.price);
                    const sale = parseFloat(fields.sale_price);
                    if (!isNaN(price) && !isNaN(sale) && sale > price) {
                        toastr.error('Sale price cannot exceed price.');
                        return;
                    }
                }

                const payload = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_id: "{{ $product->id }}",
                    apply_to: scope
                };
                Object.assign(payload, fields);

                if (scope === 'checked') {
                    const ids = getSelectedVariationIds($group);
                    if (!ids.length) {
                        toastr.error('Select at least one variation.');
                        return;
                    }
                    payload.variation_ids = ids;
                } else {
                    payload.color_id = colorId;
                }

                $.ajax({
                    url: "{{ route('products.variations.bulk-update') }}",
                    type: "POST",
                    data: payload,
                    success: function() {
                        toastr.success('Values applied successfully.');
                        // simplest: reload to reflect changes
                        window.location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Failed to apply values.');
                        }
                    }
                });
            });
        })();
    </script>
@endpush
