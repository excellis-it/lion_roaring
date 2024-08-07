@extends('user.layouts.master')
@section('title')
    Job - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Job Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Job Title* </label>
                                    <input type="text" name="job_title" id="job_title" class="form-control" value="{{ old('job_title') }}"
                                        placeholder="Enter Job Title" >
                                    @if ($errors->has('job_title'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_type"> Job Type* </label>
                                    <input type="text" name="job_type" id="job_type" class="form-control" value="{{ old('job_type') }}"
                                        placeholder="Enter Job Type" >
                                    @if ($errors->has('job_type'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_type') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_location --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_location"> Job Location* </label>
                                    <input type="text" name="job_location" id="job_location" class="form-control" value="{{ old('job_location') }}"
                                        placeholder="Enter Job Location" >
                                    @if ($errors->has('job_location'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_location') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_salary --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_salary"> Job Salary </label>
                                    <input type="text" name="job_salary" id="job_salary" class="form-control" value="{{ old('job_salary') }}"
                                        placeholder="Enter Job Salary" >
                                    @if ($errors->has('job_salary'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_salary') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_experience --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_experience"> Job Experience (Year) </label>
                                    <input type="number" name="job_experience" id="job_experience" class="form-control" value="{{ old('job_experience') }}"
                                        placeholder="Enter Job Experience" >
                                    @if ($errors->has('job_experience'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_experience') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- contact_person --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="contact_person"> Contact Person </label>
                                    <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person') }}"
                                        placeholder="Enter Contact Person" >
                                    @if ($errors->has('contact_person'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('contact_person') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- contact_email --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="contact_email"> Contact Email </label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ old('contact_email') }}"
                                        placeholder="Enter Contact Email" >
                                    @if ($errors->has('contact_email'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('contact_email') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Job Description* </label>
                                    <textarea name="job_description" id="description" class="form-control"
                                        placeholder="Enter Job Description" >{{ old('job_description') }}</textarea>
                                    @if ($errors->has('job_description'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('job_description') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('jobs.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
        <script type="text/javascript">
            Dropzone.options.imageUpload = {
                maxFilesize: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp"
            };
        </script>
        <script>
            ClassicEditor.create(document.querySelector("#description"));
            ClassicEditor.create(document.querySelector("#specification"));
        </script>
    @endpush
