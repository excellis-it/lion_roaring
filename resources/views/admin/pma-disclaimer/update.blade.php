@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update PMA Terms Page
@endsection
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.css">
@endpush
@section('head')
    Update PMA Terms Page
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('pma-terms.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $term->id ?? '' }}">
                    <div class="sales-report-card-wrap mt-5">
                        <div class="form-head">
                            <h4>Details</h4>
                        </div>

                        <div class="row">
                            {{-- Image --}}
                            <div class="col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue"> Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="title"
                                            value="{{ isset($term->title) ? $term->title : old('title') }}"
                                            placeholder=" Title">
                                        @if ($errors->has('title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue"> Description*</label>
                                        <textarea name="description" id="description" cols="41" rows="30" placeholder="Description"
                                            class="form-control">{{ isset($term->description) ? $term->description : old('description') }}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.js"></script>
    <script>
       $('#description').summernote({
        placeholder: 'Hello stand alone ui',
        tabsize: 2,
        height: 500,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });
    </script>


@endpush
