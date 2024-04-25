<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>{{ env('APP_NAME') }} - Login</title>
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
                                <h2 id="greeting">Good Afternoon</h2>
                                <h4>Sign on to enter Lion Roaring PMA Private Member area.</h4>
                                <div class="admin-form">
                                    <form name="login-form" id="login-form" action="{{ route('login.check') }}"
                                        method="post">
                                        @csrf
                                        <p class="login-username">
                                            <label for="user_login">Username or Email Address</label>
                                            <input type="text" name="user_name" id="user_name" class="input"
                                                value="{{ old('user_name') }}">
                                            @if ($errors->has('user_name'))
                                                @foreach ($errors->get('user_name') as $error)
                                                    <p class="error" style="color:red;">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </p>
                                        <p class="login-password">
                                            <label for="user_password">Password</label>
                                            <input type="password" name="password" id="user_password" class="input"
                                                value="">
                                            @if ($errors->has('password'))
                                                @foreach ($errors->get('password') as $error)
                                                    <p class="error" style="color:red;">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </p>
                                        {{-- <div class="check-main">
                                            <div class="form-group">
                                                <input type="checkbox" id="pma_check">
                                                <label for="pma_check">Remember Me</label>
                                            </div>
                                        </div> --}}
                                        <p class="login-submit mt-lg-4 mt-2">
                                            <input type="submit" name="wp-submit" id="login-submit"
                                                class="button button-primary w-100" value="Log In">
                                        </p>
                                    </form>
                                </div>
                                <div class="join-text">
                                    <a href="javascrip:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop">Join Lion
                                        Roaring Member</a> | <a href="{{route('user.forget.password.show')}}">Forgot username or password
                                    </a>
                                </div>
                                <div class="join-text join-text-1">
                                    <a href="{{route('member-privacy-policy')}}">Privacy,
                                        Cookies, and Legal </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="agre">
                            <div class="logo-admin">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="">
                            </div>
                            <div class="heading_hp">
                                <h2 id="greeting">
                                    {{ $agreement['agreement_title'] ?? 'Lion Roaring PMA (Private Members Association) Agreement' }}
                                </h2>
                            </div>
                            <div class="member-text-div admin-srl" id="admin-srl_1">
                                <div class="member-text">
                                    {!! $agreement['agreement_description'] ??
                                        'This is the agreement for Lion Roaring PMA (Private Members Association)' !!}
                                </div>
                                <div class="check-main">
                                    <div class="form-group">
                                        <input type="checkbox" id="pma_check1">
                                        <label for="pma_check1">I have read and agreed to the Lion Roaring PMA
                                            Agreement</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-lg-4">
                                    <div class="login-submit mt-lg-4 mt-2 text-end">
                                        <a href="javascript:void(0);" class="button button-primary w-100 regis">
                                            Next</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
</body>

</html>
