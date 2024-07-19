@extends('ecom.layouts.master')
@section('title')
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
                        <h2>Product listing</h2>
                        <p>Lorem ipsum dolor sit amet consectetur. Habitant ultricies sapien nunc adipiscing volutpat
                            consectetur
                            id purus rhoncus.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="filter_and_productlist">
        <div class="row m-0">
            <div class="col-xl-3 col-lg-4 p-0">
                <div class="filter">
                    <div class="padding_filter">
                        <div class="filter_heading">
                            <img src="{{ asset('ecom_assets/images/filter_icon.svg') }}" alt="" />
                            <h4>Filter</h4>
                        </div>
                        <div class="search_color w-100">
                            <input type="text" placeholder="Search Product" class="form-control">
                            <button type="button">
                                <img src="{{ asset('ecom_assets/images/search.svg') }}" alt="">
                            </button>
                        </div>
                    </div>
                    @if ($category_id != '')
                    @else
                    <div class="padding_filter">
                        <div class="accordion" id="agegroup">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingAge">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseAge" aria-expanded="true" aria-controls="collapseAge">
                                        Categories
                                    </button>
                                </h2>
                                <div id="collapseAge" class="accordion-collapse collapse show" aria-labelledby="headingAge"
                                    data-bs-parent="#agegroup">
                                    <div class="accordion-body">
                                        <div class="new">

                                            @if (count($categories) > 0)
                                                @foreach ($categories as $category)
                                                    <div class="form-group">
                                                        <input type="checkbox" id="catagory{{ $category->id }}" name="category"
                                                            value="{{ $category->id }}">
                                                        <label
                                                            for="catagory{{ $category->id }}">{{ $category->name }}</label>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="padding_filter">
                        <div class="accordion" id="price">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPrice">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsePrice" aria-expanded="false" aria-controls="collapsePrice">
                                        Price
                                    </button>
                                </h2>
                                <div id="collapsePrice" class="accordion-collapse collapse" aria-labelledby="headingPrice"
                                    data-bs-parent="#price">
                                    {{-- <div class="accordion-body">
                                        <div class="proce_slider">
                                            <div class="slider">
                                                <div class="progress"></div>
                                            </div>
                                            <div class="range-input">
                                                <input type="range" class="range-min" min="0" max="10000"
                                                    value="2500" step="100">
                                                <input type="range" class="range-max" min="0" max="10000"
                                                    value="7500" step="100">
                                            </div>
                                            <div class="price-input d-flex align-items-center justify-content-between">
                                                <div class="field">
                                                    <span>Min Price: INR</span>
                                                    <input type="number" class="input-min" value="2500">
                                                </div>
                                                <div class="separator">-</div>
                                                <div class="field">
                                                    <span>Min Price: INR</span>
                                                    <input type="number" class="input-max" value="7500">
                                                </div>
                                            </div>

                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="padding_filter">
                        <div class="accordion" id="starrating">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingStarrating">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseStarrating" aria-expanded="true"
                                        aria-controls="collapseStarrating">
                                        Star Rating
                                    </button>
                                </h2>
                                <div id="collapseStarrating" class="accordion-collapse collapse show"
                                    aria-labelledby="headingStarrating" data-bs-parent="#starrating">
                                    <div class="accordion-body">
                                        <div class="new">
                                            <div class="form-group">
                                                <input type="checkbox" id="star">
                                                <label for="star"><i class="fa-solid fa-star"></i> 1</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="star1">
                                                <label for="star1"><i class="fa-solid fa-star"></i> 2</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="star2">
                                                <label for="star2"><i class="fa-solid fa-star"></i> 3</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="star3">
                                                <label for="star3"><i class="fa-solid fa-star"></i> 4</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="star4">
                                                <label for="star4"><i class="fa-solid fa-star"></i> 5</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8 p-0">
                <div class="filter_resilt">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="filter_res_text">
                                <h4>
                                    @if ($category_id != '')
                                        {{ $category['name'] ?? '' }}
                                    @else
                                        All Products
                                    @endif
                                    ({{ $product_count ?? '' }} Products Found)
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <div class="">
                                    <select class="latest_filter">
                                        <option>Latest</option>
                                        <option>Low to High</option>
                                        <option>High to Low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mx-3">
                    @if (count($products) > 0)
                        @foreach ($products as $product)
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-5">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <div class="wishlist_icon">
                                            <a href="javascript:void(0);"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <a href="{{route('product-details', $product->slug)}}">
                                            @if (isset($product->main_image) && $product->main_image != null)
                                                <img src="{{ Storage::url($product->main_image) }}"
                                                    alt="{{ $product->main_image }}">
                                            @endif
                                        </a>
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
                                        <a href="{{route('product-details', $product->slug)}}">{{ $product->name }}</a>
                                        <p>{{ strlen($product->short_description) > 50 ? substr($product->short_description, 0, 50) . '...' : $product->short_description }}
                                        </p>
                                        <span class="price_text">${{ $product->price }}</span>
                                    </div>
                                    <div class="addtocart">
                                        <a href="{{route('product-details', $product->slug)}}">view details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-12">
                            <div class="alert alert-danger" role="alert">
                                No data found!
                            </div>
                        </div>
                    @endif
                </div>
            </div>
    </section>
@endsection

@push('scripts')
@endpush
