@extends('ecom.layouts.master')
@section('title')
    Lion Roaring Ecom | Home
@endsection

@push('styles')
@endpush

@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <section class="banner__slider banner_sec middle_arrow">
        <div class="slider stick-dots">
            <div class="slide">
                <div class="slide__img">
                    {{-- <img src="{{ asset('ecom_assets/images/banner_big_img.jpg') }}"
                        alt="banner" /> --}}
                    <img src="{{ isset($content['banner_image']) ? Storage::url($content['banner_image']) : asset('ecom_assets/images/lion_estore_banner.jpg') }}"
                        alt="banner" />
                </div>
                <div class="slide_small_img">
                    <img src="{{ asset('ecom_assets/images/small.jpg') }}" alt="banner" />
                </div>
                <div class="slide__content slide__content__left">
                    <div class="slide__content--headings">
                        <h2 class="title"> {{ isset($content['banner_title']) ? $content['banner_title'] : '' }}</h2>
                        <p class="top-title">
                            {{ isset($content['banner_subtitle']) ? $content['banner_subtitle'] : '' }}
                        </p>
                        <a class="red_btn slidebottomleft" href="{{ route('e-store.all-products') }}"><span>Shop
                                now</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="product_catagory">
        <!--<div class="row justify-content-center">-->
        <!--    <div class="col-xl-7">-->
        <!--        <div class="heading_hp text-center">-->
        <!--            <h2>{!! isset($content['product_category_title']) ? $content['product_category_title'] : '' !!}</h2>-->
        <!--            <p>-->
        <!--                {!! isset($content['product_category_subtitle']) ? $content['product_category_subtitle'] : '' !!}-->
        <!--            </p>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <div class="catagory_slider">
            @if (count($topParentCategories) > 0)
                @foreach ($topParentCategories as $category)
                    <div class="catagory_slid_padding">
                        <div class="catagory_box">
                            <div class="catagory_img">
                                <a href="{{ route($category->slug . '.e-store.page') }}"><img
                                        src="{{ Storage::url($category->image) }}" /></a>
                            </div>
                            <div class="catagory_text">
                                <a href="{{ route($category->slug . '.e-store.page') }}">{{ $category->name }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </section>

    <section class="feature_sec arrw-color bg_right_img">
        <div class="pos_zi">
            <div class="container-fluid">
                <div class="row justify-content-center align-items-center">
                    <div class="col-xl-4">
                        <div class="heading_hp pe-0 pe-lg-5">
                            <h2>{!! isset($content['featured_product_title']) ? $content['featured_product_title'] : '' !!}</h2>
                            <p>
                                {!! isset($content['featured_product_subtitle']) ? $content['featured_product_subtitle'] : '' !!}
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="featured_slider">
                            @if (count($feature_products) > 0)
                                @foreach ($feature_products as $product)
                                    <div class="feature_slid_padding">
                                        <div class="feature_box">
                                            <div class="feature_img">
                                                <div class="wishlist_icon" data-id="{{ $product->id }}">
                                                    <a href="javascript:void(0);"><i
                                                            class="fa-solid fa-heart {{ $product->isInWishlist() ? 'text-danger' : '' }}"></i></a>
                                                </div>
                                                <a href="{{ route('e-store.product-details', $product->slug) }}">
                                                    @if (isset($product->main_image) && $product->main_image != null)
                                                        <img src="{{ Storage::url($product->main_image) }}"
                                                            alt="{{ $product->main_image }}">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="feature_text">

                                                <a
                                                    href="{{ route('e-store.product-details', $product->slug) }}">{{ $product->name }}</a>
                                                <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                                </p>
                                                <div class="d-flex justify-content-between">
                                                    <span class="price_text">$ {{ $product->price }}</span>
                                                    <ul class="star_ul">
                                                        @if (Helper::getTotalProductRating($product->id))
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <li><i
                                                                        class="fa-{{ $i <= Helper::getTotalProductRating($product->id) ? 'solid' : 'regular' }} fa-star"></i>
                                                                </li>
                                                            @endfor
                                                        @else
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                        @endif

                                                        <li>({{ Helper::getRatingCount($product->id) ? Helper::getRatingCount($product->id) : 0 }})
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                            {{-- <div class="addtocart" data-id="{{ $product->id }}">
                                        <a href="javascript:void(0);">
                                            {{ $product['button_name'] ? $product['button_name'] : 'ADD TO CART' }}</a>
                                    </div> --}}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="new_arr">
        <div class="container-fluid">
            <div class="new_arr_box">
                <div class="new_arr_bg"><img src="{{ asset('ecom_assets/images/new_arr_bg.png') }}" alt="banner" /></div>
                <div class="new_arr_text">
                    <div class="heading_hp">
                        <h2>New Arrival</h2>
                        <p>
                            {!! isset($content['featured_product_subtitle']) ? $content['featured_product_subtitle'] : '' !!}
                        </p>
                        <a href="{{ route('e-store.all-products') }}" class="red_btn"><span>Shop Now</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature_sec arrw-color">
        <div class="pos_zi">
            <div class="container-fluid">
                <div class="row justify-content-center align-items-center">
                    <div class="col-xl-8">
                        <div class="featured_slider">
                            @if (count($new_products) > 0)
                                @foreach ($new_products as $product)
                                    <div class="feature_slid_padding">
                                        <div class="feature_box">
                                            <div class="feature_img">
                                                <div class="wishlist_icon" data-id="{{ $product->id }}">
                                                    <a href="javascript:void(0);"><i
                                                            class="fa-solid fa-heart {{ $product->isInWishlist() ? 'text-danger' : '' }}"></i></a>
                                                </div>
                                                <a href="{{ route('e-store.product-details', $product->slug) }}">
                                                    @if (isset($product->main_image) && $product->main_image != null)
                                                        <img src="{{ Storage::url($product->main_image) }}"
                                                            alt="{{ $product->main_image }}">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="feature_text">

                                                <a
                                                    href="{{ route('e-store.product-details', $product->slug) }}">{{ $product->name }}</a>
                                                <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                                </p>
                                                <div class="d-flex justify-content-between">
                                                    <span class="price_text">${{ $product->price }}</span>
                                                    <ul class="star_ul">
                                                        @if (Helper::getTotalProductRating($product->id))
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <li><i
                                                                        class="fa-{{ $i <= Helper::getTotalProductRating($product->id) ? 'solid' : 'regular' }} fa-star"></i>
                                                                </li>
                                                            @endfor
                                                        @else
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                        @endif

                                                        <li>({{ Helper::getRatingCount($product->id) ? Helper::getRatingCount($product->id) : 0 }})
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            {{-- <div class="addtocart" data-id="{{ $product->id }}">
                                        <a
                                            href="javascript:void(0);">{{ $product['button_name'] ? $product['button_name'] : 'ADD TO CART' }}</a>
                                    </div> --}}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="heading_hp ps-0 ps-lg-5">
                            <h2>{!! isset($content['new_product_title']) ? $content['new_product_title'] : '' !!}</h2>
                            <p>
                                {!! isset($content['new_product_subtitle']) ? $content['new_product_subtitle'] : '' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <section class="feature_sec">
        <div class="pos_zi">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7">
                        <div class="heading_hp text-center">
                            <h2>Best in books</h2>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="featured_slider">
                    @if (count($books) > 0)
                        @foreach ($books as $product)
                            <div class="feature_slid_padding">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <div class="wishlist_icon">
                                            <a href="javascript:void(0);"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <a href="{{ route('e-store.product-details', $product->slug) }}">
                                            @if (isset($product->main_image) && $product->main_image != null)
                                                <img src="{{ Storage::url($product->main_image) }}"
                                                    alt="{{ $product->main_image }}">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="feature_text">
                                        <ul class="star_ul">
                                            @if (Helper::getTotalProductRating($product->id))
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <li><i
                                                            class="fa-{{ $i <= Helper::getTotalProductRating($product->id) ? 'solid' : 'regular' }} fa-star"></i>
                                                    </li>
                                                @endfor
                                            @else
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                            @endif

                                            <li>({{ Helper::getRatingCount($product->id) ? Helper::getRatingCount($product->id) : 0 }})
                                            </li>
                                        </ul>
                                        <a href="{{ route('e-store.product-details', $product->slug) }}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">${{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart" data-id="{{ $product->id }}">
                                        <a href="javascript:void(0);">ADD TO CART</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="feature_sec back_gb lockets">
        <div class="pos_zi">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7">
                        <div class="heading_hp text-center text_white">
                            <h2>Best in lockets</h2>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="featured_slider">
                    @if (count($lockets) > 0)
                        @foreach ($lockets as $product)
                            <div class="feature_slid_padding">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <div class="wishlist_icon">
                                            <a href="javascript:void(0);"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <a href="{{ route('e-store.product-details', $product->slug) }}">
                                            @if (isset($product->main_image) && $product->main_image != null)
                                                <img src="{{ Storage::url($product->main_image) }}"
                                                    alt="{{ $product->main_image }}">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="feature_text">
                                        <ul class="star_ul">
                                            @if (Helper::getTotalProductRating($product->id))
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <li><i
                                                            class="fa-{{ $i <= Helper::getTotalProductRating($product->id) ? 'solid' : 'regular' }} fa-star"></i>
                                                    </li>
                                                @endfor
                                            @else
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                                <li><i class="fa-regular fa-star"></i></li>
                                            @endif

                                            <li>({{ Helper::getRatingCount($product->id) ? Helper::getRatingCount($product->id) : 0 }})
                                            </li>
                                        </ul>
                                        <a href="{{ route('e-store.product-details', $product->slug) }}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">${{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart" data-id="{{ $product->id }}">
                                        <a href="javascript:void(0);">ADD TO CART</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section> --}}



@endsection

@push('scripts')
@endpush
