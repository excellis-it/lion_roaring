@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($product->meta_description) ? $product->meta_description : '' }}">
@endsection
@section('title')
    {{ isset($product->meta_title) ? $product->meta_title : $product->name }}
@endsection

@push('styles')
@endpush

@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
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


    <section class="catagory_sec">
        <div class="container my-5">
            <div class="row details-snippet1">
                <div class="col-md-5">
                    <div class="slider_left">
                        <div class="slider-for">
                            @if ($product->images->count() > 0)
                                @foreach ($product->images as $image)
                                    <div class="slid_big_img">
                                        <img src="{{ Storage::url($image->image) }}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="slider-nav">
                            @if ($product->images->count() > 0)
                                @foreach ($product->images as $image)
                                    <div class="small_box_img">
                                        <div class="slid_small_img">
                                            <img src="{{ Storage::url($image->image) }}" />
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="ratings my-2">
                        <div class="stars d-flex">
                            {{ Helper::getTotalProductRating($product->id) ? Helper::getTotalProductRating($product->id) : 0 }}
                            <div class="mx-2"> <i class="fa-solid fa-star"></i> </div>
                            ({{ Helper::getRatingCount($product->id) ? Helper::getRatingCount($product->id) : 0 }})
                        </div>
                    </div>
                    <div class="title">{{ $product->name }}</div>
                    <div class="brief-description">
                        {{ $product->short_description }}
                    </div>
                    <div class="price my-2">${{ $product->price }}</div>
                    <div class="theme-text subtitle">Description:</div>
                    <div class="brief-description">
                        {!! $product->description !!}
                    </div>
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="small_number mb-3">
                            <div class="qty-input">
                                <button class="qty-count qty-count--minus" data-action="minus" type="button">-</button>
                                <input class="product-qty" type="number" name="product-qty" min="0" max="10"
                                    value="{{ $cartItem ? $cartItem->quantity : 0 }}"
                                    data-cart-id="{{ $cartItem ? $cartItem->id : '' }}"
                                    data-product-id="{{ $product->id }}">
                                <button class="qty-count qty-count--add" data-action="add" type="button">+</button>
                            </div>
                        </div>
                    </div>

                    @if ($cartItem)
                        <div class="view-cart-btn">
                            <a href="{{ route('user.profile') }}" class="red_btn w-100 text-center"><span>View
                                    Cart</span></a>
                        </div>
                    @else
                        <div class="addtocart" data-id="{{ $product->id }}">
                            <a href="javascript:void(0);" class="red_btn w-100 text-center"><span>Add to Cart</span></a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="container my-5">
            <div class="additional-details my-5 text-left">
                <!-- Nav pills -->
                <ul class="nav nav-tabs justify-content-start">
                    <li class="nav-tabs">
                        <a class="nav-link active" data-toggle="tab" data-bs-toggle="tab" href="#home">Specifications</a>
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
                                    @include('ecom.partials.product-review', ['reviews' => $reviews])
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
                                            <a href="javascript:void(0);"><i class="fa-solid fa-heart {{ $product->isInWishlist() ? 'text-danger' : '' }}"></i></a>
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
    </script>
@endpush
