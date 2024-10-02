@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Principle and Business Page
@endsection
@push('styles')
@endpush
@section('head')
    Update Principle and Business Page
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('principle-and-business.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$business->id ?? ''}}">
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>Menu Section</h4>
                        </div>

                        <div class="row justify-content-between">
                            {{-- courses --}}
                            <div class="col-md-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Banner Image</label>
                                        <input type="file" class="form-control" id="banner_image"
                                            name="banner_image" value="{{ old('banner_image') }}"
                                            placeholder="Banner Image">
                                        @if ($errors->has('banner_image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        @if(isset($business->banner_image))
                                        <img src="{{ Storage::url($business->banner_image) }}" alt="banner_image" id="preview_banner_image" style="width: 180px; height: 100px;">
                                        @else
                                        <img src="" alt="banner_image" id="preview_banner_image" style="width: 180px; height: 100px;display:none;">
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
                                            name="banner_title" value="{{ isset($business->banner_title) ? $business->banner_title : old('banner_title') }}"
                                            placeholder="Banner Title">
                                        @if ($errors->has('banner_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sales-report-card-wrap mt-5">
                        <div class="form-head">
                            <h4>Details</h4>
                        </div>

                        <div class="row">
                            {{-- Image --}}
                            <div class="col-md-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Image</label>
                                        <input type="file" class="form-control" id="image"
                                            name="image" value="{{ old('image') }}"
                                            placeholder="Image">
                                        @if ($errors->has('image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        @if(isset($business->image))
                                        <img src="{{ Storage::url($business->image) }}" alt="banner_image" id="preview_image" style="width: 180px; height: 100px;">
                                        @else
                                        <img src="" alt="banner_image" id="preview_image" style="width: 180px; height: 100px;display:none;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description*</label>
                                        <textarea name="description" id="description" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($business->description) ? $business->description : old('description') }}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description1*</label>
                                        <textarea name="description1" id="description1" cols="30" rows="10" placeholder="Description1"
                                            class="form-control">{{ isset($business->description1) ? $business->description1 : old('description1') }}</textarea>
                                        @if ($errors->has('description1'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description1') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description2*</label>
                                        <textarea name="description2" id="description2" cols="30" rows="10" placeholder="Description2"
                                            class="form-control">{{ isset($business->description2) ? $business->description2 : old('description2') }}</textarea>
                                        @if ($errors->has('description2'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description2') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description 3*</label>
                                        <textarea name="description3" id="description3" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($business->description3) ? $business->description3 : old('description3') }}</textarea>
                                        @if ($errors->has('description3'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description3') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description 4*</label>
                                        <textarea name="description4" id="description4" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($business->description4) ? $business->description4 : old('description4') }}</textarea>
                                        @if ($errors->has('description4'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description4') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

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
                                        <input type="text" class="form-control" id="floatingInputValue" name="meta_title"
                                            value="{{ isset($business->meta_title) ? $business->meta_title : old('meta_title') }}"
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
                                            value="{{ isset($business->meta_keywords) ? $business->meta_keywords : old('meta_keywords') }}"
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
                                            placeholder="Meta Description" class="form-control">{{ isset($business->meta_description) ? $business->meta_description : old('meta_description') }}</textarea>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $('#description').summernote({
        placeholder: 'Description',
        tabsize: 2,
        height: 500
    });
    $('#description1').summernote({
        placeholder: 'Description1',
        tabsize: 2,
        height: 500
    });
    $('#description2').summernote({
        placeholder: 'Description2',
        tabsize: 2,
        height: 500
    });
    $('#description3').summernote({
        placeholder: 'Description3',
        tabsize: 2,
        height: 500
    });
    $('#description4').summernote({
        placeholder: 'Description4',
        tabsize: 2,
        height: 500
    });
</script>


<script>
    $(document).ready(function() {
        $('#banner_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#preview_banner_image').show();
                    $('#preview_banner_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
    });
    </script>

<script>
    $(document).ready(function() {
        $('#image').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview_image').show();
                $('#preview_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    });
    </script>
@endpush
