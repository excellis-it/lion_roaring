@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Register Page Agreement Page
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
          
                <form action="{{ route('register-agreements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $agreement->id ?? '' }}">
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
                                        <label for="floatingInputValue">Agreement Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="agreement_title"
                                            value="{{ isset($agreement->agreement_title) ? $agreement->agreement_title : old('agreement_title') }}"
                                            placeholder="Agreement Title">
                                        @if ($errors->has('agreement_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('agreement_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Agreement Description*</label>
                                        <textarea name="agreement_description" id="description" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($agreement->agreement_description) ? $agreement->agreement_description : old('agreement_description') }}</textarea>
                                        @if ($errors->has('agreement_description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('agreement_description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- checkbox_text --}}
                                        <label for="floatingInputValue"> Checkbox Text*</label>
                                        <input type="text" name="checkbox_text" id="checkbox_text"
                                            value="{{ isset($agreement->checkbox_text) ? $agreement->checkbox_text : old('checkbox_text') }}"
                                            class="form-control" placeholder="Checkbox Text">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        // ClassicEditor.create(document.querySelector("#description"));
        $('#description').summernote({
            placeholder: 'Description*',
            tabsize: 2,
            height: 400
        });
    </script>
@endpush
