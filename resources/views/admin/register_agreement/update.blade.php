@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Register Page Agreement Page
@endsection
@push('styles')
@endpush
@section('head')
    Update Register Page Agreement Page
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
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
                                        <div class="text-muted small mb-2">
                                            <span class="me-3">Placeholders supported:</span> <strong>[[user_name]]</strong>
                                            &nbsp; <span class="mx-2">|</span> &nbsp;
                                            <strong>[[user_initial]]</strong>
                                            &nbsp; <span class="mx-2">|</span> &nbsp;
                                            <strong>[[seal_image]]</strong>
                                            &nbsp; <span class="mx-2">|</span> &nbsp;
                                            <strong>[[current_date]]</strong>
                                            &nbsp; <span class="mx-2">|</span> &nbsp;
                                            <strong>[[steward_member_1]]</strong>
                                            &nbsp; <span class="mx-2">|</span> &nbsp;
                                            <strong>[[steward_member_2]]</strong>
                                        </div>
                                        <textarea name="agreement_description" id="description" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($agreement->agreement_description) ? $agreement->agreement_description : old('agreement_description') }}</textarea>
                                        @if ($errors->has('agreement_description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('agreement_description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-md-12 mb-3">
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

                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="seal_image">Seal Image</label>
                                        <input type="file" name="seal_image" id="seal_image" class="form-control"
                                            accept="image/*">
                                        @if (isset($agreement->seal_image) && $agreement->seal_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $agreement->seal_image) }}"
                                                    alt="Seal Image" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        @if ($errors->has('seal_image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('seal_image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="steward_member_1">Office of Dominion Steward Member 1</label>
                                        <input type="text" name="steward_member_1" id="steward_member_1"
                                            class="form-control"
                                            value="{{ isset($agreement->steward_member_1) ? $agreement->steward_member_1 : old('steward_member_1') }}"
                                            placeholder="Steward Member Name">
                                        @if ($errors->has('steward_member_1'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('steward_member_1') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="steward_member_2">Office of Dominion Steward Member 2</label>
                                        <input type="text" name="steward_member_2" id="steward_member_2"
                                            class="form-control"
                                            value="{{ isset($agreement->steward_member_2) ? $agreement->steward_member_2 : old('steward_member_2') }}"
                                            placeholder="Steward Member Name">
                                        @if ($errors->has('steward_member_2'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('steward_member_2') }}</div>
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
