@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Gallery
@endsection
@push('styles')
@endpush


@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Update Gallery</h3>
                    <p class="text-muted small mb-0">Update Gallery</p>
                </div>
            </div>
            <form action="{{ route('gallery.update', $gallery->id) }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        {{-- <h4>Gallery Details</h4> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="content_country_code">Content Country*</label>
                                    <select name="content_country_code" id="content_country_code" class="form-control">
                                        @foreach (\App\Models\Country::all() as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('content_country_code', $gallery->country_code ?? 'US') == $country->code ? 'selected' : '' }}>
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
                        <div class="col-md-4">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- question --}}
                                    <label for="floatingInputValue">Gallery Image*</label>
                                    <input type="file" class="form-control" id="gallery_image" name="image"
                                        accept="image/*" value="{{ old('image') }}" placeholder="Gallery Image*">
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
                                    ` @if (isset($gallery->image))
                                        <img src="{{ Storage::url($gallery->image) }}" id="gallery_image_preview"
                                            alt="Footer Logo" style="width: 180px; height: 100px;">
                                    @else
                                        <img src="" id="footer_logo_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px; display:none;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="btn-1">
                                <button type="submit" class="print_btn me-2 mt-2">Update Gallery</button>
                                <a href="{{ route('gallery.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#gallery_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#gallery_image_preview').show();
                    $('#gallery_image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
