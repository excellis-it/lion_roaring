@extends('user.layouts.master')
@section('title')
    Cms - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="store-cms-update-form" action="{{ route('user.store-cms.update', $cms->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Update CMS Page</h3>
                                </div>
                                <div class="alert alert-info" hidden>
                                    <strong>Tip:</strong> Slug controls which e-store page uses this banner image. Use one
                                    of:
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <ul class="mb-1">
                                                <li><code>privacy-policy</code> (Privacy Policy)</li>
                                                <li><code>terms-and-condition</code> (Terms and Conditions)</li>
                                                <li><code>products</code> (Products list)</li>
                                                <li><code>product-details</code> (Product details)</li>
                                                <li><code>cart</code> (Cart)</li>
                                                <li><code>checkout</code> (Checkout)</li>
                                                <li><code>my-orders</code> (My Orders)</li>

                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-1">
                                                <li><code>order-details</code> (Order details)</li>
                                                <li><code>order-success</code> (Order success)</li>
                                                <li><code>order-tracking</code> (Public order tracking)</li>
                                                <li><code>wishlist</code> (Wishlist)</li>
                                                <li><code>profile</code> (My profile)</li>
                                                <li><code>change-password</code> (My password)</li>
                                                <li><code>product-not-available</code> (Product not available)</li>
                                            </ul>
                                        </div>
                                    </div>
                                    If not set, pages will fall back to Home CMS banner or the default theme image.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Page Banner Image *</label>
                                    <input type="file" name="page_banner_image" id="image" class="form-control"
                                        value="{{ old('page_banner_image') }}">
                                    <span class="text-sm ms-2 text-muted" style="font-size:12px;">(width: 1920px, height: 520px, max 2MB)</span>
                                    @if ($errors->has('page_banner_image'))
                                        <span class="error">{{ $errors->first('page_banner_image') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if (isset($cms->page_banner_image))
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Page Banner Image</label>
                                        <img src="{{ Storage::url($cms->page_banner_image) }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 150px; height: 100px;">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="image"> Page Banner Image</label>
                                        <img src="{{ asset('user_assets/images/no-image.png') }}" alt="Banner Image"
                                            class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                </div>
                            @endif
                            {{-- page_name --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="page_name"> Page Name *</label>
                                    <input type="text" name="page_name" id="page_name" class="form-control"
                                        value="{{ $cms->page_name ? $cms->page_name : old('page_name') }}" placeholder=""
                                        readonly>
                                    @if ($errors->has('page_name'))
                                        <span class="error">{{ $errors->first('page_name') }}</span>
                                    @endif
                                </div>
                            </div>


                            {{-- page_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="page_title"> Page Title *</label>
                                    <input type="text" name="page_title" id="page_title" class="form-control"
                                        value="{{ $cms->page_title ? $cms->page_title : old('page_title') }}"
                                        placeholder="">
                                    @if ($errors->has('page_title'))
                                        <span class="error">{{ $errors->first('page_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- slug --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="slug"> Page Slug *</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ $cms->slug ? $cms->slug : old('slug') }}" placeholder="" readonly>
                                    @if ($errors->has('slug'))
                                        <span class="error">{{ $errors->first('slug') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- page_content --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="page_content"> Page Content *</label>
                                    <textarea name="page_content" id="page_content" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Page Content">{{ $cms->page_content ? $cms->page_content : old('page_content') }}</textarea>
                                    @if ($errors->has('page_content'))
                                        <span class="error">{{ $errors->first('page_content') }}</span>
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
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <script>
            var pageContentEditor;
            ClassicEditor.create(document.querySelector("#page_content"))
                .then(function(editor) {
                    pageContentEditor = editor;
                })
                .catch(function(error) {
                    console.error(error);
                });

            (function() {
                var existingPageBannerImage = {{ isset($cms->page_banner_image) && $cms->page_banner_image ? 1 : 0 }};

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

                function validateImageFile(file, maxBytes, reqW, reqH, $input, messagePrefix) {
                    return new Promise(function(resolve) {
                        if (!file) {
                            resolve(false);
                            return;
                        }
                        if (maxBytes && file.size > maxBytes) {
                            addClientError($input, messagePrefix + ' must be under ' + (maxBytes / 1024 / 1024)
                                .toFixed(2) + 'MB.');
                            resolve(false);
                            return;
                        }
                        var url = URL.createObjectURL(file);
                        var img = new Image();
                        var timedOut = false;
                        var timer = setTimeout(function() {
                            timedOut = true;
                            URL.revokeObjectURL(url);
                            addClientError($input, messagePrefix + ' validation timed out.');
                            resolve(false);
                        }, 5000);

                        function finish(ok, msg) {
                            if (timedOut) return;
                            clearTimeout(timer);
                            URL.revokeObjectURL(url);
                            if (!ok && msg) addClientError($input, msg);
                            resolve(!!ok);
                        }

                        img.onload = function() {
                            var w = this.naturalWidth || this.width;
                            var h = this.naturalHeight || this.height;
                            if ((reqW && w !== reqW) || (reqH && h !== reqH)) {
                                finish(false, messagePrefix + ' must be ' + reqW + 'x' + reqH + '. Uploaded ' +
                                    w + 'x' + h + '.');
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

                $('#store-cms-update-form').on('submit', async function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    clearClientErrors($form);
                    var errors = [];

                    var $pageTitle = $('#page_title');
                    if (!$.trim($pageTitle.val())) {
                        addClientError($pageTitle, 'Page title is required.');
                        errors.push($pageTitle);
                    }

                    var editorData = pageContentEditor ? pageContentEditor.getData() : $('#page_content').val();
                    var plainContent = $('<div>').html(editorData || '').text().trim();
                    if (!plainContent) {
                        addClientError($('#page_content'), 'Page content is required.');
                        errors.push($('#page_content'));
                    } else if (pageContentEditor) {
                        $('#page_content').val(editorData);
                    }

                    var $bannerInput = $('#image');
                    var bannerInputEl = $bannerInput[0];
                    var hasFile = bannerInputEl && bannerInputEl.files && bannerInputEl.files.length > 0;

                    if (!existingPageBannerImage && !hasFile) {
                        addClientError($bannerInput, 'Banner image is required (existing or new).');
                        errors.push($bannerInput);
                    }

                    // else if (hasFile) {
                    //     var ok = await validateImageFile(bannerInputEl.files[0], 2 * 1024 * 1024, 1920, 520,
                    //         $bannerInput, 'Banner image');
                    //     if (!ok) errors.push($bannerInput);
                    // }

                    if (errors.length) {
                        var $first = errors[0];
                        $('html, body').animate({
                            scrollTop: $first.offset().top - 100
                        }, 300, function() {
                            $first.focus();
                        });
                        return;
                    }

                    $('#store-cms-update-form').off('submit');
                    $form.submit();
                });
            })();
        </script>
    @endpush
