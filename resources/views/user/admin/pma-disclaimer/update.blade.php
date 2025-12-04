@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update PMA Terms Page
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.css">
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Update PMA Terms Page</h3>
                    <p class="text-muted small mb-0">Update PMA Terms Page</p>
                </div>
            </div>
                <form action="{{ route('pma-terms.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $term->id ?? '' }}">
                    <div class="sales-report-card-wrap mt-5">
                        <div class="form-head">
                            <h4>Details</h4>
                        </div>

                        <div class="row mb-4">
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

                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- checkbox_text --}}
                                        <label for="floatingInputValue"> Checkbox Text*</label>
                                       <input type="text" name="checkbox_text" id="checkbox_text" value="{{ isset($term->checkbox_text) ? $term->checkbox_text : old('checkbox_text') }}" class="form-control" placeholder="Checkbox Text">
                                        @if ($errors->has('checkbox_text'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('checkbox_text') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="btn-1">
                                      <button type="submit" class="print_btn me-2 mt-2">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
