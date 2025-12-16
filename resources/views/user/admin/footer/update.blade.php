@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Footer
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
          
                <form action="{{ route('user.admin.footer.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $footer->id ?? '' }}">
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
                            <h4>Footer Section</h4>
                        </div>
                        <div class="row justify-content-between">
                            {{-- courses --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Footer logo</label>
                                        <input type="file" class="form-control" id="footer_logo" name="footer_logo"
                                            value="{{ old('footer_logo') }}" placeholder="Footer Logo">
                                        @if ($errors->has('footer_logo'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_logo') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- view footer logo --}}

                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        @if (isset($footer->footer_logo))
                                            <img src="{{ Storage::url($footer->footer_logo) }}" id="footer_logo_preview"
                                                alt="Footer Logo" style="width: 100px; height: 100px;">
                                        @else
                                            <img src="" id="footer_logo_preview" alt="Footer Logo"
                                                style="width: 100px; height: 100px; display:none;">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- courses --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Footer Flag</label>
                                        <input type="file" class="form-control" id="footer_flag" name="footer_flag"
                                            value="{{ old('footer_flag') }}" placeholder="Footer Logo">
                                        @if ($errors->has('footer_flag'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_flag') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- view footer logo --}}

                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        @if (isset($footer->footer_flag))
                                            <img src="{{ Storage::url($footer->footer_flag) }}" id="footer_flag_preview"
                                                alt="Footer Logo" style="width: 100px; height: 100px;">
                                        @else
                                            <img src="" id="footer_flag_preview" alt="Footer Logo"
                                                style="width: 100px; height: 100px; display:none;">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- our_organization_id --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="footer_title">Footer Title*</label>
                                        <textarea type="text" class="form-control" id="footer_title" name="footer_title" placeholder="Footer Title"> {{ isset($footer->footer_title) ? $footer->footer_title : old('footer_title') }}</textarea>
                                        @if ($errors->has('footer_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_address_title --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="footer_address_title">Footer Address Title*</label>
                                        <input type="text" class="form-control" id="footer_address_title"
                                            name="footer_address_title"
                                            value="{{ isset($footer->footer_address_title) ? $footer->footer_address_title : old('footer_address_title') }}"
                                            placeholder="Footer Address Title">
                                        @if ($errors->has('footer_address_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_address_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_address --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="footer_address">Footer Address*</label>
                                        <textarea type="text" class="form-control" id="footer_address" name="footer_address" placeholder="Footer Address"> {{ isset($footer->footer_address) ? $footer->footer_address : old('footer_address') }}</textarea>
                                        @if ($errors->has('footer_address'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_address') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_phone_number --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Phone Number*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_phone_number"
                                            value="{{ isset($footer->footer_phone_number) ? $footer->footer_phone_number : old('footer_phone_number') }}"
                                            placeholder="Footer Phone Number">
                                        @if ($errors->has('footer_phone_number'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_phone_number') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_email --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Email*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_email"
                                            value="{{ isset($footer->footer_email) ? $footer->footer_email : old('footer_email') }}"
                                            placeholder="Footer Email">
                                        @if ($errors->has('footer_email'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_email') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- footer_newsletter_title --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Newsletter Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_newsletter_title"
                                            value="{{ isset($footer->footer_newsletter_title) ? $footer->footer_newsletter_title : old('footer_newsletter_title') }}"
                                            placeholder="Footer Newsletter Title">
                                        @if ($errors->has('footer_newsletter_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_newsletter_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_copywrite_text --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Copywrite Text*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_copywrite_text"
                                            value="{{ isset($footer->footer_copywrite_text) ? $footer->footer_copywrite_text : old('footer_copywrite_text') }}"
                                            placeholder="Footer Copywrite Text">
                                        @if ($errors->has('footer_copywrite_text'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_copywrite_text') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_playstore_icon --}}
                            <div class="col-md-4 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Footer Playstore Icon</label>
                                        <input type="file" class="form-control" id="footer_play_icon"
                                            name="footer_playstore_icon" value="{{ old('footer_playstore_icon') }}"
                                            placeholder="Footer Playstore Icon">
                                        @if ($errors->has('footer_playstore_icon'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_playstore_icon') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- view footer playstore icon --}}

                            <div class="col-md-2 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        @if (isset($footer->footer_playstore_icon))
                                            <img src="{{ Storage::url($footer->footer_playstore_icon) }}"
                                                alt="Footer Playstore Icon" id="prev_footer_play_icon"
                                                style="width: 100px; height: 100px;">
                                        @else
                                            <img src="" alt="Footer Playstore Icon" id="prev_footer_play_icon"
                                                style="width: 100px; height: 100px; display:none;">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- footer_playstore_link --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Playstore Link</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_playstore_link"
                                            value="{{ isset($footer->footer_playstore_link) ? $footer->footer_playstore_link : old('footer_playstore_link') }}"
                                            placeholder="Footer Playstore Link">
                                        @if ($errors->has('footer_playstore_link'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_playstore_link') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_appstore_icon --}}
                            <div class="col-md-4 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Footer Appstore Icon</label>
                                        <input type="file" class="form-control" id="footer_app_icon"
                                            name="footer_appstore_icon" value="{{ old('footer_appstore_icon') }}"
                                            placeholder="Footer Appstore Icon">
                                        @if ($errors->has('footer_appstore_icon'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_appstore_icon') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- view footer appstore icon --}}

                            <div class="col-md-2 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        @if (isset($footer->footer_appstore_icon))
                                            <img src="{{ Storage::url($footer->footer_appstore_icon) }}"
                                                alt="Footer Playstore Icon" id="prev_footer_app_icon"
                                                style="width: 100px; height: 100px;">
                                        @else
                                            <img src="" alt="Footer Playstore Icon" id="prev_footer_app_icon"
                                                style="width: 100px; height: 100px; display:none;">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- footer_appstore_link --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Appstore Link</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_appstore_link"
                                            value="{{ isset($footer->footer_appstore_link) ? $footer->footer_appstore_link : old('footer_appstore_link') }}"
                                            placeholder="Footer Appstore Link">
                                        @if ($errors->has('footer_appstore_link'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_appstore_link') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sales-report-card-wrap mt-3">
                        <div class="form-head">
                            <h4>Social Link</h4>
                        </div>
                        <div class="row count-class" id="add-more">
                            @if (isset($social_links) && count($social_links) > 0)
                                @foreach ($social_links as $key => $social_link)
                                    <div class="col-xl-5 col-md-5 mt-4">
                                        <div class="form-group-div">
                                            <div class="form-group">
                                                {{-- meta title --}}
                                                <label for="floatingInputValue">Class</label>
                                                <input type="text" class="form-control" id="floatingInputValue"
                                                    name="class[]" value="{{ $social_link->class }}" required
                                                    placeholder="Class">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-4">
                                        <div class="form-group-div">
                                            <div class="form-group">
                                                {{-- banner_title --}}
                                                <label for="floatingInputValue">Url</label>
                                                <input type="text" class="form-control" id="floatingInputValue"
                                                    name="url[]" value="{{ $social_link->url }}" required
                                                    placeholder="Url">
                                            </div>
                                        </div>
                                    </div>
                                    @if ($key == 0)
                                        <div class="col-xl-2 mt-4">
                                            <div class="btn-1">
                                                <button type="button" class="add-more"><i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-xl-2 mt-4">
                                            <div class="btn-1">
                                                <button type="button" class="remove"><i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="col-xl-5 col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- meta title --}}
                                            <label for="floatingInputValue">Class</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="class[]" value="" required placeholder="Class">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- banner_title --}}
                                            <label for="floatingInputValue">Url</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="url[]" value="" required placeholder="Url">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 mt-4">
                                    <div class="btn-1">
                                        <button type="button" class="add-more"><i class="fas fa-plus"></i> </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                  <button type="submit" class="print_btn me-2 mt-2 mb-2">Update</button>
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
        // ClassicEditor.create(document.querySelector("#footer_address"));
        // ClassicEditor.create(document.querySelector("#footer_title"));

        $('#footer_title').summernote({
            placeholder: 'Footer Title*',
            tabsize: 2,
            height: 100
        });

        $('#footer_address').summernote({
            placeholder: 'Footer Address*',
            tabsize: 2,
            height: 100
        });
    </script>
    <script>
        $(document).on("click", ".add-more", function() {
            var count = $("#add-more .col-xl-5").length;
            var column_count = $('#column_count').val();
            column_count = parseInt(column_count) + 1;
            $('#column_count').val(column_count);

            var html = `<div class="col-xl-5 col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Class</label>
                                            <input type="text" class="form-control" id="floatingInputValue" name="class[]" value="" required
                                                placeholder="Class">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Url</label>
                                            <input type="text" class="form-control" id="floatingInputValue" name="url[]" value="" required
                                                placeholder="Url">

                                        </div>
                                    </div>
                                </div>
                    <div class="col-xl-2 mt-4">
                                <div class="btn-1">
                                    <button type="button" class="remove"><i class="fas fa-minus"></i> </button>
                                </div>
                            </div>`;
            $("#add-more").append(html);
            ClassicEditor.create(document.querySelectorAll('.content')[count]);
        });

        $(document).on("click", ".remove", function() {
            $(this).parent().parent().prev().remove();
            $(this).parent().parent().prev().remove();
            $(this).parent().parent().remove();
            var column_count = $('#column_count').val();
            $('#column_count').val(column_count - 1);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#footer_logo').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#footer_logo_preview').show();
                    $('#footer_logo_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            $('#footer_flag').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#footer_flag_preview').show();
                    $('#footer_flag_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            $('#footer_play_icon').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#prev_footer_play_icon').show();
                    $('#prev_footer_play_icon').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            $('#footer_app_icon').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#prev_footer_app_icon').show();
                    $('#prev_footer_app_icon').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
