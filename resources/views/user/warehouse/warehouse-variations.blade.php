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
                <h5>Product {{ $product->product_type == 'simple' ? 'stock' : 'variations' }} for Warehouse :
                    <strong>{{ $wareHouse->name }}</strong>
                </h5>

            </div>


            <div class="row mb-3" {{ $product->product_type == 'simple' ? 'hidden' : '' }}>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <strong>Filter Product Variations</strong> --}}
                        </div>
                        <div class="card-body">
                            <form id="select-colors-form"
                                action="{{ route('products.select.warehouse.variation.stock', ['warehouseId' => $wareHouse->id, 'productId' => $product->id]) }}"
                                method="POST" class="row g-2 align-items-end">
                                @csrf

                                <div class="col-12">
                                    <label class="form-label" for="color-select">Choose Colors <span style="color: #E54E4E">(Click To Load Variation Button To Load The Product)</span></label>
                                </div>

                                <div class="col-9">
                                    <select id="color-select" name="color_id[]" class="form-control" multiple
                                        aria-label="Select colors">
                                        @foreach ($product_have_colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ in_array($color->id, (array) request('color_id')) ? 'selected' : '' }}>
                                                {{ $color->color_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3 d-grid">
                                    <button class="btn btn-primary" type="submit">Load Variations</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">

                    <input type="hidden" name="product_id" value="{{ $product->id }}">


                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="heading_box mb-3">
                                <h3>Product {{ $product->product_type == 'simple' ? 'Stock' : 'Variations' }}</h3>
                            </div>
                        </div>
                    </div>

                    <div id="variation-products-container-data">

                    </div>




                    <div class="mt-3 mb-5" style="height: 10px; border-bottom: 2px solid #eee; margin: 20px 0;">
                    </div>

                    <div class="row">
                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                            <button type="button" class="print_btn me-2 submit-data-button">Update</button>
                            <a href="{{ route('products.index') }}" class="print_btn print_btn_vv">Cancel</a>
                        </div>
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
            document.addEventListener('DOMContentLoaded', function() {
                var colorSelect = document.querySelector('select[name="color_id[]"]');
                var choices = new Choices(colorSelect, {
                    removeItemButton: true,
                    searchResultLimit: 5,
                    renderChoiceLimit: 5,
                    placeholder: true,
                    placeholderValue: 'Select colors'
                });
            });
        </script>
        <script>
            ClassicEditor.create(document.querySelector("#description"));
            ClassicEditor.create(document.querySelector("#specification"));
        </script>

        <script>
            // ajax submit and get laravel view select-colors-form
            $('#select-colors-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var method = form.attr('method');
                var data = form.serialize();
                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function(response) {
                        // console.log(response);
                        $('#variation-products-container-data').html(response);
                        toastr.success('Product variations loaded successfully');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).ready(function() {
                $('#select-colors-form').submit();
            });
        </script>
        <script>
            const WAREHOUSE_ID = {{ $wareHouse->id }};
            const UPDATE_VARIATION_QTY_URL = "{{ route('warehouse.variation.update-quantity') }}";

            // Keep track of previous values on focus (useful to revert if server rejects)
            $(document).on('focus', '#variation-products-container-data input[name$="[quantity]"]', function() {
                $(this).data('prev', this.value);
            });

            // Submit all quantities at once when the Update button is clicked
            $(document).on('click', '.submit-data-button', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const variations = [];

                $('#variation-products-container-data .warehouse-variation-product-entry').each(function() {
                    const $row = $(this);
                    const variationId = $row.data('id');
                    const raw = $row.find('input[name$="[quantity]"]').val();
                    const qty = raw === '' ? 0 : parseInt(raw, 10);

                    if (isNaN(qty) || qty < 0) {
                        toastr.error('One or more quantities are invalid. Fix them before submitting.');
                        // focus the first invalid input
                        $row.find('input[name$="[quantity]"]').first().focus();
                        variations.length = 0; // cancel
                        return false; // break each()
                    }

                    variations.push({
                        variation_id: variationId,
                        quantity: qty
                    });
                });

                if (!variations.length) {
                    return;
                }

                $btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: UPDATE_VARIATION_QTY_URL,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        warehouse_id: WAREHOUSE_ID,
                        variations: variations
                    }),
                    success: function(resp) {
                        if (resp.status) {
                            // resp.data can be an object keyed by variation_id or an array of {variation_id, admin_available_quantity}
                            const data = resp.data || {};

                            // If object keyed by id
                            if (!Array.isArray(data) && typeof data === 'object') {
                                Object.keys(data).forEach(function(vid) {
                                    const avail = data[vid].admin_available_quantity ?? data[vid];
                                    const warehouse_available_quantity = data[vid]
                                        .warehouse_available_quantity ?? data[vid];
                                    const $row = $(
                                        '#variation-products-container-data .warehouse-variation-product-entry[data-id="' +
                                        vid + '"]');


                                    if ($row.length) {
                                        $row.find('input[name$="[available_quantity]"]').val(avail)
                                            .css('background-color', '#edd4d4');
                                        $row.find('input[name$="[warehouse_quantity]"]').val(
                                                warehouse_available_quantity)
                                            .css('background-color', '#d4edda');
                                        setTimeout(() => $row.find(
                                            'input[name$="[available_quantity]"]').css(
                                            'background-color', ''), 1000);
                                        setTimeout(() => $row.find(
                                            'input[name$="[warehouse_quantity]"]').css(
                                            'background-color', ''), 1000);

                                        $row.find('input[name$="[quantity]"]').val('');

                                    }
                                });
                            } else if (Array.isArray(data)) {
                                data.forEach(function(item) {
                                    const vid = item.variation_id ?? item.id;
                                    const avail = item.admin_available_quantity ?? item
                                        .available_quantity ?? item.admin_available_quantity;
                                    const warehouse_available_quantity = item
                                        .warehouse_available_quantity ?? item
                                        .warehouse_quantity ?? item.warehouse_available_quantity;
                                    const $row = $(
                                        '#variation-products-container-data .warehouse-variation-product-entry[data-id="' +
                                        vid + '"]');
                                    if ($row.length) {
                                        $row.find('input[name$="[available_quantity]"]').val(avail)
                                            .css('background-color', '#edd4d4');
                                        $row.find('input[name$="[warehouse_quantity]"]').val(
                                                warehouse_available_quantity)
                                            .css('background-color', '#d4edda');
                                        setTimeout(() => $row.find(
                                            'input[name$="[available_quantity]"]').css(
                                            'background-color', ''), 1000);
                                        setTimeout(() => $row.find(
                                            'input[name$="[warehouse_quantity]"]').css(
                                            'background-color', ''), 1000);
                                        $row.find('input[name$="[quantity]"]').val('');
                                    }
                                });
                            }

                            toastr.success(resp.message || 'Quantities updated successfully');
                        } else {
                            toastr.error(resp.message || 'Update failed');
                            // Optionally revert inputs using stored prev values
                            $('#variation-products-container-data input[name$="[quantity]"]').each(
                                function() {
                                    const prev = $(this).data('prev');
                                    if (prev !== undefined) $(this).val(prev);
                                });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            toastr.error(xhr.responseJSON.message || 'Validation error');
                        } else {
                            toastr.error('Server error');
                        }
                        // revert inputs
                        $('#variation-products-container-data input[name$="[quantity]"]').each(function() {
                            const prev = $(this).data('prev');
                            if (prev !== undefined) $(this).val(prev);
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Update');
                    }
                });
            });
        </script>
    @endpush
