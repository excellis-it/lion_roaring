@extends('ecom.layouts.master')
@section('meta')
    <meta name="description" content="{{ isset($category['meta_description']) ? $category['meta_description'] : '' }}">
@endsection
@section('title')
    {{ isset($category['meta_title']) ? $category['meta_title'] : 'Products' }}
@endsection

@push('styles')
    <style>
        .list-group-item {
            background-color: #202d4d;
            color: #fff;
        }
    </style>
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
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="my-5 ">
        <div class="container-fluid">
            <nav>
                <ol class="cd-breadcrumb custom-separator">
                    {!! \App\Helpers\Helper::renderBreadcrumbs($category ?? null) !!}
                </ol>
            </nav>
        </div>
    </section>

    <section class="filter_and_productlist">
        <div class="container-fluid">
            <div class="row m-0">
                <div class="col-xl-3 col-lg-4">
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
                        <div class="padding_filter">
                            <div class="accordion" id="categoryTreeAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingCategories">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseCategories" aria-expanded="true"
                                            aria-controls="collapseCategories">
                                            Categories
                                        </button>
                                    </h2>
                                    <div id="collapseCategories" class="accordion-collapse collapse show"
                                        aria-labelledby="headingCategories" data-bs-parent="#categoryTreeAccordion">
                                        <div class="accordion-body">
                                            <div class="category-tree" id="category-tree">
                                                @php
                                                    $selectedCategoryIds = (array) ($category_id ? [$category_id] : []);
                                                    $renderCategory = function ($cat, $level = 0) use (
                                                        &$renderCategory,
                                                        $selectedCategoryIds,
                                                    ) {
                                                        $hasChildren = $cat->children && $cat->children->count();
                                                        $isChecked = in_array($cat->id, $selectedCategoryIds)
                                                            ? 'checked'
                                                            : '';
                                                        $html =
                                                            '<li class="list-group-item category-node" data-category-id="' .
                                                            $cat->id .
                                                            '">';
                                                        if ($hasChildren) {
                                                            $html .=
                                                                '<button type="button" class="btn btn-sm btn-light toggle-children me-2" data-state="collapsed" aria-label="Expand children" style="padding:2px 6px;">+</button>';
                                                        } else {
                                                            $html .=
                                                                '<span class="me-2" style="width:18px;display:inline-block;"></span>';
                                                        }
                                                        $html .= '<div class="form-check">';
                                                        $html .=
                                                            '<input class="form-check-input category-checkbox" type="checkbox" id="cat-' .
                                                            $cat->id .
                                                            '" value="' .
                                                            $cat->id .
                                                            '" ' .
                                                            $isChecked .
                                                            ' />';
                                                        $html .=
                                                            '<label class="form-check-label ms-1" for="cat-' .
                                                            $cat->id .
                                                            '">' .
                                                            e($cat->name) .
                                                            '</label>';
                                                        $html .= '</div>';
                                                        if ($hasChildren) {
                                                            $html .=
                                                                '<ul class="list-group children mt-2 border-0" style="display:none;" data-parent-id="' .
                                                                $cat->id .
                                                                '">';
                                                            foreach ($cat->children as $child) {
                                                                $html .= $renderCategory($child, $level + 1);
                                                            }
                                                            $html .= '</ul>';
                                                        }
                                                        $html .= '</li>';
                                                        return $html;
                                                    };
                                                @endphp

                                                <ul class="list-group">
                                                    @foreach ($categories as $rootCat)
                                                        {!! $renderCategory($rootCat, 0) !!}
                                                    @endforeach
                                                </ul>

                                            </div>
                                            <div class="mt-2">
                                                <button type="button" id="expand-all" class="red_btn border-0 w-100 my-3"><span>Expand All</span></button>
                                                <button type="button" id="collapse-all" class="red_btn border-0 w-100"><span>Collapse
                                                    All</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8" id="product-filter">
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

            function showLoading() {
                $('#loading').show();
            }

            function hideLoading() {
                $('#loading').hide();
            }

            $(window).on('scroll', handleScroll);

            function handleScroll() {
                var lastProduct = $('.productitem:last');
                if (!lastProduct.length) return;
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
                        $('.category-checkbox:checked').each(function() {
                            category_id.push($(this).val());
                        });
                    @endif
                    showLoading();
                    loadMoreProducts(page, prices, category_id, latestFilter, search);
                }
            }

            $(document).on('change', 'input[name="price"], #latest_filter, .category-checkbox', function() {
                var $target = $(this);
                // Ensure parent category checkbox propagates its state to all descendants BEFORE collecting selected IDs
                if ($target.hasClass('category-checkbox')) {
                    var $node = $target.closest('.category-node');
                    // propagate to all descendant checkboxes
                    $node.find('.children .category-checkbox').prop('checked', $target.is(':checked'));
                }

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
                    $('.category-checkbox:checked').each(function() {
                        category_id.push($(this).val());
                    });
                @endif
                showLoading();
                page = 1;
                $('#products').html('');
                loadMoreProducts(page, prices, category_id, latestFilter, search);
            });

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
                    $('.category-checkbox:checked').each(function() {
                        category_id.push($(this).val());
                    });
                @endif
                page = 1;
                $('#products').html('');
                loadMoreProducts(page, prices, category_id, latestFilter, search);
            });

            $(document).on('keyup', '#serach-product', function(e) {
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
                    $('.category-checkbox:checked').each(function() {
                        category_id.push($(this).val());
                    });
                @endif
                page = 1;
                $('#products').html('');
                loadMoreProducts(page, prices, category_id, latestFilter, search);
            });

            function loadMoreProducts(page, prices = [], category_id = [], latestFilter = '', search = '') {
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
                            productsContainer.html(response.view);
                            productsCount.html(response.view2);
                            $('#proccedtologin').hide();
                        } else {
                            productsContainer.append(response.view);
                            productsCount.html(response.view2);
                        }
                        if (response.products.length < 12) {
                            $(window).off('scroll', handleScroll);
                        } else {
                            $(window).on('scroll', handleScroll);
                        }
                        hideLoading();
                        loading = false;
                    }
                });
            }

            // Tree controls
            $(document).on('click', '.toggle-children', function() {
                var $btn = $(this);
                var $node = $btn.closest('.category-node');
                var $children = $node.children('.children');
                if (!$children.length) return;
                var state = $btn.data('state');
                if (state === 'expanded') {
                    $children.slideUp(150);
                    $btn.text('+').data('state', 'collapsed').attr('aria-label', 'Expand children');
                } else {
                    $children.slideDown(150);
                    $btn.text('-').data('state', 'expanded').attr('aria-label', 'Collapse children');
                }
            });
            $('#expand-all').on('click', function() {
                $('.category-node > .children').each(function() {
                    var $wrap = $(this);
                    if ($wrap.is(':hidden')) $wrap.slideDown(120);
                });
                $('.toggle-children').each(function() {
                    var $b = $(this);
                    if ($b.data('state') === 'collapsed') {
                        $b.text('-').data('state', 'expanded');
                    }
                });
            });
            $('#collapse-all').on('click', function() {
                $('.category-node > .children').each(function() {
                    var $wrap = $(this);
                    if ($wrap.is(':visible')) $wrap.slideUp(120);
                });
                $('.toggle-children').each(function() {
                    var $b = $(this);
                    if ($b.data('state') === 'expanded') {
                        $b.text('+').data('state', 'collapsed');
                    }
                });
            });
            // (Parent/child checkbox propagation handled inside unified change handler above.)
        });
    </script>
@endpush
