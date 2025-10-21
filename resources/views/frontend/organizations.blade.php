@extends('frontend.layouts.master')
@section('meta_title')
 <meta name="description" content="{{$organization['meta_description'] ?? ''}}">
    <meta name="keywords" content="{{$organization['meta_keywords'] ?? ''}}">
    <meta name="title" content="{{$organization['meta_title'] ?? ''}}">
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{$organization['meta_title'] ?? 'Organization'}}
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{($organization['banner_image']) ? Storage::url($organization['banner_image']) : ''}}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{$organization['banner_title'] ?? 'title'}}</h1>
                        <p><strong><em>{!! $organization['banner_description'] ?? 'banner description' !!}</em></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-5">
        <div class="container">
            <div class="row align-items-center justify-content-center mb-5">
                @if ($organization->images->isNotEmpty())
                    @foreach ($organization->images as $item)
                        <div class="col-xl-10 col-lg-12 mb-5 aos-init aos-animate" data-aos="fade-up"
                            data-aos-duration="1000">
                            <div class="lion_aducation">
                                <img src="{{ Storage::url($item->image) }}" alt="">
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-12">
                        <div class="alert alert-danger" role="alert">
                            No Image Found!
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="project_sec">
        <div class="container">
            <div class="heading_hp text-center">
                <h6>{{$organization['project_section_title'] ?? 'title'}}</h6>
                <h2>{{$organization['project_section_sub_title'] ?? 'sub title'}}</h2>
                <p>{!! $organization['project_section_description'] ?? 'description' !!}</p>
            </div>
            <div
                class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 justify-content-center">
                @if ($organization->projects->isNotEmpty())
                    @foreach ($organization->projects as $item)
                        <div class="col mb-4 aos-init" data-aos="fade-up" data-aos-duration="500">
                            <div class="project">
                                <h4>{{$item->title}}</h4>
                                {!! $item->description !!}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
