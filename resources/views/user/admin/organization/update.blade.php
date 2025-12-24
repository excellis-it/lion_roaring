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
                <form action="{{ route('user.admin.organizations.store') }}" method="post" enctype="multipart/form-data">
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
                                        @if (isset($organization->banner_image))
                                            <img src="{{ Storage::url($organization->banner_image) }}"
                                                id="banner_image_preview" alt="Footer Logo"
                                                style="width: 180px; height: 100px;">
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
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="banner_title"
                                            value="{{ isset($organization->banner_title) ? $organization->banner_title : old('banner_title') }}"
                                            placeholder="Banner Title">
                                        @if ($errors->has('banner_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Banner Description*</label>
                                        <textarea name="banner_description" id="banner_description" placeholder="Banner Description"
                                            class="form-control banner_desc_">{{ isset($organization->banner_description) ? $organization->banner_description : old('banner_description') }}</textarea>
                                        @if ($errors->has('banner_description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_description') }}</div>
                                        @endif
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
                                        @if ($errors->has('image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (isset($organization->images) && count($organization->images) > 0)
                                <div class="row mb-6">
                                    @foreach ($organization->images as $image)
                                        <div class="image-area m-4" id="{{ $image->id }}">
                                            <img src="{{ Storage::url($image->image) }}" alt="Preview">
                                            <a class="remove-image" href="javascript:void(0);"
                                                data-id="{{ $image->id }}" style="display: inline;">&#215;</a>
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
                                        @if ($errors->has('project_section_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('project_section_title') }}</div>
                                        @endif
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
                                        @if ($errors->has('project_section_sub_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('project_section_sub_title') }}</div>
                                        @endif
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
                                        @if ($errors->has('project_section_description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('project_section_description') }}</div>
                                        @endif
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
                                                <label for="floatingInputValue">Card Title*</label>
                                                <input type="text" class="form-control" id="floatingInputValue"
                                                    required name="card_title[]" value="{{ $item->title }}"
                                                    placeholder="Card Title">
                                                <span class="text-danger" id="job_opportunity_title_0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-4">
                                        <div class="form-group-div">
                                            <div class="form-group">
                                                {{-- banner_title --}}
                                                <label for="floatingInputValue">Card Description*</label>
                                                <textarea name="card_description[]" id="card_description_{{ $key }}" cols="30" rows="10"
                                                    placeholder="Card Description" class="form-control card_description">{{ $item->description }}</textarea>
                                                <span class="text-danger" id="job_opportunity_description_0"></span>
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
                                            <label for="floatingInputValue">Card Title*</label>
                                            <input type="text" class="form-control" id="floatingInputValue" required
                                                name="card_title[]" value="" placeholder="Card Title">
                                            <span class="text-danger" id="job_opportunity_title_0"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- banner_title --}}
                                            <label for="floatingInputValue">Card Description*</label>
                                            <textarea name="card_description[]" id="card_description_0" cols="30" rows="10"
                                                placeholder="Card Description" class="form-control card_description"></textarea>
                                            <span class="text-danger" id="job_opportunity_description_0"></span>
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
                                                <label for="floatingInputValue">Card Title*</label>
                                                <input type="text" class="form-control" id="floatingInputValue"
                                                    required name="card_title_two[]" value="{{ $item->title }}"
                                                    placeholder="Card Title">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-4">
                                        <div class="form-group-div">
                                            <div class="form-group">
                                                {{-- banner_title --}}
                                                <label for="floatingInputValue">Card Description*</label>
                                                <textarea name="card_description_two[]" id="card_description_two_{{ $key }}" cols="30" rows="10"
                                                    placeholder="Card Description" class="form-control card_description_two">{{ $item->description }}</textarea>
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
                                            <label for="floatingInputValue">Card Title*</label>
                                            <input type="text" class="form-control" id="floatingInputValue" required
                                                name="card_title_two[]" value="" placeholder="Card Title">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Card Description*</label>
                                            <textarea name="card_description_two[]" id="card_description_two_0" cols="30" rows="10"
                                                placeholder="Card Description" class="form-control card_description_two"></textarea>
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
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="meta_title"
                                            value="{{ isset($organization->meta_title) ? $organization->meta_title : old('meta_title') }}"
                                            placeholder="Meta Title">
                                        @if ($errors->has('meta_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('meta_title') }}</div>
                                        @endif
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
                                        @if ($errors->has('meta_keywords'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('meta_keywords') }}</div>
                                        @endif
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
                                        @if ($errors->has('meta_description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('meta_description') }}</div>
                                        @endif
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
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
            // Initialize Summernote for existing card descriptions
            $(".card_description").each(function(index, element) {
                $('#card_description_' + index).summernote({
                    placeholder: 'Card Description',
                    tabsize: 2,
                    height: 400
                });
            });
            // Initialize Summernote for project_section_two card descriptions
            $(".card_description_two").each(function(index, element) {
                $('#card_description_two_' + index).summernote({
                    placeholder: 'Card Description',
                    tabsize: 2,
                    height: 400
                });
            });

            // Add more functionality
            $(document).on("click", ".add-more", function() {
                var count = $("#add-more .col-xl-5").length; // Get the current count of card entries

                // Create new card entry HTML
                var html = `
                <div class="col-xl-5 col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Title*</label>
                            <input type="text" class="form-control" name="card_title[]" value="" required placeholder="Card Title">
                            <span class="text-danger" id="job_opportunity_title_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Description*</label>
                            <textarea name="card_description[]" id="card_description_${count}" cols="30" rows="10" class="form-control card_description" placeholder="Card Description"></textarea>
                            <span class="text-danger" id="job_opportunity_description_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 mt-4">
                    <div class="btn-1">
                        <button type="button" class="remove"><i class="fas fa-minus"></i> </button>
                    </div>
                </div>`;

                // Append the new fields
                $("#add-more").append(html);

                // Initialize Summernote for the newly added card description textarea
                $('#card_description_' + count).summernote({
                    placeholder: 'Card Description',
                    tabsize: 2,
                    height: 400
                });
            });

            // Add more functionality for second project section
            $(document).on("click", ".add-more-two", function() {
                var count = $("#add-more-two .col-xl-5").length; // Get the current count of card entries

                // Create new card entry HTML for second section
                var html = `
                <div class="col-xl-5 col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Title*</label>
                            <input type="text" class="form-control" name="card_title_two[]" value="" required placeholder="Card Title">
                            <span class="text-danger" id="job_opportunity_title_two_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Card Description*</label>
                            <textarea name="card_description_two[]" id="card_description_two_${count}" cols="30" rows="10" class="form-control card_description_two" placeholder="Card Description"></textarea>
                            <span class="text-danger" id="job_opportunity_description_two_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 mt-4">
                    <div class="btn-1">
                        <button type="button" class="remove-two"><i class="fas fa-minus"></i> </button>
                    </div>
                </div>`;

                // Append the new fields
                $("#add-more-two").append(html);

                // Initialize Summernote for the newly added card description textarea
                $('#card_description_two_' + count).summernote({
                    placeholder: 'Card Description',
                    tabsize: 2,
                    height: 400
                });
            });

            // Remove functionality for second section
            $(document).on("click", ".remove-two", function() {
                $(this).closest('.col-xl-2').prev('.col-md-5').remove(); // Remove description column
                $(this).closest('.col-xl-2').prev('.col-xl-5').remove(); // Remove title column
                $(this).closest('.col-xl-2').remove(); // Remove button column
            });

            // Remove functionality
            $(document).on("click", ".remove", function() {
                $(this).closest('.col-xl-2').prev('.col-md-5').remove(); // Remove description column
                $(this).closest('.col-xl-2').prev('.col-xl-5').remove(); // Remove title column
                $(this).closest('.col-xl-2').remove(); // Remove button column
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // ClassicEditor.create(document.querySelector("#banner_description"));
            // ClassicEditor.create(document.querySelector("#project_section_description"));
            $('#banner_description').summernote({
                placeholder: 'Banner Description*',
                tabsize: 2,
                height: 400
            });
            $('#project_section_description').summernote({
                placeholder: 'Project Section Description*',
                tabsize: 2,
                height: 400
            });

            $('#project_section_two_description').summernote({
                placeholder: 'Project Section Two Description',
                tabsize: 2,
                height: 400
            });

        });
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
