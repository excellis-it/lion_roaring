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
                    <form action="{{ route('user.store-cms.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Add CMS Page</h3>
                                </div>
                                <div class="alert alert-info">
                                    <strong>Tip:</strong> To set the background image of e-store pages (the inner page
                                    banner), create a CMS page with the following slug values and upload a Page Banner
                                    Image:
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <ul class="mb-1">
                                                <li><code>home</code> (Global fallback via Home CMS Banner Image)</li>
                                                <li><code>products</code> (Products list)</li>
                                                <li><code>product-details</code> (Product details)</li>
                                                <li><code>cart</code> (Cart)</li>
                                                <li><code>checkout</code> (Checkout)</li>
                                                <li><code>my-orders</code> (My Orders)</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-1">
                                                <li><code>order-details</code> (Order details)</li>
                                                <li><code>order-success</code> (Order success)</li>
                                                <li><code>order-tracking</code> (Public order tracking)</li>
                                                <li><code>wishlist</code> (Wishlist)</li>
                                                <li><code>profile</code> (My profile)</li>
                                                <li><code>change-password</code> (My password)</li>
                                                <li><code>product-not-available</code> (Product not available)</li>
                                            </ul>
                                        </div>
                                    </div>
                                    If a page-specific slug is missing, the system falls back to the Home CMS Banner Image;
                                    otherwise, the default theme image is used.
                                </div>
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
                            {{-- page_name --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="page_name"> Page Name *</label>
                                    <input type="text" name="page_name" id="page_name" class="form-control"
                                        value="{{ old('page_name') }}" placeholder="">
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
                                        value="{{ old('page_title') }}" placeholder="">
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
                                        value="{{ old('slug') }}" placeholder="">
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
                                        placeholder="Enter Page Content">{{ old('page_content') }}</textarea>
                                    @if ($errors->has('page_content'))
                                        <span class="error">{{ $errors->first('page_content') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('user.store-cms.list') }}" class="print_btn print_btn_vv">Cancel</a>
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
