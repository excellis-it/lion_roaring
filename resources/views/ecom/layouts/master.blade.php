<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('frontend_assets/uploads/2023/04/cropped-logo-1-32x32.png') }}"
        sizes="32x32" />
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
    <style>
        /* Prevent body scroll while loading */
        body.loading {
            overflow: hidden;
        }

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #643271 0%, #4a2454 50%, #643271 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        #loading.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        #loading-content {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Golden rotating circle */
        #loading-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 6px solid transparent;
            border-top: 6px solid #d98b1c;
            border-right: 6px solid #d98b1c;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            box-shadow: 0 0 20px rgba(217, 139, 28, 0.3);
        }

        /* Inner purple pulsing circle */
        #loading-content::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(217, 139, 28, 0.2) 0%, rgba(100, 50, 113, 0.3) 100%);
            border: 3px solid rgba(217, 139, 28, 0.4);
            border-radius: 50%;
            animation: pulse 1.8s ease-in-out infinite;
        }

        /* Lion icon in center */
        .loader-icon {
            position: relative;
            z-index: 10;
            font-size: 50px;
            color: #d98b1c;
            animation: roar 2s ease-in-out infinite;
            text-shadow: 0 0 20px rgba(217, 139, 28, 0.5);
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(0.85);
                opacity: 0.6;
                border-color: rgba(217, 139, 28, 0.4);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.1);
                opacity: 1;
                border-color: rgba(217, 139, 28, 0.8);
            }
        }

        @keyframes roar {

            0%,
            100% {
                transform: scale(1);
                filter: brightness(1);
            }

            50% {
                transform: scale(1.15);
                filter: brightness(1.3);
            }
        }

        /* Loading text */
        .loading-text {
            position: absolute;
            top: 180px;
            left: 50%;
            transform: translateX(-50%);
            color: #d98b1c;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            white-space: nowrap;
            font-family: 'EB Garamond', serif;
            animation: fadeInOut 2s ease-in-out infinite;
            text-shadow: 0 2px 10px rgba(217, 139, 28, 0.3);
        }

        @keyframes fadeInOut {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }
        }

        .loading-text-bottom {
            position: absolute;
            top: 220px;
            left: 50%;
            transform: translateX(-50%);
            color: #d98b1c;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            white-space: nowrap;
            font-family: 'EB Garamond', serif;
            animation: fadeInOut 2s ease-in-out infinite;
            text-shadow: 0 2px 10px rgba(217, 139, 28, 0.3);
        }

        /* Decorative particles */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #d98b1c;
            border-radius: 50%;
            opacity: 0;
            animation: float 3s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            top: 20%;
            left: 20%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            top: 30%;
            right: 20%;
            animation-delay: 0.5s;
        }

        .particle:nth-child(3) {
            bottom: 30%;
            left: 25%;
            animation-delay: 1s;
        }

        .particle:nth-child(4) {
            bottom: 25%;
            right: 25%;
            animation-delay: 1.5s;
        }

        @keyframes float {

            0%,
            100% {
                opacity: 0;
                transform: translateY(0) scale(0);
            }

            50% {
                opacity: 0.8;
                transform: translateY(-20px) scale(1.5);
            }
        }
    </style>
    @stack('styles')
</head>

<body class="loading">
    <script>
        // Ensure loader is visible immediately
        document.body.classList.add('loading');
    </script>
    <main>
        <div id="loading">
            <div id="loading-content">
                <i class="fas fa-crown loader-icon"></i>
            </div>
            <div class="loading-text">Lion Roaring</div>

            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="loading-text-bottom">Think Supernaturally, Act Locally</div>
        </div>
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
    <script>
        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            const loader = document.getElementById('loading');
            const body = document.body;

            if (loader) {
                // Remove loading class from body
                body.classList.remove('loading');

                // Add fade-out class to loader
                loader.classList.add('fade-out');

                // Remove from DOM after transition completes
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 500);
            }
        });

        // Fallback: Hide loader after 5 seconds if page hasn't fully loaded
        setTimeout(function() {
            const loader = document.getElementById('loading');
            const body = document.body;

            if (loader && !loader.classList.contains('fade-out')) {
                body.classList.remove('loading');
                loader.classList.add('fade-out');
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
    @stack('scripts')
    @include('frontend.includes.google_translate')
    @include('frontend.includes.chatbot')
</body>

</html>
