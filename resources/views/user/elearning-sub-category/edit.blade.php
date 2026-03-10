@extends('user.layouts.master')
@section('title')
    Edit E-learning Sub Category - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('elearning-sub-categories.update', $subcategory->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Main Section</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="elearning_category_id"> Category*</label>
                                    <select name="elearning_category_id" id="elearning_category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ (old('elearning_category_id') ?? $subcategory->elearning_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('elearning_category_id'))
                                        <span class="error">{{ $errors->first('elearning_category_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Sub Category Name*</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name') ?? $subcategory->name }}"
                                        placeholder="Enter Sub Category Name">
                                    @if ($errors->has('name'))
                                        <span class="error">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- slug --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="slug"> Sub Category Slug*</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ old('slug') ?? $subcategory->slug }}"
                                        placeholder="Enter Sub Category Slug">
                                    @if ($errors->has('slug'))
                                        <span class="error">{{ $errors->first('slug') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Sub Category Image</label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        placeholder="Enter Sub Category Image" accept="image/*">
                                    @if ($subcategory->image)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($subcategory->image) }}"
                                                alt="{{ $subcategory->name }}" style="width: 100px;">
                                        </div>
                                    @endif
                                    @if ($errors->has('image'))
                                        <span class="error">{{ $errors->first('image') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- status --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status"> Status*</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1"
                                            {{ (old('status') ?? $subcategory->status) == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ (old('status') ?? $subcategory->status) == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <span class="error">{{ $errors->first('status') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                                        value="{{ old('meta_title') ?? $subcategory->meta_title }}"
                                        placeholder="Enter Meta Title">
                                    @if ($errors->has('meta_title'))
                                        <span class="error">{{ $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="meta_description">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Meta Description">{{ old('meta_description') ?? $subcategory->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <span class="error">{{ $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('elearning-sub-categories.index') }}"
                                    class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $('#name').on('keyup', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });
        </script>
    @endpush
