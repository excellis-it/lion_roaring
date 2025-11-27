<!DOCTYPE html>
<html lang="en-US">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Hugo 0.84.0">
        <title>{{ env('APP_NAME') }} - Change Password</title>
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
                        <div class="col-lg-5">
                            <div class="login_bg_sec border-top-0">
                                <div class="heading_hp">
                                    <h2 id="greeting">Change Password</h2>
                                    <h4>Enter your new password</h4>
                                    <div class="admin-form">
                                        <form name="login-form" id="login-form"
                                            action="{{ route('user.password-change') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            {{-- new password --}}
                                            <p class="login-username position-relative">
                                                <label for="user_login">New Password</label>
                                                <input type="password" name="password" id="password" class="input"
                                                    value="{{ old('password') }}">
                                                <span class="eye-btn-1" id="eye-button-1">
                                                    <i class="fa fa-eye-slash" aria-hidden="true"
                                                        id="togglePassword"></i>
                                                </span>
                                                @if ($errors->has('password'))
                                                    @foreach ($errors->get('password') as $error)
                                                        <p class="error" style="color:red;">{{ $error }}</p>
                                                    @endforeach
                                                @endif
                                            </p>

                                            {{-- confirm password --}}
                                            <p class="login-username position-relative">
                                                <label for="user_login">Confirm Password</label>
                                                <input type="password" name="confirm_password" id="confirm_password"
                                                    class="input" value="{{ old('confirm_password') }}">
                                                <span class="eye-btn-1" id="eye-button-2">
                                                    <i class="fa fa-eye-slash" aria-hidden="true"
                                                        id="togglePassword"></i>
                                                </span>
                                                @if ($errors->has('confirm_password'))
                                                    @foreach ($errors->get('confirm_password') as $error)
                                                        <p class="error" style="color:red;">{{ $error }}</p>
                                                    @endforeach
                                                @endif
                                            </p>
                                            <p class="login-submit mt-lg-4 mt-2">
                                                <input type="submit" name="wp-submit" id="login-submit"
                                                    class="button button-primary w-100" value="Send">
                                            </p>
                                        </form>
                                    </div>
                                    <div class="join-text join-text-1">
                                        <a href="{{ route('home') }}"> Back to Home</a>
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
                                    @if (isset(Helper::getFooter()['footer_logo']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_logo']) }}"
                                            alt="">
                                    @else
                                        <img src="{{ asset('user_assets/images/logo.png') }}" alt="">
                                    @endif
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
