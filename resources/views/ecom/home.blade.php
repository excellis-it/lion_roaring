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
                    <img src="{{ asset('ecom_assets/images/banner.jpg') }}" alt="" data-lazy="" class="full-image" />
                </div>
                <div class="slide__content slide__content__left">
                    <div class="slide__content--headings text-center">
                        <h2 class="title">Shop with us</h2>
                        <p class="top-title">Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc
                            adipiscing
                            volutpat consectetur id purus rhoncus. </p>
                        <!-- <a class="red_btn slidebottomleft" href="javascript:void(0);"><span>order now</span></a> -->
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="feature_sec product_catagory">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7">
                    <div class="heading_hp text-center">
                        <h2>product categories</h2>
                        <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing
                            volutpat consectetur
                            id purus rhoncus.</p>
                    </div>
                </div>
            </div>
            <div class="featured_slider">
                @if (count($categories) > 0)
                    @foreach ($categories as $category)
                        <div class="feature_slid_padding">
                            <div class="feature_box">
                                <div class="feature_img">
                                    <a href="{{ route($category->slug . '.page') }}"><img
                                            src="{{ Storage::url($category->image) }}" /></a>
                                </div>
                                <div class="feature_text">
                                    <a href="{{ route($category->slug . '.page') }}">{{ $category->name }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                {{-- <div class="feature_slid_padding">
                <div class="feature_box">
                    <div class="feature_img">
                        <a href="javascript:void(0);"><img src="{{asset('ecom_assets/images/product.jpg')}}" /></a>
                    </div>
                    <div class="feature_text">
                        <a href="javascript:void(0);">Books</a>
                    </div>
                </div>
            </div>
            <div class="feature_slid_padding">
                <div class="feature_box">
                    <div class="feature_img">
                        <a href="javascript:void(0);"><img src="{{asset('ecom_assets/images/product1.jpg')}}" /></a>
                    </div>
                    <div class="feature_text">
                        <a href="javascript:void(0);">Lockets</a>
                    </div>
                </div>
            </div>
            <div class="feature_slid_padding">
                <div class="feature_box">
                    <div class="feature_img">
                        <a href="javascript:void(0);"><img src="{{asset('ecom_assets/images/product2.jpg')}}" /></a>
                    </div>
                    <div class="feature_text">
                        <a href="javascript:void(0);">photo frame</a>
                    </div>
                </div>
            </div>
            <div class="feature_slid_padding">
                <div class="feature_box">
                    <div class="feature_img">
                        <a href="javascript:void(0);"><img src="{{asset('ecom_assets/images/product3.jpg')}}" /></a>
                    </div>
                    <div class="feature_text">
                        <a href="javascript:void(0);">showpiece</a>
                    </div>
                </div>
            </div> --}}
            </div>
        </div>
    </section>

    <section class="feature_sec">
        <div class="pos_zi">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7">
                        <div class="heading_hp text-center">
                            <h2>Featured products</h2>
                            <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing
                                volutpat consectetur id purus rhoncus.</p>
                        </div>
                    </div>
                </div>
                <div class="featured_slider">
                    @if (count($feature_products) > 0)
                        @foreach ($feature_products as $product)
                            <div class="feature_slid_padding">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <div class="wishlist_icon">
                                            <a href="javascript:void(0);"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <a href="{{ $product['affiliate_link'] }}">
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
                                        <a href="{{ $product['affiliate_link'] }}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">$ {{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart">
                                        <a href="{{ $product['affiliate_link'] }}"> go to shop</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="feature_sec back_gb">
        <div class="pos_zi">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7">
                        <div class="heading_hp text-center">
                            <h2>Newly added</h2>
                            <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing
                                volutpat consectetur id purus rhoncus.</p>
                        </div>
                    </div>
                </div>
                <div class="featured_slider">
                    @if (count($new_products) > 0)
                        @foreach ($new_products as $product)
                            <div class="feature_slid_padding">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <div class="wishlist_icon">
                                            <a href="javascript:void(0);"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <a href="{{ $product['affiliate_link'] }}">
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
                                        <a href="{{ $product['affiliate_link'] }}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">${{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart">
                                        <a href="{{ $product['affiliate_link'] }}">go to shop</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

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
                            <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing
                                volutpat consectetur id purus rhoncus.</p>
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
                                        <a href="{{ $product['affiliate_link'] }}">
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
                                        <a href="{{ $product['affiliate_link'] }}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">${{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart">
                                        <a href="{{ $product['affiliate_link'] }}">go to shop</a>
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
                            <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing
                                volutpat consectetur id purus rhoncus.</p>
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
                                        <a href="{{ $product['affiliate_link'] }}">
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
                                        <a href="{{ $product['affiliate_link'] }}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">${{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart">
                                        <a href="{{ $product['affiliate_link'] }}">go to shop</a>
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
