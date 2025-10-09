@extends('user.layouts.master')
@section('title')
    Contact Page CMS - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form id="contact-cms-form" action="{{ route('user.store-cms.contact.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $cms->id ?? '' }}">
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Contact Page CMS</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Banner Image*</label>
                        <input type="file" name="banner_image" class="form-control" />
                        <span class="text-sm ms-2 text-muted">(width: 1920px, height: 520px, max 2MB)</span><br>
                        @if (isset($cms->banner_image))
                            <img src="{{ Storage::url($cms->banner_image) }}" alt="banner" class="img-thumbnail mt-2"
                                style="max-height:120px;">
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Banner Title*</label>
                        <input type="text" name="banner_title"
                            value="{{ old('banner_title', $cms->banner_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Card One Title (Email)</label>
                        <input type="text" name="card_one_title"
                            value="{{ old('card_one_title', $cms->card_one_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Card Two Title (Address)</label>
                        <input type="text" name="card_two_title"
                            value="{{ old('card_two_title', $cms->card_two_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Card Three Title (Phone)</label>
                        <input type="text" name="card_three_title"
                            value="{{ old('card_three_title', $cms->card_three_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email (Card One Content)</label>
                        <input type="text" name="card_one_content"
                            value="{{ old('card_one_content', $cms->card_one_content ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Address (Card Two Content)</label>
                        <input type="text" name="card_two_content"
                            value="{{ old('card_two_content', $cms->card_two_content ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone (Card Three Content)</label>
                        <input type="text" name="card_three_content"
                            value="{{ old('card_three_content', $cms->card_three_content ?? '') }}" class="form-control" />
                    </div>
                    {{-- <div class="col-md-6 mb-3">
                        <label class="form-label">Call Section Title</label>
                        <input type="text" name="call_section_title"
                            value="{{ old('call_section_title', $cms->call_section_title ?? '') }}" class="form-control" />
                    </div> --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Follow Us Title</label>
                        <input type="text" name="follow_us_title"
                            value="{{ old('follow_us_title', $cms->follow_us_title ?? '') }}" class="form-control" />
                    </div>
                    {{-- <div class="col-md-12 mb-3">
                        <label class="form-label">Call Section Content</label>
                        <textarea name="call_section_content" class="form-control" rows="4">{{ old('call_section_content', $cms->call_section_content ?? '') }}</textarea>
                    </div> --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Map Iframe Src</label>
                        <textarea name="map_iframe_src" class="form-control" rows="2" placeholder="Paste Google Maps iframe src URL only">{{ old('map_iframe_src', $cms->map_iframe_src ?? '') }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary me-2">Save</button>
                    <a href="{{ route('user.store-cms.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            var existingBannerImage = {{ isset($cms->banner_image) && $cms->banner_image ? 1 : 0 }};

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

            $('#contact-cms-form').on('submit', async function(e) {
                e.preventDefault();
                var $form = $(this);
                clearClientErrors($form);
                var errors = [];

                var bannerTitleSelector = 'input[name="banner_title"]';
                if (!val(bannerTitleSelector)) {
                    addClientError($(bannerTitleSelector), 'Banner title is required.');
                    errors.push(bannerTitleSelector);
                }

                // var emailSelector = 'input[name="card_one_content"]';
                // var emailVal = val(emailSelector);
                // if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                //     addClientError($(emailSelector), 'Enter a valid email address.');
                //     errors.push(emailSelector);
                // }

                // var phoneSelector = 'input[name="card_three_content"]';
                // var phoneVal = val(phoneSelector);
                // if (phoneVal && !/^[0-9+\-\s().]+$/.test(phoneVal)) {
                //     addClientError($(phoneSelector), 'Enter a valid phone number.');
                //     errors.push(phoneSelector);
                // }

                // var mapSelector = 'textarea[name="map_iframe_src"]';
                // var mapVal = val(mapSelector);
                // if (mapVal && !/^https?:\/\//i.test(mapVal)) {
                //     addClientError($(mapSelector), 'Provide a valid iframe src URL.');
                //     errors.push(mapSelector);
                // }

                var $bannerInput = $('input[name="banner_image"]');
                var bannerInputEl = $bannerInput[0];
                var hasFile = bannerInputEl && bannerInputEl.files && bannerInputEl.files.length > 0;

                if (!existingBannerImage && !hasFile) {
                    addClientError($bannerInput, 'Banner image is required (existing or new).');
                    errors.push($bannerInput);
                }
                //  else if (hasFile) {
                //     var ok = await validateImageFile(bannerInputEl.files[0], 2 * 1024 * 1024, 1920, 520,
                //         $bannerInput, 'Banner image');
                //     if (!ok) errors.push($bannerInput);
                // }

                if (errors.length) {
                    var first = errors[0];
                    var $first = $(first);
                    if ($first.length === 0 && first instanceof jQuery) {
                        $first = first;
                    }
                    if ($first && $first.length) {
                        $('html, body').animate({
                            scrollTop: $first.offset().top - 100
                        }, 300, function() {
                            $first.focus();
                        });
                    }
                    return;
                }

                $('#contact-cms-form').off('submit');
                $form.submit();
            });
        })();
    </script>
@endpush
