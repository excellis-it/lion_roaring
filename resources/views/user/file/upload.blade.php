@extends('user.layouts.master')
@section('title')
    Upload File - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Upload Multiple Files</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="file">Choose File</label>
                                </div>
                                <input type="file" name="file[]" id="file" class="form-control" multiple>
                                @if ($errors->has('file'))
                                    <span class="error">{{ $errors->first('file') }}</span>
                                @endif
                            </div>
                            {{-- type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="type">Type</label>
                                </div>
                                <select name="type" id="type" class="form-control" >
                                    <option value="">Select Type</option>
                                    <option value="Becoming Sovereign">Becoming Sovereign</option>
                                    <option value="Becoming Christ Like">Becoming Christ Like</option>
                                    <option value="Leadership Development">Leadership Development</option>
                                </select>
                                @if ($errors->has('type'))
                                    <span class="error">{{ $errors->first('type') }}</span>
                                @endif

                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Upload</button>
                                <a href="{{ route('file.index') }}" class="print_btn print_btn_vv"
                                    >Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
