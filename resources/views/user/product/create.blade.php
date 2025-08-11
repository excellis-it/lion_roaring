@extends('user.layouts.master')
@section('title')
    Product - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
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
                                        value="{{ old('name') }}">
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
                                        @php
                                            $renderCategoryOptions = function ($nodes, $prefix = '') use (
                                                &$renderCategoryOptions,
                                            ) {
                                                foreach ($nodes as $node) {
                                                    echo '<option value="' .
                                                        $node->id .
                                                        '"' .
                                                        (old('parent_id') == $node->id ? ' selected' : '') .
                                                        '>' .
                                                        e($prefix . $node->name) .
                                                        '</option>';
                                                    if (!empty($node->children) && $node->children->count()) {
                                                        $renderCategoryOptions(
                                                            $node->children,
                                                            $prefix . $node->name . '->',
                                                        );
                                                    }
                                                }
                                            };
                                            $topLevelCategories = $categories->whereNull('parent_id');
                                            $renderCategoryOptions($topLevelCategories);
                                        @endphp
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
                                        value="{{ old('price') }}">
                                    @if ($errors->has('price'))
                                        <span class="error">{{ $errors->first('price') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- quantity --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="quantity"> Product Quantity*</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                        value="{{ old('quantity') }}">
                                    @if ($errors->has('quantity'))
                                        <span class="error">{{ $errors->first('quantity') }}</span>
                                    @endif
                                </div>
                            </div> --}}

                            {{-- slug --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="slug"> Product Slug*</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ old('slug') }}">
                                    @if ($errors->has('slug'))
                                        <span class="error">{{ $errors->first('slug') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Product Featured Image*</label>
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
                                        class="form-control" value="{{ old('short_description') }}">
                                    @if ($errors->has('short_description'))
                                        <span class="error">{{ $errors->first('short_description') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- description --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="description"> Product Description*</label>
                                    <textarea name="description" id="description" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Product Description">{{ old('description') }}</textarea>
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
                                        placeholder="Enter Product Specification">{{ old('specification') }}</textarea>
                                    @if ($errors->has('specification'))
                                        <span class="error">{{ $errors->first('specification') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- button_name --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="button_name"> Button Name*</label>
                                    <input type="text" name="button_name" id="button_name" class="form-control"
                                        value="ADD TO CART">
                                    @if ($errors->has('button_name'))
                                        <span class="error">{{ $errors->first('button_name') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- affiliate_link --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="affiliate_link"> Affiliate Link*</label>
                                    <input type="text" name="affiliate_link" id="affiliate_link" class="form-control"
                                        value="{{ old('affiliate_link') }}">
                                    @if ($errors->has('affiliate_link'))
                                        <span class="error">{{ $errors->first('affiliate_link') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- sku --}}
                            {{-- <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="sku"> Product SKU*</label>
                                    <input type="text" name="sku" id="sku" class="form-control"
                                        value="{{ old('sku') }}">
                                    @if ($errors->has('sku'))
                                        <span class="error">{{ $errors->first('sku') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            {{-- feature_product --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="feature_product"> Feature Product*</label>
                                    <select name="feature_product" id="feature_product" class="form-control">
                                        <option value="">Select Feature Product</option>
                                        <option value="1" {{ old('feature_product') == 1 ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="0" {{ old('feature_product') == 0 ? 'selected' : '' }}>No
                                        </option>
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
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <span class="error">{{ $errors->first('status') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="inputConfirmPassword2" class="col-sm-3 col-form-label">Image(Drag and drop
                                    atleast 1
                                    images)<span style="color:red">*<span></label>
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
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Seo Section</h3>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            {{-- <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="meta_title">Meta Title</label>

                                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                                        value="{{ old('meta_title') }}" placeholder="">
                                    @if ($errors->has('meta_title'))
                                        <span class="error">{{ $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="type">Mete Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="5" cols="30"
                                        placeholder="">{{ old('meta_description') }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <span class="error">{{ $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
                            </div> --}}

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('products.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
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
                // auto set slug from name
                $('#name').on('keyup', function() {
                    var name = $(this).val();
                    var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                    $('#slug').val(slug);
                });
            });
        </script>
    @endpush
