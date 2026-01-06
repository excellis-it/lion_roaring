<!DOCTYPE html>
<html lang="en-US">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="generator" content="Hugo 0.84.0">
        {{-- favicon --}}
        <link rel="icon" href="{{ asset('frontend_assets/uploads/2023/04/cropped-logo-1-32x32.png') }}"
            sizes="32x32" />
        @yield('meta_title')
        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&amp;display=swap"
            rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
            rel="stylesheet">
        <!-- Bootstrap core CSS -->
        <link href="{{ asset('frontend_assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css"
            integrity="sha512-Woz+DqWYJ51bpVk5Fv0yES/edIMXjj3Ynda+KWTIkGoynAMHrqTcDUQltbipuiaD5ymEo9520lyoVOo9jCQOCA=="
            crossorigin="anonymous" referrerpolicy="no-referrer">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link href="{{ asset('frontend_assets/css/menu.css') }}" rel="stylesheet">
        <link href="{{ asset('frontend_assets/css/style.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('user_assets/css/style.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('frontend_assets/css/responsive.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet"
            type="text/css" />
        <style>
            .goog-te-banner-frame.skiptranslate {
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
            .toast-center {
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                position: fixed;
            }
        </style>

        <!-- Page Loader Styles -->
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

            /* Improved PDF Viewer & Agreement Styles */
            .pdf-viewer-wrapper {
                background: #f8f9fa;
                border-radius: 12px;
                padding: 15px;
                box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
                border: 1px solid #e9ecef;
            }

            #pdf-viewer-container {
                max-height: 55vh;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 15px;
                padding: 10px;
                scrollbar-width: thin;
                scrollbar-color: var(--main-color) #f1f1f1;
            }

            #pdf-viewer-container::-webkit-scrollbar {
                width: 6px;
            }

            #pdf-viewer-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            #pdf-viewer-container::-webkit-scrollbar-thumb {
                background: var(--main-color);
                border-radius: 10px;
            }

            .pdf-page-canvas {
                max-width: 100%;
                height: auto !important;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                border-radius: 4px;
                border: 1px solid #dee2e6;
            }

            .agreement-glass-box {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(100, 50, 113, 0.2);
                border-radius: 15px;
                padding: 20px;
                margin-top: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .scroll-hint {
                font-size: 14px;
                color: #6c757d;
                text-align: center;
                margin-bottom: 10px;
                font-style: italic;
            }

            .scroll-hint i {
                animation: bounce 2s infinite;
            }

            @keyframes bounce {

                0%,
                20%,
                50%,
                80%,
                100% {
                    transform: translateY(0);
                }

                40% {
                    transform: translateY(-5px);
                }

                60% {
                    transform: translateY(-3px);
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
            <section id="loading">
                <div id="loading-content">
                    <i class="fas fa-crown loader-icon"></i>
                </div>
                <div class="loading-text">Lion Roaring</div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </section>
            @php
                use App\Helpers\Helper;
            @endphp


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
            {{-- payment --}}
            <div class="modal fade" id="exampleModalToggle2" aria-hidden="true"
                aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center" id="exampleModalToggleLabel2">Lion Roaring Donate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-8">
                                    <div class="payment-option-form">
                                        <form role="form" action="{{ route('donation') }}" method="post"
                                            class="require-validation" data-cc-on-file="false"
                                            data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" id="payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="amount">Enter amount(US$)</label>
                                                    <input class="form-control" id="amount" name="amount"
                                                        inputmode="decimal" value="">
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="billing_name">First Name</label>
                                                    <input class="form-control has-icon" type="text"
                                                        id="billing-fname" name="first_name" value="">
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="billing_name">Last Name</label>
                                                    <input class="form-control has-icon" type="text"
                                                        id="billing-lname" name="last_name" value="">
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="email">Email</label>
                                                    <input class="form-control has-icon" type="text"
                                                        id="email" name="email" value="">
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <div class="pure-u-1">
                                                        <legend>Billing info</legend>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 mb-3">
                                                    <label for="address">Address</label>
                                                    <input class="form-control has-icon" type="text"
                                                        id="address" name="address">
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="city">City</label>
                                                    <input class="form-control" type="text" id="city"
                                                        name="city">
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="country">Country</label>
                                                    <select class="form-control" name="country_id" id="country">
                                                        <option value="">—</option>
                                                        @foreach (Helper::getCountries() as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="state">State</label>
                                                    {{-- <input class="form-control" type="text" id="state" name="state"> --}}
                                                    <select class="form-control" name="state" id="state">
                                                        <option value="">—</option>
                                                    </select>

                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="postcode">Postcode</label>
                                                    <input class="form-control" type="text" name="postcode"
                                                        id="postcode">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <div class="pure-u-1">
                                                        <legend>Credit or debit card</legend>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label for="card-element">Card Number</label>
                                                    <div style="position: relative;">
                                                        <input class="form-control card-number" aria-hidden="true"
                                                            aria-label=" " name="card_number" id="card-number"
                                                            autocomplete="off">
                                                        <img id="card-type-image"
                                                            src="{{ asset('frontend_assets/images/unknown.webp') }}"
                                                            alt="Card Type"
                                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); max-height: 24px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="card-element">Month</label>
                                                    <select
                                                        class="form-control form-control--sm card-expiry-month valid card-expiry-month"
                                                        name="card_expiry_month" id="card-expiry-month"
                                                        aria-invalid="false">
                                                        <option selected="" value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="card-element">Year</label>
                                                    <input class="form-control" aria-hidden="true" aria-label=" "
                                                        id="card-expiry-year" name="card_expiry_year"
                                                        autocomplete="false" maxlength="5">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="card-element">CVV</label>
                                                    <input class="form-control" aria-hidden="true" aria-label=" "
                                                        name="card_cvc" id="card-cvc" autocomplete="false"
                                                        maxlength="4">
                                                </div>

                                            </div>

                                            <div class="mt-4">
                                                <div class="pure-u-5-5 centered d-flex">
                                                    <button type="submit" id="submit-btn"
                                                        class="pure-button pure-button-primary">Donate US
                                                        $0.00</button>
                                                    <p style="margin-left: 5px;"><b>We Accept </b><img
                                                            src="{{ asset('frontend_assets/images/cards.png') }}"
                                                            alt="" height="35px"></p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-4">
                                    <div class="bank-details-box">
                                        <h5>Or,</h5>
                                        <h5>Bank Transfer Details</h5>
                                        <p>
                                            {!! Helper::getSettings()->DONATE_BANK_TRANSFER_DETAILS ?? '' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Login Modal -->
            <div class="modal fade modal_code" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body login_bg_sec login_bg_sec_modal  border-top-0">
                            <div class="heading_hp heading_hp_modal">
                                <h2 class="text-center" id="greeting">
                                    <?php
                                    function getTimezoneFromIp($ip)
                                    {
                                        $url = "http://ip-api.com/json/{$ip}?fields=timezone";
                                        $response = @file_get_contents($url); // Suppress warnings and handle errors manually
                                        if ($response) {
                                            $data = json_decode($response);
                                            if ($data && isset($data->timezone)) {
                                                return $data->timezone;
                                            }
                                        }
                                        return null;
                                    }

                                    // Get user's timezone based on IP address
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                    $timezone = getTimezoneFromIp($ip);

                                    if ($timezone) {
                                        // Set the default timezone
                                        date_default_timezone_set($timezone);
                                    } else {
                                        // Fallback timezone
                                        date_default_timezone_set('UTC');
                                    }

                                    // Get the current hour in 24-hour format
                                    $time = date('H');

                                    // Determine greeting based on time
                                    if ($time < '12') {
                                        echo 'Perfect morning';
                                    } elseif ($time >= '12' && $time < '17') {
                                        echo 'Perfect afternoon';
                                    } elseif ($time >= '17' && $time < '19') {
                                        echo 'Perfect evening';
                                    } else {
                                        echo 'Perfect evening';
                                    }
                                    ?>
                                </h2>
                                <h4 class="text-center">Sign on to enter Lion Roaring PMA Private Member area.</h4>

                                <form name="login-form" id="sign-in-form" action="{{ route('login.check') }}"
                                    method="post">
                                    @csrf
                                    <p class="login-username">
                                        <label for="user_login">Username or Email Address</label>
                                        <input type="text" name="user_name" id="user_login"
                                            autocomplete="username"
                                            @if (isset($_COOKIE['email_user_name'])) value="{{ $_COOKIE['email_user_name'] }}" @endif
                                            class="input" value="" size="20">
                                        <span class="text-danger"></span>
                                    </p>
                                    <p class="login-password"
                                        style="position: relative;
                            ">

                                        <label for="user_password">Password</label>
                                        <input type="password" name="password" id="user_password"
                                            autocomplete="current-password" spellcheck="false" class="input"
                                            @if (isset($_COOKIE['password'])) value="{{ $_COOKIE['password'] }}" @endif
                                            size="20">
                                        <span class="eye-btn-1" id="eye-button-1"
                                            style="position: absolute; right: 10px; top: 41px;">
                                            <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                        </span>
                                        <span class="text-danger"></span>
                                    </p>
                                    <p class="login-username justify-content-end d-flex">
                                        <input class="form-check-input me-2" type="checkbox" name="remember"
                                            id="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember Me
                                        </label>
                                    </p>
                                    <p class="login-submit">
                                        <input type="submit" name="wp-submit" id="login-submit"
                                            class="button button-primary" value="Log In">
                                        <input type="hidden" name="redirect_to" value="">
                                    </p>

                                </form>
                                <p class="text-center join_member join_member_modal">
                                    <a href="javascrip:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#registerModalFirst">Join
                                        Lion
                                        Roaring PMA</a> |
                                    <a href="{{ route('user.forget.password.show') }}">Forgot
                                        password
                                    </a> | <a href="{{ route('user.forget.username.show') }}">Forgot
                                        username
                                    </a>
                                    <br>
                                    <a href="{{ route('member-privacy-policy') }}"
                                        class="login_privacy text-dark">Privacy,
                                        Cookies, and Legal </a>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Login Modal End -->

            <!-- Email OTP Modal -->
            <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel"
                aria-hidden="true" style="top: 47px;" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal_code">
                        <div class="modal-header">
                            <h5 class="modal-title" id="otpModalLabel">Email Verification</h5>
                        </div>
                        <div class="modal-body modal-body-inter-code">
                            <form id="otp-form" action="{{ route('verify.otp') }}" method="post">
                                @csrf
                                <input type="hidden" name="time_zone" id="time_zone">
                                <div class="mb-3">
                                    <input placeholder="Enter Code" type="text" class="form-control input"
                                        id="otp" name="otp" maxlength="4" required
                                        style="border: none;
                                        border-bottom: 1px solid rgb(0 0 0 / 29%);
                                        border-radius: 0px;">
                                    <span class="text-danger" id="otp-error"></span>
                                </div>
                                <br>
                                <button style="background-color: #643271; border-color: #643271;" type="submit"
                                    class="btn btn-primary w-100 button button-primary">Verify</button>
                                <br>
                                <div class="text-center mt-3">
                                    <p>Didn't receive the code?</p>
                                    <button type="button" id="resend-otp-btn" class="btn btn-link">
                                        Resend Code
                                    </button>
                                    <div id="countdown-timer" class="mt-2" style="display: none;">
                                        Resend available in <span id="countdown">00:05</span>
                                    </div>
                                </div>
                                <br>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Email OTP Modal End -->

            <div class="modal fade donate_bg_sec" id="exampleModal1" tabindex="-1"
                aria-labelledby="exampleModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="heading_hp">
                                <div class="asp_product_item">
                                    <div class="asp_product_item_top">
                                        <div class="asp_product_item_thumbnail">

                                        </div>
                                        <div class="asp_product_name">

                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div class="asp_product_description text-center">

                                        @php
                                            $donate_text = Helper::getSettings()->DONATE_TEXT ?? '';
                                        @endphp

                                        {!! $donate_text !!}
                                    </div>
                                    <br>
                                    <div class="asp_price_container">
                                        <span class="asp_price_amount"></span> <span
                                            class="asp_new_price_amount"></span>
                                        <span class="asp_quantity"></span>
                                        <div class="asp_under_price_line"></div>
                                    </div>
                                    <div class="asp_product_buy_button text-center">
                                        <div class="asp-processing-cont" style="display:none;"><span
                                                class="asp-processing">Processing <i>.</i><i>.</i><i>.</i></span></div>
                                        <form id="asp_ng_form_065e81241e506b" class="asp-stripe-form" action=""
                                            method="POST"> <input type="hidden" name="asp_product_id"
                                                value="861">
                                            <div class="asp-child-hidden-fields" style="display: none !important;">
                                            </div>
                                        </form>
                                        <div id="asp-all-buttons-container-065e81241e506b"
                                            class="asp_all_buttons_container">
                                            <div class="asp_product_buy_btn_container"><button
                                                    id="asp_ng_button_065e81241e506b" type="submit"
                                                    class="asp_product_buy_btn blue"><span>NEXT</span></button></div>
                                            <noscript>Stripe Payments requires Javascript to be supported by the browser
                                                in
                                                order to operate.</noscript>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="onload_popup" class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
                style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header text-center justify-content-center">
                            <!--         <button type="button" class="close" data-bs-dismiss="modal">&times;</button> -->
                            <h4 class="modal-title">
                                {{ Helper::getPmaTerm() ? Helper::getPmaTerm()->title : 'Lion Roaring PMA (Private Members Association) Agreement' }}
                            </h4>
                        </div>
                        <form action="{{ route('session.store') }}" method="POST">
                            @csrf
                            <div class="modal-body ">
                                {!! Helper::getPmaTerm()
                                    ? Helper::getPmaTerm()->description
                                    : 'It is the responsibility of the members to read and review the Articles of Association of Lion Roaring PMA in its entirety and agree to adopt and comply to its belief, foundation and purpose of the Lion Roaring PMA.' !!}
                                <div class="modal_checkbox text-left text-xl-center">
                                    <div class="form-group">
                                        <input type="checkbox" id="pma_check" name="is_checked">
                                        <label
                                            for="pma_check">{{ Helper::getPmaTerm() ? Helper::getPmaTerm()->checkbox_text : '' }}</label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="continue-btn changed " data-bs-dismiss="modal"
                                        id="myButton">Continue</button>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal fade" id="registerModalFirst" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                        <div class="modal-header bg-white border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-0">
                            <div class="agre p-3">
                                <div class="logo-admin mb-4">
                                    @if (isset(Helper::getFooter()['footer_logo']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_logo']) }}"
                                            alt="">
                                    @else
                                        <img src="{{ asset('user_assets/images/logo.png') }}" alt="">
                                    @endif
                                </div>

                                <div class="pdf-viewer-wrapper">
                                    <div class="scroll-hint mb-3">
                                        <i class="fas fa-mouse-pointer me-2"></i> Please read to the bottom to enable
                                        agreement
                                    </div>
                                    <div id="pdf-viewer-container">
                                        <div class="text-center p-5">
                                            <div class="spinner-border text-primary" role="status"></div>
                                            <p class="mt-3 text-muted fw-500">Preparing document...</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="check-main-first" style="display: none;">
                                    <div class="agreement-glass-box mt-4">
                                        <div class="check-main">
                                            <div class="form-group mb-0">
                                                <input type="checkbox" id="pma_register_check1">
                                                <label for="pma_register_check1" class="fw-bold text-dark h5 mb-0">
                                                    I have read and agree to the Articles of Association
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row justify-content-end mt-4">
                                            <div class="col-lg-4">
                                                <div class="login-submit text-end">
                                                    <a href="javascript:void(0);"
                                                        class="button button-primary w-100 register_next_first py-3 shadow">
                                                        Next <i class="fas fa-arrow-right ms-2"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="registerModalSecond" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                        {{ Helper::getAgreements()['agreement_title'] ?? 'Lion Roaring PMA (Private Members Association) Agreement' }}
                                    </h2>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="pma_register_signer_name" class="form-label fw-bold">Full
                                            name</label>
                                        <input type="text" class="form-control" id="pma_register_signer_name"
                                            placeholder="Enter your full name" autocomplete="name">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <input type="checkbox" id="pma_register_initial_check">
                                            <label for="pma_register_initial_check" id="pma_register_initial_label">
                                                I confirm my initials
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="member-text-div admin-srl" id="admin-srl_1">
                                    <div class="member-text">
                                        {!! Helper::getAgreements()['agreement_description'] ??
                                            'This is the agreement for Lion Roaring PMA (Private Members Association)' !!}
                                    </div>
                                </div>



                                <div class="row justify-content-end">
                                    <div class="col-lg-4">
                                        <div class="login-submit mt-lg-4 mt-2 text-end">
                                            <a href="javascript:void(0);"
                                                class="button button-primary w-100 register_next_second">
                                                Next</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="registerAgreementPreviewModal" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                        <div class="modal-header bg-white border-0">
                            <h5 class="modal-title fw-bold">Review & Agree</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="pdf-viewer-wrapper">
                                <div id="register_agreement_pdf_container" style="height: 70vh;">
                                    <iframe id="register_agreement_pdf_iframe" src="" width="100%"
                                        height="100%" style="border:0; border-radius: 12px;"></iframe>
                                </div>
                            </div>

                            <div class="agreement-glass-box mt-4">
                                <div class="check-main">
                                    <div class="form-group mb-0">
                                        <input type="checkbox" id="pma_register_check3">
                                        <label for="pma_register_check3" class="fw-bold text-dark mb-0">
                                            {{ Helper::getAgreements()['checkbox_text'] ?? 'I have read and agreed to the Lion Roaring PMA Agreement' }}
                                        </label>
                                    </div>
                                </div>
                                <div class="row justify-content-end mt-4">
                                    <div class="col-lg-4">
                                        <div class="login-submit text-end">
                                            <a href="javascript:void(0);"
                                                class="button button-primary w-100 register_next_preview py-3 shadow">
                                                Next <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <script>
            var register_page_route = "{{ route('register') }}";
            var register_agreement_preview_route = "{{ route('register.agreement.preview') }}";
            var csrf_token = "{{ csrf_token() }}";
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
        <script src="{{ asset('frontend_assets/js/bootstrap.bundle.min.js') }}"></script>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script src="{{ asset('frontend_assets/js/custom.js') . '?v=' . time() }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox-plus-jquery.js"
            integrity="sha512-0rYcJjaqTGk43zviBim8AEjb8cjUKxwxCqo28py38JFKKBd35yPfNWmwoBLTYORC9j/COqldDc9/d1B7dhRYmg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
        <script src="{{ asset('frontend_assets/js/cs-select.js') }}"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                document.getElementById("time_zone").value = timezone;
            });
        </script>

        {{-- <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
        </script>

        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en'
                }, 'google_translate_element');
            }
        </script> --}}
        <!-- make sure this line sets the session languages (you already have this) -->
        <script>
            // example: [{"id":202,"code":"es","name":"Spanish",...}, {"id":249,"code":"en","name":"English",...}]
            window.sessionLanguages = @json(session('visitor_country_languages') ?? []);
        </script>

        <!-- Google Translate initialization + robust allowed-language logic -->
        <script type="text/javascript">
            /**
             * parseLanguages(data)
             * - Accepts either:
             *   1) flat array of language objects: [{code:'es', name:'Spanish'}, ...]
             *   2) nested weird session shape used earlier
             * - Returns a Set of language codes (strings), ensures 'en' is present.
             */
            function parseLanguages(data) {
                const codes = new Set();

                if (!data) {
                    codes.add('en');
                    return codes;
                }

                // If it's a flat array of objects like [{code:'es'}, {code:'en'}]
                if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && data[0] !== null && 'code' in data[
                        0]) {
                    data.forEach(lang => {
                        if (lang && lang.code) codes.add(String(lang.code));
                    });
                    codes.add('en');
                    return codes;
                }

                // Fallback: attempt to safely traverse nested structure (previous attempts)
                try {
                    const arr = Array.isArray(data) ? data : [data];
                    arr.forEach(item => {
                        const innerArray = Array.isArray(item) ? item : [item];
                        innerArray.forEach(inner => {
                            if (!inner || typeof inner !== 'object') return;
                            Object.values(inner).forEach(val => {
                                const list = Array.isArray(val) ? val : [val];
                                list.forEach(lang => {
                                    if (lang && typeof lang === 'object' && lang.code) {
                                        codes.add(String(lang.code));
                                    }
                                });
                            });
                        });
                    });
                } catch (e) {
                    console.error('parseLanguages fallback error', e);
                }

                codes.add('en'); // always include English
                return codes;
            }

            /**
             * buildIncludedLanguagesString(sessionData)
             * - Returns the comma-separated string expected by Google Translate's includedLanguages option.
             */
            function buildIncludedLanguagesString(sessionData) {
                const codes = parseLanguages(sessionData);
                // convert to comma-separated string (Google expects e.g., 'en,es,fr')
                return Array.from(codes).join(',');
            }

            /**
             * waitForTranslateSelect(callback)
             * - Uses MutationObserver to wait for the Google Translate select (.goog-te-combo)
             * - Calls callback(selectElement) when it appears.
             */
            function waitForTranslateSelect(callback, timeout = 4000) {
                // If already exists, call immediately
                const existing = document.querySelector('.goog-te-combo');
                if (existing) {
                    callback(existing);
                    return;
                }

                const observer = new MutationObserver((mutations, obs) => {
                    const el = document.querySelector('.goog-te-combo');
                    if (el) {
                        obs.disconnect();
                        callback(el);
                    }
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });

                // safety timeout to disconnect and call callback with null if not found
                setTimeout(() => {
                    try {
                        observer.disconnect();
                    } catch (e) {}
                    const el = document.querySelector('.goog-te-combo');
                    callback(el);
                }, timeout);
            }

            /**
             * forceSelectValue(selectEl, value)
             * - Safely forces the .goog-te-combo select to a value (language code) if option exists.
             */
            function forceSelectValue(selectEl, value) {
                if (!selectEl) return;
                // find option with value matching code (some Google builds use full name like 'es' or 'es|es')
                let found = Array.from(selectEl.options).find(opt => opt.value === value || opt.value.startsWith(value + '|') ||
                    opt.text.toLowerCase().includes(value.toLowerCase()));
                if (found) {
                    selectEl.value = found.value;
                    // trigger change event so Google Translate applies the language
                    const evt = document.createEvent('HTMLEvents');
                    evt.initEvent('change', true, true);
                    selectEl.dispatchEvent(evt);
                } else {
                    // fallback: pick the first option (which usually is 'English' due to pageLanguage)
                    // but only if it's not already English
                    // do nothing otherwise
                }
            }

            /**
             * googleTranslateElementInit
             * - Called by Google's script callback
             * - Uses includedLanguages built from session languages
             */
            function googleTranslateElementInit() {
                const includedLanguages = buildIncludedLanguagesString(window.sessionLanguages || []);
                new google.translate.TranslateElement({
                    // pageLanguage: 'en',
                    includedLanguages: includedLanguages,
                    //   layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                }, 'google_translate_element');

                // Wait for the dropdown, then force English (en) as selected
                waitForTranslateSelect(function(selectEl) {
                    if (!selectEl) {
                        console.warn('Google translate select not found.');
                        return;
                    }
                    // Force English code 'en' if available, otherwise try to choose a matching option
                    forceSelectValue(selectEl, 'en');
                }, 5000);
            }
        </script>

        <!-- Google Translate Library (keeps the callback name googleTranslateElementInit) -->
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
        </script>





        {{-- <style>
            /* HIDE GOOGLE TRANSLATE TOOLBAR */


            iframe.skiptranslate {
                display: none !important;
            }

            .goog-logo-link,
            .goog-te-gadget-icon {
                display: none !important;
            }

            .goog-te-gadget span {
                display: none !important;
            }

            body {
                top: 0 !important;
            }
        </style>

        <script type="text/javascript">
            // Step 1: Initialize Google Translate widget
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    autoDisplay: false
                }, 'google_translate_element');
            }

            // Step 2: Load Google Translate script
            (function() {
                var gt = document.createElement('script');
                gt.type = 'text/javascript';
                gt.async = true;
                gt.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(gt, s);
            })();

            // Step 3: Map countries to preferred languages
            const countryToLang = {
                "AF": "fa", // Afghanistan -> Persian
                "AL": "sq", // Albania -> Albanian
                "DZ": "ar", // Algeria -> Arabic
                "AS": "en", // American Samoa -> English
                "AD": "ca", // Andorra -> Catalan
                "AO": "pt", // Angola -> Portuguese
                "AI": "en", // Anguilla -> English
                "AR": "es", // Argentina -> Spanish
                "AM": "hy", // Armenia -> Armenian
                "AU": "en", // Australia -> English
                "AT": "de", // Austria -> German
                "AZ": "az", // Azerbaijan -> Azerbaijani
                "BS": "en", // Bahamas -> English
                "BH": "ar", // Bahrain -> Arabic
                "BD": "bn", // Bangladesh -> Bengali
                "BB": "en", // Barbados -> English
                "BY": "be", // Belarus -> Belarusian
                "BE": "nl", // Belgium -> Dutch (could also be French or German)
                "BZ": "en", // Belize -> English
                "BJ": "fr", // Benin -> French
                "BM": "en", // Bermuda -> English
                "BT": "dz", // Bhutan -> Dzongkha
                "BO": "es", // Bolivia -> Spanish
                "BA": "bs", // Bosnia & Herzegovina -> Bosnian
                "BW": "en", // Botswana -> English
                "BR": "pt", // Brazil -> Portuguese
                "BN": "ms", // Brunei -> Malay
                "BG": "bg", // Bulgaria -> Bulgarian
                "BF": "fr", // Burkina Faso -> French
                "BI": "fr", // Burundi -> French
                "KH": "km", // Cambodia -> Khmer
                "CM": "fr", // Cameroon -> French
                "CA": "en", // Canada -> English (could also be French)
                "CV": "pt", // Cape Verde -> Portuguese
                "KY": "en", // Cayman Islands -> English
                "CF": "fr", // Central African Republic -> French
                "TD": "fr", // Chad -> French
                "CL": "es", // Chile -> Spanish
                "CN": "zh-CN", // China -> Chinese (Simplified)
                "CO": "es", // Colombia -> Spanish
                "KM": "ar", // Comoros -> Arabic
                "CD": "fr", // Congo (DRC) -> French
                "CG": "fr", // Congo (Republic) -> French
                "CR": "es", // Costa Rica -> Spanish
                "CI": "fr", // Côte d'Ivoire -> French
                "HR": "hr", // Croatia -> Croatian
                "CU": "es", // Cuba -> Spanish
                "CY": "el", // Cyprus -> Greek
                "CZ": "cs", // Czech Republic -> Czech
                "DK": "da", // Denmark -> Danish
                "DJ": "fr", // Djibouti -> French
                "DM": "en", // Dominica -> English
                "DO": "es", // Dominican Republic -> Spanish
                "EC": "es", // Ecuador -> Spanish
                "EG": "ar", // Egypt -> Arabic
                "SV": "es", // El Salvador -> Spanish
                "GQ": "es", // Equatorial Guinea -> Spanish
                "ER": "ti", // Eritrea -> Tigrinya
                "EE": "et", // Estonia -> Estonian
                "ET": "am", // Ethiopia -> Amharic
                "FJ": "en", // Fiji -> English
                "FI": "fi", // Finland -> Finnish
                "FR": "fr", // France -> French
                "GA": "fr", // Gabon -> French
                "GM": "en", // Gambia -> English
                "GE": "ka", // Georgia -> Georgian
                "DE": "de", // Germany -> German
                "GH": "en", // Ghana -> English
                "GR": "el", // Greece -> Greek
                "GD": "en", // Grenada -> English
                "GT": "es", // Guatemala -> Spanish
                "GN": "fr", // Guinea -> French
                "GW": "pt", // Guinea-Bissau -> Portuguese
                "GY": "en", // Guyana -> English
                "HT": "fr", // Haiti -> French
                "HN": "es", // Honduras -> Spanish
                "HK": "zh-CN", // Hong Kong -> Chinese
                "HU": "hu", // Hungary -> Hungarian
                "IS": "is", // Iceland -> Icelandic
                "IN": "hi", // India -> Hindi
                "ID": "id", // Indonesia -> Indonesian
                "IR": "fa", // Iran -> Persian
                "IQ": "ar", // Iraq -> Arabic
                "IE": "en", // Ireland -> English
                "IL": "he", // Israel -> Hebrew
                "IT": "it", // Italy -> Italian
                "JM": "en", // Jamaica -> English
                "JP": "ja", // Japan -> Japanese
                "JO": "ar", // Jordan -> Arabic
                "KZ": "kk", // Kazakhstan -> Kazakh
                "KE": "sw", // Kenya -> Swahili
                "KI": "en", // Kiribati -> English
                "KR": "ko", // South Korea -> Korean
                "KW": "ar", // Kuwait -> Arabic
                "KG": "ky", // Kyrgyzstan -> Kyrgyz
                "LA": "lo", // Laos -> Lao
                "LV": "lv", // Latvia -> Latvian
                "LB": "ar", // Lebanon -> Arabic
                "LS": "st", // Lesotho -> Sesotho
                "LR": "en", // Liberia -> English
                "LY": "ar", // Libya -> Arabic
                "LI": "de", // Liechtenstein -> German
                "LT": "lt", // Lithuania -> Lithuanian
                "LU": "fr", // Luxembourg -> French
                "MO": "zh-CN", // Macau -> Chinese
                "MK": "mk", // North Macedonia -> Macedonian
                "MG": "fr", // Madagascar -> French
                "MW": "en", // Malawi -> English
                "MY": "ms", // Malaysia -> Malay
                "MV": "dv", // Maldives -> Divehi
                "ML": "fr", // Mali -> French
                "MT": "mt", // Malta -> Maltese
                "MH": "en", // Marshall Islands -> English
                "MR": "ar", // Mauritania -> Arabic
                "MU": "en", // Mauritius -> English
                "MX": "es", // Mexico -> Spanish
                "FM": "en", // Micronesia -> English
                "MD": "ro", // Moldova -> Romanian
                "MC": "fr", // Monaco -> French
                "MN": "mn", // Mongolia -> Mongolian
                "ME": "sr", // Montenegro -> Serbian
                "MA": "ar", // Morocco -> Arabic
                "MZ": "pt", // Mozambique -> Portuguese
                "MM": "my", // Myanmar -> Burmese
                "NA": "en", // Namibia -> English
                "NR": "en", // Nauru -> English
                "NP": "ne", // Nepal -> Nepali
                "NL": "nl", // Netherlands -> Dutch
                "NZ": "en", // New Zealand -> English
                "NI": "es", // Nicaragua -> Spanish
                "NE": "fr", // Niger -> French
                "NG": "en", // Nigeria -> English
                "NO": "no", // Norway -> Norwegian
                "OM": "ar", // Oman -> Arabic
                "PK": "ur", // Pakistan -> Urdu
                "PW": "en", // Palau -> English
                "PA": "es", // Panama -> Spanish
                "PG": "en", // Papua New Guinea -> English
                "PY": "es", // Paraguay -> Spanish
                "PE": "es", // Peru -> Spanish
                "PH": "tl", // Philippines -> Filipino
                "PL": "pl", // Poland -> Polish
                "PT": "pt", // Portugal -> Portuguese
                "QA": "ar", // Qatar -> Arabic
                "RO": "ro", // Romania -> Romanian
                "RU": "ru", // Russia -> Russian
                "RW": "rw", // Rwanda -> Kinyarwanda
                "KN": "en", // Saint Kitts and Nevis -> English
                "LC": "en", // Saint Lucia -> English
                "VC": "en", // Saint Vincent and the Grenadines -> English
                "WS": "sm", // Samoa -> Samoan
                "SM": "it", // San Marino -> Italian
                "ST": "pt", // São Tomé and Príncipe -> Portuguese
                "SA": "ar", // Saudi Arabia -> Arabic
                "SN": "fr", // Senegal -> French
                "RS": "sr", // Serbia -> Serbian
                "SC": "en", // Seychelles -> English
                "SL": "en", // Sierra Leone -> English
                "SG": "zh-CN", // Singapore -> Chinese
                "SK": "sk", // Slovakia -> Slovak
                "SI": "sl", // Slovenia -> Slovene
                "SB": "en", // Solomon Islands -> English
                "SO": "so", // Somalia -> Somali
                "ZA": "en", // South Africa -> English
                "ES": "es", // Spain -> Spanish
                "LK": "si", // Sri Lanka -> Sinhala
                "SD": "ar", // Sudan -> Arabic
                "SR": "nl", // Suriname -> Dutch
                "SZ": "en", // Swaziland -> English
                "SE": "sv", // Sweden -> Swedish
                "CH": "de", // Switzerland -> German (also French, Italian)
                "SY": "ar", // Syria -> Arabic
                "TW": "zh-TW", // Taiwan -> Chinese (Traditional)
                "TJ": "tg", // Tajikistan -> Tajik
                "TZ": "sw", // Tanzania -> Swahili
                "TH": "th", // Thailand -> Thai
                "TL": "pt", // Timor-Leste -> Portuguese
                "TG": "fr", // Togo -> French
                "TO": "en", // Tonga -> English
                "TT": "en", // Trinidad and Tobago -> English
                "TN": "ar", // Tunisia -> Arabic
                "TR": "tr", // Turkey -> Turkish
                "TM": "tk", // Turkmenistan -> Turkmen
                "TV": "en", // Tuvalu -> English
                "UG": "en", // Uganda -> English
                "UA": "uk", // Ukraine -> Ukrainian
                "AE": "ar", // United Arab Emirates -> Arabic
                "GB": "en", // UK -> English
                "US": "en", // USA -> English
                "UY": "es", // Uruguay -> Spanish
                "UZ": "uz", // Uzbekistan -> Uzbek
                "VU": "bi", // Vanuatu -> Bislama
                "VA": "it", // Vatican City -> Italian
                "VE": "es", // Venezuela -> Spanish
                "VN": "vi", // Vietnam -> Vietnamese
                "YE": "ar", // Yemen -> Arabic
                "ZM": "en", // Zambia -> English
                "ZW": "en" // Zimbabwe -> English
            };


            // Step 4: Detect visitor country using free API
            fetch("https://ipapi.co/json/")
                .then(res => res.json())
                .then(data => {
                    const countryCode = data.country_code;
                    const targetLang = countryToLang[countryCode] || 'en';
                    // for test
                    //  const targetLang = 'ja';

                    // Step 5: Auto-select language after Google widget loads
                    if (targetLang !== 'en') {
                        const interval = setInterval(() => {
                            const select = document.querySelector('.goog-te-combo');
                            if (select) {
                                for (let i = 0; i < select.options.length; i++) {
                                    if (select.options[i].value === targetLang) {
                                        select.selectedIndex = i;
                                        select.dispatchEvent(new Event('change'));
                                        clearInterval(interval);
                                        // make the translation bar hidden
                                        //  document.querySelector('.skiptranslate').style.display = 'none';
                                        break;
                                    }
                                }
                            }
                        }, 2000);
                    }
                })
                .catch(() => {
                    console.warn("Geo-IP lookup failed; keeping English.");
                });
        </script> --}}

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
        {{-- <script>
        $(document).ready(function() {
            @if (Session::has('agree'))
            @else
                $('#onload_popup').modal('show');
            @endif
        });
    </script> --}}
        {{-- PDF.js Library --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            async function renderPDF(url, containerId) {
                const container = document.getElementById(containerId);
                try {
                    const loadingTask = pdfjsLib.getDocument(url);
                    const pdf = await loadingTask.promise;
                    container.innerHTML = ''; // Clear loader

                    for (let i = 1; i <= pdf.numPages; i++) {
                        const page = await pdf.getPage(i);
                        const viewport = page.getViewport({
                            scale: 1.5
                        });

                        const canvas = document.createElement('canvas');
                        canvas.className = 'pdf-page-canvas';
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        await page.render(renderContext).promise;
                        container.appendChild(canvas);

                        // For the last page, add a trigger point
                        if (i === pdf.numPages) {
                            const sentinel = document.createElement('div');
                            sentinel.id = 'pdf-bottom-sentinel';
                            sentinel.style.height = '1px';
                            container.appendChild(sentinel);

                            const observer = new IntersectionObserver((entries) => {
                                if (entries[0].isIntersecting) {
                                    $('.check-main-first').fadeIn(800);
                                    $('.scroll-hint').fadeOut();
                                    observer.disconnect();
                                }
                            }, {
                                threshold: 0.1
                            });
                            observer.observe(sentinel);
                        }
                    }
                } catch (error) {
                    console.error('PDF Rendering Error:', error);
                    container.innerHTML = '<div class="alert alert-danger">Failed to load and render the document.</div>';
                }
            }

            $(document).ready(function() {
                // Load PDF when modal is shown
                $('#registerModalFirst').on('shown.bs.modal', function() {
                    const pdfUrl = "{{ Helper::getPDFAttribute() }}";
                    if (pdfUrl) {
                        renderPDF(pdfUrl, 'pdf-viewer-container');
                    }
                });

                $('.regis').on('click', function() {
                    if ($('#pma_check1').is(':checked')) {
                        window.location.href = "{{ route('register') }}";
                    } else {
                        toastr.error('Please check the agreement');
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#asp_ng_button_065e81241e506b').click(function() {
                    $('#exampleModal1').modal('hide');
                    $('#exampleModalToggle2').modal('show');
                });

                $('#amount').on('keyup', function() {
                    var amount = $(this).val();
                    if (amount == '') {
                        $('#submit-btn').text('Donate US$ 0.00');
                    } else {
                        $('#submit-btn').text('Donate US$ ' + amount);

                        // $(document).on('click', '#submit-btn', function(){
                        //     $('#loading').addClass('loading');
                        //     $('#loading-content').addClass('loading-content');
                        // })
                    }
                });
            });
        </script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.4.1/jquery.payment.min.js"></script>








        <script>
            $(document).ready(function() {
                var cardTypeImages = {
                    'visa': '{{ 'frontend_assets/images/visa.png' }}',
                    'mastercard': '{{ 'frontend_assets/images/mastercard.png' }}',
                    'amex': '{{ 'frontend_assets/images/amex.png' }}',
                    'unknown': '{{ 'frontend_assets/images/unknown.webp' }}'
                };

                $('#card-number').on('keyup change', function() {
                    var cardNumber = $(this).val();
                    var cardType = $.payment.cardType(cardNumber);

                    var cardTypeImage = cardTypeImages[cardType] || cardTypeImages['unknown'];
                    $('#card-type-image').attr('src', cardTypeImage);

                    // Adjust CVV validation based on card type
                    var cvvLength = cardType === 'amex' ? 4 : 3;
                    $('#card-cvc').attr('maxlength', cvvLength);
                });
            });

            // $(document).on('click', '#submit-btn', function(){
            //     $('#loading').addClass('loading');
            //     $('#loading-content').addClass('loading-content');
            // })
        </script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.4.1/jquery.payment.min.js"></script>

        <script>
            $(document).ready(function() {
                var cardTypeImages = {
                    'visa': '{{ 'frontend_assets/images/visa.png' }}',
                    'mastercard': '{{ 'frontend_assets/images/mastercard.png' }}',
                    'amex': '{{ 'frontend_assets/images/amex.png' }}',
                    'unknown': '{{ 'frontend_assets/images/unknown.webp' }}'
                };

                $('#card-number').on('keyup change', function() {
                    var cardNumber = $(this).val();
                    var cardType = $.payment.cardType(cardNumber);

                    var cardTypeImage = cardTypeImages[cardType] || cardTypeImages['unknown'];
                    $('#card-type-image').attr('src', cardTypeImage);

                    // Adjust CVV validation based on card type
                    var cvvLength = cardType === 'amex' ? 4 : 3;
                    $('#card-cvc').attr('maxlength', cvvLength);
                });
            });
        </script>
        <script type="text/javascript">
            // $(function() {

            //     /*------------------------------------------
            //     --------------------------------------------
            //     Stripe Payment Code
            //     --------------------------------------------
            //     --------------------------------------------*/

            //     var $form = $(".require-validation");

            //     $('form.require-validation').bind('submit', function(e) {
            //         var $form = $(".require-validation"),
            //         inputSelector = ['input[type=email]', 'input[type=password]',
            //                          'input[type=text]', 'input[type=file]',
            //                          'textarea'].join(', '),
            //         $inputs = $form.find('.required').find(inputSelector),
            //         $errorMessage = $form.find('div.error'),
            //         valid = true;
            //         $errorMessage.addClass('hide');

            //         $('.has-error').removeClass('has-error');
            //         $inputs.each(function(i, el) {
            //           var $input = $(el);
            //           if ($input.val() === '') {
            //             $input.parent().addClass('has-error');
            //             $errorMessage.removeClass('hide');
            //             e.preventDefault();
            //           }
            //         });

            //         if (!$form.data('cc-on-file')) {
            //           e.preventDefault();
            //           Stripe.setPublishableKey($form.data('stripe-publishable-key'));
            //           Stripe.createToken({
            //             number: $('.card-number').val(),
            //             cvc: $('.card-cvc').val(),
            //             exp_month: $('.card-expiry-month').val(),
            //             exp_year: $('.card-expiry-year').val()
            //           }, stripeResponseHandler);
            //         }

            //     });

            //     /*------------------------------------------
            //     --------------------------------------------
            //     Stripe Response Handler
            //     --------------------------------------------
            //     --------------------------------------------*/
            //     function stripeResponseHandler(status, response) {
            //         if (response.error) {
            //             $('.error')
            //                 .removeClass('hide')
            //                 .find('.alert')
            //                 .text(response.error.message);
            //         } else {
            //             /* token contains id, last4, and card type */
            //             var token = response['id'];

            //             $form.find('input[type=text]').empty();
            //             $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            //             $form.get(0).submit();
            //         }
            //     }

            // });

            $(document).ready(function() {
                var $form = $(".require-validation");
                $('.require-validation').validate({
                    rules: {
                        amount: {
                            required: true
                        },
                        first_name: {
                            required: true
                        },
                        last_name: {
                            required: true
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        address: {
                            required: true
                        },
                        city: {
                            required: true
                        },
                        country_id: {
                            required: true
                        },
                        state: {
                            required: true
                        },
                        postcode: {
                            required: true
                        },
                        card_number: {
                            required: true
                        },
                        card_expiry_month: {
                            required: true,
                            number: true
                        },
                        card_expiry_year: {
                            required: true
                        },
                        card_cvc: {
                            required: true
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },



                    submitHandler: function(form) {
                        $('#loading').addClass('loading');
                        $('#loading-content').addClass('loading-content');
                        var $form = $(form),
                            inputSelector = ['input[type=email]', 'input[type=password]',
                                'input[type=text]', 'input[type=file]',
                                'textarea'
                            ].join(', '),
                            $inputs = $form.find('.required').find(inputSelector),
                            $errorMessage = $form.find('div.error');

                        $errorMessage.addClass('hide');
                        $('.has-error').removeClass('has-error');

                        $inputs.each(function(i, el) {
                            var $input = $(el);
                            if ($input.val() === '') {
                                $input.parent().addClass('has-error');
                                $errorMessage.removeClass('hide');
                                return false; // Stop processing on the first validation error
                            }
                        });

                        if (!$form.data('cc-on-file')) {
                            // e is not defined here, so remove it
                            Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                            Stripe.createToken({
                                number: $('.card-number').val(),
                                cvc: $('#card-cvc').val(),
                                exp_month: $('#card-expiry-month').val(),
                                exp_year: $('#card-expiry-year').val()
                            }, stripeResponseHandler);
                        }
                    }
                });

                function stripeResponseHandler(status, response) {
                    if (response.error) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        toastr.error(response.error.message);

                    } else {
                        // $('#loading').removeClass('loading');
                        // $('#loading-content').removeClass('loading-content');
                        var token = response['id'];
                        $form.find('input[type=text]').empty();
                        $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                        $form.get(0).submit();
                    }
                }
            });
        </script>
        <script>
            $('#card-number').on('input propertychange paste', function() {
                var value = $('#card-number').val();
                var formattedValue = formatCardNumber(value);
                $('#card-number').val(formattedValue);
            });

            function formatCardNumber(value) {
                var value = value.replace(/\D/g, '');
                var formattedValue;
                var maxLength;
                // american express, 15 digits
                if ((/^3[47]\d{0,13}$/).test(value)) {
                    formattedValue = value.replace(/(\d{4})/, '$1 ').replace(/(\d{4}) (\d{6})/, '$1 $2 ');
                    maxLength = 17;
                } else if ((/^3(?:0[0-5]|[68]\d)\d{0,11}$/).test(value)) { // diner's club, 14 digits
                    formattedValue = value.replace(/(\d{4})/, '$1 ').replace(/(\d{4}) (\d{6})/, '$1 $2 ');
                    maxLength = 16;
                } else if ((/^\d{0,16}$/).test(value)) { // regular cc number, 16 digits
                    formattedValue = value.replace(/(\d{4})/, '$1 ').replace(/(\d{4}) (\d{4})/, '$1 $2 ').replace(
                        /(\d{4}) (\d{4}) (\d{4})/, '$1 $2 $3 ');
                    maxLength = 19;
                }

                $('#card-number').attr('maxlength', maxLength);
                return formattedValue;
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#sign-in-form').submit(function(e) {
                    e.preventDefault();

                    var formData = $(this).serialize();
                    var url = $(this).attr('action');
                    var submitButton = $('#login-submit');
                    submitButton.prop('disabled', true).val('Loading...');

                    $.ajax({
                        url: url,
                        type: $(this).attr('method'),
                        data: formData,
                        success: function(response) {
                            if (response.status == true) {
                                if (response.otp_required) {
                                    $('#otpModal').modal('show');
                                } else {
                                    window.location.href = response.redirect;
                                }
                            } else {
                                $('.text-danger').html('');
                                toastr.error(response.message);
                                submitButton.prop('disabled', false).val('Log In');
                            }
                        },
                        error: function(xhr) {
                            $('.text-danger').html('');
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                            submitButton.prop('disabled', false).val('Log In');
                        }
                    });
                });

                $('#otp-form').submit(function(e) {
                    e.preventDefault();

                    var formData = $(this).serialize();
                    var url = $(this).attr('action');
                    $.ajax({
                        url: url,
                        type: $(this).attr('method'),
                        data: formData,
                        success: function(response) {
                            if (response.status == true) {
                                // Check for event registration redirect in sessionStorage
                                var eventRedirect = sessionStorage.getItem('post_login_redirect');
                                if (eventRedirect) {
                                    sessionStorage.removeItem('post_login_redirect');
                                    window.location.href = eventRedirect;
                                } else {
                                    window.location.href = response.redirect;
                                }
                            } else {
                                $('#otp-error').text(response.message);
                            }
                        },
                        error: function(xhr) {
                            $('#otp-error').text('Invalid Code');
                        }
                    });
                });
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
        <script>
            $(document).ready(function() {
                $('#eye-button-1').click(function() {
                    $('#user_password').attr('type', $('#user_password').is(':password') ? 'text' : 'password');
                    $(this).find('i').toggleClass('fa-eye-slash fa-eye');
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                let countdownInterval;
                let remainingSeconds = 0;

                // Function to start countdown timer
                function startCountdown(seconds) {
                    remainingSeconds = seconds || 5; // Default to 5 seconds
                    $('#resend-otp-btn').prop('disabled', true);
                    $('#countdown-timer').show();

                    clearInterval(countdownInterval);
                    countdownInterval = setInterval(function() {
                        remainingSeconds--;

                        // Format time as MM:SS
                        let minutes = Math.floor(remainingSeconds / 60);
                        let seconds = remainingSeconds % 60;
                        $('#countdown').text(minutes.toString().padStart(2, '0') + ':' + seconds.toString()
                            .padStart(2, '0'));

                        if (remainingSeconds <= 0) {
                            clearInterval(countdownInterval);
                            $('#resend-otp-btn').prop('disabled', false);
                            $('#countdown-timer').hide();
                        }
                    }, 1000);
                }

                // Handle resend OTP button click
                $('#resend-otp-btn').click(function() {
                    $.ajax({
                        url: "{{ route('resend.otp') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function() {
                            $('#resend-otp-btn').prop('disabled', true).text('Sending...');
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                startCountdown();
                                $('#resend-otp-btn').text('Resend Code');
                            } else {
                                if (response.time_left) {
                                    startCountdown(response.time_left);
                                }
                                toastr.error(response.message);
                                $('#resend-otp-btn').prop('disabled', false).text('Resend Code');
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong. Please try again.');
                            $('#resend-otp-btn').prop('disabled', false).text('Resend Code');
                        }
                    });
                });

                // When OTP modal is shown, start the countdown if a new OTP was just sent
                $('#otpModal').on('shown.bs.modal', function() {
                    startCountdown();
                });

                // When OTP modal is hidden, clear the countdown
                $('#otpModal').on('hidden.bs.modal', function() {
                    clearInterval(countdownInterval);
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                @if (isset($_GET['is_donation']) && $_GET['is_donation'] == 'yes')

                    $('#onload_popup').modal('hide');
                    $('#exampleModalToggle2').modal('show');
                @else
                    @php
                        $ip = request()->ip();
                        $visitorCountrySessionKey = 'visitor_country_flag_code_' . $ip;
                    @endphp

                    // Show agreement only AFTER country selection popup has been completed.
                    @if (!Session::has('agree') && session()->has($visitorCountrySessionKey))
                        $('#onload_popup').modal('show');
                    @endif
                @endif
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
    </body>

</html>
