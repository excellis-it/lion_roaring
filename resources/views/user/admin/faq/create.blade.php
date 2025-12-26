@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create FAQ
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Create FAQ</h3>
                    <p class="text-muted small mb-0">Create new FAQ</p>
                </div>
            </div>
                <form action="{{ route('user.admin.faq.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>FAQ Details</h4>
                        </div>
                        @if (auth()->user()->user_type == 'Global')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="content_country_code">Content Country*</label>
                                        <select name="content_country_code" id="content_country_code" class="form-control">
                                            @foreach (\App\Models\Country::all() as $country)
                                                <option value="{{ $country->code }}"
                                                    {{ old('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('content_country_code'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('content_country_code') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row justify-content-between">
                            <div class="col-md-12 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- question --}}
                                        <label for="floatingInputValue">Question*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="question"
                                            value="{{ old('question') }}" placeholder="Question*">
                                        @if ($errors->has('question'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('question') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- name --}}
                                        <label for="floatingInputValue">Answer*</label>
                                        <textarea name="answer" id="description" cols="30" rows="10" required placeholder="Answer"
                                            class="form-control description">{{ old('answer') }}</textarea>
                                        @if ($errors->has('answer'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('answer') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                  <button type="submit" class="print_btn me-2 mt-2 mb-2">Create FAQ</button>
                                  <a href="{{ route('user.admin.faq.index') }}" class="print_btn print_btn_vv">Cancel</a>
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
        $('#description').summernote({
            placeholder: 'Description',
            tabsize: 2,
            height: 400
        });
    </script>
@endpush
