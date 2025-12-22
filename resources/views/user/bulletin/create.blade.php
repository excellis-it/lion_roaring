@extends('user.layouts.master')
@section('title')
    Bulletin - {{ env('APP_NAME') }}
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
                    <form action="{{ route('bulletins.store') }}" method="POST" enctype="multipart/form-data"
                        id="create-bulletin">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Add Bulletin In Bulletin Box</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if (auth()->user()->user_type == 'Global')
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="country_id">Country*</label>

                                        <select name="country_id" id="country_id" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country_id'))
                                            <span class="error">{{ $errors->first('country_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="title">Title</label>

                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ old('title') }}" placeholder="Enter Title">
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
                                        placeholder="Enter Description">{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="error">{{ $errors->first('description') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('bulletins.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
