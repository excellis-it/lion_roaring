@extends('user.layouts.master')
@section('title')
    Bulletin Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('bulletins.update', $bulletin->id) }}" method="POST" enctype="multipart/form-data" id="update-bulletin">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Edit Bulletin In Bulletin Box</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="title">Title</label>

                                <input type="text" name="title" id="title" class="form-control" value="{{ $bulletin->title }}"
                                    placeholder="Enter Title">
                                @if ($errors->has('title'))
                                    <span class="error">{{ $errors->first('title') }}</span>
                                @endif
                            </div>
                        </div>
                            {{-- type --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="type">Message</label>

                                <textarea name="description" id="description" class="form-control" rows="5" cols="30"
                                    placeholder="Enter Description">{{ $bulletin->description }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="error">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('bulletins.index') }}" class="print_btn print_btn_vv"
                                    >Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
