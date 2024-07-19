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
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Product Details</h2>
                        <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing volutpat
                            consectetur
                            id purus rhoncus.</p>
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
                            4.4 <div class="mx-2"> <i class="fa-solid fa-star"></i> </div>(21000+)
                        </div>
                    </div>
                    <div class="title">{{ $product->name }}</div>
                    <div class="brief-description">
                        {{ $product->short_description }}
                    </div>
                    <div class="price my-2">$20.30</div>
                    <div class="theme-text subtitle">Description:</div>
                    <div class="brief-description">
                        {!! $product->description !!}
                    </div>
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="small_number mb-3">
                            <div class="qty-input">
                                <button class="qty-count qty-count--minus" data-action="minus" type="button">-</button>
                                <input class="product-qty" type="number" name="product-qty" min="0" max="10"
                                    value="1">
                                <button class="qty-count qty-count--add" data-action="add" type="button">+</button>
                            </div>
                        </div>

                    </div>
                    <div class=""><a href="" class="red_btn w-100 text-center"><span> Buy Now</span></a>
                    </div>


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
                                <form id="review-form" action="index.html" method="post">
                                    <h2>Write Your Review</h2>
                                    <div class="rating rating2">
                                        <a href="#5" title="Give 5 stars">★</a>
                                        <a href="#4" title="Give 4 stars">★</a>
                                        <a href="#3" title="Give 3 stars">★</a>
                                        <a href="#2" title="Give 2 stars">★</a>
                                        <a href="#1" title="Give 1 star">★</a>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="review">Your Review:</label>
                                        <textarea class="form-control" rows="5" placeholder="Your Reivew" name="review" id="review"></textarea>
                                        <span id="reviewInfo" class="help-block pull-right">
                                            <span id="remaining">999</span> Characters remaining
                                        </span>
                                    </div>
                                    <a href="" class="red_btn mb-5"><span>Submit</span></a>
                                </form>
                                <div class="testimonial-box">
                                    <div class="box-top">
                                        <div class="profile">
                                            <div class="profile-img">
                                                <img
                                                    src="https://cdn3.iconfinder.com/data/icons/avatars-15/64/_Ninja-2-512.png" />
                                            </div>
                                            <div class="name-user">
                                                <strong>Noah Wood</strong>
                                                <span>@noahwood</span>
                                            </div>
                                        </div>
                                        <div class="reviews">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="client-comment">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat
                                            quis?
                                            Provident temporibus architecto asperiores nobis maiores nisi a. Quae doloribus
                                            ipsum aliquam
                                            tenetur voluptates incidunt blanditiis sed atque cumque.</p>
                                    </div>
                                </div>
                                <div class="testimonial-box">
                                    <div class="box-top">
                                        <div class="profile">
                                            <div class="profile-img">
                                                <img
                                                    src="https://cdn3.iconfinder.com/data/icons/avatars-15/64/_Ninja-2-512.png" />
                                            </div>
                                            <div class="name-user">
                                                <strong>Noah Wood</strong>
                                                <span>@noahwood</span>
                                            </div>
                                        </div>
                                        <div class="reviews">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="client-comment">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat
                                            quis?
                                            Provident temporibus architecto asperiores nobis maiores nisi a. Quae doloribus
                                            ipsum aliquam
                                            tenetur voluptates incidunt blanditiis sed atque cumque.</p>
                                    </div>
                                </div>
                                <div class="testimonial-box">
                                    <div class="box-top">
                                        <div class="profile">
                                            <div class="profile-img">
                                                <img
                                                    src="https://cdn3.iconfinder.com/data/icons/avatars-15/64/_Ninja-2-512.png" />
                                            </div>
                                            <div class="name-user">
                                                <strong>Noah Wood</strong>
                                                <span>@noahwood</span>
                                            </div>
                                        </div>
                                        <div class="reviews">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="client-comment">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat
                                            quis?
                                            Provident temporibus architecto asperiores nobis maiores nisi a. Quae doloribus
                                            ipsum aliquam
                                            tenetur voluptates incidunt blanditiis sed atque cumque.</p>
                                    </div>
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
                            <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing volutpat
                                consectetur id purus rhoncus.</p>
                        </div>
                    </div>
                </div>
                <div class="featured_slider">
                    <div class="feature_slid_padding">
                        <div class="feature_box">
                            <div class="feature_img">
                                <div class="wishlist_icon">
                                    <a href=""><i class="fa-solid fa-heart"></i></a>
                                </div>
                                <a href=""><img src="{{ asset('ecom_assets/images/product.jpg') }}" /></a>
                            </div>
                            <div class="feature_text">
                                <ul class="star_ul">
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li>(5)</li>
                                </ul>
                                <a href="">holy bible book</a>
                                <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien.</p>
                                <span class="price_text">$20.30</span>
                            </div>
                            <div class="addtocart">
                                <a href="">view details</a>
                            </div>
                        </div>
                    </div>
                    <div class="feature_slid_padding">
                        <div class="feature_box">
                            <div class="feature_img">
                                <div class="wishlist_icon">
                                    <a href=""><i class="fa-solid fa-heart"></i></a>
                                </div>
                                <a href=""><img src="{{ asset('ecom_assets/images/product7.jpg') }}" /></a>
                            </div>
                            <div class="feature_text">
                                <ul class="star_ul">
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li>(5)</li>
                                </ul>
                                <a href="">photo frame</a>
                                <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien.</p>
                                <span class="price_text">$20.30</span>
                            </div>
                            <div class="addtocart">
                                <a href="">view details</a>
                            </div>
                        </div>
                    </div>
                    <div class="feature_slid_padding">
                        <div class="feature_box">
                            <div class="feature_img">
                                <div class="wishlist_icon">
                                    <a href=""><i class="fa-solid fa-heart"></i></a>
                                </div>
                                <a href=""><img src="{{ asset('ecom_assets/images/product5.jpg') }}" /></a>
                            </div>
                            <div class="feature_text">
                                <ul class="star_ul">
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li>(5)</li>
                                </ul>
                                <a href="">holy bible book</a>
                                <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien.</p>
                                <span class="price_text">$20.30</span>
                            </div>
                            <div class="addtocart">
                                <a href="">view details</a>
                            </div>
                        </div>
                    </div>
                    <div class="feature_slid_padding">
                        <div class="feature_box">
                            <div class="feature_img">
                                <div class="wishlist_icon">
                                    <a href=""><i class="fa-solid fa-heart"></i></a>
                                </div>
                                <a href=""><img src="{{ asset('ecom_assets/images/product8.jpg') }}" /></a>
                            </div>
                            <div class="feature_text">
                                <ul class="star_ul">
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li><i class="fa-solid fa-star"></i></li>
                                    <li>(5)</li>
                                </ul>
                                <a href="">Cross locket</a>
                                <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien.</p>
                                <span class="price_text">$20.30</span>
                            </div>
                            <div class="addtocart">
                                <a href="">view details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
