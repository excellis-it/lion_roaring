@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Gallery
@endsection
@push('styles')
@endpush
@section('head')
    Update Gallery
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('gallery.update', $gallery->id) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            {{-- <h4>Gallery Details</h4> --}}
                        </div> 

                        <div class="row justify-content-between">
                            <div class="col-md-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- question --}}
                                        <label for="floatingInputValue">Gallery Image*</label>
                                        <input type="file" class="form-control" id="gallery_image" name="image" accept="image/*"
                                            value="{{ old('image') }}" placeholder="Gallery Image*">
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
                                    `   @if (isset($gallery->image))
                                            <img src="{{ Storage::url($gallery->image) }}" id="gallery_image_preview" alt="Footer Logo"
                                                style="width: 180px; height: 100px;">
                                        @else    
                                        <img src="" id="footer_logo_preview" alt="Footer Logo"
                                                style="width: 180px; height: 100px; display:none;">    
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="btn-1">
                                    <button type="submit">Update Gallery</button>
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
<script>
    $(document).ready(function() {
        $('#gallery_image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#gallery_image_preview').show();
                $('#gallery_image_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    });

</script>
@endpush
