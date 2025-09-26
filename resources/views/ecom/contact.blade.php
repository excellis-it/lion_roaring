@php
    use App\Helpers\Helper;
@endphp
@extends('ecom.layouts.master')
@section('title', 'Contact Us')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ isset($contactCms->banner_image) && $contactCms->banner_image ? (\Illuminate\Support\Str::startsWith($contactCms->banner_image, 'http') ? $contactCms->banner_image : (\Illuminate\Support\Str::startsWith($contactCms->banner_image, 'storage/') ? asset($contactCms->banner_image) : Storage::url($contactCms->banner_image))) : \App\Helpers\Helper::estorePageBannerUrl('contact') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>{{ $contactCms->banner_title ?? 'Contact Us' }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="form-sec common-padd">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="contact-card-wprepper">
                        <div class="con-card text-center">
                            <div class="icon"><i class="fa-regular fa-envelope"></i></div>
                            <div class="content">
                                <div class="icon-heading">{{ $contactCms->card_one_title ?? 'Mail us' }}</div>
                                <div class="cont-details">
                                    <a href="mailto:{{ $contactCms->card_one_content ?? 'info@example.com' }}">
                                        {{ $contactCms->card_one_content ?? 'info@example.com' }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="con-card text-center">
                            <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="content">
                                <div class="icon-heading">{{ $contactCms->card_two_title ?? 'Our Address' }}</div>
                                <div class="cont-details">
                                    <a href="javascript:void(0);"> {{ $contactCms->card_two_content ?? '---' }} </a>
                                </div>
                            </div>
                        </div>

                        <div class="con-card text-center">
                            <div class="icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="content">
                                <div class="icon-heading">{{ $contactCms->card_three_title ?? 'Call us' }}</div>
                                <div class="cont-details">
                                    <a href="tel:{{ $contactCms->card_three_content ?? '' }}">
                                        {{ $contactCms->card_three_content ?? '+1 000 000 0000' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="form-area-wrepper">
                        <form action="{{ route('contact-us.form') }}" id="contact-us" method="POST">
                            @csrf
                            <div class="row">

                                <div class="col-lg-6 mb-4">
                                    <label for="" class="form-label">First Name: </label>
                                    <input name="first_name" type="text" class="form-control" aria-label="First name"
                                        placeholder="First Name">
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <label for="" class="form-label">Last Name: </label>
                                    <input name="last_name" type="text" class="form-control" aria-label="Last name"
                                        placeholder="Last Name">
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <label for="" class="form-label">Email: </label>
                                    <input name="email" type="email" class="form-control" aria-label="email"
                                        placeholder="Email">
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <label for="" class="form-label">Phone Number: </label>
                                    <input name="phone" type="tel" class="form-control" aria-label="Phone"
                                        placeholder="Phone Number">
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <label for="" class="form-label">Type Your Message: </label>
                                    <textarea name="message" class="form-control" id="exampleFormControlTextarea1" rows="3"
                                        placeholder="Type Your Message..."></textarea>
                                </div>
                                <!-- Inside your <form> -->
                                <div class="col-xl-12 text-center mb-3">
                                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                    <span class="text-danger" id="g-recaptcha-response_error"></span>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                                @if (session('success'))
                                    <div class="alert alert-success mt-4">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <section class="common-padd pt-0" hidden>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="">
                        <div class="w-100 text-center text-white">
                            <h2>{{ $contactCms->call_section_title ?? 'Call us Today! We’ll be happy to assist you.' }}
                            </h2>
                            <p class="mt-2">{!! nl2br(e($contactCms->call_section_content ?? '')) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="follow-us common-padd pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ $contactCms->follow_us_title ?? 'Follow Us On' }}</h2>
                    <div class="follow-us-icon-list">
                        <ul>
                            <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_facebook_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-facebook"></i></a></li>
                            <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_instagram_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-instagram"></i></a></li>
                            <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_twitter_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-twitter"></i></a></li>
                            <li><a href="{!! Helper::getFooterCms() ? Helper::getFooterCms()->footer_youtube_link : 'javascript:void(0);' !!}"><i class="fa-brands fa-youtube"></i></a></li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (isset($contactCms->map_iframe_src) && $contactCms->map_iframe_src)
        <section>
            <div class="map">
                <iframe src="{{ $contactCms->map_iframe_src }}" width="100%" height="450" style="border:0;"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
    @endif


@endsection

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        $(document).ready(function() {
            $('#contact-us').on('submit', function(e) {
                var recaptchaResponse = grecaptcha.getResponse();
                if (recaptchaResponse.length === 0) {
                    e.preventDefault(); // Prevent form submission
                    $('#g-recaptcha-response_error').text('Please verify that you are not a robot.');
                } else {
                    $('#g-recaptcha-response_error').text(''); // Clear any previous error message

                    e.preventDefault();
                    var form = $(this);
                    var url = form.attr('action');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: form.serialize(),
                        success: function(response) {
                            // window.location.reload();
                            // clear form fields and toastr success message
                            form[0].reset();
                            toastr.success(response.message);
                            console.log(response);
                            window.location.reload();
                        },
                        error: function(xhr) {
                            $('.text-danger').html('');
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                if (key.includes('.')) {
                                    var fieldName = key.split('.')[0];
                                    // Display errors for array fields
                                    var num = key.match(/\d+/)[0];
                                    $('#' + fieldName + '_' + num).html(
                                        value[0]);
                                } else {
                                    console.log(key + '_error');
                                    // after text danger span
                                    $('#' + key + '_error').html(value[0]);
                                }
                            });
                        }
                    });

                }
            });
        });
    </script>
@endpush
