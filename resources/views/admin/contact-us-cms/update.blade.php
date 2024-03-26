@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Contact Us Page
@endsection
@push('styles')
@endpush
@section('head')
    Update Contact Us Page
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('contact-us-cms.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$contact_us->id ?? ''}}">
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>Menu Section</h4>
                        </div>

                        <div class="row justify-content-between">
                            {{-- courses --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Banner Image</label>
                                        <input type="file" class="form-control" id="floatingInputValue"
                                            name="banner_image" value="{{ old('banner_image') }}"
                                            placeholder="Banner Image">
                                        @if ($errors->has('banner_image'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- our_organization_id --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Banner Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="banner_title" value="{{ isset($contact_us->banner_title) ? $contact_us->banner_title : old('banner_title') }}"
                                            placeholder="Banner Title">
                                        @if ($errors->has('banner_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('banner_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sales-report-card-wrap mt-5">
                        <div class="form-head">
                            <h4>Details</h4>
                        </div>

                        <div class="row">

                            {{-- phone --}}
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta title --}}
                                        <label for="floatingInputValue">Call Us*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="phone" value="{{ isset($contact_us->phone) ? $contact_us->phone : old('phone') }}"
                                            placeholder="Call Us">
                                        @if ($errors->has('phone'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- email --}}
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta title --}}
                                        <label for="floatingInputValue">Email Us*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="email" value="{{ isset($contact_us->email) ? $contact_us->email : old('email') }}"
                                            placeholder="Email Us">
                                        @if ($errors->has('email'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- title --}}
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta title --}}
                                        <label for="floatingInputValue">Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="title" value="{{ isset($contact_us->title) ? $contact_us->title : old('title') }}"
                                            placeholder="Title">
                                        @if ($errors->has('title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                             {{-- address --}}
                             <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta title --}}
                                        <label for="floatingInputValue">Write Us*</label>
                                        <textarea type="text" class="form-control" id="floatingInputValue"
                                            name="address"
                                            placeholder="Write Us">{{ isset($contact_us->address) ? $contact_us->address : old('address') }}</textarea>
                                        @if ($errors->has('address'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('address') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description*</label>
                                        <textarea name="description" id="description" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($contact_us->description) ? $contact_us->description : old('description') }}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="btn-1">
                                    <button type="submit">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
@endpush
