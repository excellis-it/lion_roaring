@if ($available_product_variations->count() > 0)
    @foreach ($available_product_variations as $index => $variation)
        <div class="warehouse-variation-product-entry" data-id="{{ $variation->id }}">
            <input type="hidden" name="variation_products[{{ $index }}][variation_id]"
                value="{{ $variation->id }}">
            <input type="hidden" name="variation_products[{{ $index }}][warehouse_variation_id]"
                value="{{ $variation->id }}">
            <div class="row">


                <div class="col-md-2 mb-2">
                    <div class="box_label">
                        <label>SKU <span class="text-danger"></span></label>
                        <input type="text" name="variation_products[{{ $index }}][sku]" class="form-control"
                            value="{{ $variation->sku }}" readonly>
                    </div>
                </div>

                <div class="col-md-1 mb-2">
                    <div class="box_label">
                        <label>Price <span class="text-danger"></span></label>
                        <input type="number" step="0.01" name="variation_products[{{ $index }}][price]"
                            class="form-control" value="{{ $variation->warehouse_price }}" readonly>
                    </div>
                </div>

                <div class="col-md-1 mb-2">
                    <div class="box_label">
                        <label>Before Price </label>

                        <input type="number" step="0.01"
                            name="variation_products[{{ $index }}][before_sale_price]" class="form-control"
                            value="{{ $variation->warehouse_before_sale_price }}" readonly>
                    </div>
                </div>

                <div class="col-md-1 mb-2">
                    <div class="box_label">
                        <label>Color</label>
                        <input type="hidden" name="variation_products[{{ $index }}][color_id]"
                            class="form-control" value="{{ $variation->color_id }}">
                        <input type="text" name="variation_products[{{ $index }}][color]"
                            class="form-control" value="{{ $variation->colorDetail->color_name ?? '' }}" readonly>
                    </div>
                </div>

                <div class="col-md-1 mb-2">
                    <div class="box_label">
                        <label>Size</label>
                        <input type="hidden" name="variation_products[{{ $index }}][size_id]"
                            class="form-control" value="{{ $variation->size_id }}">
                        <input type="text" name="variation_products[{{ $index }}][size]" class="form-control"
                            value="{{ $variation->sizeDetail->size ?? '' }}" readonly>

                    </div>
                </div>









                {{-- Images --}}


                {{-- <div class="col-md-3 mb-2 d-flex flex-wrap align-items-start">
                    @php
                        if (!isset($shownColorImages)) {
                            $shownColorImages = [];
                        }
                        $showImagesForThisColor = !in_array($variation->color_id, $shownColorImages);
                    @endphp

                    @if ($showImagesForThisColor)
                        <div class="d-flex flex-wrap">
                            @if ($variation->images && $variation->images->count() > 0)
                                @foreach ($variation->images as $image)
                                    <div class="image-area m-1 position-relative" id="{{ $image->id }}"
                                        style="width:80px; height:80px; overflow:hidden; border-radius:4px; background:#fff;">
                                        <img src="{{ Storage::url($image->image_path) }}" alt="Variation Image"
                                            style="width:100%; height:100%; object-fit:cover; display:block;">

                                    </div>
                                @endforeach
                            @else
                                <div class="image-area m-1 d-flex align-items-center justify-content-center"
                                    style="width:80px; height:80px; background:#f8f9fa; border:1px dashed #e9ecef; color:#6c757d; border-radius:4px;">
                                    <small>No images</small>
                                </div>
                            @endif
                        </div>
                        @php $shownColorImages[] = $variation->color_id; @endphp
                    @endif
                </div> --}}

                <div class="col-md-2 mb-2">
                    <div class="box_label">
                        <label>Global Stock Quantity <span class="text-danger"></span></label>
                        <input type="number" min="0"
                            name="variation_products[{{ $index }}][available_quantity]"
                            class="form-control available-qty-input" value="{{ $variation->admin_available_quantity }}"
                            readonly>
                    </div>
                </div>

                <div class="col-md-2 mb-2">
                    <div class="box_label">
                        <label>Assign Quantity <span class="text-danger">*</span></label>
                        <input type="number" min="0" name="variation_products[{{ $index }}][quantity]"
                            class="form-control" value="">
                    </div>
                </div>

                <div class="col-md-2 mb-2">
                    <div class="box_label">
                        <label>Warehouse Stock Quantity <span class="text-danger"></span></label>
                        <input type="number" min="0"
                            name="variation_products[{{ $index }}][warehouse_quantity]" class="form-control"
                            value="{{ $variation->warehouse_quantity }}" readonly>
                    </div>
                </div>

            </div>
            <hr>
        </div>
    @endforeach
@else
    <h4 class="text-center">No variations available</h4>
@endif
