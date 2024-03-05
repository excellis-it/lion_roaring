<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @yield('meta_title')
    <title>@yield('title')</title>
    <meta name="robots" content="noindex, nofollow" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('frontend_assets/images/icons/favicon.ico')}}" />
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/icomoon.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/RemixIcon_Fonts_v4.0.0/fonts/remixicon.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/medhavi-floating-menu-icons/font/flaticon_mycollection.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/magnifypopup.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/owl.carousel.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/odometer.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/sal.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/animation.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/jqueru-ui-min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/vendor/swiper.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Google Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"> <!-- Site Stylesheet -->
    <link rel="stylesheet" href="{{asset('frontend_assets/css/main.css')}}">
    <style>
        .rbt-card-subtext {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .view_all_button {
            padding: 10px 40px;
            border-radius: 5px;
            background-color: #0B7EAE;
            color: white;
            width: max-content;
        }

        .view_all_button_bg a {
            width: max-content;
        }

        .view_all_button_bg {
            display: flex;
            align-items: center;
            justify-content: center;
        }


    </style>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @stack('styles')

</head>

<body class="sticky-header">
    <div id="main-wrapper" class="main-wrapper">
        <!--=====================================-->
        <!--=        Header Area Start       	=-->
        <!--=====================================-->


        @include('frontend.includes.header')

        <!--=====================================-->
        <!--=       Hero Inner Page Banner Area Start =-->
        <!--=====================================-->
        @yield('content')


        <!--=====================================-->
        <!--=        Footer Area Start       	=-->
        <!--=====================================-->
        <!-- Start Footer Area  -->
        @include('frontend.includes.footer')
        <!-- End Footer Area  -->


    </div>

    <div class="rn-progress-parent">
        <svg class="rn-back-circle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    <!-- JS
 ============================================ -->
    <!-- Modernizer JS -->
    <script src="{{asset('frontend_assets/js/vendor/modernizr.min.js')}}"></script>
    <!-- Jquery Js -->
    <script src="{{asset('frontend_assets/js/vendor/jquery.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/bootstrap.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/sal.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/magnifypopup.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/backtotop.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/owl.carousel.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/odometer.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/isotop.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/imageloaded.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/jquery-ui.min.')}}js"></script>
    <script src="{{asset('frontend_assets/js/vendor/swiper.min.js')}}"></script>
    <script src="{{asset('frontend_assets/js/vendor/smooth-scroll.min')}}.js"></script>
    <script src="{{asset('frontend_assets/js/vendor/isInViewport.jquery.min.js')}}"></script>

    <!-- Site Scripts -->
    <script src="{{asset('frontend_assets/js/hamburger.js')}}"></script>
    <script src="{{asset('frontend_assets/js/app.js')}}"></script>
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
            $('.submit-newsletter').click(function() {
                var email = $('#newsletter_email').val();
                if (email === '') {
                    toastr.error('Please enter an email address');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        email: email,
                    },
                    success: function(response) {
                        if (response.status === true) {
                            $('#newsletter_email').val('');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: true,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                                showConfirmButton: true,
                                timer: 3000
                            });
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
