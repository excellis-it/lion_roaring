@extends('user.layouts.master')
@section('title')
    Upload Becoming a Leader - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
<section id="loading">
    <div id="loading-content"></div>
</section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('leadership-development.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Save Becoming a Leader</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="file">Choose File</label>

                                    <input type="file" name="file" id="file" class="form-control" >
                                    @if ($errors->has('file'))
                                        <span class="error">{{ $errors->first('file') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Topics *</label>
                                    <select name="topic_id" id="topics" class="form-control">
                                        <option value="">Select Topics</option>
                                        @foreach ($topics as $topic)
                                            <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->topic_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('topic_id'))
                                    <span class="error">{{ $errors->first('topic_id') }}</span>
                                @endif

                                </div>
                            </div>
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Save</button>
                                <a href="{{ route('leadership-development.index') }}"
                                    class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    <script>
        $(document).ready(function() {
            $("#uploadForm").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
    @endpush
