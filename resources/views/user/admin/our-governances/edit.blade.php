@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Our Governance Page
@endsection
@push('styles')
@endpush


@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Edit Our Governance</h3>
                    <p class="text-muted small mb-0">Update governance structure details</p>
                </div>
            </div>

            <form action="{{ route('our-governances.update', $our_governance->id) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        {{-- <h4>Menu Section</h4> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="content_country_code">Country</label>
                                    <select name="content_country_code" id="content_country_code" class="form-control">
                                        @foreach (\App\Models\Country::all() as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('content_country_code', $our_governance->country_code ?? 'US') == $country->code ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('content_country_code'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('content_country_code') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-between">
                        {{-- courses --}}
                        <div class="col-md-6">
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
                        <!-- image preview -->

                        <div class="col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if ($our_governance->banner_image)
                                        <img src="{{ Storage::url($our_governance->banner_image) }}"
                                            id="banner_image_preview" style="width: 250px; height: 80px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-3">
                    <div class="form-head">
                        <h4>Details</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Governance Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="name"
                                        value="{{ $our_governance->name ? $our_governance->name : old('name') }}"
                                        placeholder="Governance Name">
                                    @if ($errors->has('name'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- banner_title --}}
                                    <label for="floatingInputValue">Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    @if ($errors->has('image'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if ($our_governance->image)
                                        <img src="{{ Storage::url($our_governance->image) }}" id="image_preview"
                                            style="width: 150px; height: 80px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta description --}}
                                    <label for="floatingInputValue">Description*</label>
                                    <textarea name="description" id="description" cols="30" rows="10" placeholder="Description"
                                        class="form-control">{{ $our_governance->description ? $our_governance->description : old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>SEO Management</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Meta Title</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="meta_title"
                                        value="{{ $our_governance->meta_title ? $our_governance->meta_title : old('meta_title') }}"
                                        placeholder="Meta Title">
                                    @if ($errors->has('meta_title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('meta_title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Meta Keywords</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="meta_keywords"
                                        value="{{ $our_governance->meta_keywords ? $our_governance->meta_keywords : old('meta_keywords') }}"
                                        placeholder="Meta Keywords">
                                    @if ($errors->has('meta_keywords'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('meta_keywords') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta description --}}
                                    <label for="floatingInputValue">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" cols="30" rows="10"
                                        placeholder="Meta Description" class="form-control">{{ $our_governance->meta_description ? $our_governance->meta_description : old('meta_description') }}</textarea>
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
                                <button type="submit" class="print_btn me-2 mt-2">Update</button>
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
        $(".select2").select2({
            placeholder: "Select a Course",
            allowClear: true,
        });
    </script>

    <script>
        $(document).ready(function() {
            // ClassicEditor.create(document.querySelector("#description"));
            $('#description').summernote({
                placeholder: 'Description',
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

            $('#image').change(function() {

                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview').show();
                    $('#image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
