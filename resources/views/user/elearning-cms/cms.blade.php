@extends('user.layouts.master')
@section('title')
    Cms - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('user.elearning-cms.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($cms->id) ? $cms->id : '' }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Update CMS Page</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-5">
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
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Page Banner Image *</label>
                                    <input type="file" name="page_banner_image" id="image" class="form-control"
                                        value="{{ old('page_banner_image') }}">
                                    @if ($errors->has('page_banner_image'))
                                        <span class="error">{{ $errors->first('page_banner_image') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if (isset($cms->page_banner_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Page Banner Image</label>
                                        <img src="{{ Storage::url($cms->page_banner_image) }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Page Banner Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif
                            {{-- page_name --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="page_name"> Page Name *</label>
                                    <input type="text" name="page_name" id="page_name" class="form-control"
                                        value="{{ isset($cms->page_name) ? $cms->page_name : old('page_name') }}" placeholder="" readonly>
                                    @if ($errors->has('page_name'))
                                        <span class="error">{{ $errors->first('page_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- page_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="page_title"> Page Title *</label>
                                    <input type="text" name="page_title" id="page_title" class="form-control"
                                        value="{{ isset($cms->page_title) ? $cms->page_title : old('page_title') }}"
                                        placeholder="" readonly>
                                    @if ($errors->has('page_title'))
                                        <span class="error">{{ $errors->first('page_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- slug --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="slug"> Page Slug *</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ isset($cms->slug) ? $cms->slug : old('slug') }}" placeholder="" readonly>
                                    @if ($errors->has('slug'))
                                        <span class="error">{{ $errors->first('slug') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- page_content --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="page_content"> Page Content *</label>
                                    <textarea name="page_content" id="page_content" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Page Content">{{ isset($cms->page_content) ? $cms->page_content : old('page_content') }}</textarea>
                                    @if ($errors->has('page_content'))
                                        <span class="error">{{ $errors->first('page_content') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('user.elearning-cms.list') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <script>
            ClassicEditor.create(document.querySelector("#page_content"));
        </script>
    @endpush
