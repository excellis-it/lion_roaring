@extends('user.layouts.master')
@section('title')
    Home Cms Update - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="home-cms-form" action="{{ route('user.store-cms.home.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($cms->id) ? $cms->id : '' }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Home Cms Content </h3>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="country_code">Content Country</label>
                                <select onchange="window.location.href='?content_country_code='+$(this).val()"
                                    name="content_country_code" id="content_country_code" class="form-control">
                                    @foreach (\App\Models\Country::all() as $country)
                                        <option value="{{ $country->code }}"
                                            {{ request()->get('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">

                            {{-- banner_title --}}
                            <div class="col-md-6 mb-2" hidden>
                                <div class="box_label">
                                    <label for="banner_title"> Banner Title*</label>
                                    <input type="text" name="banner_title" id="banner_title" class="form-control"
                                        value="{{ isset($cms->banner_title) ? $cms->banner_title : old('banner_title') }}">
                                    @if ($errors->has('banner_title'))
                                        <span class="error">{{ $errors->first('banner_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- banner_subtitle --}}
                            <div class="col-md-6 mb-2" hidden>
                                <div class="box_label">
                                    <label for="banner_subtitle"> Banner Subtitle*</label>
                                    <input type="text" name="banner_subtitle" id="banner_subtitle" class="form-control"
                                        value="{{ isset($cms->banner_subtitle) ? $cms->banner_subtitle : old('banner_subtitle') }}">
                                    @if ($errors->has('banner_subtitle'))
                                        <span class="error">{{ $errors->first('banner_subtitle') }}</span>
                                    @endif
                                </div>
                            </div>





                            {{-- new_arrival_title --}}
                            <div class="col-md-6 mb-2" hidden>
                                <div class="box_label">
                                    <label for="new_arrival_title"> New Arrival Title*</label>
                                    <input type="text" name="new_arrival_title" id="new_arrival_title"
                                        class="form-control"
                                        value="{{ isset($cms->new_arrival_title) ? $cms->new_arrival_title : old('new_arrival_title') }}">
                                    @if ($errors->has('new_arrival_title'))
                                        <span class="error">{{ $errors->first('new_arrival_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- new_arrival_subtitle --}}
                            <div class="col-md-6 mb-2" hidden>
                                <div class="box_label">
                                    <label for="new_arrival_subtitle"> New Arrival Subtitle*</label>
                                    <input type="text" name="new_arrival_subtitle" id="new_arrival_subtitle"
                                        class="form-control"
                                        value="{{ isset($cms->new_arrival_subtitle) ? $cms->new_arrival_subtitle : old('new_arrival_subtitle') }}">
                                    @if ($errors->has('new_arrival_subtitle'))
                                        <span class="error">{{ $errors->first('new_arrival_subtitle') }}</span>
                                    @endif
                                </div>
                            </div>








                            <div class="accordion " id="accordion1">


                                {{-- Header Logo --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading_header_logo">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse_header_logo" aria-expanded="true"
                                            aria-controls="collapse_header_logo">
                                            <strong>Header Logo</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse_header_logo" class="accordion-collapse collapse"
                                        aria-labelledby="heading_header_logo" data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <div class="col-md-12 mb-4">

                                                <div class="box_label">
                                                    <label for="header_logo"> Header Logo (width: 90px, height:
                                                        90px, max 1MB)*</label>
                                                    <input type="file" name="header_logo" id="header_logo"
                                                        class="form-control" accept="image/*">
                                                    @if ($errors->has('header_logo'))
                                                        <span class="error">{{ $errors->first('header_logo') }}</span>
                                                    @endif
                                                </div>
                                                @if (isset($cms->header_logo) && $cms->header_logo)
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($cms->header_logo) }}" alt="Header Logo"
                                                            class="img-thumbnail"
                                                            style="width: 150px; height: 150px; object-fit: contain;">
                                                    </div>
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Header Logo end --}}


                                {{-- Product Category --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading_product_category">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse_product_category" aria-expanded="true"
                                            aria-controls="collapse_product_category">
                                            <strong>Product Category Section</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse_product_category" class="accordion-collapse collapse"
                                        aria-labelledby="heading_product_category" data-bs-parent="#accordion1">
                                        <div class="accordion-body">

                                            <div class="row">


                                                {{-- product_category_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="product_category_title"> Product Category
                                                            Title*</label>
                                                        <input type="text" name="product_category_title"
                                                            id="product_category_title" class="form-control"
                                                            value="{{ isset($cms->product_category_title) ? $cms->product_category_title : old('product_category_title') }}">
                                                        @if ($errors->has('product_category_title'))
                                                            <span
                                                                class="error">{{ $errors->first('product_category_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- product_category_subtitle --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="product_category_subtitle"> Product Category
                                                            Subtitle*</label>
                                                        <input type="text" name="product_category_subtitle"
                                                            id="product_category_subtitle" class="form-control"
                                                            value="{{ isset($cms->product_category_subtitle) ? $cms->product_category_subtitle : old('product_category_subtitle') }}">
                                                        @if ($errors->has('product_category_subtitle'))
                                                            <span
                                                                class="error">{{ $errors->first('product_category_subtitle') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {{-- Product Category end --}}


                                {{-- Top banner accordion start --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                            <strong>Top Banner Slider Section</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1"
                                        data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <div class="col-md-12 mb-4">

                                                <div id="slider-container">
                                                    @php
                                                        // $sliderData = isset($cms->slider_data)
                                                        //     ? json_decode($cms->slider_data, true)
                                                        //     : [];
                                                        $sliderDataRaw = $cms->slider_data ?? [];
                                                        $sliderData = is_array($sliderDataRaw)
                                                            ? $sliderDataRaw
                                                            : (json_decode($sliderDataRaw ?: '[]', true) ?:
                                                            []);
                                                    @endphp

                                                    @if (count($sliderData) > 0)
                                                        @foreach ($sliderData as $index => $slide)
                                                            <div class="slider-item mb-4 p-0 border rounded">
                                                                <div class="row">
                                                                    <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index + 1 }} Title*</label>
                                                                            <input type="text" name="slider_titles[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['title'] ?? '' }}"
                                                                                placeholder="Enter slide title">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xxl-3 col-lg-3 col-md-6">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index + 1 }}
                                                                                Subtitle*</label>
                                                                            <input type="text"
                                                                                name="slider_subtitles[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['subtitle'] ?? '' }}"
                                                                                placeholder="Enter slide subtitle">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index + 1 }} Link*</label>
                                                                            <input type="text" name="slider_links[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['link'] ?? '' }}"
                                                                                placeholder="Enter slide link">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index + 1 }} Link
                                                                                Button*</label>
                                                                            <input type="text" name="slider_buttons[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['button'] ?? '' }}"
                                                                                placeholder="Enter slide button text">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                        <div class="box_label">

                                                                            <label>Slide {{ $index + 1 }} Image
                                                                                *</label>
                                                                            <input type="file" name="slider_images[]"
                                                                                class="form-control" accept="image/*">
                                                                            <span class="text-sm ms-2 text-muted"
                                                                                style="font-size:12px;">(width:
                                                                                1920px, height: 550px, max 2MB)</span>
                                                                            @if (isset($slide['image']) && $slide['image'])
                                                                                <div class="mt-2">
                                                                                    <img src="{{ Storage::url($slide['image']) }}"
                                                                                        alt="Slide Image"
                                                                                        class="img-thumbnail"
                                                                                        style="width: 100px; height: 60px; object-fit: cover;">
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xxl-1 col-lg-1 col-md-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                                                class="fa-solid fa-trash"></i></button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="slider-item mb-4 p-0 border rounded">
                                                            <div class="row">
                                                                <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Title*</label>
                                                                        <input type="text" name="slider_titles[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide title">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xxl-3 col-lg-3 col-md-6">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Subtitle*</label>
                                                                        <input type="text" name="slider_subtitles[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide subtitle">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Link*</label>
                                                                        <input type="text" name="slider_links[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide link">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Link Button*</label>
                                                                        <input type="text" name="slider_buttons[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide button text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xxl-2 col-lg-3 col-md-6">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Image*</label>
                                                                        <input type="file" name="slider_images[]"
                                                                            class="form-control" accept="image/*">
                                                                        <span class="text-sm ms-2 text-muted"
                                                                            style="font-size:12px;">(width:
                                                                            1920px, height: 550px, max 2MB)</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-1 col-lg-1 col-md-1">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                                        class="fa-solid fa-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <button type="button" id="add-slide" class="btn btn-primary">Add New
                                                    Slide</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Top banner accordion end --}}

                                {{-- Featured Product accordion start --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading2">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                            <strong>Featured Product Section</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse " aria-labelledby="heading2"
                                        data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <div class="row">
                                                {{-- featured_product_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="featured_product_title"> Featured Product
                                                            Title*</label>
                                                        <input type="text" name="featured_product_title"
                                                            id="featured_product_title" class="form-control"
                                                            value="{{ isset($cms->featured_product_title) ? $cms->featured_product_title : old('featured_product_title') }}">
                                                        @if ($errors->has('featured_product_title'))
                                                            <span
                                                                class="error">{{ $errors->first('featured_product_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- featured_product_subtitle --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="featured_product_subtitle"> Featured Product
                                                            Subtitle*</label>
                                                        <input type="text" name="featured_product_subtitle"
                                                            id="featured_product_subtitle" class="form-control"
                                                            value="{{ isset($cms->featured_product_subtitle) ? $cms->featured_product_subtitle : old('featured_product_subtitle') }}">
                                                        @if ($errors->has('featured_product_subtitle'))
                                                            <span
                                                                class="error">{{ $errors->first('featured_product_subtitle') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {{-- Featured Product accordion end --}}

                                {{-- Shop Now accordion start --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingShopNow">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseShopNow" aria-expanded="true"
                                            aria-controls="collapseShopNow">
                                            <strong>Shop Now Section</strong>
                                        </button>
                                    </h2>
                                    <div id="collapseShopNow" class="accordion-collapse collapse "
                                        aria-labelledby="headingShopNow" data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <div class="row">
                                                {{-- shop_now_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="shop_now_title"> Shop Now Section
                                                            Title*</label>
                                                        <input type="text" name="shop_now_title" id="shop_now_title"
                                                            class="form-control"
                                                            value="{{ isset($cms->shop_now_title) ? $cms->shop_now_title : old('shop_now_title') }}">
                                                        @if ($errors->has('shop_now_title'))
                                                            <span
                                                                class="error">{{ $errors->first('shop_now_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- shop_now_description --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="shop_now_description"> Shop Now Section
                                                            Description*</label>
                                                        <input type="text" name="shop_now_description"
                                                            id="shop_now_description" class="form-control"
                                                            value="{{ isset($cms->shop_now_description) ? $cms->shop_now_description : old('shop_now_description') }}">
                                                        @if ($errors->has('shop_now_description'))
                                                            <span
                                                                class="error">{{ $errors->first('shop_now_description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>


                                                {{-- shop_now_button_text --}}
                                                <div class="col-md-3 mb-2">
                                                    <div class="box_label">
                                                        <label for="shop_now_button_text"> Shop Now Button
                                                            Text*</label>
                                                        <input type="text" name="shop_now_button_text"
                                                            id="shop_now_button_text" class="form-control"
                                                            value="{{ isset($cms->shop_now_button_text) ? $cms->shop_now_button_text : old('shop_now_button_text') }}">
                                                        @if ($errors->has('shop_now_button_text'))
                                                            <span
                                                                class="error">{{ $errors->first('shop_now_button_text') }}</span>
                                                        @endif
                                                    </div>

                                                </div>
                                                {{-- shop_now_button_link --}}
                                                <div class="col-md-3 mb-2">
                                                    <div class="box_label">
                                                        <label for="shop_now_button_link"> Shop Now Button
                                                            Link*</label>
                                                        <input type="text" name="shop_now_button_link"
                                                            id="shop_now_button_link" class="form-control"
                                                            value="{{ isset($cms->shop_now_button_link) ? $cms->shop_now_button_link : old('shop_now_button_link') }}">
                                                        @if ($errors->has('shop_now_button_link'))
                                                            <span
                                                                class="error">{{ $errors->first('shop_now_button_link') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- shop_now_image --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="shop_now_image"> Shop Now Image*</label>
                                                        <input type="file" name="shop_now_image" id="shop_now_image"
                                                            class="form-control"
                                                            value="{{ isset($cms->shop_now_image) ? $cms->shop_now_image : old('shop_now_image') }}"
                                                            accept="image/*">
                                                        <span class="text-sm ms-2 text-muted"
                                                            style="font-size:12px;">(width: 1920px, height:
                                                            550px, max 2MB)</span>
                                                        @if ($errors->has('shop_now_image'))
                                                            <span
                                                                class="error">{{ $errors->first('shop_now_image') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-2 mb-4">
                                                    @if (isset($cms->shop_now_image) && $cms->shop_now_image)
                                                        <div class="mt-2">
                                                            <img src="{{ Storage::url($cms->shop_now_image) }}"
                                                                alt="Shop Now Image" class="img-thumbnail"
                                                                style="width: 150px; height: 150px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {{-- Featured Product accordion end --}}

                                {{-- 2nd Banner accordion start --}}
                                <div class="accordion-item" hidden>
                                    <h2 class="accordion-header" id="heading3">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                            <strong>Second Banner Slider Management</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse " aria-labelledby="heading3"
                                        data-bs-parent="#accordion1">
                                        <div class="accordion-body">

                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="slider_data_second_title"> Second Banner Main
                                                            Title*</label>
                                                        <input type="text" name="slider_data_second_title"
                                                            id="slider_data_second_title" class="form-control"
                                                            value="{{ isset($cms->slider_data_second_title) ? $cms->slider_data_second_title : old('slider_data_second_title') }}">
                                                        @if ($errors->has('slider_data_second_title'))
                                                            <span
                                                                class="error">{{ $errors->first('slider_data_second_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-12 mb-4">

                                                <div id="slider-container-second">
                                                    @php
                                                        // $sliderDataSecond = isset($cms->slider_data_second)
                                                        //     ? json_decode($cms->slider_data_second, true)
                                                        //     : [];
                                                        $sliderDataSecondRaw = $cms->slider_data_second ?? [];
                                                        $sliderDataSecond = is_array($sliderDataSecondRaw)
                                                            ? $sliderDataSecondRaw
                                                            : (json_decode($sliderDataSecondRaw ?: '[]', true) ?:
                                                            []);
                                                    @endphp

                                                    @if (count($sliderDataSecond) > 0)
                                                        @foreach ($sliderDataSecond as $index2 => $slide)
                                                            <div class="slider-item-second mb-4 p-0 border rounded">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index2 + 1 }} Title</label>
                                                                            <input type="text"
                                                                                name="slider_titles_second[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['title'] ?? '' }}"
                                                                                placeholder="Enter slide title">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index2 + 1 }}
                                                                                Subtitle</label>
                                                                            <input type="text"
                                                                                name="slider_subtitles_second[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['subtitle'] ?? '' }}"
                                                                                placeholder="Enter slide subtitle">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index2 + 1 }} Link</label>
                                                                            <input type="text"
                                                                                name="slider_links_second[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['link'] ?? '' }}"
                                                                                placeholder="Enter slide link">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index2 + 1 }} Link
                                                                                Button</label>
                                                                            <input type="text"
                                                                                name="slider_buttons_second[]"
                                                                                class="form-control"
                                                                                value="{{ $slide['button'] ?? '' }}"
                                                                                placeholder="Enter slide button text">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="box_label">
                                                                            <label>Slide {{ $index2 + 1 }} Image</label>
                                                                            <input type="file"
                                                                                name="slider_images_second[]"
                                                                                class="form-control" accept="image/*">
                                                                            @if (isset($slide['image']) && $slide['image'])
                                                                                <div class="mt-2">
                                                                                    <img src="{{ Storage::url($slide['image']) }}"
                                                                                        alt="Slide Image"
                                                                                        class="img-thumbnail"
                                                                                        style="width: 100px; height: 60px; object-fit: cover;">
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm mt-2 remove-slide-second"><i
                                                                                class="fa-solid fa-trash"></i></button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="slider-item-second mb-4 p-0 border rounded">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Title</label>
                                                                        <input type="text"
                                                                            name="slider_titles_second[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide title">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Subtitle</label>
                                                                        <input type="text"
                                                                            name="slider_subtitles_second[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide subtitle">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Link</label>
                                                                        <input type="text" name="slider_links_second[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide link">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Link Button</label>
                                                                        <input type="text"
                                                                            name="slider_buttons_second[]"
                                                                            class="form-control"
                                                                            placeholder="Enter slide button text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="box_label">
                                                                        <label>Slide 1 Image</label>
                                                                        <input type="file"
                                                                            name="slider_images_second[]"
                                                                            class="form-control" accept="image/*">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                                            class="fa-solid fa-trash"></i></button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endif
                                                </div>

                                                <button type="button" id="add-slide-second" class="btn btn-primary">Add
                                                    New
                                                    Slide</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- 2nd Banner accordion end --}}


                                {{-- New Product accordion start --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading4">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                            <strong>New Product Section</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse4" class="accordion-collapse collapse " aria-labelledby="heading4"
                                        data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <div class="row">
                                                {{-- new_product_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="new_product_title"> New Product Title*</label>
                                                        <input type="text" name="new_product_title"
                                                            id="new_product_title" class="form-control"
                                                            value="{{ isset($cms->new_product_title) ? $cms->new_product_title : old('new_product_title') }}">
                                                        @if ($errors->has('new_product_title'))
                                                            <span
                                                                class="error">{{ $errors->first('new_product_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- new_product_subtitle --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="new_product_subtitle"> New Product Subtitle*</label>
                                                        <input type="text" name="new_product_subtitle"
                                                            id="new_product_subtitle" class="form-control"
                                                            value="{{ isset($cms->new_product_subtitle) ? $cms->new_product_subtitle : old('new_product_subtitle') }}">
                                                        @if ($errors->has('new_product_subtitle'))
                                                            <span
                                                                class="error">{{ $errors->first('new_product_subtitle') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {{-- New Product accordion end --}}


                                {{-- About section accordian start --}}

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading5">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                                            <strong>About Section</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse5" class="accordion-collapse collapse " aria-labelledby="heading5"
                                        data-bs-parent="#accordion1">
                                        <div class="accordion-body">
                                            <div class="row">

                                                {{-- about_section_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_title"> About Section
                                                            Title*</label>
                                                        <input type="text" name="about_section_title"
                                                            id="about_section_title" class="form-control"
                                                            value="{{ isset($cms->about_section_title) ? $cms->about_section_title : old('about_section_title') }}">
                                                        @if ($errors->has('about_section_title'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- about_section_image --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_image"> About Section Image*</label>
                                                        <input type="file" name="about_section_image"
                                                            id="about_section_image" class="form-control"
                                                            accept="image/*">
                                                        <span class="text-sm ms-2 text-muted"
                                                            style="font-size:12px;">(width: 420px, height:
                                                            300px, max 2MB)</span>
                                                        @if ($errors->has('about_section_image'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_image') }}</span>
                                                        @endif
                                                        @if (isset($cms->about_section_image) && $cms->about_section_image)
                                                            <div class="mt-2">
                                                                <img src="{{ Storage::url($cms->about_section_image) }}"
                                                                    alt="About Section Image" class="img-thumbnail"
                                                                    style="width: 100px; height: 60px; object-fit: cover;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- about_section_text_one_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_text_one_title"> About Section Text
                                                            One Title*</label>
                                                        <input type="text" name="about_section_text_one_title"
                                                            id="about_section_text_one_title" class="form-control"
                                                            value="{{ isset($cms->about_section_text_one_title) ? $cms->about_section_text_one_title : old('about_section_text_one_title') }}">
                                                        @if ($errors->has('about_section_text_one_title'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_text_one_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- about_section_text_one_content --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_text_one_content"> About Section Text
                                                            One Content*</label>
                                                        <textarea name="about_section_text_one_content" id="about_section_text_one_content" class="form-control"
                                                            rows="4">{{ isset($cms->about_section_text_one_content) ? $cms->about_section_text_one_content : old('about_section_text_one_content') }}</textarea>
                                                        @if ($errors->has('about_section_text_one_content'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_text_one_content') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- about_section_text_two_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_text_two_title"> About Section Text
                                                            Two Title*</label>
                                                        <input type="text" name="about_section_text_two_title"
                                                            id="about_section_text_two_title" class="form-control"
                                                            value="{{ isset($cms->about_section_text_two_title) ? $cms->about_section_text_two_title : old('about_section_text_two_title') }}">
                                                        @if ($errors->has('about_section_text_two_title'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_text_two_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- about_section_text_two_content --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_text_two_content"> About Section Text
                                                            Two Content*</label>
                                                        <textarea name="about_section_text_two_content" id="about_section_text_two_content" class="form-control"
                                                            rows="4">{{ isset($cms->about_section_text_two_content) ? $cms->about_section_text_two_content : old('about_section_text_two_content') }}</textarea>
                                                        @if ($errors->has('about_section_text_two_content'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_text_two_content') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- about_section_text_three_title --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_text_three_title"> About Section Text
                                                            Three Title*</label>
                                                        <input type="text" name="about_section_text_three_title"
                                                            id="about_section_text_three_title" class="form-control"
                                                            value="{{ isset($cms->about_section_text_three_title) ? $cms->about_section_text_three_title : old('about_section_text_three_title') }}">
                                                        @if ($errors->has('about_section_text_three_title'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_text_three_title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- about_section_text_three_content --}}
                                                <div class="col-md-6 mb-2">
                                                    <div class="box_label">
                                                        <label for="about_section_text_three_content"> About Section Text
                                                            Three Content*</label>
                                                        <textarea name="about_section_text_three_content" id="about_section_text_three_content" class="form-control"
                                                            rows="4">{{ isset($cms->about_section_text_three_content) ? $cms->about_section_text_three_content : old('about_section_text_three_content') }}</textarea>
                                                        @if ($errors->has('about_section_text_three_content'))
                                                            <span
                                                                class="error">{{ $errors->first('about_section_text_three_content') }}</span>
                                                        @endif
                                                    </div>

                                                </div>



                                            </div>
                                        </div>
                                    </div>
                                    {{-- About section accordian end --}}


                                </div>






                                <div class="">


                                    <div class="w-100 text-end d-flex align-items-center justify-content-end mt-5">
                                        <button type="submit" class="print_btn me-2">Update</button>
                                        <a href="{{ route('user.store-cms.list') }}"
                                            class="print_btn print_btn_vv">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let slideIndex = {{ count($sliderData ?? []) }};

            // Add new slide
            $('#add-slide').click(function() {
                slideIndex++;
                const newSlide = `
                    <div class="slider-item mb-4 p-0 border rounded">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Title*</label>
                                    <input type="text" name="slider_titles[]" class="form-control"
                                        placeholder="Enter slide title">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Subtitle*</label>
                                    <input type="text" name="slider_subtitles[]" class="form-control"
                                        placeholder="Enter slide subtitle">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Link*</label>
                                    <input type="text" name="slider_links[]" class="form-control"
                                        placeholder="Enter slide link">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Link Button*</label>
                                    <input type="text" name="slider_buttons[]" class="form-control"
                                        placeholder="Enter slide button text">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Image*</label>
                                    <input type="file" name="slider_images[]" class="form-control" accept="image/*">
                                    <span class="text-sm ms-2 text-muted">(width: 1920px, height: 550px, max 2MB)</span>
                                </div>
                            </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                        class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                `;
                $('#slider-container').append(newSlide);
            });

            // Remove slide
            $(document).on('click', '.remove-slide', function() {
                if ($('.slider-item').length > 1) {
                    $(this).closest('.slider-item').remove();
                } else {
                    alert('At least one slide is required.');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            let secondSlideIndex = {{ count($sliderDataSecond ?? []) }};

            // Add new slide
            $('#add-slide-second').click(function() {
                secondSlideIndex++;
                const newSlide = `
                    <div class="slider-item mb-4 p-0 border rounded">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${secondSlideIndex} Title</label>
                                    <input type="text" name="slider_titles_second[]" class="form-control"
                                        placeholder="Enter slide title">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box_label">
                                    <label>Slide ${secondSlideIndex} Subtitle</label>
                                    <input type="text" name="slider_subtitles_second[]" class="form-control"
                                        placeholder="Enter slide subtitle">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${secondSlideIndex} Link</label>
                                    <input type="text" name="slider_links_second[]" class="form-control"
                                        placeholder="Enter slide link">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${secondSlideIndex} Link Button</label>
                                    <input type="text" name="slider_buttons_second[]" class="form-control"
                                        placeholder="Enter slide button text">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="box_label">
                                    <label>Slide ${secondSlideIndex} Image</label>
                                    <input type="file" name="slider_images_second[]" class="form-control" accept="image/*">
                                </div>
                            </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                        class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                `;
                $('#slider-container-second').append(newSlide);
            });

            // Remove slide
            $(document).on('click', '.remove-slide-second', function() {
                if ($('.slider-item-second').length > 1) {
                    $(this).closest('.slider-item-second').remove();
                } else {
                    alert('At least one slide is required.');
                }
            });
        });
    </script>

    <!-- Client-side validation for home-cms-form -->
    <script>
        (function() {
            // Flags injected from server: whether images already exist
            var existingHeaderLogo = {{ isset($cms->header_logo) && $cms->header_logo ? 1 : 0 }};
            var existingShopNowImage = {{ isset($cms->shop_now_image) && $cms->shop_now_image ? 1 : 0 }};
            var existingAboutImage = {{ isset($cms->about_section_image) && $cms->about_section_image ? 1 : 0 }};

            function addClientError($el, message) {
                $el.addClass('is-invalid');
                if ($el.next('.client-error').length) {
                    $el.next('.client-error').text(message);
                } else {
                    $el.after('<span class="error client-error" style="color:red;display:block;margin-top:4px;">' +
                        message + '</span>');
                    toastr.error(message);
                }
            }

            function clearClientErrors($form) {
                $form.find('.client-error').remove();
                $form.find('.is-invalid').removeClass('is-invalid');
            }

            function val(selector) {
                return $.trim($(selector).val() || '');
            }

            // helper: validate a File object's size and required width/height. Returns Promise<boolean>
            function validateImageFile(file, maxBytes, reqW, reqH, $input, messagePrefix) {
                return new Promise(function(resolve) {
                    if (!file) {
                        resolve(false);
                        return;
                    }
                    if (maxBytes && file.size > maxBytes) {
                        addClientError($input, messagePrefix + ' file size must be less than ' + (maxBytes /
                            1024 / 1024).toFixed(2) + 'MB.');
                        resolve(false);
                        return;
                    }
                    // if no resolution required, pass
                    if (!reqW && !reqH) {
                        resolve(true);
                        return;
                    }

                    var url = URL.createObjectURL(file);
                    var img = new Image();
                    var timedOut = false;
                    var timer = setTimeout(function() {
                        timedOut = true;
                        try {
                            URL.revokeObjectURL(url);
                        } catch (e) {}
                        addClientError($input, messagePrefix + ' could not be validated (timeout).');
                        resolve(false);
                    }, 5000);

                    function finish(ok, msg) {
                        if (timedOut) return;
                        clearTimeout(timer);
                        try {
                            URL.revokeObjectURL(url);
                        } catch (e) {}
                        if (!ok && msg) addClientError($input, msg);
                        resolve(!!ok);
                    }

                    img.onload = function() {
                        var w = this.naturalWidth || this.width;
                        var h = this.naturalHeight || this.height;
                        if ((reqW && w !== reqW) || (reqH && h !== reqH)) {

                            finish(false, messagePrefix + ' resolution must be ' + reqW + 'x' + reqH +
                                '. Your image is ' + w + 'x' + h + '.');
                            return;
                        } else {
                            finish(true);
                        }
                    };

                    img.onerror = function() {
                        finish(false, messagePrefix + ' is not a valid image.');
                    };

                    img.src = url;
                });
            }

            // Make submit handler async to await image resolution checks
            $('#home-cms-form').on('submit', async function(e) {
                var $form = $(this);
                // prevent default immediately so async validation can run without the browser submitting
                e.preventDefault();
                clearClientErrors($form);
                var errors = [];

                // Product Category Section
                if ($('#product_category_title').length && !val('#product_category_title')) {
                    addClientError($('#product_category_title'), 'Product category title is required.');
                    errors.push('#product_category_title');
                }
                if ($('#product_category_subtitle').length && !val('#product_category_subtitle')) {
                    addClientError($('#product_category_subtitle'),
                        'Product category subtitle is required.');
                    errors.push('#product_category_subtitle');
                }

                // Featured Product
                if ($('#featured_product_title').length && !val('#featured_product_title')) {
                    addClientError($('#featured_product_title'), 'Featured product title is required.');
                    errors.push('#featured_product_title');
                }
                if ($('#featured_product_subtitle').length && !val('#featured_product_subtitle')) {
                    addClientError($('#featured_product_subtitle'),
                        'Featured product subtitle is required.');
                    errors.push('#featured_product_subtitle');
                }

                // Shop Now Section
                if ($('#shop_now_title').length && !val('#shop_now_title')) {
                    addClientError($('#shop_now_title'), 'Shop Now title is required.');
                    errors.push('#shop_now_title');
                }
                if ($('#shop_now_description').length && !val('#shop_now_description')) {
                    addClientError($('#shop_now_description'), 'Shop Now description is required.');
                    errors.push('#shop_now_description');
                }
                if ($('#shop_now_button_text').length && !val('#shop_now_button_text')) {
                    addClientError($('#shop_now_button_text'), 'Shop Now button text is required.');
                    errors.push('#shop_now_button_text');
                }
                if ($('#shop_now_button_link').length && !val('#shop_now_button_link')) {
                    addClientError($('#shop_now_button_link'), 'Shop Now button link is required.');
                    errors.push('#shop_now_button_link');
                }

                // Shop Now image required only if no existing image present
                var shopNowInput = $('#shop_now_image')[0];
                var shopNowHasFile = shopNowInput && shopNowInput.files && shopNowInput.files.length > 0;
                if (!existingShopNowImage && !shopNowHasFile) {
                    addClientError($('#shop_now_image'), 'Shop Now image is required (existing or new).');
                    errors.push('#shop_now_image');
                }
                // else if (shopNowHasFile) {
                //     // validate size/resolution: 1920x550, max 2MB
                //     var ok = await validateImageFile(shopNowInput.files[0], 2 * 1024 * 1024, 1920, 550, $(
                //         '#shop_now_image'), 'Shop Now image');
                //     if (!ok) errors.push('#shop_now_image');
                // }

                // Header logo required only if no existing
                var headerInput = $('#header_logo')[0];
                var headerHasFile = headerInput && headerInput.files && headerInput.files.length > 0;
                if (!existingHeaderLogo && !headerHasFile) {
                    addClientError($('#header_logo'), 'Header logo is required (existing or new).');
                    errors.push('#header_logo');
                }
                // else if (headerHasFile) {
                //     // validate header: 90x90, max 1MB
                //     var okHeader = await validateImageFile(headerInput.files[0], 1 * 1024 * 1024, 90, 90, $(
                //         '#header_logo'), 'Header logo');
                //     if (!okHeader) errors.push('#header_logo');
                // }

                // About section
                if ($('#about_section_title').length && !val('#about_section_title')) {
                    addClientError($('#about_section_title'), 'About section title is required.');
                    errors.push('#about_section_title');
                }
                if ($('#about_section_text_one_content').length && !val(
                        '#about_section_text_one_content')) {
                    addClientError($('#about_section_text_one_content'),
                        'About section text one content is required.');
                    errors.push('#about_section_text_one_content');
                }
                if ($('#about_section_text_two_content').length && !val(
                        '#about_section_text_two_content')) {
                    addClientError($('#about_section_text_two_content'),
                        'About section text two content is required.');
                    errors.push('#about_section_text_two_content');
                }
                if ($('#about_section_text_three_content').length && !val(
                        '#about_section_text_three_content')) {
                    addClientError($('#about_section_text_three_content'),
                        'About section text three content is required.');
                    errors.push('#about_section_text_three_content');
                }

                var aboutInput = $('#about_section_image')[0];
                var aboutHasFile = aboutInput && aboutInput.files && aboutInput.files.length > 0;
                if (!existingAboutImage && !aboutHasFile) {
                    addClientError($('#about_section_image'),
                        'About section image is required (existing or new).');
                    errors.push('#about_section_image');
                }
                // else if (aboutHasFile) {
                //     // validate about image: 420x300, max 2MB
                //     var okAbout = await validateImageFile(aboutInput.files[0], 2 * 1024 * 1024, 420, 300, $(
                //         '#about_section_image'), 'About section image');
                //     if (!okAbout) errors.push('#about_section_image');
                // }

                // Top Banner Slider validation:
                var $slides = $('#slider-container .slider-item');
                if ($slides.length === 0) {
                    addClientError($('#add-slide'), 'At least one top banner slide is required.');
                    errors.push('#slider-container');
                } else {
                    // iterate slides and validate title, subtitle and image (file or existing img)
                    var slidePromises = [];
                    $slides.each(function(idx, el) {
                        var $slide = $(el);
                        var slideIndex = idx + 1;
                        var $title = $slide.find('input[name="slider_titles[]"]');
                        var $subtitle = $slide.find('input[name="slider_subtitles[]"]');
                        var $fileInput = $slide.find('input[type="file"][name="slider_images[]"]');
                        var hasExistingImg = $slide.find('img').length > 0;
                        // title
                        if ($title.length && !$.trim($title.val() || '')) {
                            addClientError($title, 'Slide ' + slideIndex + ' title is required.');
                            errors.push($title);
                        }
                        // subtitle
                        if ($subtitle.length && !$.trim($subtitle.val() || '')) {
                            addClientError($subtitle, 'Slide ' + slideIndex +
                                ' subtitle is required.');
                            errors.push($subtitle);
                        }
                        // image: require if no existing img and no file selected
                        var fileObj = ($fileInput.length && $fileInput[0].files && $fileInput[0]
                            .files[0]) ? $fileInput[0].files[0] : null;
                        if (!hasExistingImg && !fileObj) {
                            addClientError($fileInput, 'Slide ' + slideIndex +
                                ' image is required (existing or new).');
                            errors.push($fileInput);
                        }
                        // else if (fileObj) {
                        //     // push promise to validate resolution/size: 1920x550, max 2MB
                        //     slidePromises.push(
                        //         validateImageFile(fileObj, 2 * 1024 * 1024, 1920, 550,
                        //             $fileInput, 'Slide ' + slideIndex + ' image')
                        //         .then(function(ok) {
                        //             if (!ok) errors.push($fileInput);
                        //         })
                        //     );
                        // }
                    });
                    // await all slide image validations
                    if (slidePromises.length) {
                        await Promise.all(slidePromises);
                    }
                }

                // if any errors collected, prevent submission and scroll to first
                if (errors.length) {
                    // we've already prevented default; just scroll to first error
                    var firstSel = errors[0];
                    var $first = $(firstSel);
                    if ($first.length === 0 && typeof firstSel === 'string') {
                        $first = $(firstSel);
                    }
                    if ($first && $first.length) {
                        $('html, body').animate({
                            scrollTop: $first.offset().top - 100
                        }, 300, function() {
                            $first.focus();
                        });
                    }
                    return;
                }
                // no errors -> submit programmatically (remove handler to avoid recursion)
                $('#home-cms-form').off('submit');
                $form.submit();
            });
        })();
    </script>
@endpush
