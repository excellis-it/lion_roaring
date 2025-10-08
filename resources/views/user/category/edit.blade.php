@extends('user.layouts.master')
@section('title')
    Category Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="category-edit-form" action="{{ route('categories.update', $category->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Main Section</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Category Name*</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $category->name }}" placeholder="Enter Category Name">
                                    @if ($errors->has('name'))
                                        <span class="error">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- slug --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="slug"> Category Slug*</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        value="{{ $category->slug }}" placeholder="Enter Category Slug"
                                        @if ($category->main == 1) readonly @endif>
                                    @if ($errors->has('slug'))
                                        <span class="error">{{ $errors->first('slug') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Parent Category --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="parent_id"> Parent Category</label>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="">Select Parent Category</option>
                                        @php
                                            $renderCategoryOptions = function (
                                                $nodes,
                                                $prefix = '',
                                                $selectedParentId = null,
                                                $excludeId = null,
                                            ) use (&$renderCategoryOptions) {
                                                foreach ($nodes as $node) {
                                                    if ($node->id != $excludeId) {
                                                        echo '<option value="' .
                                                            $node->id .
                                                            '"' .
                                                            ($selectedParentId == $node->id ? ' selected' : '') .
                                                            '>' .
                                                            e($prefix . $node->name) .
                                                            '</option>';
                                                        if (!empty($node->children) && $node->children->count()) {
                                                            $renderCategoryOptions(
                                                                $node->children,
                                                                $prefix . $node->name . '->',
                                                                $selectedParentId,
                                                                $excludeId,
                                                            );
                                                        }
                                                    }
                                                }
                                            };
                                            $topLevelCategories = $categories->whereNull('parent_id');
                                            $renderCategoryOptions(
                                                $topLevelCategories,
                                                '',
                                                $category->parent_id,
                                                $category->id,
                                            );
                                        @endphp
                                    </select>
                                    @if ($errors->has('parent_id'))
                                        <span class="error">{{ $errors->first('parent_id') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                            </div>

                            {{-- image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="image"> Category Image</label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        value="{{ old('image') }}" placeholder="Enter Category Image">
                                    <span class="text-sm ms-2 text-muted">(width: 410px, height: 150px, max 2MB)</span>
                                    @if ($errors->has('image'))
                                        <span class="error">{{ $errors->first('image') }}</span>
                                    @endif
                                </div>
                                {{-- view image --}}
                                @if ($category->image)
                                    <div class="mb-2">
                                        <div class="box_label">
                                            <label for="image"> Category Image</label>
                                            <img src="{{ Storage::url($category->image) }}" alt="Category Image"
                                                class="img-thumbnail" style="width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- background image --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="background_image"> Category Background Image</label>
                                    <input type="file" name="background_image" id="background_image" class="form-control"
                                        value="{{ old('background_image') }}"
                                        placeholder="Enter Category Background Image">
                                    <span class="text-sm ms-2 text-muted">(width: 1920px, height: 520px, max 2MB)</span>
                                    @if ($errors->has('background_image'))
                                        <span class="error">{{ $errors->first('background_image') }}</span>
                                    @endif
                                </div>
                                {{-- view background image --}}
                                @if ($category->background_image)
                                    <div class="mb-2">
                                        <div class="box_label">
                                            <label for="background_image"> Category Background Image</label>
                                            <img src="{{ Storage::url($category->background_image) }}"
                                                alt="Category Background Image" class="img-thumbnail"
                                                style="width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                @endif
                            </div>


                            {{-- status --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status"> Status*</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <span class="error">{{ $errors->first('status') }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Seo Section</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="meta_title">Meta Title</label>

                                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                                        value="{{ $category->meta_title }}" placeholder="Enter Meta Title">
                                    @if ($errors->has('meta_title'))
                                        <span class="error">{{ $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="type">Mete Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="5" cols="30"
                                        placeholder="Enter Meta Description">{{ $category->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <span class="error">{{ $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('categories.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            (function() {
                var existingCategoryImage = {{ $category->image ? 1 : 0 }};

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

                function validateImageFile(file, maxBytes, reqW, reqH, $input, label) {
                    return new Promise(function(resolve) {
                        if (!file) {
                            resolve(false);
                            return;
                        }
                        if (maxBytes && file.size > maxBytes) {
                            addClientError($input, label + ' must be under ' + (maxBytes / 1024 / 1024).toFixed(2) +
                                'MB.');
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
                            URL.revokeObjectURL(url);
                            addClientError($input, label + ' validation timed out.');
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
                                finish(false, label + ' must be ' + reqW + 'x' + reqH + '. Uploaded ' + w +
                                    'x' + h + '.');
                            } else {
                                finish(true);
                            }
                        };

                        img.onerror = function() {
                            finish(false, label + ' is not a valid image.');
                        };

                        img.src = url;
                    });
                }

                $('#category-edit-form').on('submit', async function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    clearClientErrors($form);

                    var errors = [];
                    var $name = $('#name');
                    if (!val('#name')) {
                        addClientError($name, 'Category name is required.');
                        errors.push($name);
                    }

                    var $slug = $('#slug');
                    if (!val('#slug')) {
                        addClientError($slug, 'Category slug is required.');
                        errors.push($slug);
                    }

                    var $status = $('#status');
                    if (!val('#status')) {
                        addClientError($status, 'Status is required.');
                        errors.push($status);
                    }

                    var $image = $('#image');
                    var imageInput = $image[0];
                    if (!existingCategoryImage && (!imageInput || !imageInput.files || !imageInput.files
                        .length)) {
                        addClientError($image, 'Category image is required (existing or new).');
                        errors.push($image);
                    } else if (imageInput && imageInput.files && imageInput.files.length) {
                        var okFeatured = await validateImageFile(imageInput.files[0], 2 * 1024 * 1024, 410, 150,
                            $image, 'Category image');
                        if (!okFeatured) errors.push($image);
                    }

                    var $bgImage = $('#background_image');
                    var bgInput = $bgImage[0];
                    if (bgInput && bgInput.files && bgInput.files.length) {
                        var okBackground = await validateImageFile(bgInput.files[0], 2 * 1024 * 1024, 1920, 520,
                            $bgImage, 'Background image');
                        if (!okBackground) errors.push($bgImage);
                    }

                    if (errors.length) {
                        var $first = errors[0];
                        $('html, body').animate({
                            scrollTop: $first.offset().top - 100
                        }, 300, function() {
                            $first.focus();
                        });
                        return;
                    }

                    $('#category-edit-form').off('submit');
                    $form.submit();
                });
            })();
        </script>
    @endpush
