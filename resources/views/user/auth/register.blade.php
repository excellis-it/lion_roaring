<!DOCTYPE html>
<html lang="en-US">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Hugo 0.84.0">
        <title>{{ env('APP_NAME') }} - Register</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
            rel="stylesheet">
        <!-- Bootstrap core CSS -->
        <link href="{{ asset('user_assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css"
            integrity="sha512-Woz+DqWYJ51bpVk5Fv0yES/edIMXjj3Ynda+KWTIkGoynAMHrqTcDUQltbipuiaD5ymEo9520lyoVOo9jCQOCA=="
            crossorigin="anonymous" referrerpolicy="no-referrer">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link href="{{ asset('user_assets/css/menu.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('user_assets/css/style.min.') }}css">
        <link href="{{ asset('user_assets/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('user_assets/css/responsive.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <style>
            .eye-btn-1 {
                top: 29px;
            }
        </style>
    </head>

    <body style="background: #643271">
        <main>
            <section class="log-main">
                <div class="container">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-8">
                            <div class="login_bg_sec border-top-0">
                                <div class="logo-admin">
                                    <img src="{{ asset('user_assets/images/logo.png') }}" alt="">
                                </div>
                                <div class="heading_hp">
                                    <h2 id="greeting">Request Private Member Access</h2>
                                    <div class="admin-form">
                                        <form name="login-form" id="login-form" action="{{ route('register.check') }}"
                                            method="post">
                                            @csrf

                                            <div class="row">
                                                <div class="col-lg-6 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Username</label>
                                                        <input type="text" name="user_name" id="user_login"
                                                            class="input" value="{{ old('user_name') }}">
                                                        @if ($errors->has('user_name'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('user_name') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- eclessias --}}
                                                <div class="col-lg-6 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Ecclesia</label>
                                                        <select name="ecclesia_id" id="ecclesia_id" class="input">
                                                            <option value=""></option>
                                                            @foreach ($eclessias as $item)
                                                                <option value="{{ $item->id }}"
                                                                    {{ old('ecclesia_id') == $item->id ? 'selected' : '' }}>
                                                                    {{ $item->name . ' (' . $item->countryName->name . ')' ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('ecclesia_id'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('ecclesia_id') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">First Name</label>
                                                        <input type="text" name="first_name" id="user_login"
                                                            class="input" value="{{ old('first_name') }}">
                                                        @if ($errors->has('first_name'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('first_name') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Middle Name (Optional)</label>
                                                        <input type="text" name="middle_name" id="user_login"
                                                            class="input" value="{{ old('middle_name') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Last Name</label>
                                                        <input type="text" name="last_name" id="user_login"
                                                            class="input" value="{{ old('last_name') }}">
                                                        @if ($errors->has('last_name'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('last_name') }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Country</label>
                                                        <select name="country" id="country" class="input">
                                                            <option value="">Select Country</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    {{ old('country', $country->code == 'US' ? $country->id : '') == $country->id ? 'selected' : '' }}>
                                                                    {{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('country'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('country') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- state --}}
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">State</label>
                                                        <select name="state" id="state" class="input">
                                                            <option value="">Select State</option>
                                                        </select>
                                                        @if ($errors->has('state'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('state') }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- city --}}
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">City</label>
                                                        <input type="text" name="city" id="user_login"
                                                            class="input" value="{{ old('city') }}">
                                                        @if ($errors->has('city'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('city') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- address --}}
                                                <div class="col-lg-12 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Address Line 1</label>
                                                        <input type="text" name="address" id="user_login"
                                                            class="input" value="{{ old('address') }}">
                                                        @if ($errors->has('address'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('address') }}</div>
                                                        @endif

                                                    </div>
                                                </div>
                                                {{-- address2 --}}
                                                <div class="col-lg-6 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Address Line 2</label>
                                                        <input type="text" name="address2" id="user_login"
                                                            class="input" value="{{ old('address2') }}">
                                                        @if ($errors->has('address2'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('address2') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- zip --}}
                                                <div class="col-lg-6 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Zip</label>
                                                        <input type="text" name="zip" id="user_login"
                                                            class="input" value="{{ old('zip') }}">
                                                        @if ($errors->has('zip'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('zip') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Phone Number</label>
                                                        <input type="text" name="phone_number" id="mobile_code"
                                                            placeholder="Enter Phone Number" class="input"
                                                            value="{{ old('full_phone_number') }}">
                                                        @if ($errors->has('phone_number'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('phone_number') }}</div>
                                                        @endif
                                                    </div>
                                                </div>


                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Email ID</label>
                                                        <input type="email" name="email" id="user_login"
                                                            class="input" value="{{ old('email') }}">
                                                        @if ($errors->has('email'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('email') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <div class="login-username">
                                                        <label for="user_login">Confirm Email ID</label>
                                                        <input type="email" name="email_confirmation"
                                                            id="user_login" class="input"
                                                            value="{{ old('email_confirmation') }}">
                                                        @if ($errors->has('email_confirmation'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('email_confirmation') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 mb-3">
                                                    <div class="login-username position-relative">
                                                        <label for="password">Password</label>
                                                        <input type="password" name="password" id="password"
                                                            class="input" value="{{ old('password') }}">
                                                        <span class="eye-btn-1" id="eye-button-1">
                                                            <i class="fa fa-eye-slash" aria-hidden="true"
                                                                id="togglePassword"></i>
                                                        </span>
                                                        @if ($errors->has('password'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('password') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <div class="login-username position-relative">
                                                        <label for="confirm_password">Confirm Password</label>
                                                        <input type="password" name="password_confirmation"
                                                            id="confirm_password" class="input"
                                                            value="{{ old('password_confirmation') }}">
                                                        <span class="eye-btn-1" id="eye-button-2">
                                                            <i class="fa fa-eye-slash" aria-hidden="true"
                                                                id="togglePassword"></i>
                                                        </span>
                                                        @if ($errors->has('password_confirmation'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('password_confirmation') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="login-submit mt-lg-4 mt-2">
                                                        <input type="submit" name="wp-submit" id="login-submit"
                                                            class="button button-primary w-100" value="submit">
                                                        <input type="hidden" name="redirect_to" value="">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="login-submit cancel-sub mt-lg-4 mt-2">
                                                        <button type="button" id="back-login"
                                                            class="button button-primary w-100"
                                                            value="Cancel">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <script src="{{ asset('user_assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('user_assets/js/simplebar.min.js') }}"></script>
        <script src="{{ asset('user_assets/js/bootstrap.bundle.min.js') }}"></script>
        <!--  core files -->
        <script src="{{ asset('user_assets/js/app.min.js') }}"></script>
        <script src="{{ asset('user_assets/js/app.init.js') }}"></script>
        <script src="{{ asset('user_assets/js/app-style-switcher.js') }}"></script>
        <script src="{{ asset('user_assets/js/sidebarmenu.js') }}"></script>
        <script src="{{ asset('user_assets/js/custom.js') }}"></script>
        <!--  current page js files -->
        <script src="{{ asset('user_assets/js/owl.carousel.min.js') }}"></script>
        <!-- <script src="js/apexcharts.min.js"></script> -->
        <!-- <script src="js/dashboard.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
        {{-- trippy cdn link --}}
        <script src="https://unpkg.com/popper.js@1"></script>
        <script src="https://unpkg.com/tippy.js@5"></script>
        <script>
            $(document).ready(function() {
                $('#eye-button-1').click(function() {
                    $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                    $(this).find('i').toggleClass('fa-eye-slash fa-eye');
                });
                $('#eye-button-2').click(function() {
                    $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                        'password');
                    $(this).find('i').toggleClass('fa-eye-slash fa-eye');
                });
            });
        </script>
        <script>
            @if (Session::has('message'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right", // Change position to bottom right
                    "timeOut": "3000", // Duration before it auto-closes
                }
                toastr.success("{{ session('message') }}");
            @endif

            @if (Session::has('error'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right", // Change position to bottom right
                    "timeOut": "3000",
                }
                toastr.error("{{ session('error') }}");
            @endif

            @if (Session::has('info'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right", // Change position to bottom right
                    "timeOut": "3000",
                }
                toastr.info("{{ session('info') }}");
            @endif

            @if (Session::has('warning'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right", // Change position to bottom right
                    "timeOut": "3000",
                }
                toastr.warning("{{ session('warning') }}");
            @endif
        </script>
        <script>
            $('#back-login').click(function() {
                window.location.href = "{{ route('home') }}";
            });
        </script>

        <script>
            $(document).ready(function() {
                // Initially load states for the selected country
                getStates($('#country').val());

                // Fetch states when the country changes
                $('#country').change(function() {
                    var country = $(this).val();
                    getStates(country);
                });

                function getStates(country) {
                    $.ajax({
                        url: "{{ route('get.states') }}", // Ensure this route returns the states for the given country
                        type: "GET",
                        data: {
                            country: country
                        },
                        success: function(response) {
                            var states = response;
                            var selectedState =
                                "{{ old('state') }}"; // Fetch the old input value for state
                            var html = '<option value="">Select State</option>';

                            states.forEach(function(state) {
                                html += '<option value="' + state.id + '"';

                                if (selectedState == state.id) {
                                    html += ' selected';
                                }

                                html += '>' + state.name + '</option>';
                            });

                            $('#state').html(html); // Populate the state dropdown
                        }
                    });
                }
            });
        </script>




        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput-jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js"></script>
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

                let mask;
                if (dialCode && dialCode.length > 2) {
                    // Use a fixed mask pattern for countries with dial codes of length greater than 2
                    mask = '999 999 999';
                    maskNumber = '999 999 999';
                } else {
                    // Dynamically create a mask by replacing digits with 0 for shorter dial codes
                    mask = maskNumber.replace(/[0-9+]/g, '0');
                }

                // Apply the mask with the placeholder
                phoneInput.mask(mask, {
                    placeholder: 'Enter Phone Number',
                });

                phoneInput.on('countrychange', function() {
                    $(this).val('');
                    const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                    const newDialCode = newSelectedCountry.dialCode;
                    const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

                    let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2,
                        intlTelInputUtils.numberFormat.NATIONAL);
                    newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');
                    let newMask;
                    if (newDialCode.length > 2) {
                        // If dial code length is more than 2, use a 999 999 999 mask (or a similar format)
                        newMask = '999 999 999';
                        newMaskNumber = '999 999 999';
                    } else {
                        // Otherwise, replace all digits with 0
                        newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                    }

                    phoneInput.mask(newMask, {
                        placeholder: 'Enter Phone Number',
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
                    const countryData = phoneInput.intlTelInput('getSelectedCountryData');
                    const countryCodeName = countryData.iso2;

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

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'phone_country_code_name',
                        value: countryCodeName
                    }).appendTo('form');
                });
            });
        </script>

    </body>

</html>
