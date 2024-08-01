@extends('user.layouts.master')
@section('title')
    Meeting - {{ env('APP_NAME') }}
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
                    <form action="{{ route('meetings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Meeting Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Meeting Title* </label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}"
                                        placeholder="Enter Meeting Title" >
                                    @if ($errors->has('title'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>
                            </div>
                              {{-- meeting_link --}}
                              <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="meeting_link"> Meeting Link </label>
                                    <input type="text" name="meeting_link" id="meeting_link" class="form-control" value="{{ old('meeting_link') }}"
                                        placeholder="Enter Meeting Link" >
                                    @if ($errors->has('meeting_link'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('meeting_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- start_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_time"> Meeting Start Time* </label>
                                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time') }}"
                                        placeholder="Enter Meeting Start Time" >
                                    @if ($errors->has('start_time'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('start_time') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- end_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_time"> Meeting End Time* </label>
                                    <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time') }}"
                                        placeholder="Enter Meeting End Time" >
                                    @if ($errors->has('end_time'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('end_time') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Description* </label>
                                    <textarea name="description" id="description" class="form-control"
                                        placeholder="Enter Description" >{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('description') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('meetings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
