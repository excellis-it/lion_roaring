@extends('elearning.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($category['meta_description']) ? $category['meta_description'] : '' }}">
@endsection
@section('title')
    {{ isset($category['meta_title']) ? $category['meta_title'] : 'Products' }}
@endsection

@push('styles')
@endpush

@section('content')
    @php
        $bannerImage = asset('ecom_assets/images/banner.jpg');
        if (isset($subcategory) && $subcategory && $subcategory->image) {
            $bannerImage = Storage::url($subcategory->image);
        } elseif (isset($category) && $category && $category->image) {
            $bannerImage = Storage::url($category->image);
        }
    @endphp
    <section class="inner_banner_sec"
        style="background-image: url('{{ $bannerImage }}'); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>
                            @if (isset($subcategory) && $subcategory)
                                {{ $category ? $category->name . ' > ' : '' }}{{ $subcategory->name }}
                            @elseif(isset($category) && $category)
                                {{ $category->name }}
                            @else
                                Our Collection
                            @endif
                        </h2>
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
                            <input type="text" placeholder="Search Collection" class="form-control"
                                id="serach-product" />
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
                    <div class="padding_filter">
                        <div class="accordion" id="subcategorygroup">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSubcategory">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseSubcategory" aria-expanded="true" aria-controls="collapseSubcategory">
                                        Subcategories
                                    </button>
                                </h2>
                                <div id="collapseSubcategory" class="accordion-collapse collapse show"
                                    aria-labelledby="headingSubcategory" data-bs-parent="#subcategorygroup">
                                    <div class="accordion-body">
                                        <div class="new">
                                            <div class="mb-2">
                                                <input type="text" id="elearning-subcategory-search" class="form-control"
                                                    placeholder="Search subcategories" />
                                            </div>
                                            <div id="elearning-subcategory-list">
                                            @if (isset($subcategories) && count($subcategories) > 0)
                                                @foreach ($subcategories as $sub)
                                                    <div class="form-group subcategory-item">
                                                        <input type="checkbox" id="subcategory{{ $sub->id }}"
                                                            name="elearning_sub_category_id" value="{{ $sub->id }}"
                                                            @if (isset($subcategory_id) && $subcategory_id == $sub->id) checked @endif>
                                                        <label for="subcategory{{ $sub->id }}">{{ $sub->name }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                @include('elearning.partials.product-filter')

            </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var page = 1;
            var loading = false;
            var numSlick = 0;

            function collectFilters() {
                var prices = [], category_id = [], sub_ids = [], topic_ids = [];
                $('input[name="price"]:checked').each(function() { prices.push($(this).val()); });
                $('input[name="elearning_sub_category_id"]:checked').each(function() { sub_ids.push($(this).val()); });
                var topicVal = $('#topic_filter').val();
                if (topicVal) topic_ids.push(topicVal);
                var latestFilter = $('#latest_filter').val();
                var search = $('#serach-product').val();
                @if ($category_id != '')
                    category_id.push('{{ $category_id }}');
                @else
                    $('input[name="category_id"]:checked').each(function() { category_id.push($(this).val()); });
                @endif
                return { prices, category_id, sub_ids, topic_ids, latestFilter, search };
            }

            function reloadProducts() {
                var f = collectFilters();
                showLoading();
                page = 1;
                $('#products').html('');
                loadMoreProducts(page, f.prices, f.category_id, f.latestFilter, f.search, f.topic_ids, '', f.sub_ids);
            }

            function renderSubcategories(subcategories, previouslyChecked) {
                var $list = $('#elearning-subcategory-list');
                $list.empty();
                if (!subcategories || subcategories.length === 0) {
                    return;
                }
                $.each(subcategories, function(_, sub) {
                    var $item = $('<div class="form-group subcategory-item">');
                    var $input = $('<input type="checkbox">').attr({
                        id: 'subcategory' + sub.id,
                        name: 'elearning_sub_category_id',
                        value: sub.id
                    }).prop('checked', previouslyChecked.indexOf(String(sub.id)) > -1);
                    var $label = $('<label>').attr('for', 'subcategory' + sub.id).text(sub.name);
                    $item.append($input, $label);
                    $list.append($item);
                });
            }

            function refreshSubcategories(categoryIds, callback) {
                var previouslyChecked = [];
                $('input[name="elearning_sub_category_id"]:checked').each(function() {
                    previouslyChecked.push($(this).val());
                });

                $.ajax({
                    url: '{{ route('e-learning.get-subcategories') }}',
                    type: 'GET',
                    data: { category_id: categoryIds },
                    success: function(response) {
                        renderSubcategories(response.data || [], previouslyChecked);
                        if (typeof callback === 'function') {
                            callback();
                        }
                    },
                    error: function() {
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }
                });
            }


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
                    var f = collectFilters();
                    showLoading();
                    loadMoreProducts(page, f.prices, f.category_id, f.latestFilter, f.search, f.topic_ids, '', f.sub_ids);
                }
            }

            // When categories change, refresh subcategory list then reload products
            $(document).on('change', 'input[name="category_id"]', function() {
                var f = collectFilters();
                refreshSubcategories(f.category_id, reloadProducts);
            });

            // Filter products on other checkbox/select changes
            $(document).on('change',
                'input[name="price"], #latest_filter, #topic_filter, input[name="elearning_sub_category_id"]',
                function() {
                    reloadProducts();
                });

            // Subcategory search filter
            $(document).on('keyup', '#elearning-subcategory-search', function() {
                var q = $(this).val().toLowerCase();
                $('.subcategory-item').each(function() {
                    $(this).toggle($(this).find('label').text().toLowerCase().indexOf(q) > -1);
                });
            });

            // Search products
            $(document).on('submit', '#product-search-form', function(e) {
                e.preventDefault();
                reloadProducts();
            });

            $(document).on('keyup', '#serach-product', function(e) {
                e.preventDefault();
                reloadProducts();
            });



            function loadMoreProducts(page, prices = [], category_id = [],
                latestFilter = '', search = '', elearning_topic_id = [], elearning_topic_search = '',
                elearning_sub_category_id = '') {
                $.ajax({
                    url: '{{ route('e-learning.products-filter') }}',
                    type: 'GET',
                    data: {
                        page: page,
                        category_id: category_id,
                        elearning_sub_category_id: elearning_sub_category_id,
                        prices: prices,
                        elearning_topic_id: elearning_topic_id,
                        elearning_topic_search: elearning_topic_search,
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
