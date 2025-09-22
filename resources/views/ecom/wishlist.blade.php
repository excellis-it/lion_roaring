@extends('ecom.layouts.master')
@section('title', 'Wishlist')

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
                        <h2>Wishlist</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec">
        <div class="container">
            <div class="heading_hp mb-3">
                <h2>My wishlist</h2>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="cart_item">


                        @if ($wishlistItems->isEmpty())
                            <div class="wishlist-empty text-center py-1">
                                <div class="empty-illustration mb-3">
                                    <i class="fa-regular fa-heart fa-4x text-muted"></i>
                                </div>

                                <h4 class="mb-2">Your wishlist is empty</h4>
                                <p class="text-muted mb-4">Save items you love to find them quickly later.</p>

                                <div class="d-flex justify-content-center gap-2 mb-3">
                                    <a href="{{ route('e-store') }}" class="btn btn-outline-primary px-4">Continue
                                        shopping</a>

                                    @if (!auth()->check())
                                        <a href="javascript:void(0);" data-bs-toggle="modal"
                                            data-bs-target="#loginModalEstore" class="btn btn-primary px-4">Login to view
                                            wishlist</a>
                                    @else
                                        <a href="{{ route('e-store') }}" class="btn btn-primary px-4">Browse
                                            products</a>
                                    @endif
                                </div>

                                <p class="small text-muted mb-0">Tip: Click the heart on any product to add it to your
                                    wishlist.</p>
                            </div>
                        @endif

                        @foreach ($wishlistItems as $item)
                            <div class="cart_product wishlist-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="cart_images">
                                            <img src="{{ Storage::url($item->product->main_image) }}" alt="" />
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="cart_text">
                                            <div class="wish_head">
                                                <h4>{{ $item->product->name }}</h4>
                                                <div class="">
                                                    <a href="" class="add_wishlist remove-from-wishlist"
                                                        data-id="{{ $item->product->id }}"><i
                                                            class="fa-solid fa-trash"></i></a>
                                                </div>
                                            </div>
                                            <div class="row justify-content-between align-items-center mb-3">
                                                <div class="col-md-8">
                                                    <p class="expeted"></p>
                                                    <p class="expeted stock">In Stock</p>
                                                    <ul class="star_ul">
                                                        @if (Helper::getTotalProductRating($item->product->id))
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <li><i
                                                                        class="fa-{{ $i <= Helper::getTotalProductRating($item->product->id) ? 'solid' : 'regular' }} fa-star"></i>
                                                                </li>
                                                            @endfor
                                                        @else
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                            <li><i class="fa-regular fa-star"></i></li>
                                                        @endif

                                                        <li>({{ Helper::getRatingCount($item->product->id) ? Helper::getRatingCount($item->product->id) : 0 }})
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="col-md-4 text-end">
                                                    <div class="">
                                                        <a href="{{ route('e-store.product-details', $item->product->slug) }}"
                                                            class="red_btn red_btn_wish"><span>View
                                                                Details</span></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between final_price">
                                                <div class="left_p_text">
                                                    <h4>Price</h4>
                                                </div>
                                                <div class="right_p_text">
                                                    <h4>$ {{ $item->product->price }}</h4>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="col-lg-4">
                </div>
            </div>
        </div>
    </section>

@endsection
