@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create FAQ
@endsection
@push('styles')
@endpush
@section('head')
    Create FAQ
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('faq.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>FAQ Details</h4>
                        </div>

                        <div class="row justify-content-between">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- name --}}
                                        <label for="floatingInputValue">Answer*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="answer" value="{{ old('answer') }}"
                                            placeholder="Answer*">
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
                                <button type="submit">Create FAQ</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector("#description"));
    </script>
@endpush
