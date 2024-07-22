@extends('user.layouts.master')
@section('title')
    Product Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }
    </style>
     <style>
        .image-area {
            position: relative;
            width: 15%;
            background: #333;
        }

        .image-area img {
            max-width: 100%;
            height: auto;
        }

        .remove-image {
            display: none;
            position: absolute;
            top: -10px;
            right: -10px;
            border-radius: 10em;
            padding: 2px 6px 3px;
            text-decoration: none;
            font: 700 21px/20px sans-serif;
            background: #555;
            border: 3px solid #fff;
            color: #FFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5), inset 0 2px 4px rgba(0, 0, 0, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            -webkit-transition: background 0.5s;
            transition: background 0.5s;
        }

        .remove-image:hover {
            background: #E54E4E;
            padding: 3px 7px 5px;
            top: -11px;
            right: -11px;
        }

        .remove-image:active {
            background: #E54E4E;
            top: -10px;
            right: -11px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Product Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Product Name*</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $product->name }}" placeholder="">
                                    @if ($errors->has('name'))
                                        <span class="error">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- category_id --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="category_id"> Category*</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('category_id'))
                                        <span class="error">{{ $errors->first('category_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- price --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="price"> Product Price*</label>
                                    <input type="text" name="price" id="price" class="form-control"
                                        value="{{ $product->price }}">
                                    @if ($errors->has('price'))
                                        <span class="error">{{ $errors->first('price') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- quantity --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="quantity"> Product Quantity*</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                        value="{{ $product->quantity }}">
                                    @if ($errors->has('quantity'))
                                        <span class="error">{{ $errors->first('quantity') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- slug --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="slug"> Product Slug*</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ $product->slug }}">
                                    @if ($errors->has('slug'))
                                        <span class="error">{{ $errors->first('slug') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Product Featured Image</label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        value="{{ old('image') }}">
                                    @if ($errors->has('image'))
                                        <span class="error">{{ $errors->first('image') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- short_description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="short_description"> Product Short Description*</label>
                                    <input type="text" name="short_description" id="short_description"
                                        class="form-control" value="{{ $product->short_description }}">
                                    @if ($errors->has('short_description'))
                                        <span class="error">{{ $errors->first('short_description') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- affiliate_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="affiliate_link"> Affiliate Link*</label>
                                    <input type="text" name="affiliate_link" id="affiliate_link" class="form-control"
                                        value="{{ $product->affiliate_link }}">
                                    @if ($errors->has('affiliate_link'))
                                        <span class="error">{{ $errors->first('affiliate_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- sku --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="sku"> Product SKU*</label>
                                    <input type="text" name="sku" id="sku" class="form-control"
                                        value="{{ $product->sku }}">
                                    @if ($errors->has('sku'))
                                        <span class="error">{{ $errors->first('sku') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- feature_product --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="feature_product"> Feature Product*</label>
                                    <select name="feature_product" id="feature_product" class="form-control">
                                        <option value="">Select Feature Product</option>
                                        <option value="1" {{ $product->feature_product == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $product->feature_product == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                    @if ($errors->has('feature_product'))
                                        <span class="error">{{ $errors->first('feature_product') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- status --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status"> Status*</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <span class="error">{{ $errors->first('status') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- description --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="description"> Product Description*</label>
                                    <textarea name="description" id="description" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Product Description">{{ $product['description'] }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="error">{{ $errors->first('description') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- specification --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="specification"> Product Specification*</label>
                                    <textarea name="specification" id="specification" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Product Specification">{{ $product['specification'] }}</textarea>
                                    @if ($errors->has('specification'))
                                        <span class="error">{{ $errors->first('specification') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image(Drag and drop
                                    atleast 1
                                    images)</label>
                                <input type="file" class="form-control dropzone" id="image-upload" name="images[]"
                                    multiple>
                                @if ($errors->has('images.*'))
                                    <div class="error" style="color:red;">
                                        {{ $errors->first('images.*') }}</div>
                                @endif
                                @if ($errors->has('images'))
                                    <div class="error" style="color:red;">
                                        {{ $errors->first('images') }}</div>
                                @endif
                            </div>
                        </div>
                        @if ($product->withOutMainImage)
                        <div class="row mb-6">
                            <label for="inputConfirmPassword2"
                                class="col-form-label">Image Preview</label>

                            @foreach ($product->withOutMainImage as $image)
                                <div class="image-area m-4" id="{{ $image->id }}">
                                    <img src="{{ Storage::url($image->image) }}"
                                        alt="Preview">
                                    <a class="remove-image" href="javascript:void(0);"
                                        data-id="{{ $image->id }}"
                                        style="display: inline;">&#215;</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Seo Section</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="meta_title">Meta Title</label>

                                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                                        value="{{ $product->meta_title }}" placeholder="">
                                    @if ($errors->has('meta_title'))
                                        <span class="error">{{ $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="type">Mete Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="5" cols="30"
                                        placeholder="">{{ $product->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <span class="error">{{ $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('products.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
        </script>
        <script>
            ClassicEditor.create(document.querySelector("#description"));
            ClassicEditor.create(document.querySelector("#specification"));
        </script>
        <script>
            $(document).ready(function() {
                $('.remove-image').click(function() {
                    var id = $(this).data('id');
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax({
                        url: "{{ route('products.image.delete') }}",
                        type: 'GET',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function() {
                            console.log("it Works");
                            $('#' + id).remove();
                        }
                    });
                });
            });
        </script>
    @endpush
