<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <!--<link-->
    <!--    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"-->
    <!--    rel="stylesheet">-->

    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('ecom_assets/bootstrap-5.3.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('ecom_assets/css/menu.css') }}" rel="stylesheet" />
    <link href="{{ asset('ecom_assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('ecom_assets/css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .lr-map {
            width: 100%;
            height: 320px;
            border-radius: 10px;
            overflow: hidden;
        }

        #savedAddresses .list-group-item {
            cursor: pointer;
        }

        .lr-address-modal .lr-left {
            background: var(--bs-gray-100);
            border-right: 1px solid var(--bs-border-color);
            min-height: 560px;
        }

        .lr-address-modal .lr-left-header {
            padding: 16px 16px 8px;
        }

        .lr-address-modal .lr-left-body {
            padding: 0 16px 16px;
        }

        .lr-address-modal .lr-right {
            padding: 16px;
        }

        .lr-address-item {
            border-radius: 12px;
            border: 1px solid var(--bs-border-color);
            background: var(--bs-body-bg);
            padding: 12px;
            margin-bottom: 10px;
        }

        .lr-address-item.active {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        }

        .lr-map-wrap {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
        }

        .lr-map-action {
            position: absolute;
            right: 12px;
            bottom: 12px;
            z-index: 500;
        }

        .lr-chip-group .btn {
            border-radius: 999px;
            padding: 6px 14px;
        }

        /* Google Places Autocomplete dropdown inside Bootstrap modal */
        .pac-container {
            z-index: 2000 !important;
        }
    </style>
    <style>
        .skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        .goog-logo-link {
            display: none !important;
        }

        .trans-section {
            margin: 100px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <main>
        @include('ecom.includes.header')
        @yield('content')

        @include('ecom.includes.footer')


    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="{{ asset('ecom_assets/bootstrap-5.3.2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Cart Routes for JavaScript -->
    <script>
        window.cartRoutes = {
            addToCart: "{{ route('e-store.add-to-cart') }}",
            removeFromCart: "{{ route('e-store.remove-from-cart') }}",
            updateCart: "{{ route('e-store.update-cart') }}",
            clearCart: "{{ route('e-store.clear-cart') }}",
            cartCount: "{{ route('e-store.cart-count') }}",
            cartList: "{{ route('e-store.cart-list') }}",
            checkProductInCart: "{{ route('e-store.check-product-in-cart') }}",
            viewCart: "{{ route('e-store.cart') }}",
            addToWishlist: "{{ route('e-store.add-to-wishlist') }}",
            removeFromWishlist: "{{ route('e-store.remove-from-wishlist') }}",
        };
        window.csrfToken = "{{ csrf_token() }}";
    </script>

    <script src="{{ asset('ecom_assets/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
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
            $(document).on('submit', '#submit-newsletter', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === true) {
                            $('.text-danger').html('');
                            $('#newsletter_email').val('');
                            $('#newsletter_name').val('');
                            $('#newsletter_message').val('');
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
                                // after text danger span
                                $('#' + key + '_error').html(value[0]);
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('submit', '#submit-newsletter-home', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === true) {
                            $('.text-danger').html('');
                            $('#newsletter_email_home').val('');

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
                                // after text danger span
                                $('#' + key + '_error').html(value[0]);
                            }
                        });
                    }
                });
            });
        });
    </script>
    @stack('scripts')
    @include('frontend.includes.google_translate')
    @include('frontend.includes.chatbot')
</body>

</html>
