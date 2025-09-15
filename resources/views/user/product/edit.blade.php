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

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
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
                                    {{-- price --}}
                                    <div class="col-md-6 mb-2" hidden>
                                        <div class="box_label">
                                            <label for="price"> Product Price*</label>
                                            <input type="text" name="price" id="price" class="form-control"
                                                value="{{ $product->price }}">
                                            @if ($errors->has('price'))
                                                <span class="error">{{ $errors->first('price') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- quantity --}}
                                    {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="quantity"> Product Quantity*</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                        value="{{ $product->quantity }}">
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
                                    {{-- button_name --}}
                                    {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="button_name"> Button Name*</label>
                                    <input type="text" name="button_name" id="button_name" class="form-control"
                                        value="{{ $product->button_name }}">
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
                                        value="{{ $product->affiliate_link }}">
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
                                        value="{{ $product->sku }}">
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

                                    {{-- <div class="col-md-12">
                                <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image(Drag and drop
                                    atleast 1
                                    images)</label>
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
                                @if ($product->withOutMainImage)
                                    {{-- <div class="row mb-6">
                                <label for="inputConfirmPassword2" class="col-form-label">Image Preview</label>

                                @foreach ($product->withOutMainImage as $image)
                                    <div class="image-area m-4" id="{{ $image->id }}">
                                        <img src="{{ Storage::url($image->image) }}" alt="Preview">
                                        <a class="remove-image" href="javascript:void(0);" data-id="{{ $image->id }}"
                                            style="display: inline;">&#215;</a>
                                    </div>
                                @endforeach
                            </div> --}}
                                @endif
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
                                        value="{{ $product->meta_title }}" placeholder="">
                                    @if ($errors->has('meta_title'))
                                        <span class="error">{{ $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="type">Mete Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="5" cols="30"
                                        placeholder="">{{ $product->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <span class="error">{{ $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
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
                                                            <option value="{{ $size->id }}"
                                                                {{ $product->sizeIds()->contains($size->id) ? 'selected' : '' }}>
                                                                {{ $size->size }}</option>
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
                                                            <option value="{{ $color->id }}"
                                                                {{ $product->colorIds()->contains($color->id) ? 'selected' : '' }}>
                                                                {{ $color->color_name }}</option>
                                                        @endforeach
                                                    </select>
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
                            <div class="tab-pane fade" id="variation" role="tabpanel" aria-labelledby="variation">
                                <!-- Warehouse Products Section -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-3">
                                            <h3>Warehouse Assignment</h3>
                                        </div>
                                    </div>
                                </div>

                                <div id="warehouse-products-container">
                                    @if ($warehouseProducts->count() > 0)
                                        @foreach ($warehouseProducts as $index => $warehouseProduct)
                                            <div class="warehouse-product-entry">
                                                <div class="row">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="box_label">
                                                            <label>Warehouse <span class="text-danger">*</span></label>
                                                            <select
                                                                name="warehouse_products[{{ $index }}][warehouse_id]"
                                                                class="form-control warehouse-id">
                                                                <option value="">Select Warehouse</option>
                                                                @foreach ($warehouses as $warehouse)
                                                                    <option value="{{ $warehouse->id }}"
                                                                        {{ $warehouseProduct->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                                        {{ $warehouse->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden"
                                                                name="warehouse_products[{{ $index }}][id]"
                                                                value="{{ $warehouseProduct->id }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="box_label">
                                                            <label>SKU <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                name="warehouse_products[{{ $index }}][sku]"
                                                                class="form-control"
                                                                value="{{ $warehouseProduct->sku }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="box_label">
                                                            <label>Price <span class="text-danger">*</span></label>
                                                            <input type="number" step="0.01"
                                                                name="warehouse_products[{{ $index }}][price]"
                                                                class="form-control"
                                                                value="{{ $warehouseProduct->price }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-2">
                                                        <div class="box_label">
                                                            <label>Size</label>
                                                            <select
                                                                name="warehouse_products[{{ $index }}][size_id]"
                                                                class="form-control">
                                                                <option value="">No Size</option>
                                                                @foreach ($sizes as $size)
                                                                    <option value="{{ $size->id }}"
                                                                        {{ $warehouseProduct->size_id == $size->id ? 'selected' : '' }}>
                                                                        {{ $size->size }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-2">
                                                        <div class="box_label">
                                                            <label>Color</label>
                                                            <select
                                                                name="warehouse_products[{{ $index }}][color_id]"
                                                                class="form-control">
                                                                <option value="">No Color</option>
                                                                @foreach ($colors as $color)
                                                                    <option value="{{ $color->id }}"
                                                                        {{ $warehouseProduct->color_id == $color->id ? 'selected' : '' }}>
                                                                        {{ $color->color_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-2">
                                                        <div class="box_label">
                                                            <label>Images</label>
                                                            <input type="file"
                                                                name="warehouse_products[{{ $index }}][images][]"
                                                                class="form-control" multiple>
                                                        </div>
                                                        @if ($warehouseProduct->images->count() > 0)
                                                            <div class="existing-images mt-2">
                                                                <label class="small text-muted">Existing Images:</label>
                                                                <ul class="list-unstyled">
                                                                    @foreach ($warehouseProduct->images as $image)
                                                                        <li id="warehouse-image-{{ $image->id }}"
                                                                            class="d-flex align-items-center mb-1 p-2 border rounded">
                                                                            <img src="{{ Storage::url($image->image_path) }}"
                                                                                style="max-width: 80px; max-height: 80px;"
                                                                                alt="">
                                                                            <a hidden
                                                                                href="{{ Storage::url($image->image_path) }}"
                                                                                target="_blank" class="me-2 text-truncate"
                                                                                style="max-width: 150px;">
                                                                                {{ basename($image->image_path) }}
                                                                            </a>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger remove-warehouse-image"
                                                                                data-id="{{ $image->id }}"
                                                                                title="Remove Image">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-2 mb-2">
                                                        <div class="box_label">
                                                            <label>Quantity <span class="text-danger">*</span></label>
                                                            <input type="number" min="0"
                                                                name="warehouse_products[{{ $index }}][quantity]"
                                                                class="form-control"
                                                                value="{{ $warehouseProduct->quantity }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1 mb-2 d-flex">
                                                        <button type="button"
                                                            class="btn btn-danger remove-warehouse-product"><i
                                                                class="fa fa-trash"></i></button>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="warehouse-product-entry">
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        <label>Warehouse <span class="text-danger">*</span></label>
                                                        <select name="warehouse_products[0][warehouse_id]"
                                                            class="form-control warehouse-id">
                                                            <option value="">Select Warehouse</option>
                                                            @foreach ($warehouses as $warehouse)
                                                                <option value="{{ $warehouse->id }}">
                                                                    {{ $warehouse->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        <label>SKU <span class="text-danger">*</span></label>
                                                        <input type="text" name="warehouse_products[0][sku]"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="box_label">
                                                        <label>Price <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01"
                                                            name="warehouse_products[0][price]" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <div class="box_label">
                                                        <label>Size</label>
                                                        <select name="warehouse_products[0][size_id]"
                                                            class="form-control">
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
                                                        <select name="warehouse_products[0][color_id]"
                                                            class="form-control">
                                                            <option value="">No Color</option>
                                                            @foreach ($colors as $color)
                                                                <option value="{{ $color->id }}">
                                                                    {{ $color->color_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>



                                                <div class="col-md-3 mb-2">
                                                    <div class="box_label">
                                                        <label>Images</label>
                                                        <input type="file" name="warehouse_products[0][images][]"
                                                            class="form-control" multiple>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 mb-2">
                                                    <div class="box_label">
                                                        <label>Quantity <span class="text-danger">*</span></label>
                                                        <input type="number" min="0"
                                                            name="warehouse_products[0][quantity]" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-1 mb-2 d-flex ">
                                                    <button type="button"
                                                        class="btn btn-danger remove-warehouse-product"><i
                                                            class="fa fa-trash"></i></button>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    @endif
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" id="add-warehouse-product">
                                            <i class="fa fa-plus"></i> Add Warehouse Product
                                        </button>
                                    </div>
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
                        }
                    });
                });

                // Remove warehouse product images
                $(document).on('click', '.remove-warehouse-image', function() {
                    var id = $(this).data('id');
                    var token = $("meta[name='csrf-token']").attr("content");
                    var $this = $(this);

                    if (confirm('Are you sure you want to delete this image?')) {
                        $.ajax({
                            url: "{{ route('warehouse-product.image.delete') }}",
                            type: 'GET',
                            data: {
                                "id": id,
                                "_token": token,
                            },
                            success: function(response) {
                                $('#warehouse-image-' + id).remove();
                                console.log(response.message);
                            },
                            error: function(xhr) {
                                console.log('Error deleting image');
                                alert('Error deleting image');
                            }
                        });
                    }
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
                $(document).on('change',
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
            $(document).ready(function() {
                // Warehouse product management
                let warehouseProductIndex =
                    {{ $warehouseProducts->count() > 0 ? $warehouseProducts->count() - 1 : 0 }};

                // Add new warehouse product entry
                $('#add-warehouse-product').on('click', function() {
                    warehouseProductIndex++;

                    const newEntry = `
                    <div class="warehouse-product-entry">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse_products[${warehouseProductIndex}][warehouse_id]" class="form-control warehouse-id" >
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
                                    <input type="text" name="warehouse_products[${warehouseProductIndex}][sku]" class="form-control" >
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="warehouse_products[${warehouseProductIndex}][price]" class="form-control" >
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
                                    <label>Images</label>
                                    <input type="file" name="warehouse_products[${warehouseProductIndex}][images][]"
                                        class="form-control" multiple>
                                </div>
                            </div>



                            <div class="col-md-2 mb-2">
                                <div class="box_label">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="warehouse_products[${warehouseProductIndex}][quantity]" class="form-control" >
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

        <script>
            $(document).ready(function() {
                // if url has ?tab=variations, open that tab (Bootstrap 5)
                const urlParams = new URLSearchParams(window.location.search);
                const tab = urlParams.get('tab');
                if (tab === 'variations') {
                    // Use Bootstrap 5 Tab API to show the variation tab
                    const variationTrigger = document.querySelector('#variation-tab');
                    if (variationTrigger && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const tabInstance = bootstrap.Tab.getInstance(variationTrigger) || new bootstrap.Tab(
                            variationTrigger);
                        tabInstance.show();
                    }
                }
            });
        </script>
    @endpush
