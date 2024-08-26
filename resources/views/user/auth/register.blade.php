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
                                                        @foreach ($eclessias as $ecclesia)
                                                            <option value="{{ $ecclesia->id }}"
                                                                {{ old('ecclesia_id') == $ecclesia->id ? 'selected' : '' }}>
                                                                {{ $ecclesia->name }} -
                                                                ({{ $ecclesia->country ? $ecclesia->countryName->name : '-' }})
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
                                                        class="input" value="{{ old('phone_number') }}">
                                                    @if ($errors->has('phone_number'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('phone_number') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Email ID</label>
                                                    <input type="text" name="email" id="user_login"
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
                                                    <input type="text" name="email_confirmation" id="user_login"
                                                        class="input" value="{{ old('email_confirmation') }}">
                                                    @if ($errors->has('email_confirmation'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('email_confirmation') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Password</label>
                                                    <input type="password" name="password" id="user_login"
                                                        class="input" value="{{ old('password') }}">
                                                    @if ($errors->has('password'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('password') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Confirm Password</label>
                                                    <input type="password" name="password_confirmation"
                                                        id="user_login" class="input"
                                                        value="{{ old('password_confirmation') }}">
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
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
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
        function validate() {
            var number = $("#mobile_code").intlTelInput('getNumber');
            var iso = $("#mobile_code").intlTelInput('getSelectedCountryData').iso2;

            var isValidNumber = intlTelInputUtils.isValidNumber(number, iso);
            var validationError = intlTelInputUtils.getValidationError(number, iso);

            console.log(number);
            console.log(intlTelInputUtils.formatNumber(number, iso, intlTelInputUtils.numberFormat.INTERNATIONAL));
            console.log(isValidNumber);
            console.log(validationError);

            // You can add custom validation logic here if needed
        }

        $("#mobile_code").intlTelInput({
            geoIpLookup: function(callback) {
                $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            },
            initialCountry: "auto",
            separateDialCode: true,
        });

        $('#mobile_code').on('countrychange', function(e) {
            $(this).val('');

            var selectedCountry = $(this).intlTelInput('getSelectedCountryData');
            var dialCode = selectedCountry.dialCode;
            var exampleNumber = intlTelInputUtils.getExampleNumber(selectedCountry.iso2, 0, 0);

            var maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils.numberFormat.NATIONAL);
            maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

            var mask = maskNumber.replace(/[0-9+]/g, '0');
            $('#mobile_code').mask(mask, { placeholder: maskNumber });
        });

        // Example to submit the form
        $('form').on('submit', function(e) {
            // Get the full number including the country code
            var fullNumber = $("#mobile_code").intlTelInput('getNumber');
            var countryCode = $("#mobile_code").intlTelInput('getSelectedCountryData').dialCode;

            // Store the full number in a hidden input field
            $('<input>').attr({
                type: 'hidden',
                name: 'full_phone_number',
                value: fullNumber
            }).appendTo('form');

            // Store the country code in a hidden input field
            $('<input>').attr({
                type: 'hidden',
                name: 'country_code',
                value: countryCode
            }).appendTo('form');
        });
    </script>
</body>

</html>
