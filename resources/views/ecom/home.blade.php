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
            @if (isset($sliderData) && count($sliderData) > 0)
                @foreach ($sliderData as $slide)
                    <div class="slide">
                        <div class="slide__img">
                            @if (isset($slide['image']) && $slide['image'])
                                <img src="{{ Storage::url($slide['image']) }}" alt="{{ $slide['title'] ?? 'Banner' }}" />
                            @else
                                <img src="{{ asset('ecom_assets/images/banner_big.jpg') }}" alt="banner" />
                            @endif
                        </div>
                        <div class="slide__content slide__content__left">
                            <div class="slide__content--headings">
                                <h2 class="title">{{ $slide['title'] ?? 'Experience Luxury. Shop with Excellence.' }}</h2>
                                <p class="top-title">
                                    {{ $slide['subtitle'] ?? (isset($content['banner_subtitle']) ? $content['banner_subtitle'] : '') }}
                                </p>
                                <a class="red_btn slidebottomleft" href="{{ $slide['link'] ?? '#' }}" target="_blank">
                                    <span>{{ $slide['button'] ?? 'visit' }} <i class="fa-solid fa-arrow-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Default slides if no dynamic slider data -->
                <div class="slide">
                    <div class="slide__img">
                        <img src="{{ asset('ecom_assets/images/banner_big.jpg') }}" alt="banner" />
                    </div>
                    <div class="slide__content slide__content__left">
                        <div class="slide__content--headings">
                            <h2 class="title">Experience Luxury. Shop with Excellence.</h2>
                            <p class="top-title">
                                {{ isset($content['banner_subtitle']) ? $content['banner_subtitle'] : '' }}
                            </p>
                            <a class="red_btn slidebottomleft" href="{{ route('e-store.all-products') }}">
                                <span>Shop now <i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>


    <section class="product_catagory arrw-color">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-7">
                    <div class="heading_hp text-center mb-3">
                        <h2 class="text-white">{!! isset($content['product_category_title']) ? $content['product_category_title'] : '' !!}</h2>
                        <p class="text-white">
                            {!! isset($content['product_category_subtitle']) ? $content['product_category_subtitle'] : '' !!}
                        </p>
                    </div>
                </div>
            </div>
            <div class="catagory-slider-wrepper">
                <div class="catagory_slider">
                    @if (count($topParentCategories) > 0)
                        @foreach ($topParentCategories as $category)
                            <div class="catagory_slid_padding">
                                <div class="catagory_box">
                                    <div class="catagory_img">
                                        <a href="{{ route($category->slug . '.page') }}"><img
                                                src="{{ Storage::url($category->image) }}" /></a>
                                    </div>
                                    <div class="catagory_text">
                                        <a href="{{ route($category->slug . '.page') }}">{{ $category->name }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </section>

    <section class="feature_sec arrw-color bg_right_img">
        <div class="container-fluid">
            <div class="pos_zi">
                <!--<div class="container">-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="heading_hp pe-0 pe-lg-5">
                            <h2>{!! isset($content['featured_product_title']) ? $content['featured_product_title'] : '' !!}</h2>
                            <p>
                                {!! isset($content['featured_product_subtitle']) ? $content['featured_product_subtitle'] : '' !!}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">
                    <div class="col-xl-12">
                        <div class="featured_slider">
                            @if (count($feature_products) > 0)
                                @foreach ($feature_products as $product)
                                    <div class="feature_slid_padding">
                                        <div class="feature_box">
                                            <div class="feature_img">
                                                @if (($product['sale_price'] ?? false) || ($product->sale_price ?? false))
                                                    <div class="sales">Sale</div>
                                                    {{-- <span>{{ round((($product['price'] - $product['sale_price']) / $product['price']) * 100) }}%OFF</span> --}}
                                                @endif
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
                                                    @if (($product['is_free'] ?? false) || ($product->is_free ?? false))
                                                        <span class="price_text"><strong>Free</strong></span>
                                                    @else
                                                        @if (($product['sale_price'] ?? false) || ($product->sale_price ?? false))
                                                            <span class="price_text">${{ $product['sale_price'] }}</span>
                                                            <span
                                                                class=" text-muted text-decoration-line-through">${{ $product['price'] }}</span>
                                                            <span></span>
                                                            <span></span>
                                                            <span></span>
                                                        @else
                                                            <span class="price_text">${{ $product['price'] }}</span>
                                                        @endif
                                                    @endif
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
                <!--</div>-->
            </div>

    </section>




    <section class="news-letter news-letter-center"
        style="background-image:url('{{ isset($content['shop_now_image']) ? Storage::url($content['shop_now_image']) : 'ecom_assets/images/banner_big.jpg' }}');">
        <div class="news ">
            <div class="container position-relative z-1">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <h2 class="news-heading text-center">{!! isset($content['shop_now_title']) ? $content['shop_now_title'] : '' !!}</h2>
                        <p class="des how-de text-center">{!! isset($content['shop_now_description']) ? $content['shop_now_description'] : '' !!}</p>
                        <div class="text-center">
                            <a class="red_btn slidebottomleft" href="{{ $content['shop_now_button_link'] ?? '#' }}"
                                target="_blank">
                                <span>{{ $content['shop_now_button_text'] ?? 'Shop Now' }} <i
                                        class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!--<section class="home-appliances-sec">-->
    <!--    <div class="container-fluid">-->
    <!--        <div class="heading_hp">-->
    <!--            <h2 class="text-white">-->
    <!--                {{ isset($content['slider_data_second_title']) ? $content['slider_data_second_title'] : '' }}</h2>-->
    <!--        </div>-->
    <!--        <div class="home-appliances-wrepper">-->
    <!--            <div class="row">-->

    <!--                @if (isset($sliderDataSecond) && count($sliderDataSecond) > 0)
    -->
    <!--                    @foreach ($sliderDataSecond as $slide)
    -->
    <!--                        <div class="col-lg-4">-->
    <!--                            <div class="h-appicent">-->
    <!--                                <img src="{{ Storage::url($slide['image']) }}"-->
    <!--                                    alt="{{ $slide['title'] ?? 'Banner' }}" />-->
    <!--                                <div class="text-box">-->
    <!--                                    <h4>{{ $slide['title'] ?? '' }}</h4>-->
    <!--                                    <h5>{{ $slide['subtitle'] ?? '' }}</h5>-->
    <!--                                    <a href="{{ $slide['link'] ?? '#' }}" target="_blank"-->
    <!--                                        class="red_btn"><span>{{ $slide['button'] ?? 'Visit' }}</span></a>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--
    @endforeach-->
    <!--
    @endif-->


    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->







    <section class="feature_sec arrw-color">
        <div class="pos_zi" style="background-color: transparent; padding:0px;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="heading_hp ps-0">
                            <h2 class="text-white">{!! isset($content['new_product_title']) ? $content['new_product_title'] : '' !!}</h2>
                            <p class="text-white">
                                {!! isset($content['new_product_subtitle']) ? $content['new_product_subtitle'] : '' !!}
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">
                    <div class="col-xl-12">
                        <div class="featured_slider_two">
                            @if (count($new_products) > 0)
                                @foreach ($new_products as $product)
                                    <div class="feature_slid_padding">
                                        <div class="feature_box">
                                            <div class="feature_img">
                                                @if (($product['sale_price'] ?? false) || ($product->sale_price ?? false))
                                                    <div class="sales">Sale</div>
                                                    {{-- <span>{{ round((($product['price'] - $product['sale_price']) / $product['price']) * 100) }}%OFF</span> --}}
                                                @endif
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
                                                    @if (($product['is_free'] ?? false) || ($product->is_free ?? false))
                                                        <span class="price_text"><strong>Free</strong></span>
                                                    @else
                                                        @if (($product['sale_price'] ?? false) || ($product->sale_price ?? false))
                                                            <span class="price_text">${{ $product['sale_price'] }}</span>
                                                            <span
                                                                class=" text-muted text-decoration-line-through">${{ $product['price'] }}</span>
                                                            <span></span>
                                                            <span></span>
                                                            <span></span>
                                                        @else
                                                            <span class="price_text">${{ $product['price'] }}</span>
                                                        @endif
                                                    @endif
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

                </div>
            </div>
        </div>
    </section>



    <section class="small-card-sec mb-5">
        <div class="container-fluid">
            <div class="heading_hp">
                <h2 class="text-white">{{ isset($content['about_section_title']) ? $content['about_section_title'] : '' }}
                </h2>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="small-img">
                        <img src="{{ Storage::url($content['about_section_image'] ?? '') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="long-card">
                        <div class="center-box">
                            <h4>{{ $content['about_section_text_one_title'] ?? '' }}</h4>
                            <p>{{ $content['about_section_text_one_content'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="long-card">
                        <div class="center-box">
                            <h4>{{ $content['about_section_text_two_title'] ?? '' }}</h4>
                            <p>{{ $content['about_section_text_two_content'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="long-card">
                        <div class="center-box">
                            <h4>{{ $content['about_section_text_three_title'] ?? '' }}</h4>
                            <p>{{ $content['about_section_text_three_content'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>





@endsection

@push('scripts')
@endpush
