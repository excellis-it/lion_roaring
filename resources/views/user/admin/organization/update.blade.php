@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Organization Page
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <style>
        .image-area {
            position: relative;
            width: 15%;
            background: #333;
        }

        .image-area img {
            max-width: 100%;
            height: auto;
        }

        .remove-image {
            display: none;
            position: absolute;
            top: -10px;
            right: -10px;
            border-radius: 10em;
            padding: 2px 6px 3px;
            text-decoration: none;
            font: 700 21px/20px sans-serif;
            background: #555;
            border: 3px solid #fff;
            color: #FFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5), inset 0 2px 4px rgba(0, 0, 0, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            -webkit-transition: background 0.5s;
            transition: background 0.5s;
        }

        .remove-image:hover {
            background: #E54E4E;
            padding: 3px 7px 5px;
            top: -11px;
            right: -11px;
        }

        .remove-image:active {
            background: #E54E4E;
            top: -10px;
            right: -11px;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Update Organization</h3>
                    <p class="text-muted small mb-0">Update Organization</p>
                </div>
            </div>
            <form id="update-organization-form" action="{{ route('user.admin.organizations.store') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $organization->id ?? '' }}">
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Menu Section</h4>
                    </div>
                    @if (auth()->user()->user_type == 'Global')
                        <div class="row mb-3">
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
                    @endif

                    <div class="row justify-content-between">
                        {{-- courses --}}
                        <div class="col-md-4 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- banner_title --}}
                                    <label for="floatingInputValue">Banner Image</label>
                                    <input type="file" class="form-control" id="banner_image" name="banner_image"
                                        value="{{ old('banner_image') }}" placeholder="Banner Image">
                                    <span class="text-danger error-message" id="error_banner_image"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    @if (isset($organization->banner_image))
                                        <img src="{{ Storage::url($organization->banner_image) }}" id="banner_image_preview"
                                            alt="Footer Logo" style="width: 180px; height: 100px;">
                                    @else
                                        <img src="" id="banner_image_preview" alt="Footer Logo"
                                            style="width: 180px; height: 100px; display:none;">
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
                                        value="{{ isset($organization->banner_title) ? $organization->banner_title : old('banner_title') }}"
                                        placeholder="Banner Title">
                                    <span class="text-danger error-message" id="error_banner_title"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Banner Description*</label>
                                    <textarea name="banner_description" id="banner_description" placeholder="Banner Description"
                                        class="form-control  banner_desc_">{{ isset($organization->banner_description) ? $organization->banner_description : old('banner_description') }}</textarea>
                                    <span class="text-danger error-message" id="error_banner_description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>About Section</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Image (Multiple Image Upload)</label>
                                    <input type="file" class="form-control dropzone" id="floatingInputValue"
                                        name="image[]" value="{{ old('image') }}" placeholder="Image" multiple>
                                    <span class="text-danger error-message" id="error_image"></span>
                                    <span class="text-danger error-message" id="error_image_0"></span>
                                </div>
                            </div>
                        </div>
                        @if (isset($organization->images) && count($organization->images) > 0)
                            <div class="row mb-6">
                                @foreach ($organization->images as $image)
                                    <div class="image-area m-4" id="{{ $image->id }}">
                                        <img src="{{ Storage::url($image->image) }}" alt="Preview">
                                        <a class="remove-image" href="javascript:void(0);" data-id="{{ $image->id }}"
                                            style="display: inline;">&#215;</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Project Section</h4>
                    </div>

                    <div class="row">
                        {{-- project_section_title --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Project Section Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="project_section_title"
                                        value="{{ isset($organization->project_section_title) ? $organization->project_section_title : old('project_section_title') }}"
                                        placeholder="Project Section Title">
                                    <span class="text-danger error-message" id="error_project_section_title"></span>
                                </div>
                            </div>
                        </div>
                        {{-- project_section_sub_title --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Project Section Sub Title*</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="project_section_sub_title"
                                        value="{{ isset($organization->project_section_sub_title) ? $organization->project_section_sub_title : old('project_section_sub_title') }}"
                                        placeholder="Project Section Sub Title">
                                    <span class="text-danger error-message" id="error_project_section_sub_title"></span>
                                </div>
                            </div>
                        </div>
                        {{-- project_section_description --}}
                        <div class="col-xl-12 col-md-12 mb-2">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Project Section Description*</label>
                                    <textarea name="project_section_description" id="project_section_description" cols="30" rows="10"
                                        placeholder="Project Section Description" class="form-control">{{ isset($organization->project_section_description) ? $organization->project_section_description : old('project_section_description') }}</textarea>
                                    <span class="text-danger error-message" id="error_project_section_description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row count-class" id="add-more">
                        @if (isset($organization->projects) && count($organization->projects) > 0)
                            @foreach ($organization->projects as $key => $item)
                                <div class="col-xl-5 col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- meta title --}}
                                            <label for="floatingInputValue">Card Title</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="card_title[]" value="{{ $item->title }}"
                                                placeholder="Card Title">
                                            <span class="text-danger error-message"
                                                id="error_card_title_{{ $key }}"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- banner_title --}}
                                            <label for="floatingInputValue">Card Description</label>
                                            <textarea name="card_description[]" id="card_description_{{ $key }}" cols="30" rows="10"
                                                placeholder="Card Description" class="form-control card_description">{{ $item->description }}</textarea>
                                            <span class="text-danger error-message"
                                                id="error_card_description_{{ $key }}"></span>
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
                                        <label for="floatingInputValue">Card Title</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="card_title[]" value="" placeholder="Card Title">
                                        <span class="text-danger error-message" id="error_card_title_0"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 mt-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Card Description</label>
                                        <textarea name="card_description[]" id="card_description_0" cols="30" rows="10"
                                            placeholder="Card Description" class="form-control card_description"></textarea>
                                        <span class="text-danger error-message" id="error_card_description_0"></span>
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
                </div>
                {{-- Second Project Section --}}
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Project Section Two</h4>
                    </div>

                    <div class="row">
                        {{-- project_section_two_title --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Project Section Two Title</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="project_section_two_title"
                                        value="{{ isset($organization->project_section_two_title) ? $organization->project_section_two_title : old('project_section_two_title') }}"
                                        placeholder="Project Section Two Title">
                                    <span class="text-danger error-message" id="error_project_section_two_title"></span>
                                </div>
                            </div>
                        </div>
                        {{-- project_section_two_sub_title --}}
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Project Section Two Sub Title</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="project_section_two_sub_title"
                                        value="{{ isset($organization->project_section_two_sub_title) ? $organization->project_section_two_sub_title : old('project_section_two_sub_title') }}"
                                        placeholder="Project Section Two Sub Title">
                                    <span class="text-danger error-message"
                                        id="error_project_section_two_sub_title"></span>
                                </div>
                            </div>
                        </div>
                        {{-- project_section_two_description --}}
                        <div class="col-xl-12 col-md-12 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Project Section Two Description</label>
                                    <textarea name="project_section_two_description" id="project_section_two_description" cols="30" rows="10"
                                        placeholder="Project Section Two Description" class="form-control">{{ isset($organization->project_section_two_description) ? $organization->project_section_two_description : old('project_section_two_description') }}</textarea>
                                    <span class="text-danger error-message"
                                        id="error_project_section_two_description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row count-class" id="add-more-two">
                        @if (isset($organization->projectsTwo) && count($organization->projectsTwo) > 0)
                            @foreach ($organization->projectsTwo as $key => $item)
                                <div class="col-xl-5 col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- meta title --}}
                                            <label for="floatingInputValue">Card Title</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="card_title_two[]" value="{{ $item->title }}"
                                                placeholder="Card Title">
                                            <span class="text-danger error-message"
                                                id="error_card_title_two_{{ $key }}"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- banner_title --}}
                                            <label for="floatingInputValue">Card Description</label>
                                            <textarea name="card_description_two[]" id="card_description_two_{{ $key }}" cols="30" rows="10"
                                                placeholder="Card Description" class="form-control card_description_two">{{ $item->description }}</textarea>
                                            <span class="text-danger error-message"
                                                id="error_card_description_two_{{ $key }}"></span>
                                        </div>
                                    </div>
                                </div>
                                @if ($key == 0)
                                    <div class="col-xl-2 mt-4">
                                        <div class="btn-1">
                                            <button type="button" class="add-more-two"><i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xl-2 mt-4">
                                        <div class="btn-1">
                                            <button type="button" class="remove-two"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="col-xl-5 col-md-5 mt-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Card Title</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="card_title_two[]" value="" placeholder="Card Title">
                                        <span class="text-danger error-message" id="error_card_title_two_0"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 mt-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Card Description</label>
                                        <textarea name="card_description_two[]" id="card_description_two_0" cols="30" rows="10"
                                            placeholder="Card Description" class="form-control card_description_two"></textarea>
                                        <span class="text-danger error-message" id="error_card_description_two_0"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 mt-4">
                                <div class="btn-1">
                                    <button type="button" class="add-more-two"><i class="fas fa-plus"></i> </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>SEO Management</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Meta Title</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="meta_title"
                                        value="{{ isset($organization->meta_title) ? $organization->meta_title : old('meta_title') }}"
                                        placeholder="Meta Title">
                                    <span class="text-danger error-message" id="error_meta_title"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta title --}}
                                    <label for="floatingInputValue">Meta Keywords</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        name="meta_keywords"
                                        value="{{ isset($organization->meta_keywords) ? $organization->meta_keywords : old('meta_keywords') }}"
                                        placeholder="Meta Keywords">
                                    <span class="text-danger error-message" id="error_meta_keywords"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    {{-- meta description --}}
                                    <label for="floatingInputValue">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" cols="30" rows="10"
                                        placeholder="Meta Description" class="form-control">{{ isset($organization->meta_description) ? $organization->meta_description : old('meta_description') }}</textarea>
                                    <span class="text-danger error-message" id="error_meta_description"></span>
                                </div>
                            </div>
                        </div>
                        {{-- button --}}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
    <script type="text/javascript">
        Dropzone.options.imageUpload = {
            maxFilesize: 1,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
        };
    </script>
    <script>
        $(document).ready(function() {
            $('.remove-image').click(function() {
                var id = $(this).data('id');
                var token = $("meta[name='csrf-token']").attr("content");
                // show confirm alert
                if (!confirm("Do you really want to delete this image?")) {
                    return false;
                } else {
                    $.ajax({
                        url: "{{ route('user.admin.organization.image.delete') }}",
                        type: 'GET',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function() {
                            toastr.success('Image Deleted Successfully');
                            $('#' + id).remove();
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // window.allEditors = {};

            // function initializeCKEditor(selector) {
            //     document.querySelectorAll(selector).forEach((textarea) => {
            //         if (!textarea.classList.contains('ckeditor-initialized')) {
            //             ClassicEditor
            //                 .create(textarea)
            //                 .then(editor => {
            //                     textarea.classList.add('ckeditor-initialized');
            //                     editor.ui.view.editable.element.style.height = '250px';

            //                     // Store instance for AJAX sync
            //                     var id = textarea.id || (textarea.name + '_' + Math.random().toString(
            //                         36).substr(2, 9));
            //                     window.allEditors[id] = editor;
            //                 })
            //                 .catch(error => {
            //                     console.error(error);
            //                 });
            //         }
            //     });
            // }

            // // Initialize CKEditor for existing textareas
            // initializeCKEditor('.card_description');
            // initializeCKEditor('.card_description_two');
            // initializeCKEditor('#banner_description');
            // initializeCKEditor('#project_section_description');
            // initializeCKEditor('#project_section_two_description');

            // Add more functionality
            $(document).on("click", ".add-more", function() {
                var count = $("#add-more .col-xl-5").length;
                var html = `
                <div class="col-xl-5 col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Title</label>
                            <input type="text" class="form-control" name="card_title[]" value="" placeholder="Card Title">
                            <span class="text-danger error-message" id="error_card_title_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Description</label>
                            <textarea name="card_description[]" id="card_description_${count}" cols="30" rows="10" class="form-control card_description" placeholder="Card Description"></textarea>
                            <span class="text-danger error-message" id="error_card_description_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 mt-4">
                    <div class="btn-1">
                        <button type="button" class="remove"><i class="fas fa-minus"></i> </button>
                    </div>
                </div>`;
                $("#add-more").append(html);
                initializeCKEditor('#card_description_' + count);
                toastr.success('Card Added Successfully');
            });

            $(document).on("click", ".add-more-two", function() {
                var count = $("#add-more-two .col-xl-5").length;
                var html = `
                <div class="col-xl-5 col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Title</label>
                            <input type="text" class="form-control" name="card_title_two[]" value="" placeholder="Card Title">
                            <span class="text-danger error-message" id="error_card_title_two_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Description</label>
                            <textarea name="card_description_two[]" id="card_description_two_${count}" cols="30" rows="10" class="form-control card_description_two" placeholder="Card Description"></textarea>
                            <span class="text-danger error-message" id="error_card_description_two_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 mt-4">
                    <div class="btn-1">
                        <button type="button" class="remove-two"><i class="fas fa-minus"></i> </button>
                    </div>
                </div>`;
                $("#add-more-two").append(html);
                initializeCKEditor('#card_description_two_' + count);
                toastr.success('Card Added Successfully');
            });

            $(document).on("click", ".remove", function() {
                $(this).closest('.col-xl-2').prev('.col-md-5').remove();
                $(this).closest('.col-xl-2').prev('.col-xl-5').remove();
                $(this).closest('.col-xl-2').remove();
            });

            $(document).on("click", ".remove-two", function() {
                $(this).closest('.col-xl-2').prev('.col-md-5').remove();
                $(this).closest('.col-xl-2').prev('.col-xl-5').remove();
                $(this).closest('.col-xl-2').remove();
            });

            // Form Submit by AJAX
            $('#update-organization-form').on('submit', function(e) {
                // Sync CKEditor data
                if (window.allEditors) {
                    Object.values(window.allEditors).forEach(editor => {
                        editor.updateSourceElement();
                    });
                }

                e.preventDefault();

                var formData = new FormData(this);
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var originalBtnText = submitBtn.text();

                submitBtn.prop('disabled', true).text('Updating...');
                $('.error-message').text(''); // Clear previous errors

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        submitBtn.prop('disabled', false).text(originalBtnText);
                        if (response.status) {
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text(originalBtnText);
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                // Handle array keys like card_title.0 -> card_title_0
                                var errorKey = key.replace(/\./g, '_');
                                var errorElement = $('#error_' + errorKey);
                                if (errorElement.length) {
                                    errorElement.text(value[0]);
                                } else {
                                    // Fallback for fields without specific error IDs
                                    toastr.error(value[0]);
                                }
                            });

                            // Scroll to the first error
                            var firstError = $('.error-message:not(:empty)').first();
                            if (firstError.length) {
                                $('html, body').animate({
                                    scrollTop: firstError.offset().top - 150
                                }, 500);
                            }
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });
        });
    </script>


    <script>
        // $(document).ready(function() {
        //     function initStaticCKEditor(selector) {
        //         const el = document.querySelector(selector);
        //         if (el) {
        //             ClassicEditor
        //                 .create(el)
        //                 .then(editor => {
        //                     editor.ui.view.editable.element.style.height = '250px';
        //                 })
        //                 .catch(error => {
        //                     console.error(error);
        //                 });
        //         }
        //     }
        //     initStaticCKEditor('#banner_description');
        //     initStaticCKEditor('#project_section_description');
        //     initStaticCKEditor('#project_section_two_description');
        // });
    </script>
    <script>
        $(document).ready(function() {
            $('#banner_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#banner_image_preview').show();
                    $('#banner_image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
