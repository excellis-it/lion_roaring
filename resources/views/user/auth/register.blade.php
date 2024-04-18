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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet"
        type="text/css" />
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
                                <h2 id="greeting">Create Private Member Account</h2>
                                <div class="admin-form">
                                    <form name="login-form" id="login-form" action="{{ route('register.check') }}"
                                        method="post">
                                        @csrf
                                        <div class="login-username mb-3">
                                            <label for="user_login">Username</label>
                                            <input type="text" name="user_name" id="user_login" class="input"
                                                value="{{ old('user_name') }}">
                                            @if ($errors->has('user_name'))
                                                <div class="error" style="color:red;">
                                                    {{ $errors->first('user_name') }}</div>
                                            @endif
                                        </div>
                                        <div class="row">
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
                                        </div>
                                        <div class="login-password mb-3">
                                            <label for="user_password">Home Address</label>
                                            <input type="text" name="address" id="user_password"
                                                spellcheck="false" class="input" value="{{ old('address') }}">
                                            @if ($errors->has('address'))
                                                <div class="error" style="color:red;">
                                                    {{ $errors->first('address') }}</div>
                                            @endif

                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Phone Number (Optional)</label>
                                                    <input type="text" name="phone_number" id="user_login"
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
                                                    <input type="password" name="password_confirmation" id="user_login"
                                                        class="input" value="{{ old('password_confirmation') }}">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="{{ asset('user_assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('user_assets/js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox-plus-jquery.js"
        integrity="sha512-0rYcJjaqTGk43zviBim8AEjb8cjUKxwxCqo28py38JFKKBd35yPfNWmwoBLTYORC9j/COqldDc9/d1B7dhRYmg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
        $('#back-login').click(function() {
            window.location.href = "{{ route('login') }}";
        });
    </script>
</body>

</html>
