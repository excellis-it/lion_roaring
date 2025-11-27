@extends('user.layouts.master')
@section('title')
    Elearning Topic Edit - {{ env('APP_NAME') }}
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
                    <form action="{{ route('elearning-topics.update', $topic->id) }}" method="POST"
                        enctype="multipart/form-data" id="uploadForm">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Edit Elearning Topic</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Topic Name *</label>
                                    <input type="text" class="form-control" name="topic_name"
                                        value="{{ $topic->topic_name }}" placeholder="">
                                    @if ($errors->has('topic_name'))
                                        <div class="error" style="color:red !important;">
                                            {{ $errors->first('topic_name') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                    <button type="submit" class="print_btn me-2">Save</button>
                                    <a href="{{ route('elearning-topics.index') }}"
                                        class="print_btn print_btn_vv">Cancel</a>
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
                $("#uploadForm").on("submit", function(e) {
                    // e.preventDefault();
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                });
            });
        </script>
    @endpush
