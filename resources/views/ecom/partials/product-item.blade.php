@php
    use App\Helpers\Helper;
@endphp
@if (count($products) > 0)

    @foreach ($products as $product)
        <div class="col-xl-3 col-lg-4 col-md-4 mb-4 productitem">
            <div class="feature_box">
                <div class="feature_img">

                    @if (($product['is_free'] ?? false) || ($product->is_free ?? false))
                        <span class="badge bg-success position-absolute" style="top:10px;left:10px;z-index:9">FREE</span>
                    @else
                        <div class="sales">Sale</div>
                    @endif
                    <div class="wishlist_icon" data-id="{{ $product['id'] }}">
                        <a href="javascript:void(0);"><i
                                class="fa-solid fa-heart {{ $product['is_in_wishlist'] ? 'text-danger' : '' }}"></i></a>
                    </div>
                    <a href="{{ route('e-store.product-details', $product['slug']) }}">
                        @if (isset($product['image']['image']) && $product['image']['image'] != null)
                            <img src="{{ Storage::url($product['image']['image']) }}"
                                alt="{{ $product['image']['image'] }}">
                        @endif
                    </a>
                </div>
                <div class="feature_text">
                    <a href="{{ route('e-store.product-details', $product['slug']) }}">{{ $product['name'] }}</a>
                    <p>{{ strlen($product['short_description']) > 50 ? substr($product['short_description'], 0, 50) . '...' : $product['short_description'] }}
                    </p>
                    <div class="d-flex justify-content-between">
                        @if (($product['is_free'] ?? false) || ($product->is_free ?? false))
                            <span class="price_text"><strong>Free</strong></span>
                        @else
                            <span class="price_text">${{ $product['price'] }}</span>
                        @endif
                        <ul class="star_ul">
                            @if (Helper::getTotalProductRating($product['id']))
                                @for ($i = 1; $i <= 5; $i++)
                                    <li><i
                                            class="fa-{{ $i <= Helper::getTotalProductRating($product['id']) ? 'solid' : 'regular' }} fa-star"></i>
                                    </li>
                                @endfor
                            @else
                                <li><i class="fa-regular fa-star"></i></li>
                                <li><i class="fa-regular fa-star"></i></li>
                                <li><i class="fa-regular fa-star"></i></li>
                                <li><i class="fa-regular fa-star"></i></li>
                                <li><i class="fa-regular fa-star"></i></li>
                            @endif

                            <li>({{ Helper::getRatingCount($product['id']) ? Helper::getRatingCount($product['id']) : 0 }})
                            </li>
                        </ul>
                    </div>
                </div>
                {{-- <a class="red_btn w-100"
                    href="{{ route('e-store.product-details', $product['slug']) }}"><span>Details</span></a> --}}
                {{-- <div class="addtocart" data-id="{{ $product['id'] }}">
                    <a href="javascript:void(0);">
                        @php
                            $cartItem = \App\Models\EstoreCart::where('user_id', auth()->id())
                                ->where('product_id', $product['id'])
                                ->first();
                        @endphp
                        {{ $cartItem ? 'View Cart' : ($product['button_name'] ? $product['button_name'] : 'ADD TO CART') }}
                    </a>
                </div> --}}
            </div>
        </div>
    @endforeach
@else
    {{-- <div class="col-md-12">
    <div class="alert alert-danger" role="alert">
        No data found!
    </div>
</div> --}}
@endif
