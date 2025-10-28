@extends('user.layouts.master')
@section('title')
    Footer Cms Update - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('user.elearning-cms.footer.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($cms->id) ? $cms->id : '' }}">


                        <div class="row mb-4">
                            <div class="col-md-4">
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Footer Cms Content </h3>
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            {{-- image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Footer Logo</label>
                                    <input type="file" name="footer_logo" id="image" class="form-control"
                                        value="{{ old('footer_logo') }}">
                                    @if ($errors->has('footer_logo'))
                                        <span class="error">{{ $errors->first('footer_logo') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- view image --}}
                            @if (isset($cms->footer_logo))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Footer Logo</label>
                                        <img src="{{ Storage::url($cms->footer_logo) }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Footer Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif
                            {{-- footer_title --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="footer_title"> Footer Sub Title*</label>
                                    <input type="text" name="footer_title" id="footer_title" class="form-control"
                                        value="{{ isset($cms->footer_title) ? $cms->footer_title : old('footer_title') }}">
                                    @if ($errors->has('footer_title'))
                                        <span class="error">{{ $errors->first('footer_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_facebook_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_facebook_link"> Footer Facebook Link</label>
                                    <input type="text" name="footer_facebook_link" id="footer_facebook_link"
                                        class="form-control"
                                        value="{{ isset($cms->footer_facebook_link) ? $cms->footer_facebook_link : old('footer_facebook_link') }}">
                                    @if ($errors->has('footer_facebook_link'))
                                        <span class="error">{{ $errors->first('footer_facebook_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_twitter_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_twitter_link"> Footer Twitter Link</label>
                                    <input type="text" name="footer_twitter_link" id="footer_twitter_link"
                                        class="form-control"
                                        value="{{ isset($cms->footer_twitter_link) ? $cms->footer_twitter_link : old('footer_twitter_link') }}">
                                    @if ($errors->has('footer_twitter_link'))
                                        <span class="error">{{ $errors->first('footer_twitter_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_instagram_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_instagram_link"> Footer Instagram Link</label>
                                    <input type="text" name="footer_instagram_link" id="footer_instagram_link"
                                        class="form-control"
                                        value="{{ isset($cms->footer_instagram_link) ? $cms->footer_instagram_link : old('footer_instagram_link') }}">
                                    @if ($errors->has('footer_instagram_link'))
                                        <span class="error">{{ $errors->first('footer_instagram_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_youtube_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_youtube_link"> Footer Youtube Link</label>
                                    <input type="text" name="footer_youtube_link" id="footer_youtube_link"
                                        class="form-control"
                                        value="{{ isset($cms->footer_youtube_link) ? $cms->footer_youtube_link : old('footer_youtube_link') }}">
                                    @if ($errors->has('footer_youtube_link'))
                                        <span class="error">{{ $errors->first('footer_youtube_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_address_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_address_title"> Footer Address Title*</label>
                                    <input type="text" name="footer_address_title" id="footer_address_title"
                                        class="form-control"
                                        value="{{ isset($cms->footer_address_title) ? $cms->footer_address_title : old('footer_address_title') }}">
                                    @if ($errors->has('footer_address_title'))
                                        <span class="error">{{ $errors->first('footer_address_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_address --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_address"> Footer Address*</label>
                                    <input type="text" name="footer_address" id="footer_address" class="form-control"
                                        value="{{ isset($cms->footer_address) ? $cms->footer_address : old('footer_address') }}">
                                    @if ($errors->has('footer_address'))
                                        <span class="error">{{ $errors->first('footer_address') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_email --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_email"> Footer Email*</label>
                                    <input type="text" name="footer_email" id="footer_email" class="form-control"
                                        value="{{ isset($cms->footer_email) ? $cms->footer_email : old('footer_email') }}">
                                    @if ($errors->has('footer_email'))
                                        <span class="error">{{ $errors->first('footer_email') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_phone_number --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_phone_number"> Footer Phone Number*</label>
                                    <input type="text" name="footer_phone_number" id="footer_phone_number"
                                        class="form-control"
                                        value="{{ isset($cms->footer_phone_number) ? $cms->footer_phone_number : old('footer_phone_number') }}">
                                    @if ($errors->has('footer_phone_number'))
                                        <span class="error">{{ $errors->first('footer_phone_number') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_newsletter_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_newsletter_title"> Footer Newsletter Title*</label>
                                    <input type="text" name="footer_newsletter_title" id="footer_newsletter_title"
                                        class="form-control"
                                        value="{{ isset($cms->footer_newsletter_title) ? $cms->footer_newsletter_title : old('footer_newsletter_title') }}">
                                    @if ($errors->has('footer_newsletter_title'))
                                        <span class="error">{{ $errors->first('footer_newsletter_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- footer_copywrite_text --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="footer_copywrite_text"> Footer Copywrite Text*</label>
                                    <input type="text" name="footer_copywrite_text" id="footer_copywrite_text"
                                        class="form-control"
                                        value="{{ isset($cms->footer_copywrite_text) ? $cms->footer_copywrite_text : old('footer_copywrite_text') }}">
                                    @if ($errors->has('footer_copywrite_text'))
                                        <span class="error">{{ $errors->first('footer_copywrite_text') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('user.elearning-cms.list') }}"
                                    class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
