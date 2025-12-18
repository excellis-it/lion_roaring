@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Contact Us Page
@endsection
@push('styles')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Update Contact Us Page</h3>
                    <p class="text-muted small mb-0">Manage contact us page content</p>
                </div>
            </div>

            <form action="{{ route('user.admin.contact-us-cms.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $contact_us->id ?? '' }}">
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Menu Section</h4>
                    </div>

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
                                    @if (isset($contact_us->banner_image))
                                        <img src="{{ Storage::url($contact_us->banner_image) }}" alt="banner_image"
                                            id="preview_banner_image" style="width: 180px; height: 100px;">
                                    @else
                                        <img src="" alt="banner_image" id="preview_banner_image"
                                            style="width: 180px; height: 100px;display:none;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- our_organization_id --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Banner Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="banner_title"
                                        value="{{ isset($contact_us->banner_title) ? $contact_us->banner_title : old('banner_title') }}"
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
                <div class="sales-report-card-wrap mt-3">
                    <div class="form-head">
                        <h4>Details</h4>
                    </div>

                    <div class="row">

                        {{-- phone --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Call Us*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="phone"
                                        value="{{ isset($contact_us->phone) ? $contact_us->phone : old('phone') }}"
                                        placeholder="Call Us">
                                    @if ($errors->has('phone'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- email --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Email Us*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="email"
                                        value="{{ isset($contact_us->email) ? $contact_us->email : old('email') }}"
                                        placeholder="Email Us">
                                    @if ($errors->has('email'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- title --}}
                        <div class="col-xl-12 col-md-12 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="title"
                                        value="{{ isset($contact_us->title) ? $contact_us->title : old('title') }}"
                                        placeholder="Title">
                                    @if ($errors->has('title'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('title') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- address --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Write Us*</label>
                                    <textarea type="text" class="form-control" id="write-us" name="address" placeholder="Write Us">{{ isset($contact_us->address) ? $contact_us->address : old('address') }}</textarea>
                                    @if ($errors->has('address'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('address') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta description --}}
                                    <label for="floatingInputValue">Description*</label>
                                    <textarea name="description" id="description" cols="30" rows="10" placeholder="Description"
                                        class="form-control">{{ isset($contact_us->description) ? $contact_us->description : old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <div class="error" style="color:red;">
                                            {{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
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
        $('#write-us').summernote({
            placeholder: 'Write Us*',
            tabsize: 2,
            height: 500
        });

        $('#description').summernote({
            placeholder: 'Description*',
            tabsize: 2,
            height: 500
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#banner_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#preview_banner_image').show();
                    $('#preview_banner_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
