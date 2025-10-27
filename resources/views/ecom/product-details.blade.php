{{-- {{  $product->variation_unique_color_first_images; }} --}}
{{-- {{dd(auth()->user())}} --}}
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
        style="background-image: url({{ isset($product->background_image) && $product->background_image ? (\Illuminate\Support\Str::startsWith($product->background_image, 'http') ? $product->background_image : (\Illuminate\Support\Str::startsWith($product->background_image, 'storage/') ? asset($product->background_image) : Storage::url($product->background_image))) : \App\Helpers\Helper::estorePageBannerUrl('product-details') }}); background-position: center; background-repeat: no-repeat; background-size: cover">

        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Product Details</h2>
                        <p></p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="catagory_sec product_details common-padd">
        <div class="container">
            <div class="row details-snippet1 justify-content-between">
                <div class="col-md-5" id="product-images-section">
                    <div class="slider_left">
                        <div class="slider-for" id="prodcut-first-image">
                            @if ($product->images->count() > 0)
                                @foreach ($product->images as $image)
                                    <div class="slid_big_img">
                                        <img src="{{ Storage::url($image->image) }}"
                                            onerror="this.onerror=null; this.src='{{ asset('ecom_assets/images/no-image.png') }}';" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="slider-nav" id="product-other-images">
                            @if ($product->images->count() > 0)
                                @foreach ($product->images as $image)
                                    <div class="small_box_img">
                                        <div class="slid_small_img">
                                            <img src="{{ Storage::url($image->image) }}"
                                                onerror="this.onerror=null; this.src='{{ asset('ecom_assets/images/no-image.png') }}';" />
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-details-main-div">
                        <div class="ratings my-2">
                            <div class="stars d-flex">
                                {{ Helper::getTotalProductRating($product->id) ? Helper::getTotalProductRating($product->id) : 0 }}
                                <div class="mx-2"> <i class="fa-solid fa-star"></i> </div>
                                ({{ Helper::getRatingCount($product->id) ? Helper::getRatingCount($product->id) : 0 }})
                            </div>
                        </div>

                        <div class="title">{{ $product->name }}</div>
                        <div class="product-category mt-2 mb-2">
                            @php
                                $categoryPath = [];
                                $currentCategory = $product->category;
                                while ($currentCategory) {
                                    array_unshift($categoryPath, $currentCategory->name);
                                    $currentCategory = $currentCategory->parent;
                                }
                            @endphp
                            Category: {{ implode(' > ', $categoryPath) }}
                        </div>
                        <div class="brief-description">
                            {{ $product->short_description }}
                        </div>
                        <div class="price my-2 warehouse-product-price-div">
                            @if ($product->is_free ?? false)
                                <span class="badge bg-success">FREE</span>
                            @else
                                $<span
                                    id="warehouse-product-price">{{ $wareHouseHaveProductVariables?->price ?? '' }}</span>
                            @endif
                        </div>
                        <div class=" mb-2">
                            <div class="theme-text subtitle">Description:</div>
                            <div class="subtitle p-descrition-text">
                                {!! $product->description !!}
                            </div>
                        </div>

                        <div class="d-flex mb-2">
                            <div class="theme-text subtitle">Warehouse:</div>
                            <div class="subtitle ms-2">
                                {{ $wareHouseHaveProductVariables?->warehouse?->name ?? '' }}
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="theme-text subtitle">SKU:</div>
                            <div class="subtitle ms-2" id="product-sku">
                                {{ $wareHouseHaveProductVariables?->sku ?? '' }}
                            </div>
                        </div>

                        <input id="warehouse-product-id" type="hidden"
                            value="{{ $wareHouseHaveProductVariables?->id }}" />
                        <input id="product-variation-id" type="hidden"
                            value="{{ $wareHouseHaveProductVariables?->product_variation_id }}" />
                        {{-- Select Size radio input button $product->sizes --}}


                        <div class="mb-3">
                            {{-- Select Size radio input button $product->sizes --}}
                            @if ($product->sizes->count() > 0)
                                <p>Select Size:</p>
                                @foreach ($product->sizes as $key => $size)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input product-select-size-input" type="radio"
                                            name="size" id="size-{{ $size->size?->id }}"
                                            value="{{ $size->size?->id }}">
                                        <label class="form-check-label" for="size-{{ $size->size?->id }}">
                                            {{ $size->size?->size }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif

                        </div>

                        <div class="mb-3">
                            @if ($product->product_type != 'simple')

                                @if ($product->variation_unique_color_first_images->count() > 0)
                                    <p class="theme-text subtitle">Selected Color: <span id="selected-color"
                                            class="text-dark ms-2"></span></p>
                                    @foreach ($product->variation_unique_color_first_images as $key => $item)
                                        @php
                                            $color = $item->color;
                                            $image_path = $item->image_path;
                                        @endphp
                                        @if ($color and $image_path)
                                            <div class="form-check form-check-inline border rounded" hidden>
                                                {{-- add class product-select-color-input --}}
                                                <input class="btn-check product-select-color-input " type="radio"
                                                    name="color" id="color-{{ $color->id }}"
                                                    value="{{ $color->id }}">
                                                <label class="btn" for="color-{{ $color->id }}">
                                                    {{ $color->color_name }}

                                                </label>
                                            </div>
                                            <img class="product-select-color-input-image"
                                                data-color-id="{{ $color->id }}"
                                                data-color-name="{{ $color->color_name ?? ($color->name ?? '') }}"
                                                style="max-width: 80px;
                                    max-height: 80px;
                                    cursor: pointer;
                                    border: 2px solid #ddd; border-radius: 5px; margin-right: 10px;
                                    opacity: 1;
                                    pointer-events: auto;"
                                                src="{{ $image_path ? Storage::url($image_path) : asset('ecom_assets/images/no-image.png') }}"
                                                alt="{{ $color->color_name ?? ($color->name ?? '') }}"
                                                onerror="this.onerror=null; this.src='{{ asset('ecom_assets/images/no-image.png') }}';">
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        </div>
                        <div class="d-flex">
                            {{-- <div id="qty-div" class="me-3">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="small_number mb-3">
                                    <div class="qty-input">
                                        <button class="qty-count qty-count--minus" data-action="minus"
                                            type="button">-</button>
                                        <input class="product-qty" type="number" name="product-qty" min="0"
                                            max="{{ $wareHouseHaveProductVariables?->quantity ?? 0 }}" value="0"
                                            data-product-id="{{ $product->id }}">
                                        <button class="qty-count qty-count--add" data-action="add" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                            {{-- hidden div for out of stock message badge --}}
                            <div id="out-of-stock-message" class="text-danger me-2" style="display: none;">
                                <span class="h5">Out of Stock</span>
                            </div>

                            {{-- Select option dropdown for quantity --}}
                            <div class="me-3" id="qty-div">
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="small_number mb-3">
                                        <div class="d-flex ">
                                            <label for="product-qty" class="form-label me-2">Qty:</label>
                                            {{-- default value 1 and max value from $wareHouseHaveProductVariables->quantity --}}
                                            {{-- if $wareHouseHaveProductVariables->quantity is 0 then hide this div --}}
                                            <select class="form-select product-qty" name="product-qty"
                                                data-product-id="{{ $product->id }}">
                                                <option value="1"> 1</option>
                                                @php
                                                    $qty = (int) ($wareHouseHaveProductVariables?->quantity ?? 0);
                                                    $max = $qty > 0 ? min($qty, 20) : 0;
                                                @endphp
                                                @for ($i = 2; $i <= $max; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="addtocartdetails cart-btns" data-id="{{ $product->id }}">
                                <a href="javascript:void(0);" class="red_btn w-100 text-center "><span>Add to
                                        Cart</span></a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
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
                                @if (auth()->check() && $product->isPurchasedByUser(auth()->id()) && !$product->isReviewedByUser(auth()->id()))
                                    {{-- Review Form --}}
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
                                @endif

                                <div id="show-review">
                                    @include('ecom.partials.product-review', ['reviews' => $reviews])
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if (count($related_products) > 0)
        <section class="feature_sec">
            <div class="container-fluid">
                <div class="pos_zi">
                    <div class="row justify-content-center">
                        <div class="col-xl-7">
                            <div class="heading_hp text-center">
                                <h2>Related products</h2>
                                <p> </p>
                            </div>
                        </div>
                    </div>
                    <div class="featured_slider">

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
                                                    alt="{{ $related_product->main_image }}"
                                                    onerror="this.onerror=null; this.src='{{ asset('ecom_assets/images/no-image.png') }}';">
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

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).on("click", ".addtocartdetails", function(e) {
            e.preventDefault();

            var $button = $(this);
            var productId = $button.data("id");
            var buttonText = $button.find("a").text().trim();

            if (buttonText === "View Cart") {
                window.location.href = window.cartRoutes.viewCart;
                return;
            }

            var quantity = 1;
            var qtyInput = $button.closest(".feature_box").find(".product-qty");
            if (qtyInput.length === 0) {
                qtyInput = $(".product-qty");
            }
            if (qtyInput.length > 0) {
                quantity = parseInt(qtyInput.val()) || 1;
            }

            var originalText = $button.find("a").text();
            $button.find("a").text("Adding...");
            $button.addClass("loading");

            var sizeId = $(".product-select-size-input:checked").val();
            var colorId = $(".product-select-color-input:checked").val();
            var productType = "{{ $product->product_type ?? 'simple' }}"; // Laravel blade variable

            // 🚨 Validation for non-simple products
            if (productType !== "simple") {
                if (!sizeId && $(".product-select-size-input").length > 0) {
                    toastr.error("Please select a size before adding to cart.");
                    $button.find("a").text(originalText);
                    $button.removeClass("loading");
                    return;
                }
                if (!colorId && $(".product-select-color-input").length > 0) {
                    toastr.error("Please select a color before adding to cart.");
                    $button.find("a").text(originalText);
                    $button.removeClass("loading");
                    return;
                }
            }

            // 🛒 Proceed with AJAX call only if validation passed
            $.ajax({
                url: window.cartRoutes.addToCart,
                type: "POST",
                data: {
                    warehouse_product_id: $("#warehouse-product-id").val(),
                    product_id: productId,
                    product_variation_id: $("#product-variation-id").val(),
                    size_id: sizeId,
                    color_id: colorId,
                    quantity: quantity,
                    _token: window.csrfToken,
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        updateCartCount();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    var errorMessage = "Something went wrong!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    if (!$button.find("a").text().includes("View Cart")) {
                        $button.find("a").text(originalText);
                    }
                    $button.removeClass("loading");
                },
            });
        });
    </script>
    <script>
        function enableZoomOnSlide(slide) {
            const img = slide.querySelector('img');
            if (!img) return;

            if (slide.dataset.zoomAttached) return;
            slide.dataset.zoomAttached = true;

            function moveZoom(e) {
                const rect = img.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                img.style.transformOrigin = `${x}% ${y}%`;
                img.style.transform = 'scale(2.2)';
            }

            function resetZoom() {
                img.style.transformOrigin = 'center center';
                img.style.transform = 'scale(1)';
            }

            slide.addEventListener('mousemove', moveZoom);
            slide.addEventListener('mouseleave', resetZoom);
        }



        function initZoom() {
            const visibleSlides = document.querySelectorAll('.slick-slide .slid_big_img');
            visibleSlides.forEach(slide => {
                const img = slide.querySelector('img');
                if (!img) return;

                if (img.complete) {
                    enableZoomOnSlide(slide);
                } else {
                    img.addEventListener('load', function() {
                        enableZoomOnSlide(slide);
                    });
                }
            });
        }
        // Flag from backend whether this product is free
        const IS_FREE_PRODUCT = {{ $product->is_free ?? false ? 'true' : 'false' }};
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
                            $("#review-form").remove();
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

        function getWareHouseProductDetails() {
            var selectedSize = $(".product-select-size-input:checked").val();
            var selectedColor = $(".product-select-color-input:checked").val();
            console.log("Selected size:", selectedSize);
            console.log("Selected color:", selectedColor);
            $.ajax({
                url: "{{ route('e-store.get-warehouse-product-details') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: "{{ $product->id }}",
                    size_id: selectedSize,
                    color_id: selectedColor
                },
                success: function(response) {
                    if (response.status == true) {
                        console.log("Warehouse product details:", response.data);

                        // response example is :
                        // {
                        //     "status": true,
                        //     "data": {
                        //         "id": 139,
                        //         "product_variation_id": 110,
                        //         "sku": "SKU-1-1-68EF5D88A74BC",
                        //         "warehouse_id": 1,
                        //         "product_id": 201,
                        //         "color_id": 1,
                        //         "size_id": 1,
                        //         "tax_rate": "0.00",
                        //         "quantity": 10,
                        //         "price": "30.00",
                        //         "before_sale_price": null,
                        //         "created_at": "2025-10-15T09:12:04.000000Z",
                        //         "updated_at": "2025-10-15T09:14:10.000000Z"
                        //     },
                        //     "productImages": [
                        //         {
                        //             "id": 5,
                        //             "product_id": 201,
                        //             "color_id": 1,
                        //             "image_path": "product_variation\/compressed_20251015083854_68ef5d9e0688f.webp",
                        //             "color_name": "Red"
                        //         },
                        //         {
                        //             "id": 7,
                        //             "product_id": 201,
                        //             "color_id": 1,
                        //             "image_path": "product_variation\/compressed_20251015083854_68ef5d9e20b08.webp",
                        //             "color_name": "Red"
                        //         },
                        //         {
                        //             "id": 8,
                        //             "product_id": 201,
                        //             "color_id": 1,
                        //             "image_path": "product_variation\/compressed_20251015083854_68ef5d9e286f2.webp",
                        //             "color_name": "Red"
                        //         }
                        //     ]
                        // }

                        $("#product-sku").text(response.data.sku);
                        // update warehouse-product-id input value
                        $("#warehouse-product-id").val(response.data.id);
                        $("#product-variation-id").val(response.data.product_variation_id);
                        $("#warehouse-product-price").text(response.data.price);
                        // Update max quantity
                        $(".product-qty").attr("max", response.data.quantity);
                        $(".product-qty").trigger("change");

                        var productImagesSection = $("#product-images-section");
                        var sliderLeft = $('<div class="slider_left"></div>');
                        var sliderFor = $('<div class="slider-for" id="prodcut-first-image"></div>');
                        var sliderNav = $('<div class="slider-nav" id="product-other-images"></div>');

                        var storageBase = "{{ rtrim(Storage::url(''), '/') }}/";
                        var noImage = "{{ asset('ecom_assets/images/no-image.png') }}";

                        var productImages = [];
                        if (Array.isArray(response.productImages) && response.productImages.length) {
                            productImages = response.productImages;
                        } else if (response.data && Array.isArray(response.data.productImages) && response.data
                            .productImages.length) {
                            productImages = response.data.productImages;
                        } else if (response.data && Array.isArray(response.data.images) && response.data.images
                            .length) {
                            productImages = response.data.images;
                        }

                        var resolveSrc = function(path) {
                            if (!path) return noImage;
                            if (/^https?:\/\//i.test(path)) return path;
                            path = path.replace(/^\/+/, "").replace(/^storage\//, "");
                            return storageBase + path;
                        };

                        if (productImages.length) {
                            productImages.forEach(function(image) {
                                var src = resolveSrc(image.image_path || image.image);
                                var altText = image.color_name || image.color || "Product image";

                                sliderFor.append(
                                    $('<div class="slid_big_img"></div>').append(
                                        $('<img>')
                                        .attr('src', src)
                                        .attr('alt', altText)
                                        .on('error', function() {
                                            $(this).attr('src', noImage);
                                        })
                                    )
                                );

                                sliderNav.append(
                                    $('<div class="small_box_img"></div>').append(
                                        $('<div class="slid_small_img"></div>').append(
                                            $('<img>')
                                            .attr('src', src)
                                            .attr('alt', altText)
                                            .on('error', function() {
                                                $(this).attr('src', noImage);
                                            })
                                        )
                                    )
                                );
                            });
                        } else {
                            sliderFor.append(
                                $('<div class="slid_big_img"></div>').append(
                                    $('<img>').attr('src', noImage).attr('alt', 'No image available')
                                )
                            );
                            sliderNav.append(
                                $('<div class="small_box_img"></div>').append(
                                    $('<div class="slid_small_img"></div>').append(
                                        $('<img>').attr('src', noImage).attr('alt', 'No image available')
                                    )
                                )
                            );
                        }

                        productImagesSection.empty().append(sliderLeft.append(sliderFor).append(sliderNav));
                        // re-init sliders if slick is available
                        if ($.fn.slick) {
                            // destroy existing instances if any
                            if ($('.slider-for').hasClass('slick-initialized')) {
                                $('.slider-for').slick('unslick');
                            }
                            if ($('.slider-nav').hasClass('slick-initialized')) {
                                $('.slider-nav').slick('unslick');
                            }

                            $('.slider-for').slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: false,
                                fade: true,
                                asNavFor: '.slider-nav'
                            });
                            $('.slider-nav').slick({
                                slidesToShow: 4,
                                slidesToScroll: 1,
                                asNavFor: '.slider-for',
                                dots: false,
                                centerMode: false,
                                focusOnSelect: true
                            });
                            initZoom();
                        }

                        // If stock quantity available; for free product ignore price check
                        if (response.data.quantity > 0 && (IS_FREE_PRODUCT || response.data.price > 0)) {
                            $("#qty-div").show();
                            $("#out-of-stock-message").hide();
                            $(".cart-btns").show();


                            // Keep price (or FREE badge) visible
                            $(".warehouse-product-price-div").show();

                        } else {
                            $("#qty-div").hide();
                            $("#out-of-stock-message").show();
                            $(".cart-btns").hide();

                            // Only hide price area if not free product
                            if (!IS_FREE_PRODUCT) {
                                $(".warehouse-product-price-div").hide();
                            }
                        }
                        initZoom();
                    } else {
                        // toastr.error(response.message);
                        $("#product-sku").text('');
                        $("#qty-div").hide();
                        $("#out-of-stock-message").show();
                        $(".cart-btns").hide();
                        if (!IS_FREE_PRODUCT) {
                            $(".warehouse-product-price-div").hide();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("An error occurred while fetching product details.");
                }
            });
        }

        // on page load get-warehouse-product-details
        $(document).ready(function() {
            // Auto-select first size if sizes exist and none selected
            var $firstSize = $(".product-select-size-input").first();
            if ($firstSize.length && $(".product-select-size-input:checked").length === 0) {
                $firstSize.prop('checked', true);
            }

            // Auto-select first color if color images / radios exist and none selected
            var $firstColorRadio = $(".product-select-color-input").first();
            if ($firstColorRadio.length && $(".product-select-color-input:checked").length === 0) {
                $firstColorRadio.prop('checked', true);
                // Also visually highlight corresponding image if present
                var colorId = $firstColorRadio.val();
                $(".product-select-color-input-image").css('border', '4px solid #ddd');
                $(".product-select-color-input-image[data-color-id='" + colorId + "']").css('border',
                    '4px solid #643171');
                var colorName = $('label[for="color-' + colorId + '"]').text().trim();
                if (colorName) {
                    $("#selected-color").text(colorName);
                }
            }

            getWareHouseProductDetails();
        });

        // on change product-select-size-input or product-select-color-input // by ajax get warehouse product details by product id with optional size and color
        $(document).on("change", ".product-select-size-input, .product-select-color-input", function() {
            getWareHouseProductDetails();
        });
    </script>

    <script>
        // initialize selected-color on load
        $(function() {
            var $checked = $(".product-select-color-input:checked").first();
            if ($checked.length) {
                var id = $checked.val();
                var name = $('label[for="color-' + id + '"]').text().trim();
                $("#selected-color").text(name);
                $('.product-select-color-input-image').css('border', '4px solid #ddd');
                $('.product-select-color-input-image[data-color-id="' + id + '"]').css('border',
                    '4px solid #643171');
            }
        });

        // click on color image -> check radio + update selected color
        $(document).on('click', '.product-select-color-input-image', function() {
            var $img = $(this);
            var colorId = $img.data('color-id');
            var colorName = $img.data('color-name') || $img.attr('alt') || '';

            var $input = $('#color-' + colorId);
            if (!$input.length) {
                // fallback: try to match by label text
                $input = $(".product-select-color-input").filter(function() {
                    return $('label[for="' + $(this).attr('id') + '"]').text().trim() === colorName;
                }).first();
            }

            if ($input.length) {
                $input.prop('checked', true).trigger('change');
                $("#selected-color").text(colorName);
                // visual highlight
                $('.product-select-color-input-image').css('border', '4px solid #ddd');
                $img.css('border', '4px solid #643171');
            }
        });

        // when radio changes (e.g. keyboard) update selected-color and highlight image
        $(document).on('change', '.product-select-color-input', function() {
            var id = $(this).val();
            var name = $('label[for="color-' + id + '"]').text().trim();
            $("#selected-color").text(name);
            $('.product-select-color-input-image').css('border', '4px solid #ddd');
            $('.product-select-color-input-image[data-color-id="' + id + '"]').css('border', '4px solid #643171');
        });
    </script>
@endpush
