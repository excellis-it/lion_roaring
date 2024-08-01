@extends('user.layouts.master')
@section('title')
    Meeting Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('meetings.update', $meeting->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
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
                                    <input type="text" name="title" id="title" class="form-control"  placeholder="Enter Meeting Title" value="{{ $meeting->title }}">

                                    @if ($errors->has('title'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>
                            </div>
                              {{-- meeting_link --}}
                              <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="meeting_link"> Meeting Link </label>
                                    <input type="text" name="meeting_link" id="meeting_link" class="form-control" placeholder="Enter Meeting Link" value="{{ $meeting->meeting_link }}">

                                    @if ($errors->has('meeting_link'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('meeting_link') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- start_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_time"> Meeting Start Time* </label>
                                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" placeholder="Enter Meeting Start Time" value="{{ date('Y-m-d\TH:i', strtotime($meeting->start_time)) }}">
                                    @if ($errors->has('start_time'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('start_time') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- end_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_time"> Meeting End Time* </label>
                                    <input type="datetime-local" name="end_time" id="end_time" class="form-control" placeholder="Enter Meeting End Time" value="{{ date('Y-m-d\TH:i', strtotime($meeting->end_time)) }}">
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
                                        placeholder="Enter Description" > {{ $meeting->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="text-danger" style="color:red !important;">{{ $errors->first('description') }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('meetings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')

    @endpush
