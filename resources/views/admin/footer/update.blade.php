@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Footer
@endsection
@push('styles')
@endpush
@section('head')
    Update Footer
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('footer.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $footer->id ?? '' }}">
                    <div class="sales-report-card-wrap">
                        <div class="row justify-content-between">
                            {{-- courses --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">Footer logo</label>
                                        <input type="file" class="form-control" id="floatingInputValue"
                                            name="footer_logo" value="{{ old('footer_logo') }}" placeholder="Footer Logo">
                                        @if ($errors->has('footer_logo'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_logo') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- our_organization_id --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_title"
                                            value="{{ isset($footer->footer_title) ? $footer->footer_title : old('footer_title') }}"
                                            placeholder="Footer Title">
                                        @if ($errors->has('footer_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_title') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_playstore_link --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Playstore Link</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_playstore_link"
                                            value="{{ isset($footer->footer_playstore_link) ? $footer->footer_playstore_link : old('footer_playstore_link') }}"
                                            placeholder="Footer Playstore Link">
                                        @if ($errors->has('footer_playstore_link'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_playstore_link') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_appstore_link --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Appstore Link</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_appstore_link"
                                            value="{{ isset($footer->footer_appstore_link) ? $footer->footer_appstore_link : old('footer_appstore_link') }}"
                                            placeholder="Footer Appstore Link">
                                        @if ($errors->has('footer_appstore_link'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_appstore_link') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- footer_newsletter_title --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Footer Newsletter Title*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="footer_newsletter_title"
                                            value="{{ isset($footer->footer_newsletter_title) ? $footer->footer_newsletter_title : old('footer_newsletter_title') }}"
                                            placeholder="Footer Newsletter Title">
                                        @if ($errors->has('footer_newsletter_title'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('footer_newsletter_title') }}</div>
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
