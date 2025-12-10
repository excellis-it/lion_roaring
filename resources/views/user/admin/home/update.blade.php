@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Home Page
@endsection
@push('styles')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Update Home Page</h3>
                    <p class="text-muted small mb-0">Manage home page content</p>
                </div>
            </div>

            <form action="{{ route('home-cms.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $home->id ?? '' }}">
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
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
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Menu Section</h4>
                    </div>

                    <div class="row justify-content-between">
                        {{-- courses --}}
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- banner_title --}}
                                    <label for="floatingInputValue">Banner Image</label>
                                    <input type="file" class="form-control" id="banner_image" name="banner_image"
                                        value="{{ old('banner_image') }}" placeholder="Banner Image">
                                    @if ($errors->has('banner_image'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('banner_image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if (isset($home->banner_image))
                                        <img src="{{ Storage::url($home->banner_image) }}" id="banner_image_preview"
                                            alt="Footer Logo" style="width: 180px; height: 100px;">
                                    @else
                                        <img src="" id="banner_image_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px; display:none;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- banner_video --}}
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Banner Video</label>
                                    <input type="file" class="form-control" id="floatingInputValue" name="banner_video"
                                        value="{{ old('banner_video') }}" placeholder="Banner Video">
                                    @if ($errors->has('banner_video'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('banner_video') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if (isset($home->banner_video))
                                        <video controls style="width: 200px; height:100px;">
                                            <source src="{{ Storage::url($home->banner_video) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- our_organization_id --}}
                        <div class="col-md-12 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Banner Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="banner_title"
                                        value="{{ isset($home->banner_title) ? $home->banner_title : old('banner_title') }}"
                                        placeholder="Banner Title">
                                    @if ($errors->has('banner_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('banner_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>About Us Section</h4>
                    </div>

                    <div class="row">
                        {{-- section_1_title --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 1 Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_1_title"
                                        value="{{ isset($home->section_1_title) ? $home->section_1_title : old('section_1_title') }}"
                                        placeholder="Section 1 Title">
                                    @if ($errors->has('section_1_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_1_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_1_sub_title --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 1 Sub Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_1_sub_title"
                                        value="{{ isset($home->section_1_sub_title) ? $home->section_1_sub_title : old('section_1_sub_title') }}"
                                        placeholder="Section 1 Sub Title">
                                    @if ($errors->has('section_1_sub_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_1_sub_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_1_video --}}
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 1 Video</label>
                                    <input type="file" class="form-control" id="floatingInputValue"
                                        name="section_1_video" value="{{ old('section_1_video') }}"
                                        placeholder="Section 1 Video">
                                    @if ($errors->has('section_1_video'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_1_video') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if (isset($home->section_1_video))
                                        <video controls style="width: 200px; height:100px;">
                                            <source src="{{ Storage::url($home->section_1_video) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                    @endif
                                </div>
                            </div>
                        </div>


                        {{-- section_1_description --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 1 Description*</label>
                                    <textarea name="section_1_description" id="section1_des" cols="30" rows="10"
                                        placeholder="Section 1 Description" class="form-control">{{ isset($home->section_1_description) ? $home->section_1_description : old('section_1_description') }}</textarea>
                                    @if ($errors->has('section_1_description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_1_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-3">
                    <div class="form-head">
                        <h4>Book Section</h4>
                    </div>

                    <div class="row">
                        {{-- section_2_left_title --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 2 Left Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_2_left_title"
                                        value="{{ isset($home->section_2_left_title) ? $home->section_2_left_title : old('section_2_left_title') }}"
                                        placeholder="Section 2 Left Title">
                                    @if ($errors->has('section_2_left_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_2_left_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_2_left_image --}}
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 2 Left Image</label>
                                    <input type="file" class="form-control" id="section_2_left"
                                        name="section_2_left_image" value="{{ old('section_2_left_image') }}"
                                        placeholder="Section 2 Left Image">
                                    @if ($errors->has('section_2_left_image'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_2_left_image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if (isset($home->section_2_left_image))
                                        <img src="{{ Storage::url($home->section_2_left_image) }}"
                                            id="section_2_left_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px;">
                                    @else
                                        <img src="" id="section_2_left_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px; display:none;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- section_2_left_description --}}
                        <div class="col-md-12 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 2 Left Description*</label>
                                    <textarea name="section_2_left_description" cols="30" rows="10" placeholder="Section 2 Left Description"
                                        id="section2_left_des" class="form-control">{{ isset($home->section_2_left_description) ? $home->section_2_left_description : old('section_2_left_description') }}</textarea>
                                    @if ($errors->has('section_2_left_description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_2_left_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_2_right_title --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 2 Right Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_2_right_title"
                                        value="{{ isset($home->section_2_right_title) ? $home->section_2_right_title : old('section_2_right_title') }}"
                                        placeholder="Section 2 Right Title">
                                    @if ($errors->has('section_2_right_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_2_right_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_2_right_image --}}
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 2 Right Image</label>
                                    <input type="file" class="form-control" id="section_2_right_image"
                                        name="section_2_right_image" value="{{ old('section_2_right_image') }}"
                                        placeholder="Section 2 Right Image">
                                    @if ($errors->has('section_2_right_image'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_2_right_image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if (isset($home->section_2_right_image))
                                        <img src="{{ Storage::url($home->section_2_right_image) }}"
                                            id="section_2_right_image_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px;">
                                    @else
                                        <img src="" id="section_2_right_image_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px; display:none;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- section_2_right_description --}}
                        <div class="col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 2 Right Description*</label>
                                    <textarea name="section_2_right_description" cols="30" rows="10" placeholder="Section 2 Right Description"
                                        id="section2_right_des" class="form-control">{{ isset($home->section_2_right_description) ? $home->section_2_right_description : old('section_2_right_description') }}</textarea>
                                    @if ($errors->has('section_2_right_description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_2_right_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Our Governance Board Section</h4>
                    </div>

                    <div class="row">
                        {{-- section_3_title --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 3 Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_3_title"
                                        value="{{ isset($home->section_3_title) ? $home->section_3_title : old('section_3_title') }}"
                                        placeholder="Section 3 Title">
                                    @if ($errors->has('section_3_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_3_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_3_description --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 3 Description*</label>
                                    <textarea name="section_3_description" id="section3_des" cols="30" rows="10"
                                        placeholder="Section 3 Description" class="form-control">{{ isset($home->section_3_description) ? $home->section_3_description : old('section_3_description') }}</textarea>
                                    @if ($errors->has('section_3_description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_3_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Organization Section</h4>
                    </div>

                    <div class="row">
                        {{-- section_3_title --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 4 Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_4_title"
                                        value="{{ isset($home->section_4_title) ? $home->section_4_title : old('section_4_title') }}"
                                        placeholder="Section 4 Title">
                                    @if ($errors->has('section_4_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_4_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- section_3_description --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 4 Description*</label>
                                    <textarea name="section_4_description" cols="30" rows="10" placeholder="Section 4 Description"
                                        id="section4_des" class="form-control">{{ isset($home->section_4_description) ? $home->section_4_description : old('section_4_description') }}</textarea>
                                    @if ($errors->has('section_4_description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_4_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Testimonies Section</h4>
                    </div>

                    <div class="row">
                        {{-- section_3_title --}}
                        <div class="col-md-12 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Section 5 Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="section_5_title"
                                        value="{{ isset($home->section_5_title) ? $home->section_5_title : old('section_5_title') }}"
                                        placeholder="Section 5 Title">
                                    @if ($errors->has('section_5_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('section_5_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-3">
                    <div class="form-head">
                        <h4>SEO Management</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Meta Title</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="meta_title"
                                        value="{{ isset($home->meta_title) ? $home->meta_title : old('meta_title') }}"
                                        placeholder="Meta Title">
                                    @if ($errors->has('meta_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('meta_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Meta Keywords</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="meta_keywords"
                                        value="{{ isset($home->meta_keywords) ? $home->meta_keywords : old('meta_keywords') }}"
                                        placeholder="Meta Keywords">
                                    @if ($errors->has('meta_keywords'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('meta_keywords') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta description --}}
                                    <label for="floatingInputValue">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" cols="30" rows="10"
                                        placeholder="Meta Description" class="form-control">{{ isset($home->meta_description) ? $home->meta_description : old('meta_description') }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('meta_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- button --}}
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit" class="print_btn me-2 mt-2 mb-2">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>


    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            // ClassicEditor.create(document.querySelector("#section1_des"));
            // ClassicEditor.create(document.querySelector("#section2_left_des"));
            // ClassicEditor.create(document.querySelector("#section2_right_des"));
            // ClassicEditor.create(document.querySelector("#section3_des"));
            // ClassicEditor.create(document.querySelector("#section4_des"));

            $('#section1_des').summernote({
                placeholder: 'Section 1 Description*',
                tabsize: 2,
                height: 400
            });

            $('#section2_left_des').summernote({
                placeholder: 'Section 2 Left Description*',
                tabsize: 2,
                height: 400
            });

            $('#section2_right_des').summernote({
                placeholder: 'Section 2 Right Description*',
                tabsize: 2,
                height: 400
            });

            $('#section3_des').summernote({
                placeholder: 'Section 3 Description*',
                tabsize: 2,
                height: 400
            });

            $('#section4_des').summernote({
                placeholder: 'Section 4 Description*',
                tabsize: 2,
                height: 400
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('#banner_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#banner_image_preview').show();
                    $('#banner_image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#section_2_left').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#section_2_left_preview').show();
                    $('#section_2_left_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#section_2_left').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#section_2_left_preview').show();
                    $('#section_2_left_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#section_2_right_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#section_2_right_image_preview').show();
                    $('#section_2_right_image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
