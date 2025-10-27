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
                    <form action="{{ route('user.elearning-cms.home.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($cms->id) ? $cms->id : '' }}">

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
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Home Cms Content </h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Banner Image</label>
                                    <input type="file" name="banner_image" id="image" class="form-control"
                                        value="{{ old('banner_image') }}">
                                    @if ($errors->has('banner_image'))
                                        <span class="error">{{ $errors->first('banner_image') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- view image --}}
                            @if (isset($cms->banner_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Banner Image</label>
                                        <img src="{{ Storage::url($cms->banner_image) }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Banner Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif
                            {{-- banner_title --}}
                            <div class="col-md-6 mb-2">
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
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="banner_subtitle"> Banner Subtitle*</label>
                                    <input type="text" name="banner_subtitle" id="banner_subtitle" class="form-control"
                                        value="{{ isset($cms->banner_subtitle) ? $cms->banner_subtitle : old('banner_subtitle') }}">
                                    @if ($errors->has('banner_subtitle'))
                                        <span class="error">{{ $errors->first('banner_subtitle') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- product_category_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="product_category_title"> Product Category Title*</label>
                                    <input type="text" name="product_category_title" id="product_category_title"
                                        class="form-control"
                                        value="{{ isset($cms->product_category_title) ? $cms->product_category_title : old('product_category_title') }}">
                                    @if ($errors->has('product_category_title'))
                                        <span class="error">{{ $errors->first('product_category_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- product_category_subtitle --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="product_category_subtitle"> Product Category Subtitle*</label>
                                    <input type="text" name="product_category_subtitle" id="product_category_subtitle"
                                        class="form-control"
                                        value="{{ isset($cms->product_category_subtitle) ? $cms->product_category_subtitle : old('product_category_subtitle') }}">
                                    @if ($errors->has('product_category_subtitle'))
                                        <span class="error">{{ $errors->first('product_category_subtitle') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- featured_product_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="featured_product_title"> Featured Product Title*</label>
                                    <input type="text" name="featured_product_title" id="featured_product_title"
                                        class="form-control"
                                        value="{{ isset($cms->featured_product_title) ? $cms->featured_product_title : old('featured_product_title') }}">
                                    @if ($errors->has('featured_product_title'))
                                        <span class="error">{{ $errors->first('featured_product_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- featured_product_subtitle --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="featured_product_subtitle"> Featured Product Subtitle*</label>
                                    <input type="text" name="featured_product_subtitle" id="featured_product_subtitle"
                                        class="form-control"
                                        value="{{ isset($cms->featured_product_subtitle) ? $cms->featured_product_subtitle : old('featured_product_subtitle') }}">
                                    @if ($errors->has('featured_product_subtitle'))
                                        <span class="error">{{ $errors->first('featured_product_subtitle') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- new_product_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="new_product_title"> New Product Title*</label>
                                    <input type="text" name="new_product_title" id="new_product_title"
                                        class="form-control"
                                        value="{{ isset($cms->new_product_title) ? $cms->new_product_title : old('new_product_title') }}">
                                    @if ($errors->has('new_product_title'))
                                        <span class="error">{{ $errors->first('new_product_title') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- new_product_subtitle --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="new_product_subtitle"> New Product Subtitle*</label>
                                    <input type="text" name="new_product_subtitle" id="new_product_subtitle"
                                        class="form-control"
                                        value="{{ isset($cms->new_product_subtitle) ? $cms->new_product_subtitle : old('new_product_subtitle') }}">
                                    @if ($errors->has('new_product_subtitle'))
                                        <span class="error">{{ $errors->first('new_product_subtitle') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('user.elearning-cms.list') }}"
                                    class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
