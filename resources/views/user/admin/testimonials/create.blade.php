@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Testimonial
@endsection
@push('styles')
@endpush


@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Create Testimonial</h3>
                    <p class="text-muted small mb-0">Add a new testimonial</p>
                </div>
            </div>

            {{-- <div class="card search_bar sales-report-card"> --}}
            <form action="{{ route('user.admin.testimonials.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Testimonial Details</h4>
                    </div>

                    <div class="row">
                         @if (auth()->user()->user_type == 'Global')
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="content_country_code">Content Country*</label>
                                    <select name="content_country_code" id="content_country_code" class="form-control">
                                        @foreach (\App\Models\Country::all() as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
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
                        @endif
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- name --}}
                                    <label for="floatingInputValue">Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="name"
                                        value="{{ old('name') }}" placeholder="Name*">
                                    @if ($errors->has('name'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- Address --}}
                                    <label for="floatingInputValue">Address*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="address"
                                        value="{{ old('address') }}" placeholder="Address*">
                                    @if ($errors->has('address'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('address') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- image --}}
                                    <label for="floatingInputValue">Image*</label>
                                    <input type="file" class="form-control" id="image" name="image"
                                        value="{{ old('image') }}" placeholder="Image*" onchange="readURL(this);">
                                    @if ($errors->has('image'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- image preview -->
                        <div class="col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <img src="" id="image_preview"
                                        style="width: 150px; height: 100px; display:none;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- description --}}
                                    <label for="floatingInputValue">Description*</label>
                                    <textarea name="description" id="description" placeholder="Description" class="form-control">{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="btn-1">
                            <button type="submit" class="print_btn me-2 mt-2 mb-2">Create Testimonial</button>
                            <a href="{{ route('user.admin.testimonials.index') }}" class="print_btn print_btn_vv">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
            {{-- </div> --}}


        </div>
    @endsection

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#description').summernote({
                    placeholder: 'Description',
                    tabsize: 2,
                    height: 400
                });
            });
        </script>
        <script>
            $(document).ready(function() {
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
