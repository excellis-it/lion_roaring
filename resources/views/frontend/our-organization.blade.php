@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - {{$our_organization['meta_title'] ?? 'Ecclesia Association'}}
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
                        <h1>{{$our_organization['name'] ?? 'title'}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="key_feature_sec">
        <div class="v_text right_v feature_v"></div>
        <div class="key_bg">
            <img src="{{ asset('frontend_assets/images/line.png') }}" alt="">
        </div>
        <div class="container">
            <div class="row align-items-center justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="text-center">
                        <h2>{{$our_organization['description'] ?? 'description'}}</h2>
                    </div>
                </div>
            </div>
            <div class="slid_bh">
                @if (count($organization_centers) > 0)
                    @foreach ($organization_centers as $key => $organization_center)
                    <div class="padding_k">
                        <div class="{{($key % 2 == 0 ) ? 'bounce_1' : 'bounce_2' }}">
                            <div class="{{($key % 2 == 0 ) ? 'one_cli' : 'one_cli1' }}">
                                <div class="one_cli_nh">
                                    <img src="{{ asset('frontend_assets/images/before_n.png') }}" alt="">
                                </div>
                                <div class="clild_box">
                                    <div class="clild_sec">
                                        <img src="{{Storage::url($organization_center->image) ?? 'https://via.placeholder.com/150'}}"
                                            alt="">
                                        <h4>{{$organization_center->name}}</h4>

                                    </div>
                                    <a href="{{route('features', $organization_center->slug)}}" class="ellipss_right" tabindex="-1">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                </div>
                            </div>
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
