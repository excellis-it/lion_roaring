@extends('admin.layouts.master')
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
@section('head')
    Update Organization Page
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('organizations.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $organization->id ?? '' }}">
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>Menu Section</h4>
                        </div>

                        <div class="row justify-content-between">
                            {{-- courses --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Banner Image</label>
                                        <input type="file" class="form-control" id="floatingInputValue"
                                            name="banner_image" value="{{ old('banner_image') }}"
                                            placeholder="Banner Image">
                                        @if ($errors->has('banner_image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- our_organization_id --}}
                            <div class="col-md-6">
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
                                        <textarea name="banner_description" id="banner_description" 
                                            placeholder="Banner Description" class="form-control banner_desc_">{{ isset($organization->banner_description) ? $organization->banner_description : old('banner_description') }}</textarea>
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
                            <div class="col-xl-6 col-md-6">
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
                            <div class="col-xl-6 col-md-6">
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
                            <div class="col-xl-12 col-md-12">
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
                                                <button type="button" class="add-more"><i class="ph ph-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-xl-2 mt-4">
                                            <div class="btn-1">
                                                <button type="button" class="remove"><i class="ph ph-minus"></i>
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
                                        <button type="button" class="add-more"><i class="ph ph-plus"></i> </button>
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
                            <div class="col-xl-6 col-md-6">
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
                            <div class="col-xl-6 col-md-6">
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
                                    <button type="submit">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js"></script>
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
                        url: "{{ route('organization.image.delete') }}",
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
            $(".card_description").each(function(index, element) {
                ClassicEditor.create(document.getElementById("card_description_" + index));
            });
            $(document).on("click", ".add-more", function() {
                var count = $("#add-more .col-xl-5").length;
                var html = `
                    <div class="col-xl-5 col-md-5 mt-4">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Card Title*</label>
                                <input type="text" class="form-control" id="floatingInputValue" required name="card_title[]" value="" placeholder="Card Title">
                                <span class="text-danger" id="job_opportunity_title_${count}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 mt-4">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Card Description*</label>
                                <textarea name="card_description[]" cols="30" rows="10" placeholder="Card Description" class="form-control card_description"></textarea>
                                <span class="text-danger" id="job_opportunity_description_${count}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 mt-4">
                                <div class="btn-1">
                                    <button type="button" class="remove"><i class="ph ph-minus"></i> </button>
                                </div>
                            </div>`;
                $("#add-more").append(html);
                // Initialize CKEditor on the newly added textarea
                ClassicEditor.create(document.querySelectorAll('.card_description')[count]);
            });

            $(document).on("click", ".remove", function() {
                $(this).parent().parent().prev().remove();
                $(this).parent().parent().prev().remove();
                $(this).parent().parent().remove();
            });
        });
    </script>

<script>
    $(document).ready(function() {
        ClassicEditor.create(document.querySelector("#banner_description"));
        ClassicEditor.create(document.querySelector("#project_section_description"));
    
    });
</script>
@endpush
