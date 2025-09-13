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
                    <form action="{{ route('user.store-cms.home.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($cms->id) ? $cms->id : '' }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Home Cms Content </h3>
                                </div>
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

                            {{-- new_arrival_title --}}
                            <div class="col-md-6 mb-2">
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
                            <div class="col-md-6 mb-2">
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


                            {{-- banner image --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Banner Image</label>
                                    <input type="file" name="banner_image" id="image" class="form-control"
                                        value="{{ old('banner_image') }}">
                                    @if ($errors->has('banner_image'))
                                        <span class="error">{{ $errors->first('banner_image') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- view image --}}
                            @if (isset($cms->banner_image))
                                {{-- <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Banner Image</label>
                                        <img src="{{ Storage::url($cms->banner_image) }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div> --}}
                            @else
                                {{-- <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Banner Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div> --}}
                            @endif

                            {{-- small banner image --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Small Banner Image</label>
                                    <input type="file" name="banner_image_small" id="image" class="form-control"
                                        value="{{ old('banner_image_small') }}">
                                    @if ($errors->has('banner_image_small'))
                                        <span class="error">{{ $errors->first('banner_image_small') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- view image --}}
                            @if (isset($cms->banner_image_small))
                                {{-- <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Small Banner Image</label>
                                        <img src="{{ Storage::url($cms->banner_image_small) }}" alt="Small Banner Image"
                                            class="img-thumbnail" style="width: 100px; height:  100px;">
                                    </div>
                                </div> --}}
                            @else
                                {{-- <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Small Banner Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}"
                                            alt="Small Banner Image" class="img-thumbnail"
                                            style="width: 100px; height: 100px;">
                                    </div>
                                </div> --}}
                            @endif

                            {{-- product_category_image --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="product_category_image"> Product Category Image</label>
                                    <input type="file" name="product_category_image" id="product_category_image"
                                        class="form-control" value="{{ old('product_category_image') }}">
                                    @if ($errors->has('product_category_image'))
                                        <span class="error">{{ $errors->first('product_category_image') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- view product_category_image --}}
                            {{-- @if (isset($cms->product_category_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> Product Category Image</label>
                                        <img src="{{ Storage::url($cms->product_category_image) }}"
                                            alt="Product Category Image" class="img-thumbnail"
                                            style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> Product Category Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}"
                                            alt="Product Category Image" class="img-thumbnail"
                                            style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif --}}

                            {{-- featured_product_image --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="featured_product_image"> Featured Product Image</label>
                                    <input type="file" name="featured_product_image" id="featured_product_image"
                                        class="form-control" value="{{ old('featured_product_image') }}">
                                    @if ($errors->has('featured_product_image'))
                                        <span class="error">{{ $errors->first('featured_product_image') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- view featured_product_image --}}
                            {{-- @if (isset($cms->featured_product_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> Featured Product Image</label>
                                        <img src="{{ Storage::url($cms->featured_product_image) }}"
                                            alt="Featured Product Image" class="img-thumbnail"
                                            style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> Featured Product Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}"
                                            alt="Featured Product Image" class="img-thumbnail"
                                            style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif --}}

                            {{-- new_product_image --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="new_product_image"> New Product Image</label>
                                    <input type="file" name="new_product_image" id="new_product_image"
                                        class="form-control" value="{{ old('new_product_image') }}">
                                    @if ($errors->has('new_product_image'))
                                        <span class="error">{{ $errors->first('new_product_image') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- view new_product_image --}}
                            {{-- @if (isset($cms->new_product_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> New Product Image</label>
                                        <img src="{{ Storage::url($cms->new_product_image) }}" alt="New Product Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> New Product Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="New Product Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif --}}

                            {{-- new_arrival_image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="new_arrival_image"> New Arrival Image</label>
                                    <input type="file" name="new_arrival_image" id="new_arrival_image"
                                        class="form-control" value="{{ old('new_arrival_image') }}">
                                    @if ($errors->has('new_arrival_image'))
                                        <span class="error">{{ $errors->first('new_arrival_image') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- view new_arrival_image --}}
                            @if (isset($cms->new_arrival_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> New Arrival Image</label>
                                        <img src="{{ Storage::url($cms->new_arrival_image) }}" alt="New Arrival Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label> New Arrival Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="New Arrival Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif

                            <!-- Dynamic Slider Section -->
                            <div class="col-md-12 mb-4">
                                <div class="heading_box mb-3">
                                    <h4>Banner Slider Management</h4>
                                </div>

                                <div id="slider-container">
                                    @php
                                        $sliderData = isset($cms->slider_data)
                                            ? json_decode($cms->slider_data, true)
                                            : [];
                                    @endphp

                                    @if (count($sliderData) > 0)
                                        @foreach ($sliderData as $index => $slide)
                                            <div class="slider-item mb-4 p-3 border rounded">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="box_label">
                                                            <label>Slide {{ $index + 1 }} Title</label>
                                                            <input type="text" name="slider_titles[]"
                                                                class="form-control" value="{{ $slide['title'] ?? '' }}"
                                                                placeholder="Enter slide title">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="box_label">
                                                            <label>Slide {{ $index + 1 }} Subtitle</label>
                                                            <input type="text" name="slider_subtitles[]"
                                                                class="form-control"
                                                                value="{{ $slide['subtitle'] ?? '' }}"
                                                                placeholder="Enter slide subtitle">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="box_label">
                                                            <label>Slide {{ $index + 1 }} Image</label>
                                                            <input type="file" name="slider_images[]"
                                                                class="form-control" accept="image/*">
                                                            @if (isset($slide['image']) && $slide['image'])
                                                                <div class="mt-2">
                                                                    <img src="{{ Storage::url($slide['image']) }}"
                                                                        alt="Slide Image" class="img-thumbnail"
                                                                        style="width: 100px; height: 60px; object-fit: cover;">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                                class="fa-solid fa-trash"></i></button>
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    @else
                                        <div class="slider-item mb-4 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="box_label">
                                                        <label>Slide 1 Title</label>
                                                        <input type="text" name="slider_titles[]" class="form-control"
                                                            placeholder="Enter slide title">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="box_label">
                                                        <label>Slide 1 Subtitle</label>
                                                        <input type="text" name="slider_subtitles[]"
                                                            class="form-control" placeholder="Enter slide subtitle">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="box_label">
                                                        <label>Slide 1 Image</label>
                                                        <input type="file" name="slider_images[]" class="form-control"
                                                            accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm mt-2 remove-slide"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" id="add-slide" class="btn btn-primary">Add New Slide</button>
                            </div>

                            <div class="">


                                <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                    <button type="submit" class="print_btn me-2">Update</button>
                                    <a href="{{ route('user.store-cms.list') }}"
                                        class="print_btn print_btn_vv">Cancel</a>
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
                    <div class="slider-item mb-4 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Title</label>
                                    <input type="text" name="slider_titles[]" class="form-control"
                                        placeholder="Enter slide title">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Subtitle</label>
                                    <input type="text" name="slider_subtitles[]" class="form-control"
                                        placeholder="Enter slide subtitle">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box_label">
                                    <label>Slide ${slideIndex} Image</label>
                                    <input type="file" name="slider_images[]" class="form-control" accept="image/*">
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
        @endpush
