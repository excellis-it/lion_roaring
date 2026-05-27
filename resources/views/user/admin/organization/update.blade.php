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

        /* ── Modern Gallery Upload UI ─────────────────── */
        .gallery-upload-wrapper {
            background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
        }

        .gallery-section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .existing-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .12);
            background: #e2e8f0;
            animation: fadeInUp .3s ease;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .gallery-item .delete-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .3s ease;
            backdrop-filter: blur(2px);
        }

        .gallery-item:hover .delete-overlay {
            opacity: 1;
        }

        .gallery-item .delete-gallery-btn {
            background: #ef4444;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s, background .2s;
            box-shadow: 0 2px 8px rgba(239, 68, 68, .4);
        }

        .gallery-item .delete-gallery-btn:hover {
            transform: scale(1.1);
            background: #dc2626;
        }

        .gallery-item .drag-handle {
            position: absolute;
            top: 6px;
            left: 6px;
            background: rgba(255, 255, 255, 0.9);
            color: #64748b;
            border: none;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            cursor: grab;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .15);
            transition: background .2s, color .2s;
        }

        .gallery-item .drag-handle:hover {
            background: #fff;
            color: #334155;
        }

        .gallery-item .drag-handle:active {
            cursor: grabbing;
        }

        .gallery-item.sortable-ghost {
            opacity: 0.4;
        }

        .gallery-item.sortable-chosen {
            box-shadow: 0 4px 20px rgba(99, 102, 241, .35);
        }

        .upload-dropzone {
            border: 2.5px dashed #c7d2fe;
            border-radius: 14px;
            padding: 36px 20px;
            text-align: center;
            cursor: pointer;
            transition: all .3s ease;
            background: #fff;
        }

        .upload-dropzone:hover,
        .upload-dropzone.dragover {
            border-color: #6366f1;
            background: #f5f3ff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .1);
        }

        .upload-icon-wrap {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 26px;
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
        }

        .new-images-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .preview-card {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .12);
            animation: fadeInScale .3s ease;
        }

        .preview-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-card .remove-preview-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .92);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: #ef4444;
            transition: all .2s;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
        }

        .preview-card .remove-preview-btn:hover {
            background: #ef4444;
            color: #fff;
            transform: scale(1.1);
        }

        .preview-card .preview-badge {
            position: absolute;
            bottom: 6px;
            left: 6px;
            background: rgba(99, 102, 241, .85);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            letter-spacing: .5px;
        }

        .update-images-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border: none;
            padding: 11px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all .3s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .update-images-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, .45);
        }

        .update-images-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(.85);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                            <div class="gallery-upload-wrapper">

                                {{-- Existing Images Gallery --}}
                                @if (isset($organization->images) && count($organization->images) > 0)
                                    <p class="gallery-section-label">Existing Images</p>
                                    <div class="existing-gallery-grid" id="existing-gallery-grid">
                                        @foreach ($organization->images as $image)
                                            <div class="gallery-item" id="gallery-item-{{ $image->id }}" data-id="{{ $image->id }}">
                                                <span class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                                                <img src="{{ Storage::url($image->image) }}" alt="Gallery Image">
                                                <div class="delete-overlay">
                                                    <button type="button" class="delete-gallery-btn"
                                                        data-id="{{ $image->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="existing-gallery-grid" id="existing-gallery-grid"></div>
                                @endif

                                {{-- Drop Zone --}}
                                <div class="upload-dropzone" id="upload-dropzone">
                                    <input type="file" id="gallery-images-input" name="image[]" multiple
                                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display:none;">
                                    <div id="upload-placeholder">
                                        <div class="upload-icon-wrap">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <h5 class="mb-1 fw-semibold">Drag &amp; drop images here</h5>
                                        <p class="text-muted small mb-0">or <strong>click to browse</strong> &mdash; JPG,
                                            PNG, GIF, WEBP</p>
                                    </div>
                                </div>
                                <span class="text-danger error-message" id="error_image"></span>
                                <span class="text-danger error-message" id="error_image_0"></span>

                                {{-- New Images Preview Grid --}}
                                <div class="new-images-preview-grid" id="new-images-preview-grid" style="display:none;">
                                </div>

                                {{-- Update Images Button --}}
                                {{-- <div class="d-flex align-items-center gap-3 mt-3" id="update-images-btn-wrapper" style="display:none !important;">
                                    <button type="button" id="update-images-btn" class="update-images-btn">
                                        <i class="fas fa-upload"></i> Update Images
                                    </button>
                                    <span class="text-muted small" id="selected-count"></span>
                                </div> --}}

                            </div>
                        </div>
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
    <script>
        /* ── Modern Gallery Upload JS ──────────────────── */
        (function() {
            var selectedFiles = [];

            var dropzone  = document.getElementById('upload-dropzone');
            var fileInput = document.getElementById('gallery-images-input');

            // Click anywhere on dropzone opens picker
            dropzone.addEventListener('click', function() { fileInput.click(); });

            // Drag-over styling
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            dropzone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                handleFiles(e.dataTransfer.files);
            });

            // Native file picker
            fileInput.addEventListener('change', function() { handleFiles(this.files); });

            function handleFiles(files) {
                Array.from(files).forEach(function(file) {
                    if (!file.type.startsWith('image/')) return;
                    var idx = selectedFiles.length;
                    selectedFiles.push(file);
                    renderPreview(file, idx);
                });
                syncGrid();
            }

            function renderPreview(file, idx) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var card = document.createElement('div');
                    card.className = 'preview-card';
                    card.id = 'preview-card-' + idx;
                    card.innerHTML =
                        '<img src="' + e.target.result + '" alt="Preview">' +
                        '<button type="button" class="remove-preview-btn" onclick="galleryRemovePreview(' + idx + ')">' +
                        '<i class="fas fa-times"></i></button>' +
                        '<span class="preview-badge">NEW</span>';
                    document.getElementById('new-images-preview-grid').appendChild(card);
                };
                reader.readAsDataURL(file);
            }

            // Remove a staged file: nullify it, rebuild the actual input.files via DataTransfer
            window.galleryRemovePreview = function(idx) {
                selectedFiles[idx] = null;
                var card = document.getElementById('preview-card-' + idx);
                if (card) card.remove();
                rebuildInput();
                syncGrid();
            };

            // Keep fileInput.files in sync with selectedFiles so the main form submits only the remaining files
            function rebuildInput() {
                var dt = new DataTransfer();
                selectedFiles.filter(Boolean).forEach(function(f) { dt.items.add(f); });
                fileInput.files = dt.files;
            }

            function syncGrid() {
                var count = selectedFiles.filter(Boolean).length;
                document.getElementById('new-images-preview-grid').style.display = count ? 'grid' : 'none';
            }

            // Delete existing image
            $(document).on('click', '.delete-gallery-btn', function() {
                var id    = $(this).data('id');
                var token = $("meta[name='csrf-token']").attr('content');
                if (!confirm('Delete this image?')) return;
                $.ajax({
                    url: '{{ route("user.admin.organization.image.delete") }}',
                    type: 'GET',
                    data: { id: id, _token: token },
                    success: function() {
                        toastr.success('Image deleted');
                        $('#gallery-item-' + id).fadeOut(250, function() { $(this).remove(); });
                    },
                    error: function() { toastr.error('Failed to delete image'); }
                });
            });
        })();
    </script>

    {{-- SortableJS for image reordering --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var grid = document.getElementById('existing-gallery-grid');
            if (!grid) return;
            Sortable.create(grid, {
                handle: '.drag-handle',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function() {
                    var items = grid.querySelectorAll('.gallery-item[data-id]');
                    var order = Array.from(items).map(function(el) { return el.getAttribute('data-id'); });
                    $.ajax({
                        url: "{{ route('user.admin.organization.image.reorder') }}",
                        method: 'POST',
                        data: {
                            _token: $("meta[name='csrf-token']").attr('content'),
                            order: order
                        },
                        success: function(res) {
                            if (res.status) toastr.success('Image order updated');
                        },
                        error: function() { toastr.error('Failed to update image order'); }
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
                            window.location.reload();
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
