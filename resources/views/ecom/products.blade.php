@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($category['meta_description']) ? $category['meta_description'] : '' }}">
@endsection
@section('title')
    {{ isset($category['meta_title']) ? $category['meta_title'] : 'Products' }}
@endsection

@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/slider-bg.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        @if (request('type') === 'new-arrivals')
                            <h2>New Arrivals</h2>
                        @else
                            <h2>{{ $category_name ?? 'Our Collection' }}</h2>
                        @endif

                        <nav>
                            <ol class="cd-breadcrumb custom-separator">
                               {!! \App\Helpers\Helper::renderBreadcrumbs($category ?? null) !!}

                            </ol>
                        </nav>
                    </div>
                </div>

                {{-- <div class="featured_slider">
                    @if (count($childCategoriesList) > 0)
                        @foreach ($childCategoriesList as $category)
                            <div class="feature_slid_padding">
                                <div class="feature_box">
                                    <div class="feature_img">
                                        <a href="{{ route($category->slug . '.e-store.page') }}"><img
                                                src="{{ Storage::url($category->image) }}" /></a>
                                    </div>
                                    <div class="feature_text text-white">
                                        <a class="text-white text-center"
                                            href="{{ route($category->slug . '.e-store.page') }}">{{ $category->name }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div> --}}


            </div>
        </div>
    </section>

    <section class="filter_and_productlist">
        <div class="container-fluid">
            <div class="row m-0">
                <div class="col-xl-3 col-lg-4 p-0">
                    <div class="filter">
                        <div class="padding_filter">
                            <div class="filter_heading">
                                <img src="{{ asset('ecom_assets/images/filter_icon.svg') }}" alt="" />
                                <h4>Filter</h4>
                            </div>
                            <div class="search_color w-100">
                                <input type="text" placeholder="Search Collection" class="form-control"
                                    id="serach-product">
                                <button type="button">
                                    <img src="{{ asset('ecom_assets/images/search.svg') }}" alt="">
                                </button>
                            </div>
                        </div>
                        @if ($category_id != '')
                            <div class="padding_filter">
                                <div class="accordion" id="agegroup">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingAgeChG">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseAge" aria-expanded="true"
                                                aria-controls="collapseAge">
                                                Category - {{ $category_name }}
                                            </button>
                                        </h2>
                                        <div id="collapseAgeChg" class="accordion-collapse collapse show"
                                            aria-labelledby="headingAgeChg" data-bs-parent="#agegroup">
                                            <div class="accordion-body">
                                                <div class="new">

                                                    @if (count($childCategoriesList) > 0)
                                                        @foreach ($childCategoriesList as $category)
                                                            <div class="form-group">
                                                                <input type="checkbox" id="catagory{{ $category->id }}"
                                                                    name="category_id" value="{{ $category->id }}">
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
                        @else
                            <div class="padding_filter">
                                <div class="accordion" id="agegroup">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingAge">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseAge" aria-expanded="true"
                                                aria-controls="collapseAge">
                                                Categories
                                            </button>
                                        </h2>
                                        <div id="collapseAge" class="accordion-collapse collapse show"
                                            aria-labelledby="headingAge" data-bs-parent="#agegroup">
                                            <div class="accordion-body">
                                                <div class="new">

                                                    @if (count($categories) > 0)
                                                        @foreach ($categories as $category)
                                                            <div class="form-group">
                                                                <input type="checkbox" id="catagory{{ $category->id }}"
                                                                    name="category_id" value="{{ $category->id }}">
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



                        {{-- <div class="padding_filter">
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
                                    <div class="accordion-body">
                                        <div class="new">
                                            <div class="form-group">
                                                <input type="checkbox" id="price1" value="Below 500" name="price">
                                                <label for="price1">BELOW 500</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="price2" value="500-999" name="price">
                                                <label for="price2">₹500 - ₹999</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="price3" value="1000-1499" name="price">
                                                <label for="price3">₹1000 - ₹1,499</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="price4" value="1500-2499" name="price">
                                                <label for="price4">₹1500 - ₹2,499</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="price5" value="2500-4499" name="price">
                                                <label for="price5">₹₹2500 - ₹4,499</label>
                                            </div>

                                            <div class="form-group">
                                                <input type="checkbox" id="price6" value="Above 5000" name="price">
                                                <label for="price6">ABOVE ₹5000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                        {{-- <div class="padding_filter">
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
                    </div> --}}
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 p-0" id="product-filter">
                    @include('ecom.partials.product-filter')

                </div>
            </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var page = 1;
            var loading = false;
            var numSlick = 0; // Initialize numSlick variable


            // Show loading GIF function
            function showLoading() {
                $('#loading').show(); // Show loading GIF
            }

            // Hide loading GIF function
            function hideLoading() {
                $('#loading').hide(); // Hide loading GIF
            }

            // Scroll event handler
            $(window).on('scroll', handleScroll);

            function handleScroll() {
                var lastProduct = $('.productitem:last');
                var lastProductOffset = lastProduct.offset().top + lastProduct.outerHeight();
                var scrollTop = $(window).scrollTop() + $(window).height();

                if (scrollTop > lastProductOffset && !loading) {
                    loading = true;
                    page++;
                    var prices = [];
                    var category_id = [];
                    var search = $('#serach-product').val();


                    $('input[name="price"]:checked').each(function() {
                        prices.push($(this).val());
                    });

                    var latestFilter = $('#latest_filter').val();

                    @if ($category_id != '')
                        var cat = '{{ $category_id }}';
                        category_id.push(cat);
                    @else
                        $('input[name="category_id"]:checked').each(function() {
                            category_id.push($(this).val());
                        });
                    @endif

                    showLoading(); // Show loading GIF
                    loadMoreProducts(page, prices, category_id, latestFilter, search); // Load more products
                }
            }

            // Filter products by frame shape, size, material, price, gender
            $(document).on('change',
                'input[name="price"], #latest_filter, input[name="category_id"]',
                function() {
                    var prices = [];
                    var category_id = [];
                    var search = $('#serach-product').val();

                    $('input[name="price"]:checked').each(function() {
                        prices.push($(this).val());
                    });


                    var latestFilter = $('#latest_filter').val();

                    @if ($category_id != '')
                        var cat = '{{ $category_id }}';
                        category_id.push(cat);
                    @else
                        $('input[name="category_id"]:checked').each(function() {
                            category_id.push($(this).val());
                        });
                    @endif

                    showLoading(); // Show loading GIF before making the AJAX request

                    // Reset page to 1 and container for new filtered results
                    page = 1;
                    $('#products').html('');

                    loadMoreProducts(page, prices, category_id, latestFilter, search);
                });

            // Search products
            $(document).on('submit', '#product-search-form', function(e) {
                e.preventDefault();
                var search = $('#serach-product').val();
                var prices = [];
                var category_id = [];


                $('input[name="price"]:checked').each(function() {
                    prices.push($(this).val());
                });

                var latestFilter = $('#latest_filter').val();

                @if ($category_id != '')
                    var cat = '{{ $category_id }}';
                    category_id.push(cat);
                @else
                    $('input[name="category_id"]:checked').each(function() {
                        category_id.push($(this).val());
                    });
                @endif

                page = 1;
                $('#products').html('');

                loadMoreProducts(page, prices, category_id, latestFilter, search);
            });

            $(document).on('keyup', '#serach-product', function(e) {
                e.preventDefault();
                var search = $('#serach-product').val();
                var prices = [];
                var category_id = [];


                $('input[name="price"]:checked').each(function() {
                    prices.push($(this).val());
                });

                var latestFilter = $('#latest_filter').val();

                @if ($category_id != '')
                    var cat = '{{ $category_id }}';
                    category_id.push(cat);
                @else
                    $('input[name="category_id"]:checked').each(function() {
                        category_id.push($(this).val());
                    });
                @endif

                page = 1;
                $('#products').html('');

                loadMoreProducts(page, prices, category_id, latestFilter, search);
            });



            function loadMoreProducts(page, prices = [], category_id = [],
                latestFilter = '', search = '') {
                $.ajax({
                    url: '{{ route('e-store.products-filter') }}',
                    type: 'GET',
                    data: {
                        page: page,
                        category_id: category_id,
                        prices: prices,
                        latestFilter: latestFilter,
                        search: search

                    },
                    success: function(response) {
                        var productsContainer = $('#products');
                        var productsCount = $('#count-product');
                        if (page === 1) {
                            productsContainer.html(response
                                .view); // Replace products if it's the first page
                            productsCount.html(response
                                .view2); // Replace filters if it's the first page
                            $('#proccedtologin').hide();
                        } else {
                            productsContainer.append(response.view); // Append new products
                            productsCount.html(response
                                .view2); // Replace filters if it's the first page
                        }

                        // console.log(response);

                        if (response.products.length < 12) {
                            $(window).off('scroll',
                                handleScroll
                            ); // Stop loading more if fewer than 12 products are returned
                        } else {
                            $(window).on('scroll', handleScroll); // Reattach scroll event
                        }

                        hideLoading(); // Hide loading GIF after products are loaded
                        loading = false;
                    }
                });
            }
        });
    </script>
@endpush
