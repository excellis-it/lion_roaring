<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>{{ env('APP_NAME') }} - Forgot User Name</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet"
        type="text/css" />
</head>

<body style="background: #643271">
    <main>
        <section class="log-main">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-5">
                        <div class="login_bg_sec border-top-0">
                            <div class="heading_hp">
                                <h2 id="greeting">Forgot Username ?</h2>
                                <h4>Enter your phone number to get your username</h4>
                                <div class="admin-form">
                                    <form name="login-form" id="login-form" action="{{ route('user.forget.username') }}"
                                        method="post">
                                        @csrf
                                        <div class="col-lg-12 mb-3">
                                            <div class="login-username">
                                                <label for="user_login">Phone Number</label>
                                                <input type="text" name="phone_number" id="mobile_code" class="input"
                                                    value="{{ old('full_phone_number') }}">
                                                @if ($errors->has('phone_number'))
                                                    <div class="error" style="color:red;">{{ $errors->first('phone_number') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="login-submit mt-lg-4 mt-2">
                                            <input type="submit" name="wp-submit" id="login-submit"
                                                class="button button-primary w-100" value="Send">
                                        </p>
                                    </form>
                                </div>
                                <div class="join-text join-text-1">
                                    <a href="{{route('home')}}"> Back to Home</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="{{ asset('user_assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('user_assets/js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
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
        $(document).ready(function() {
            $('.regis').on('click', function() {
                if ($('#pma_check1').is(':checked')) {
                    window.location.href = "{{ route('register') }}";
                } else {
                    toastr.error('Please check the agreement');
                }
            });
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

          let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils.numberFormat.NATIONAL);
          maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

          const mask = maskNumber.replace(/[0-9+]/g, '0');
          phoneInput.mask(mask, { placeholder: maskNumber });

          phoneInput.on('countrychange', function() {
              $(this).val('');
              const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
              const newDialCode = newSelectedCountry.dialCode;
              const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

              let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2, intlTelInputUtils.numberFormat.NATIONAL);
              newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');

              const newMask = newMaskNumber.replace(/[0-9+]/g, '0');
              phoneInput.mask(newMask, { placeholder: newMaskNumber });
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
</body>

</html>
