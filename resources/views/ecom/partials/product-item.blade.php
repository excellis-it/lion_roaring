@php
use App\Helpers\Helper;
@endphp
@if (count($products) > 0)

@foreach ($products as $product)
    <div class="col-xl-3 col-lg-4 col-md-6 mb-5 productitem">
        <div class="feature_box">
            <div class="feature_img">
                <div class="wishlist_icon">
                    <a href="javascript:void(0);"><i class="fa-solid fa-heart"></i></a>
                </div>
                <a href="{{$product['affiliate_link']}}">
                    @if (isset($product['image']['image']) && $product['image']['image'] != null)
                        <img src="{{ Storage::url($product['image']['image']) }}"
                            alt="{{ $product['image']['image'] }}">
                    @endif
                </a>
            </div>
            <div class="feature_text">
                <ul class="star_ul">
                    @if (Helper::getTotalProductRating($product['id']))
                    @for ($i = 1; $i <= 5; $i++)
                        <li><i class="fa-{{ $i <= Helper::getTotalProductRating($product['id']) ? 'solid' : 'regular' }} fa-star"></i>
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
                <a href="{{$product['affiliate_link']}}">{{ $product['name'] }}</a>
                <p>{{ strlen($product['short_description']) > 50 ? substr($product['short_description'], 0, 50) . '...' : $product['short_description'] }}
                </p>
                {{-- <span class="price_text">${{ $product['price'] }}</span> --}}
            </div>
            <div class="addtocart">
                <a href="{{ $product['affiliate_link'] }}">go to shop</a>
            </div>
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
