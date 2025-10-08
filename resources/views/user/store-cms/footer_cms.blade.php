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
                    <form id="footer-cms-form" action="{{ route('user.store-cms.footer.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($cms->id) ? $cms->id : '' }}">
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
                                    <label for="image"> Footer Logo*</label>
                                    <input type="file" name="footer_logo" id="image" class="form-control"
                                        value="{{ old('footer_logo') }}">
                                    <span class="text-sm ms-2 text-muted">(width: 120px, height: 120px, max 1MB)</span>
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
                                <a href="{{ route('user.store-cms.list') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            (function() {
                var existingFooterLogo = {{ isset($cms->footer_logo) && $cms->footer_logo ? 1 : 0 }};

                function addClientError($el, message) {
                    $el.addClass('is-invalid');
                    if ($el.next('.client-error').length) {
                        $el.next('.client-error').text(message);
                    } else {
                        $el.after('<span class="error client-error" style="color:red;display:block;margin-top:4px;">' +
                            message + '</span>');
                    }
                }

                function clearClientErrors($form) {
                    $form.find('.client-error').remove();
                    $form.find('.is-invalid').removeClass('is-invalid');
                }

                function val(selector) {
                    return $.trim($(selector).val() || '');
                }

                function validateImageFile(file, maxBytes, reqW, reqH, $input, messagePrefix) {
                    return new Promise(function(resolve) {
                        if (!file) {
                            resolve(false);
                            return;
                        }
                        if (maxBytes && file.size > maxBytes) {
                            addClientError($input, messagePrefix + ' file size must be less than ' + (maxBytes /
                                1024 / 1024).toFixed(2) + 'MB.');
                            resolve(false);
                            return;
                        }
                        if (!reqW && !reqH) {
                            resolve(true);
                            return;
                        }
                        var url = URL.createObjectURL(file);
                        var img = new Image();
                        var timedOut = false;
                        var timer = setTimeout(function() {
                            timedOut = true;
                            try {
                                URL.revokeObjectURL(url);
                            } catch (e) {}
                            addClientError($input, messagePrefix + ' could not be validated (timeout).');
                            resolve(false);
                        }, 5000);

                        function finish(ok, msg) {
                            if (timedOut) return;
                            clearTimeout(timer);
                            try {
                                URL.revokeObjectURL(url);
                            } catch (e) {}
                            if (!ok && msg) addClientError($input, msg);
                            resolve(!!ok);
                        }
                        img.onload = function() {
                            var w = this.naturalWidth || this.width;
                            var h = this.naturalHeight || this.height;
                            if ((reqW && w !== reqW) || (reqH && h !== reqH)) {
                                finish(false, messagePrefix + ' resolution must be ' + reqW + 'x' + reqH +
                                    '. Your image is ' + w + 'x' + h + '.');
                            } else {
                                finish(true);
                            }
                        };
                        img.onerror = function() {
                            finish(false, messagePrefix + ' is not a valid image.');
                        };
                        img.src = url;
                    });
                }

                $('#footer-cms-form').on('submit', async function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    clearClientErrors($form);
                    var errors = [];

                    var requiredFields = [
                        '#footer_title',
                        '#footer_address_title',
                        '#footer_address',
                        '#footer_email',
                        '#footer_phone_number',
                        '#footer_newsletter_title',
                        '#footer_copywrite_text'
                    ];
                    requiredFields.forEach(function(sel) {
                        var $f = $(sel);
                        if ($f.length && !val(sel)) {
                            addClientError($f, 'This field is required.');
                            errors.push(sel);
                        }
                    });

                    var $logoInput = $('#image');
                    var logoEl = $logoInput[0];
                    var hasFile = logoEl && logoEl.files && logoEl.files.length > 0;
                    if (!existingFooterLogo && !hasFile) {
                        addClientError($logoInput, 'Footer logo is required (existing or new).');
                        errors.push('#image');
                    } else if (hasFile) {
                        var ok = await validateImageFile(logoEl.files[0], 1 * 1024 * 1024, 120, 120, $logoInput,
                            'Footer logo');
                        if (!ok) errors.push('#image');
                    }

                    if (errors.length) {
                        var firstSel = errors[0];
                        var $first = $(firstSel);
                        if ($first && $first.length) {
                            $('html, body').animate({
                                scrollTop: $first.offset().top - 100
                            }, 300, function() {
                                $first.focus();
                            });
                        }
                        return;
                    }

                    // submit programmatically
                    $('#footer-cms-form').off('submit');
                    $form.submit();
                });
            })();
        </script>
    @endpush
