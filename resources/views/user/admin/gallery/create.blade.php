@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Gallery
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Create Gallery</h3>
                    <p class="text-muted small mb-0">Create new gallery</p>
                </div>
            </div>
                <form action="{{ route('gallery.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            {{-- <h4>Gallery Details</h4> --}}
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="content_country_code">Content Country*</label>
                                        <select name="content_country_code" id="content_country_code" class="form-control">
                                            @foreach (\App\Models\Country::all() as $country)
                                                <option value="{{ $country->code }}"
                                                    {{ old('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('content_country_code'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('content_country_code') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="col-md-6 mb-3">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- question --}}
                                        <label for="floatingInputValue">Gallery Image* (Select Multiple)</label>
                                        <input type="file" class="form-control" id="floatingInputValue" name="image[]" multiple
                                            value="{{ old('image') }}" placeholder="Gallery Image*" accept="image/*">
                                        @if ($errors->has('image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-xl-6">
                                <div class="btn-1">
                                      <button type="submit" class="print_btn me-2 mt-2 mb-2">Create Gallery</button>
                                      <a href="{{ route('gallery.index') }}" class="print_btn print_btn_vv">Cancel</a>
                                    </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>


    </div>
@endsection

@push('scripts')
@endpush
