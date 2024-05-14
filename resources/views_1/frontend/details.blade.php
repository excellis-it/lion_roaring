@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Details
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('frontend_assets/uploads/2023/07/inner_banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>Details</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about_sec">
        <div class="container">
            @if (count($details) > 0)
                @php
                    $count = 2;
                @endphp
                @foreach ($details as $key => $item)
                    <div class="row align-items-center justify-content-center mb-5">
                        <div class="col-xl-7 col-lg-7 {{ $count % 2 == 0 ? 'order-2 order-lg-1' : '' }}" data-aos="fade-up"
                            data-aos-duration="500">
                            <div class="about_text heading_hp text_white">
                                <p>{{ $item->description }}

                                </p>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 " data-aos="fade-up" data-aos-duration="1000">
                            <div class="single_img_lion">
                                <img src="{{ Storage::url($item->image) }}" alt="" />
                            </div>
                        </div>
                    </div>
                    @php
                        $count++;
                    @endphp
                @endforeach
            @else
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        No Details Found!
                    </div>
                </div>
            @endif
        
        </div>
    </section>
@endsection

@push('scripts')
@endpush
