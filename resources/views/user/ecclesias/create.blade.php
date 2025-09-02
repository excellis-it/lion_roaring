@extends('user.layouts.master')
@section('title')
    Ecclesias - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('ecclesias.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Create Ecclesia</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Ecclesia Name *</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                        placeholder="">
                                    @if ($errors->has('name'))
                                        <div class="error" style="color:red !important;">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- country --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Country *</label>
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                @if (old('country') == $country->id) selected @endif
                                                {{ $country->code == 'US' ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <div class="error" style="color:red !important;">
                                            {{ $errors->first('country') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Submit</button>
                                <a href="{{ route('ecclesias.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
