@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Gallery
@endsection
@push('styles')
@endpush

@section('content')
<section class="inner_banner_sec"
            style="background-image: url({{asset('frontend_assets/uploads/2023/07/inner_banner.jpg')}}); background-position: center; background-repeat: no-repeat; background-size: cover">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="inner_banner_ontent text-center">
                            <h1>Gallery</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="gallery_page_sec">
            <div class="container">
                <div class="row">
                    @if (count($galleries) > 0)
                    @foreach ($galleries as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="picture_wrapper">
                            <a href="{{Storage::url($item->image)}}" data-lightbox="models">
                                <img src="{{Storage::url($item->image)}}" alt="">
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="col-md-12">
                        <div class="alert alert-danger" role="alert">
                            No Gallery Found!
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>
@endsection

@push('scripts')
@endpush
