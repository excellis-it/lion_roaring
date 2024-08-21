@extends('user.layouts.master')
@section('title')
    Topics - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('topics.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Save Topic</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Topic Name *</label>
                                    <input type="text" class="form-control" name="topic_name"
                                        value="{{ old('topic_name') }}" placeholder="">
                                    @if ($errors->has('topic_name'))
                                        <div class="error" style="color:red !important;">
                                            {{ $errors->first('topic_name') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label >Education Type</label>

                                <select name="education_type" id="type" class="form-control" >
                                    <option value="">Select Type</option>
                                    <option value="Becoming Sovereign" {{ old('education_type') == 'Becoming Sovereign' ? 'selected' : '' }}>Becoming Sovereign</option>
                                    <option value="Becoming Christ Like" {{ old('education_type') == 'Becoming Christ Like' ? 'selected' : '' }}>Becoming Christ Like</option>
                                    <option value="Becoming a Leader" {{ old('education_type') == 'Becoming a Leader' ? 'selected' : '' }}>Becoming a Leader</option>
                                </select>
                                @if ($errors->has('education_type'))
                                    <span class="error">{{ $errors->first('education_type') }}</span>
                                @endif
                            </div>
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Save</button>
                                <a href="{{ route('topics.index') }}" class="print_btn print_btn_vv"
                                    >Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
