@extends('user.layouts.master')
@section('title')
    Job Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('jobs.update', $job->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Job Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- job_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Job Title* </label>
                                    <input type="text" name="job_title" id="job_title" class="form-control"
                                        placeholder="Enter Job Title" value="{{ $job->job_title }}">
                                    @if ($errors->has('job_title'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_type"> Job Type* </label>
                                    <input type="text" name="job_type" id="job_type" class="form-control"
                                        placeholder="Enter Job Type" value="{{ $job->job_type }}">
                                    @if ($errors->has('job_type'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_type') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_location --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_location"> Job Location* </label>
                                    <input type="text" name="job_location" id="job_location" class="form-control"
                                        placeholder="Enter Job Location" value="{{ $job->job_location }}">
                                    @if ($errors->has('job_location'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_location') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_salary --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_salary"> Job Salary </label>
                                    <input type="text" name="job_salary" id="job_salary" class="form-control"
                                        placeholder="Enter Job Salary" value="{{ $job->job_salary }}">
                                    @if ($errors->has('job_salary'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_salary') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_experience --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_experience"> Job Experience </label>
                                    <input type="text" name="job_experience" id="job_experience" class="form-control"
                                        placeholder="Enter Job Experience" value="{{ $job->job_experience }}">
                                    @if ($errors->has('job_experience'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_experience') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Job Description </label>
                                    <textarea name="job_description" id="description" class="form-control"
                                        placeholder="Enter Job Description">{{ $job->job_description }}</textarea>
                                    @if ($errors->has('job_description'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_description') }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('jobs.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
        </script>
        <script>
            ClassicEditor.create(document.querySelector("#description"));
        </script>
    @endpush
