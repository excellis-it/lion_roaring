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

            <div class="row mb-3">
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
                                    <label class="form-label" for="color-select">Select colors to load available
                                        variations</label>
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
                                <h3>Product Variations</h3>
                            </div>
                        </div>
                    </div>

                    <div id="variation-products-container-data">

                    </div>




                    <div class="mt-3 mb-5" style="height: 10px; border-bottom: 2px solid #eee; margin: 20px 0;">
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

            // Delegate events to container (content is loaded via AJAX)
            $(document)
                .on('focus', '#variation-products-container-data input[name$="[quantity]"]', function() {
                    $(this).data('prev', this.value);
                })
                .on('change keyup', '#variation-products-container-data input[name$="[quantity]"]', function() {
                    const $input = $(this);
                    const newVal = $input.val().trim() === '' ? 0 : parseInt($input.val(), 10);
                    if (isNaN(newVal) || newVal < 0) {
                        toastr.error('Invalid quantity');
                        $input.val($input.data('prev'));
                        return;
                    }

                    const $row = $input.closest('.warehouse-variation-product-entry');
                    const variationId = $row.data('id');

                    //   $input.prop('disabled', true);

                    $.ajax({
                        url: UPDATE_VARIATION_QTY_URL,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            warehouse_id: WAREHOUSE_ID,
                            variation_id: variationId,
                            quantity: newVal
                        },
                        success: function(resp) {
                            if (resp.status) {
                                // Update available quantity in this row
                                $row.find('input[name$="[available_quantity]"]').val(resp.data
                                    .admin_available_quantity);
                                // toastr.success('Quantity updated');
                            } else {
                                toastr.error(resp.message || 'Update failed');
                                $input.val($input.data('prev'));
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON) {
                                toastr.error(xhr.responseJSON.message || 'Validation error');
                                if (xhr.responseJSON.max_allowed !== undefined) {
                                    $input.val(xhr.responseJSON.max_allowed).trigger('change');
                                } else {
                                    $input.val($input.data('prev'));
                                }
                            } else {
                                toastr.error('Server error');
                                $input.val($input.data('prev'));
                            }
                        },
                        complete: function() {
                            $input.prop('disabled', false);
                        }
                    });
                });
        </script>
    @endpush
