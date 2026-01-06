@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Our Organization Page
@endsection
@push('styles')
@endpush


@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Create Our Organization</h3>
                    <p class="text-muted small mb-0">Add a new organization structure</p>
                </div>
            </div>

            <form action="{{ route('user.admin.our-organizations.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Details</h4>
                    </div>
                    @if (auth()->user()->user_type == 'Global')
                        <div class="row">
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
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValueName">Organization Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValueName" name="name"
                                        value="{{ old('name') }}" placeholder="Organization Name">
                                    @if ($errors->has('name'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValueSlug">Slug*</label>
                                    <input type="text" class="form-control" id="floatingInputValueSlug" name="slug"
                                        value="{{ old('slug') }}" placeholder="Slug">
                                    @if ($errors->has('slug'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('slug') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- banner_title --}}
                                    <label for="floatingInputValue">Image*</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    @if ($errors->has('image'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <img src="" id="image_preview"
                                        style="width: 150px; height: 80px; display:none;">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta description --}}
                                    <label for="floatingInputValue">Description*</label>
                                    <textarea name="description" id="description" cols="30" rows="10" placeholder="Description"
                                        class="form-control">{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit" class="print_btn me-2 mt-2 mb-2">Create</button>
                                <a href="{{ route('user.admin.our-organizations.index') }}"
                                    class="print_btn print_btn_vv">Cancel</a>
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
                placeholder: 'Description*',
                tabsize: 2,
                height: 500
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

    <script>
        // Auto-generate slug from name
        $('#floatingInputValueName').on('input', function() {
            let name = $(this).val();
            let slug = name.toLowerCase().trim()
                .replace(/&/g, '-and-') // Replace & with 'and'
                .replace(/[\s\W-]+/g, '-') // Replace spaces and non-word characters with hyphen
                .replace(/^-+|-+$/g, ''); // Remove leading and trailing hyphens
            $('#floatingInputValueSlug').val(slug);
        });
    </script>
@endpush
