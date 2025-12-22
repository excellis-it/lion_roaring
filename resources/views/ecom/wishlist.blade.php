@extends('ecom.layouts.master')
@section('title', 'Wishlist')

@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('wishlist') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Wishlist</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shopping_cart_sec wishlist_cart_sec">
        <div class="container">
            <div class="heading_hp mb-3">
                {{-- <h2>My wishlist</h2> --}}
            </div>
            <div class="row justify-content-center">
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
                                    <a href="{{ route('e-store') }}" class="red_btn"><span>Continue shopping</span></a>

                                    @if (!auth()->check())
                                        <a href="javascript:void(0);" data-bs-toggle="modal"
                                            data-bs-target="#loginModalEstore" class="btn btn-primary px-4">Login to view
                                            wishlist</a>
                                    @else
                                        <a href="{{ route('e-store') }}" class="red_btn"><span>Browse
                                                products</span></a>
                                    @endif
                                </div>

                                <p class="small text-muted mb-0">Tip: Click the heart on any product to add it to your
                                    wishlist.</p>
                            </div>
                        @endif

                        @foreach ($wishlistItems as $item)
                            <div class="cart_product wishlist-item">
                                <div class="img-with-name">
                                    <div class="cart_images">
                                        <img src="{{ Storage::url($item->product->main_image) }}" alt="" />
                                    </div>
                                    <div>
                                        <h4>{{ $item->product->name }}</h4>
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
                                        {{-- <p class="expeted stock">In Stock</p> --}}
                                    </div>
                                    <div class="right_p_text">
                                        <h4><span>Price : </span>$ {{ $item->product->price }}</h4>
                                    </div>

                                </div>


                                <div class="cart_text">
                                    <div class="wish_head">
                                        <!-- <div class=""> -->
                                        <a href="{{ route('e-store.product-details', $item->product->slug) }}"
                                            class="red_btn red_btn_wish"><span>View
                                                Details</span></a>
                                        <!-- </div> -->
                                        <!-- <div class=""> -->
                                        <a href="" class="add_wishlist remove-from-wishlist"
                                            data-id="{{ $item->product->id }}"><i class="fa-solid fa-trash"></i></a>
                                        <!-- </div> -->
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
