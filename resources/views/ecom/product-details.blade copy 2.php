@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($product->meta_description) ? $product->meta_description : '' }}">
@endsection
@section('title')
    {{ isset($product->meta_title) ? $product->meta_title : $product->name }}
@endsection

@push('styles')
    <style>
        .qty-input {
            border: none;
        }

        .btn-check:checked+.btn {
            color: #643171;
            background-color: var(--bs-btn-active-bg);
            border-color: #643171;
            border: 4px solid;
        }
    </style>
@endpush

@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/slider-bg.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Product Details</h2>
                        <p></p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="catagory_sec product_details">

        <div class="container py-5">
            <div class="row g-4">
                <!-- Left Section -->
                <div class="col-md-4">
                    <div class="zoom-wrepper">
                        <div class="img-container">
                            @php
                                $mainImage = '';

                                if ($product->product_type == 'simple') {
                                    $mainImage = $product->getMainImageAttribute()
                                        ? asset('storage/' . $product->getMainImageAttribute())
                                        : asset('ecom_assets/images/no-image.jpg');
                                } else {
                                    // For variable products, try to get the first variation's image if available
    if (!empty($availableColors) && count($availableColors) > 0) {
        $firstColor = $availableColors[0];
        $colorId = $firstColor->id;
        if (isset($variations[$colorId]) && !empty($variations[$colorId]['images'])) {
            $mainImage = asset('storage/' . $variations[$colorId]['images'][0]);
        } else {
            $mainImage = $product->getMainImageAttribute()
                ? asset('storage/' . $product->getMainImageAttribute())
                : asset('ecom_assets/images/no-image.jpg');
        }
    } else {
        $mainImage = $product->getMainImageAttribute()
            ? asset('storage/' . $product->getMainImageAttribute())
            : asset('ecom_assets/images/no-image.jpg');
                                    }
                                }
                            @endphp
                            <img id="mainImage" src="{{ $mainImage }}" class="main-img">
                            <div id="lens" class="lens"></div>
                        </div>
                        <div id="result" class="result"></div>
                    </div>

                    <div class="thumbnails-box thumbnails d-flex gap-2 mt-2" id="product-thumbnails">
                        @if ($product->product_type == 'simple')
                            @if ($product->withOutMainImage && $product->withOutMainImage->count() > 0)
                                @foreach ($product->withOutMainImage as $image)
                                    <img src="{{ asset('storage/' . $image->image) }}" onclick="changeImage(this)">
                                @endforeach
                            @endif
                        @else
                            @if (!empty($availableColors) && count($availableColors) > 0)
                                @php
                                    $firstColor = $availableColors[0];
                                    $colorId = $firstColor->id;
                                @endphp
                                @if (isset($variations[$colorId]) && !empty($variations[$colorId]['images']))
                                    @foreach ($variations[$colorId]['images'] as $image)
                                        <img src="{{ asset('storage/' . $image) }}" onclick="changeImage(this)">
                                    @endforeach
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Middle Section -->
                <div class="col-md-5 bg-white p-3 rounded">
                    <div class="all-product-details">
                        <h2>{{ $product->name }}</h2>
                        <div class="rate-box">
                            <ul class="rate-star">
                                @php
                                    $avgRating = $product->reviews->avg('rating') ?? 0;
                                    $fullStars = floor($avgRating);
                                    $halfStar = $avgRating - $fullStars >= 0.5;
                                @endphp

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $fullStars)
                                        <li><i class="fa-solid fa-star"></i></li>
                                    @elseif($i == $fullStars + 1 && $halfStar)
                                        <li><i class="fa-solid fa-star-half-stroke"></i></li>
                                    @else
                                        <li><span><i class="fa-regular fa-star"></i></span></li>
                                    @endif
                                @endfor
                            </ul>
                            <p>({{ $product->reviews->count() }} ratings)</p>
                        </div>

                        @if (!$availableInWarehouse)
                            <div class="alert alert-warning">
                                This product is currently not available in your area.
                            </div>
                        @endif

                        @if ($product->product_type == 'simple')
                            <div class="price" id="product-price">{{ $product->price }}</div>
                            <div class="sku mt-2 mb-2" id="product-sku">SKU: {{ $product->sku }}</div>
                        @else
                            @php
                                $firstVariation = null;
                                $firstVariationPrice = null;
                                $firstVariationSku = null;

                                if (!empty($availableColors) && count($availableColors) > 0) {
                                    $firstColor = $availableColors[0];
                                    $colorId = $firstColor->id;
                                    if (
                                        isset($variations[$colorId]) &&
                                        isset($variations[$colorId]['firstVariation'])
                                    ) {
                                        $firstVariation = $variations[$colorId]['firstVariation'];
                                        $firstVariationPrice = $firstVariation->price;
                                        $firstVariationSku = $firstVariation->sku;
                                    }
                                }
                            @endphp
                            <div class="price" id="product-price">
                                @if ($firstVariationPrice)
                                    {{ $firstVariationPrice }}
                                @else
                                    {{ $product->price }}
                                @endif
                            </div>
                            <div class="sku mt-2 mb-2" id="product-sku">
                                SKU: {{ $firstVariationSku ?? $product->sku }}
                            </div>
                        @endif

                        @if ($product->product_type == 'variable' && $availableInWarehouse && !empty($availableSizes))
                            <!-- Size Selection -->
                            <h4>Size:</h4>
                            <div class="sizes d-flex flex-wrap gap-2 mb-3" id="product-sizes">
                                @php
                                    $firstColor = !empty($availableColors) ? $availableColors[0] : null;
                                    $firstColorId = $firstColor ? $firstColor->id : null;
                                @endphp

                                @if ($firstColorId && isset($variations[$firstColorId]) && !empty($variations[$firstColorId]['sizes']))
                                    @foreach ($variations[$firstColorId]['sizes'] as $sizeId => $sizeData)
                                        <div class="form-check form-check-inline">
                                            <input class="btn-check product-select-size-input" type="radio" name="size"
                                                id="size-{{ $sizeId }}" value="{{ $sizeId }}"
                                                {{ $loop->first ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary" for="size-{{ $sizeId }}">
                                                {{ $sizeData['size']->size }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Color Selection -->
                            <h4>Colour: <span id="selected-color-name">
                                    {{ !empty($availableColors) ? $availableColors[0]->color_name : '' }}
                                </span></h4>

                            <div class="colors d-flex flex-wrap gap-2" id="product-colors">
                                @foreach ($availableColors as $index => $color)
                                    <div class="form-check form-check-inline">
                                        <input class="btn-check product-select-color-input" type="radio" name="color"
                                            id="color-{{ $color->id }}" value="{{ $color->id }}"
                                            data-color-name="{{ $color->color_name }}" {{ $index == 0 ? 'checked' : '' }}>
                                        <label class="btn" for="color-{{ $color->id }}"
                                            style="background-color: {{ $color->color }}; width: 40px; height: 40px; border-radius: 50%;">
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        <div class="product-details-box">
                            <h4 class="list-heading">Product details</h4>
                            <div class="product-details">
                                <p>{!! $product->description !!}</p>
                            </div>

                        </div>


                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-md-3 bg-white p-3 rounded border">
                    <div class="right-delevery-box">
                        <div class="price m-3" id="right-product-price">
                            @if ($product->product_type == 'simple')
                                {{ $product->price }}
                            @else
                                @php
                                    $firstVariationPrice = null;
                                    if (!empty($availableColors) && count($availableColors) > 0) {
                                        $firstColor = $availableColors[0];
                                        $colorId = $firstColor->id;
                                        if (
                                            isset($variations[$colorId]) &&
                                            isset($variations[$colorId]['firstVariation'])
                                        ) {
                                            $firstVariationPrice = $variations[$colorId]['firstVariation']->price;
                                        }
                                    }
                                @endphp
                                {{ $firstVariationPrice ? $firstVariationPrice : $product->price }}
                            @endif
                        </div>

                        @if ($nearbyWareHouse)
                            <p>Sold By: {{ $nearbyWareHouse->name }}</p>
                        @endif

                        <div class="stock" id="product-stock-status">
                            @if ($availableInWarehouse)
                                @if ($product->product_type == 'simple')
                                    @if ($product->quantity > 0)
                                        <span class="text-success">In stock</span>
                                    @else
                                        <span class="text-danger">Out of stock</span>
                                    @endif
                                @else
                                    @php
                                        $inStock = false;
                                        if (!empty($availableColors) && count($availableColors) > 0) {
                                            $firstColor = $availableColors[0];
                                            $colorId = $firstColor->id;
                                            if (
                                                isset($variations[$colorId]) &&
                                                !empty($variations[$colorId]['sizes'])
                                            ) {
                                                foreach ($variations[$colorId]['sizes'] as $sizeData) {
                                                    if ($sizeData['warehouse_quantity'] > 0) {
                                                        $inStock = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    @if ($inStock)
                                        <span class="text-success">In stock</span>
                                    @else
                                        <span class="text-danger">Out of stock</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-warning">Not available in your area</span>
                            @endif
                        </div>

                        @if ($availableInWarehouse)
                            <div class="form-group mt-3">
                                <label for="quantity">Quantity: </label>
                                <input type="number" min="1" id="quantity" class="form-control product-qty"
                                    value="1"
                                    @if ($product->product_type == 'simple') max="{{ $product->quantity }}"
                                    @else
                                        @php
                                            $maxQuantity = 0;
                                            if(!empty($availableColors) && count($availableColors) > 0) {
                                                $firstColor = $availableColors[0];
                                                $colorId = $firstColor->id;
                                                if(isset($variations[$colorId]) && !empty($variations[$colorId]['sizes'])) {
                                                    $firstSize = array_key_first($variations[$colorId]['sizes']);
                                                    if($firstSize) {
                                                        $maxQuantity = $variations[$colorId]['sizes'][$firstSize]['warehouse_quantity'];
                                                    }
                                                }
                                            }
                                        @endphp
                                        max="{{ $maxQuantity }}" @endif>
                            </div>

                            <!-- Product Variation ID Hidden Field -->
                            <input type="hidden" id="product-variation-id"
                                value="{{ $product->product_type == 'variable' && !empty($productVariations) ? $productVariations[0]['id'] : '' }}">

                            <!-- Warehouse ID Hidden Field -->
                            <input type="hidden" id="warehouse-id" value="{{ $nearbyWareHouse->id ?? '' }}">

                            <!-- Buttons -->
                            <button class="btn w-100 mb-2 btn-warning mt-3" id="add-to-cart-btn"
                                @if (($product->product_type == 'simple' && $product->quantity <= 0) || !$availableInWarehouse) disabled @endif>
                                Add to Cart
                            </button>

                            @php
                                $isInWishlist = false;
                                if ($isAuth) {
                                    $isInWishlist = App\Models\EcomWishList::where('user_id', auth()->id())
                                        ->where('product_id', $product->id)
                                        ->exists();
                                }
                            @endphp

                            <button class="btn w-100 btn-outline-danger" id="add-to-wishlist-btn"
                                data-product-id="{{ $product->id }}">
                                <span><i class="fa-{{ $isInWishlist ? 'solid' : 'regular' }} fa-heart"></i></span>
                                {{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                            </button>
                        @else
                            <div class="alert alert-info mt-3">
                                This product is not available in your location. Please check back later or contact customer
                                support.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>




        <div class="container py-5">
            <div class="additional-details my-5 text-left">
                <!-- Nav pills -->
                <ul class="nav nav-tabs justify-content-start">
                    <li class="nav-tabs">
                        <a class="nav-link active" data-toggle="tab" data-bs-toggle="tab"
                            href="#home">Specifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" data-bs-toggle="tab" href="#menu1">Reviews</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content mb-3">
                    <div class="tab-pane active" id="home">
                        <div class="description">
                            {!! $product->specification !!}
                        </div>
                    </div>
                    <div class="tab-pane fade" id="menu1">
                        <div class="review">
                            <div class="pure_tab">
                                <form id="review-form" action="javascript:void(0);" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                    <h2>Write Your Review</h2>
                                    <div class="rate">
                                        <input type="radio" id="star5" name="rate" value="5" />
                                        <label for="star5" title="text">5 stars</label>
                                        <input type="radio" id="star4" name="rate" value="4" />
                                        <label for="star4" title="text">4 stars</label>
                                        <input type="radio" id="star3" name="rate" value="3" />
                                        <label for="star3" title="text">3 stars</label>
                                        <input type="radio" id="star2" name="rate" value="2" />
                                        <label for="star2" title="text">2 stars</label>
                                        <input type="radio" id="star1" name="rate" value="1" />
                                        <label for="star1" title="text">1 star</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="review">Your Review:</label>
                                        <textarea class="form-control" rows="5" placeholder="Your Reivew" name="review" id="review"></textarea>
                                        <span id="reviewInfo" class="help-block pull-right ">

                                        </span>
                                    </div>
                                    <button type="submit" class="red_btn mb-5 mt-3"
                                        style="border: none"><span>Submit</span></button>
                                </form>

                                <div id="show-review">
                                    @include('ecom.partials.product-review', ['reviews' => $reviews ?? ''])
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="feature_sec">
        <div class="pos_zi">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7">
                        <div class="heading_hp text-center">
                            <h2>Related products</h2>
                            <p> </p>
                        </div>
                    </div>
                </div>
                <div class="featured_slider">
                    @if (count($related_products) > 0)
                        @foreach ($related_products as $related_product)
                            <div class="feature_slid_padding">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <div class="wishlist_icon" data-id="{{ $related_product->id }}">
                                            <a href="javascript:void(0);"><i
                                                    class="fa-solid fa-heart {{ $product->isInWishlist() ? 'text-danger' : '' }}"></i></a>
                                        </div>
                                        <a href="{{ route('e-store.product-details', $related_product->slug) }}">
                                            @if (isset($related_product->main_image) && $related_product->main_image != null)
                                                <img src="{{ Storage::url($related_product->main_image) }}"
                                                    alt="{{ $related_product->main_image }}">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="feature_text">
                                        <ul class="star_ul">
                                            @if (Helper::getTotalProductRating($related_product->id))
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <li><i
                                                            class="fa-{{ $i <= Helper::getTotalProductRating($related_product->id) ? 'solid' : 'regular' }} fa-star"></i>
                                                    </li>
                                                @endfor
                                            @else
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                            @endif

                                            <li>({{ Helper::getRatingCount($related_product->id) ? Helper::getRatingCount($related_product->id) : 0 }})
                                            </li>
                                        </ul>
                                        <a
                                            href="{{ route('e-store.product-details', $related_product->slug) }}">{{ $related_product->name }}</a>
                                        <p>{{ strlen($related_product->short_description) > 50 ? substr($related_product->short_description, 0, 50) . '...' : $related_product->short_description }}
                                        </p>
                                        <span class="price_text">$ {{ $related_product->price }}</span>
                                    </div>
                                    <div class="addtocart" data-id="{{ $related_product->id }}">
                                        <a href="javascript:void(0);">
                                            @php
                                                $relatedCartItem = \App\Models\EstoreCart::where(
                                                    'user_id',
                                                    auth()->id(),
                                                )
                                                    ->where('product_id', $related_product->id)
                                                    ->first();
                                            @endphp
                                            {{ $relatedCartItem ? 'View Cart' : 'ADD TO CART' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>





@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('submit', '#review-form', function() {
                var formData = $(this).serialize();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('e-store.product-add-review') }}",
                    type: 'POST',
                    data: formData,
                    // processData: false,
                    // contentType: false,
                    success: function(response) {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#review-form')[0].reset();
                            $('#show-review').html(response.view);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.text-danger').html('');
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            if (key.includes('.')) {
                                var fieldName = key.split('.');
                                var fieldName = fieldName[0];
                                var num = key.match(/\d+/)[0];
                                console.log(value[0]);
                                toastr.error(value[0]);
                            } else {
                                toastr.error(value[0]);
                            }
                        });

                    }
                });
            });
        });

        // on page load get-warehouse-product-details
        $(document).ready(function() {
            console.log("Document ready - Initializing product details");
            var productType = "{{ $product->product_type }}";
            console.log("Product type:", productType);

            if (productType === 'variable') {
                var selectedSize = $(".product-select-size-input:checked").val();
                var selectedColor = $(".product-select-color-input:checked").val();
                console.log("Initial selection - Size:", selectedSize, "Color:", selectedColor);

                if (selectedSize || selectedColor) {
                    getProductVariationDetails(selectedColor, selectedSize);
                }
            }
        });

        // Function to get product variation details
        function getProductVariationDetails(colorId, sizeId) {
            console.log("Fetching variation details for color:", colorId, "size:", sizeId);
            $.ajax({
                url: "{{ route('e-store.get-warehouse-product-details') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: "{{ $product->id }}",
                    size_id: sizeId,
                    color_id: colorId
                },
                success: function(response) {
                    if (response.status == true) {
                        console.log("Product details:", response.data);

                        // Update SKU, price and product ID
                        $("#product-sku").text("SKU: " + response.data.sku);
                        $("#product-price, #right-product-price").text(response.data.price);

                        // Update hidden fields
                        $("#product-variation-id").val(response.data.id);

                        // Update quantity max
                        $(".product-qty").attr("max", response.data.quantity);

                        // Update stock status
                        if (response.data.quantity > 0) {
                            $("#product-stock-status").html('<span class="text-success">In stock</span>');
                            $("#add-to-cart-btn").prop("disabled", false);
                        } else {
                            $("#product-stock-status").html('<span class="text-danger">Out of stock</span>');
                            $("#add-to-cart-btn").prop("disabled", true);
                        }

                        // Update product images if available
                        if (response.data.images && response.data.images.length > 0) {
                            updateProductImages(response.data.images);
                        }
                    } else {
                        console.error("Error:", response.message);
                        toastr.error(response.message);

                        // Update stock status
                        $("#product-stock-status").html('<span class="text-warning">' + response.message +
                            '</span>');
                        $("#add-to-cart-btn").prop("disabled", true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    toastr.error("An error occurred while fetching product details.");
                }
            });
        }

        // Function to update product images
        function updateProductImages(images) {
            console.log("Updating product images with:", images);

            var mainImage = document.getElementById('mainImage');
            var thumbnailsContainer = $('#product-thumbnails');
            thumbnailsContainer.empty();

            // Set main image to first image
            if (images && images.length > 0) {
                mainImage.src = "{{ asset('storage') }}/" + images[0];

                // Add all images as thumbnails
                images.forEach(function(image) {
                    var imgUrl = "{{ asset('storage') }}/" + image;
                    var thumbnailImg = $('<img>').attr('src', imgUrl).attr('onclick', 'changeImage(this)');
                    thumbnailsContainer.append(thumbnailImg);
                });

                // Reinitialize zoom functionality if needed
                initZoom();
            }
        }

        // Handle color selection change
        $(document).ready(function() {
            $(document).on("change", ".product-select-color-input", function() {
                var selectedColor = $(this).val();
                var colorName = $(this).data('color-name');
                console.log("Color changed to:", selectedColor, colorName);

                // Update selected color name display
                $('#selected-color-name').text(colorName);

                // Get available sizes for this color
                updateAvailableSizesForColor(selectedColor);
            });

            // Handle size selection change
            $(document).on("change", ".product-select-size-input", function() {
                var selectedSize = $(this).val();
                var selectedColor = $(".product-select-color-input:checked").val();
                console.log("Size changed to:", selectedSize, "with color:", selectedColor);

                // Get product details for this color and size combination
                getProductVariationDetails(selectedColor, selectedSize);
            });
        });

        // Function to update available sizes for a color
        function updateAvailableSizesForColor(colorId) {
            // Get the variations data from the server-side
            var variationsData = @json($variations ?? []);
            var sizesContainer = $('#product-sizes');
            console.log("Updating sizes for color ID:", colorId, "Variations data:", variationsData[colorId]);

            if (variationsData[colorId] && variationsData[colorId].sizes) {
                sizesContainer.empty();

                // Keep track of first available size
                var firstAvailableSize = null;
                var sizeAdded = false;

                // Add each size option for this color
                Object.entries(variationsData[colorId].sizes).forEach(function([sizeId, sizeData], index) {
                    var isOutOfStock = sizeData.warehouse_quantity <= 0;
                    var disabledAttr = isOutOfStock ? 'disabled' : '';
                    var outOfStockClass = isOutOfStock ? 'text-decoration-line-through' : '';

                    if (!isOutOfStock && !sizeAdded) {
                        firstAvailableSize = sizeId;
                        sizeAdded = true;
                    }

                    var sizeHtml = `
                        <div class="form-check form-check-inline">
                            <input class="btn-check product-select-size-input" type="radio" name="size"
                                id="size-${sizeId}" value="${sizeId}" ${index === 0 ? 'checked' : ''} ${disabledAttr}>
                            <label class="btn btn-outline-secondary ${outOfStockClass}" for="size-${sizeId}">
                                ${sizeData.size.size} ${isOutOfStock ? ' (Out of stock)' : ''}
                            </label>
                        </div>
                    `;

                    sizesContainer.append(sizeHtml);
                });

                // Select first available size and update product details
                if (firstAvailableSize) {
                    console.log("Selecting first available size:", firstAvailableSize);
                    $(`#size-${firstAvailableSize}`).prop('checked', true);
                    getProductVariationDetails(colorId, firstAvailableSize);
                } else {
                    // No sizes available, select the first one anyway
                    var firstSizeId = Object.keys(variationsData[colorId].sizes)[0];
                    if (firstSizeId) {
                        console.log("No available sizes, selecting first size:", firstSizeId);
                        $(`#size-${firstSizeId}`).prop('checked', true);
                        getProductVariationDetails(colorId, firstSizeId);
                    }
                }
            }
        }

        // Add to Cart button functionality
        $(document).ready(function() {
            $(document).on("click", "#add-to-cart-btn", function() {
                var productType = "{{ $product->product_type }}";
                var productId = {{ $product->id }};
                var quantity = $(".product-qty").val();
                var warehouseId = $("#warehouse-id").val();
                var data = {
                    _token: "{{ csrf_token() }}",
                    product_id: productId,
                    quantity: quantity,
                    warehouse_id: warehouseId
                };

                if (productType === 'variable') {
                    data.product_variation_id = $("#product-variation-id").val();
                    data.color_id = $(".product-select-color-input:checked").val();
                    data.size_id = $(".product-select-size-input:checked").val();
                }

                console.log("Adding to cart with data:", data);

                // Make AJAX request to add to cart
                $.ajax({
                    url: "{{ route('e-store.add-to-cart') }}",
                    type: "POST",
                    data: data,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            // Update cart counter if available
                            updateCartCount(response.quantity);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(function(error) {
                                toastr.error(error[0]);
                            });
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    }
                });
            });
        });

        // Function to update cart count in header
        function updateCartCount(count) {
            var cartCountElement = $(".cart-count");
            if (cartCountElement.length > 0) {
                cartCountElement.text(count);
            }
        }

        // Handle quantity change
        $(document).on("change", ".product-qty", function() {
            var max = parseInt($(this).attr("max")) || 1;
            var val = parseInt($(this).val()) || 1;

            if (val < 1) {
                $(this).val(1);
            } else if (val > max) {
                $(this).val(max);
                toastr.warning("Maximum available quantity is " + max);
            }
        });

        // Initialize zoom functionality
        function initZoom() {
            var mainImg = document.getElementById('mainImage');
            var lens = document.getElementById('lens');
            var result = document.getElementById('result');

            if (mainImg && lens && result) {
                mainImg.addEventListener('mouseenter', function() {
                    lens.style.display = 'block';
                    result.style.display = 'block';
                });

                mainImg.addEventListener('mouseleave', function() {
                    lens.style.display = 'none';
                    result.style.display = 'none';
                });

                mainImg.addEventListener('mousemove', moveLens);
            }
        }

        function moveLens(e) {
            var mainImg = document.getElementById('mainImage');
            var lens = document.getElementById('lens');
            var result = document.getElementById('result');

            var pos = getCursorPos(e);
            var x = pos.x - (lens.offsetWidth / 2);
            var y = pos.y - (lens.offsetHeight / 2);

            if (x > mainImg.width - lens.offsetWidth) {
                x = mainImg.width - lens.offsetWidth;
            }
            if (x < 0) {
                x = 0;
            }
            if (y > mainImg.height - lens.offsetHeight) {
                y = mainImg.height - lens.offsetHeight;
            }
            if (y < 0) {
                y = 0;
            }

            lens.style.left = x + "px";
            lens.style.top = y + "px";

            var cx = result.offsetWidth / lens.offsetWidth;
            var cy = result.offsetHeight / lens.offsetHeight;

            result.style.backgroundImage = "url('" + mainImg.src + "')";
            result.style.backgroundSize = (mainImg.width * cx) + "px " + (mainImg.height * cy) + "px";
            result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";
        }

        function getCursorPos(e) {
            var mainImg = document.getElementById('mainImage');
            var a = mainImg.getBoundingClientRect();
            var x = e.pageX - a.left - window.pageXOffset;
            var y = e.pageY - a.top - window.pageYOffset;
            return {
                x: x,
                y: y
            };
        }

        // Initialize zoom on page load
        $(document).ready(function() {
            initZoom();

            // Handle thumbnail clicks
            window.changeImage = function(thumbnail) {
                var mainImg = document.getElementById('mainImage');
                mainImg.src = thumbnail.src;
                var result = document.getElementById('result');
                if (result) {
                    result.style.backgroundImage = "url('" + mainImg.src + "')";
                }
            };
        });
    </script>
@endpush
