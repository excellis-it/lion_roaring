@extends('elearning.layouts.master')
@section('meta')
@endsection
@section('title')
    {{ isset($cms['page_title']) ? $cms['page_title'] : '' }}
@endsection

@push('styles')
@endpush

@section('content')
<section class="banner__slider banner_sec middle_arrow">
    <div class="slider stick-dots">
        <div class="slide">
            <div class="slide__img">
                <img src="{{ isset($cms['page_banner_image']) ? Storage::url($cms['page_banner_image']) : asset('ecom_assets/images/banner.jpg') }}"
                    alt="banner" />
            </div>
            <div class="slide__content slide__content__left">
                <div class="slide__content--headings text-center">
                    <h2 class="title">{{ isset($cms['page_title']) ? $cms['page_title'] : '' }}</h2>
                    <!-- <a class="red_btn slidebottomleft" href="javascript:void(0);"><span>order now</span></a> -->
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about__section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="about__content">
                    <div class="about__content--headings">
                    </div>
                    <div class="about__content--text mt-5">
                        {!! isset($cms['page_content']) ? $cms['page_content'] : '' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')

@endpush
