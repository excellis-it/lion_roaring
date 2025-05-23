@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Contact Us
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ $contact['banner_image'] ? Storage::url($contact['banner_image']) : '' }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{ $contact['banner_title'] ?? 'title' }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-us">
        <div class="container">
            <div class="contact-wrap-main">
                <div class="row">
                    <div class="col-xl-6 col-md-12">
                        <div class="contact-left heading_hp">
                            <h2>{{ $contact['title'] ?? 'title' }}</h2>
                            <p>{!! $contact['description'] ?? 'description' !!}</p>
                            <form action="{{ route('contact-us.form') }}" id="contact-us" method="POST">
                                @csrf
                                <div class="contact-form">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-12">
                                            <div class="form-group-wrap">
                                                <label for="First-Name" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="First-Name" placeholder=""
                                                    name="first_name">
                                                <span class="text-danger" id="first_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-12">
                                            <div class="form-group-wrap">
                                                <label for="Last-Name" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="Last-Name" placeholder=""
                                                    name="last_name">
                                                <span class="text-danger" id="last_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-12">
                                            <div class="form-group-wrap">
                                                <label for="Email-Id" class="form-label">Email Id</label>
                                                <input type="email" class="form-control" id="First-Name" placeholder=""
                                                    name="email">
                                                <span class="text-danger" id="email_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-12">
                                            <div class="form-group-wrap">
                                                <label for="Phone Number" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="mobile_code" placeholder=""
                                                    name="phone">
                                                <span class="text-danger" id="phone_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group-wrap">
                                                <label for="message" class="form-label">Message</label>
                                                <textarea class="form-control" id="message" rows="3" name="message"></textarea>
                                                <span class="text-danger" id="message_error"></span>
                                            </div>
                                        </div>
                                        <!-- Inside your <form> -->
                                        <div class="col-xl-12 text-center mb-3">
                                            <div class="g-recaptcha" data-sitekey="{{env('RECAPTCHA_SITE_KEY')}}"></div>
                                            <span class="text-danger" id="g-recaptcha-response_error"></span>
                                        </div>

                                        <div class="col-xl-12 text-center">
                                            <div class="send-msg">
                                                <button type="submit" class="btn">SEND MESSAGE</button>
                                            </div>
                                        </div>
                                        {{-- success message --}}

                                        @if (session('success'))
                                            <div class="alert alert-success mt-4">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <div class="contact-right">
                            <div class="contact-info d-flex justify-content-start align-items-start">
                                <div>
                                    <div class="con-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                </div>
                                <div class="con-text">
                                    <h3>WRITE US</h3>
                                    <p>
                                    </p>
                                    <p>{!! nl2br($contact['address']) !!}</p>
                                    <p></p>
                                </div>
                            </div>
                            <div class="contact-info d-flex justify-content-start align-items-start">
                                <div>
                                    <div class="con-icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                </div>
                                <div class="con-text">
                                    <h3>CALL US</h3>
                                    <a href="tel:{{ $contact['phone'] ?? 'phone' }}">{{ $contact['phone'] ?? 'phone' }}</a>
                                </div>
                            </div>
                            <div class="contact-info d-flex justify-content-start align-items-start">
                                <div>
                                    <div class="con-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="con-text">
                                    <h3>EMAIL US</h3>
                                    <a
                                        href="mailto:{{ $contact['email'] ?? 'email' }}">{{ $contact['email'] ?? 'email' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function initializeIntlTelInput() {
            const phoneInput = $("#mobile_code");

            phoneInput.intlTelInput({
                geoIpLookup: function(callback) {
                    $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        const countryCode = (resp && resp.country) ? resp.country : "US";
                        callback(countryCode);
                    });
                },
                initialCountry: "auto",
                separateDialCode: true,
            });

            const selectedCountry = phoneInput.intlTelInput('getSelectedCountryData');
            const dialCode = selectedCountry.dialCode;
            const exampleNumber = intlTelInputUtils.getExampleNumber(selectedCountry.iso2, 0, 0);

            let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils
                .numberFormat.NATIONAL);
            maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

            const mask = maskNumber.replace(/[0-9+]/g, '0');
            phoneInput.mask(mask, {
                placeholder: maskNumber
            });

            phoneInput.on('countrychange', function() {
                $(this).val('');
                const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                const newDialCode = newSelectedCountry.dialCode;
                const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

                let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2,
                    intlTelInputUtils.numberFormat.NATIONAL);
                newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');

                const newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                phoneInput.mask(newMask, {
                    placeholder: newMaskNumber
                });
            });
        }

        function setPhoneNumber() {
            const phoneInput = $("#mobile_code");
            const fullNumber = "{{ old('full_phone_number') }}";

            if (fullNumber) {
                phoneInput.intlTelInput("setNumber", fullNumber);
            }
        }

        $(document).ready(function() {
            initializeIntlTelInput();
            setPhoneNumber();

            $('form').on('submit', function() {
                const phoneInput = $("#mobile_code");
                const fullNumber = phoneInput.intlTelInput('getNumber');
                const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;

                $('<input>').attr({
                    type: 'hidden',
                    name: 'full_phone_number',
                    value: fullNumber
                }).appendTo('form');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'country_code',
                    value: countryCode
                }).appendTo('form');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#mobile_code").intlTelInput({
                // insizlized usa country code
                initialCountry: "US",
                separateDialCode: true,
                // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
            });
            $('#contact-us').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
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
                                $('#' + fieldName + '_' + num).html(value[0]);
                            } else {
                                console.log(key + '_error');
                                // after text danger span
                                $('#' + key + '_error').html(value[0]);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
