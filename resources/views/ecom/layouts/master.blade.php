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
    @include('frontend.includes.toast-layering')

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

        .lr-address-modal {
            --lr-accent: var(--yellow-color, #ff6632);
            --lr-navy: var(--main-color, #202d4d);
            --lr-surface: #f7f5f2;
            --lr-card: #ffffff;
            --lr-border: rgba(32, 45, 77, 0.12);
            --lr-muted: #6b7280;
            position: relative;
            display: flex;
            flex-direction: column;
            max-height: min(78vh, 680px);
            background: #fff;
        }

        .lr-address-modal .lr-step {
            display: flex;
            flex-direction: column;
            min-height: 0;
            flex: 1 1 auto;
            max-height: min(78vh, 680px);
        }

        .lr-address-modal .lr-step-body {
            padding: 16px 18px 8px;
            overflow-y: auto;
            flex: 1 1 auto;
            min-height: 0;
        }

        .lr-address-modal .lr-step-hint {
            margin-bottom: 12px !important;
        }

        .lr-address-modal .lr-step-footer {
            padding: 12px 18px 16px;
            border-top: 1px solid var(--lr-border);
            background: var(--lr-surface);
            flex-shrink: 0;
        }

        .lr-footer-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .lr-footer-actions .btn {
            width: auto;
            min-width: 0;
            flex: 0 1 auto;
        }

        .lr-footer-actions .red_btn,
        .lr-footer-actions .lr-btn-ghost {
            padding: 10px 20px;
        }

        .lr-footer-actions-single {
            justify-content: center;
        }

        .lr-address-modal .lr-form-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            background: #fff;
        }

        .lr-address-modal .red_btn {
            border: 0;
            padding: 11px 22px;
            letter-spacing: 0.4px;
        }

        .lr-back-btn {
            padding: 6px 12px;
            font-size: 13px;
        }

        .lr-busy-badge {
            position: absolute;
            top: 10px;
            right: 14px;
            z-index: 2;
            font-size: 12px;
            color: var(--lr-muted);
        }

        .lr-address-item {
            border-radius: 14px;
            border: 1.5px solid var(--lr-border);
            background: var(--lr-card);
            padding: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .lr-address-item:hover {
            border-color: rgba(255, 102, 50, 0.45);
            box-shadow: 0 6px 18px rgba(32, 45, 77, 0.06);
        }

        .lr-address-item:focus-visible {
            outline: 2px solid var(--lr-accent);
            outline-offset: 2px;
        }

        .lr-address-item.selected,
        .lr-address-item.active {
            border-color: var(--lr-accent);
            background: rgba(255, 102, 50, 0.06);
            box-shadow: 0 0 0 3px rgba(255, 102, 50, 0.14);
        }

        .lr-address-check {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #c5cad3;
            flex-shrink: 0;
            margin-top: 2px;
            transition: border-color .15s ease, background .15s ease;
        }

        .lr-address-item.selected .lr-address-check,
        .lr-address-item.active .lr-address-check {
            border-color: var(--lr-accent);
            background: var(--lr-accent);
            box-shadow: inset 0 0 0 3px #fff;
        }

        .lr-address-title {
            color: var(--lr-navy);
            font-size: 0.95rem;
        }

        .lr-address-text {
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .lr-badge-default {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #fff;
            background: var(--lr-navy);
            border-radius: 999px;
            padding: 3px 9px;
            line-height: 1.2;
        }

        .lr-address-item .lr-item-actions {
            display: flex;
            gap: 2px;
            flex-shrink: 0;
            align-items: flex-start;
        }

        .lr-btn-text {
            color: var(--lr-navy);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 2px 6px;
            line-height: 1.4;
        }

        .lr-btn-text:hover {
            color: var(--lr-accent);
        }

        .lr-btn-text.text-danger {
            color: #dc3545;
        }

        .lr-btn-text.text-danger:hover {
            color: #b02a37;
        }

        .lr-btn-ghost {
            border: 1.5px solid var(--lr-border);
            background: #fff;
            color: var(--lr-navy);
            border-radius: 30px;
            padding: 10px 18px;
            font-weight: 600;
            font-size: 14px;
        }

        .lr-btn-ghost:hover {
            border-color: var(--lr-accent);
            color: var(--lr-accent);
            background: rgba(255, 102, 50, 0.04);
        }

        .lr-choose-empty {
            border: 1.5px dashed var(--lr-border);
            border-radius: 16px;
            padding: 36px 22px;
            text-align: center;
            background: var(--lr-surface);
        }

        .lr-map-wrap {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--lr-border);
            min-height: 200px;
        }

        .lr-map {
            min-height: 200px;
            height: 220px;
        }

        .lr-map-action {
            position: absolute;
            right: 12px;
            bottom: 12px;
            z-index: 500;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            background: #fff !important;
        }

        .lr-address-modal .form-control:focus {
            border-color: var(--lr-accent);
            box-shadow: 0 0 0 .2rem rgba(255, 102, 50, 0.18);
        }

        .lr-address-modal .form-check-input:checked {
            background-color: var(--lr-accent);
            border-color: var(--lr-accent);
        }

        .location-modal .modal-header {
            border-bottom: 1px solid var(--lr-border, rgba(32, 45, 77, 0.12));
        }

        .location-modal .modal-title {
            color: var(--main-color, #202d4d);
            font-weight: 700;
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
        /* E-Store: best-effort image "protection" (deterrent, not DRM) */
        .ecom-protect-images img {
            -webkit-user-drag: none;
            user-drag: none;
            -webkit-touch-callout: none; /* iOS Safari long-press menu */
            user-select: none;
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

<body class="loading ecom-protect-images has-floating-chat">
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
            <div class="loading-text-bottom">{{ \App\Helpers\Helper::getSettings()->SITE_TAGLINE ?? 'Think Supernaturally, Act Locally' }}</div>
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
    <script>
        // BUG-074: confirm before logging out
        document.addEventListener('click', function(e) {
            var link = e.target.closest('a.js-confirm-logout');
            if (!link) return;
            e.preventDefault();
            var url = link.getAttribute('href');
            Swal.fire({
                title: 'Log out?',
                text: 'Are you sure you want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        (function () {
            // Block right-click / long-press save for images inside E-Store pages
            // (Deterrent only; images can still be retrieved from network.)
            function isEcomImageTarget(target) {
                if (!target) return false;
                // direct image
                if (target.tagName && target.tagName.toLowerCase() === 'img') return true;
                // sometimes right click on wrappers; find closest img
                return !!(target.closest && target.closest('img'));
            }

            document.addEventListener('contextmenu', function (e) {
                if (isEcomImageTarget(e.target)) {
                    e.preventDefault();
                    try {
                        // if (window.toastr) toastr.info('Image saving is disabled on this page.');
                    } catch (_) {}
                }
            }, { capture: true });

            document.addEventListener('dragstart', function (e) {
                if (isEcomImageTarget(e.target)) {
                    e.preventDefault();
                }
            }, { capture: true });
        })();
    </script>
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
     @if (env('CHATBOT') == 'AI')
        @include('frontend.includes.ai_chatbot')
    @else
        @include('frontend.includes.chatbot')
    @endif
</body>

</html>
