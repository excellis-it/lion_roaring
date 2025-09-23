@extends('user.layouts.master')
@section('title')
    Contact Page CMS - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.store-cms.contact.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $cms->id ?? '' }}">
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Contact Page CMS</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Banner Image</label>
                        <input type="file" name="banner_image" class="form-control" />
                        @if (isset($cms->banner_image))
                            <img src="{{ asset($cms->banner_image) }}" alt="banner" class="img-thumbnail mt-2"
                                style="max-height:120px;">
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Banner Title</label>
                        <input type="text" name="banner_title"
                            value="{{ old('banner_title', $cms->banner_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Card One Title (Email)</label>
                        <input type="text" name="card_one_title"
                            value="{{ old('card_one_title', $cms->card_one_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Card Two Title (Address)</label>
                        <input type="text" name="card_two_title"
                            value="{{ old('card_two_title', $cms->card_two_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Card Three Title (Phone)</label>
                        <input type="text" name="card_three_title"
                            value="{{ old('card_three_title', $cms->card_three_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email (Card One Content)</label>
                        <input type="text" name="card_one_content"
                            value="{{ old('card_one_content', $cms->card_one_content ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Address (Card Two Content)</label>
                        <input type="text" name="card_two_content"
                            value="{{ old('card_two_content', $cms->card_two_content ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone (Card Three Content)</label>
                        <input type="text" name="card_three_content"
                            value="{{ old('card_three_content', $cms->card_three_content ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Call Section Title</label>
                        <input type="text" name="call_section_title"
                            value="{{ old('call_section_title', $cms->call_section_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Follow Us Title</label>
                        <input type="text" name="follow_us_title"
                            value="{{ old('follow_us_title', $cms->follow_us_title ?? '') }}" class="form-control" />
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Call Section Content</label>
                        <textarea name="call_section_content" class="form-control" rows="4">{{ old('call_section_content', $cms->call_section_content ?? '') }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Map Iframe Src</label>
                        <textarea name="map_iframe_src" class="form-control" rows="2" placeholder="Paste Google Maps iframe src URL only">{{ old('map_iframe_src', $cms->map_iframe_src ?? '') }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary me-2">Save</button>
                    <a href="{{ route('user.store-cms.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
