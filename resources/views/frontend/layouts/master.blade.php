<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Swarnadwip Nath">
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

    @stack('styles')
</head>

<body>
    <main>
        <section id="loading">
            <div id="loading-content"></div>
        </section>
        @php
            use App\Helpers\Helper;
            // dd(date('H'))
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
        <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel2">Lion Roaring Donate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form role="form" action="{{ route('donation') }}" method="post" class="require-validation"
                            data-cc-on-file="false" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
                            id="payment-form">
                            @csrf
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="amount">Enter amount(US$)</label>
                                    <input class="form-control" id="amount" name="amount" inputmode="decimal"
                                        value="">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="billing_name">First Name</label>
                                    <input class="form-control has-icon" type="text" id="billing-fname"
                                        name="first_name" value="">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="billing_name">Last Name</label>
                                    <input class="form-control has-icon" type="text" id="billing-lname"
                                        name="last_name" value="">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="email">Email</label>
                                    <input class="form-control has-icon" type="text" id="email" name="email"
                                        value="">
                                </div>
                                <div class="pure-u-1">
                                    <legend>Billing info</legend>
                                </div>
                                <hr />
                                <div class="col-lg-6 mb-3">
                                    <label for="address">Address</label>
                                    <input class="form-control has-icon" type="text" id="address"
                                        name="address">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="city">City</label>
                                    <input class="form-control" type="text" id="city" name="city">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="country">Country</label>
                                    <select class="form-control" name="country_id" id="country">
                                        <option value="">—</option>
                                        @foreach (Helper::getCountries() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                                    <input class="form-control" type="text" name="postcode" id="postcode">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="card-element">Credit or debit card</label>
                                </div>
                                <hr />
                                <div class="col-md-12">
                                    <label for="card-element">Card Number</label>
                                    <div style="position: relative;">
                                        <input class="form-control card-number" aria-hidden="true" aria-label=" "
                                            name="card_number" id="card-number" autocomplete="off">
                                        <img id="card-type-image"
                                            src="{{ asset('frontend_assets/images/unknown.webp') }}" alt="Card Type"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); max-height: 24px;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="card-element">Month</label>
                                    <select
                                        class="form-control form-control--sm card-expiry-month valid card-expiry-month"
                                        name="card_expiry_month" id="card-expiry-month" aria-invalid="false">
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
                                <div class="col-md-4">
                                    <label for="card-element">Year</label>
                                    <input class="form-control" aria-hidden="true" aria-label=" "
                                        id="card-expiry-year" name="card_expiry_year" autocomplete="false"
                                        maxlength="5">
                                </div>
                                <div class="col-md-4">
                                    <label for="card-element">CVV</label>
                                    <input class="form-control" aria-hidden="true" aria-label=" " name="card_cvc"
                                        id="card-cvc" autocomplete="false" maxlength="4">
                                </div>

                            </div>

                            <div class="mt-4">
                                <div class="pure-u-5-5 centered d-flex">
                                    <button type="submit" id="submit-btn"
                                        class="pure-button pure-button-primary">Donate US $0.00</button>
                                    <p style="margin-left: 5px;"><b>We Accept </b><img
                                            src="{{ asset('frontend_assets/images/cards.png') }}" alt=""
                                            height="35px"></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Login Modal -->
        <div class="modal fade modal_code" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body login_bg_sec border-top-0">
                        <div class="heading_hp">
                            <h2 id="greeting">
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
                                    <input type="text" name="user_name" id="user_login" autocomplete="username"
                                        @if (isset($_COOKIE['email_user_name'])) value="{{ $_COOKIE['email_user_name'] }}" @endif
                                        class="input" value="" size="20">
                                    <span class="text-danger"></span>
                                </p>
                                <p class="login-password" style="position: relative;
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
                            <p class="text-center join_member">
                                <a href="javascrip:void(0);" data-bs-toggle="modal"
                                    data-bs-target="#staticBackdrop">Join
                                    Lion
                                    Roaring Member</a> | <a href="{{ route('user.forget.password.show') }}">Forgot
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
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal_code">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Email Verification</h5>
                    </div>
                    <div class="modal-body">
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
                                    Resend available in <span id="countdown">10:00</span>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Email OTP Modal End -->

        <div class="modal fade donate_bg_sec" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="heading_hp">
                            <div class="asp_product_item">
                                <div class="asp_product_item_top">
                                    <div class="asp_product_item_thumbnail">

                                    </div>
                                    <div class="asp_product_name">
                                        Lion Roaring Donate
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="asp_product_description">
                                    <p>Lion Roaring is a Private Member Agreement, thus any donations will not be
                                        eligible for tax deductible purposes under the IRS Code. Please consult with
                                        your tax advisor should there be any questions related to your donation.</p>

                                </div>
                                <div class="asp_price_container">
                                    <span class="asp_price_amount"></span> <span class="asp_new_price_amount"></span>
                                    <span class="asp_quantity"></span>
                                    <div class="asp_under_price_line"></div>
                                </div>
                                <div class="asp_product_buy_button">
                                    <div class="asp-processing-cont" style="display:none;"><span
                                            class="asp-processing">Processing <i>.</i><i>.</i><i>.</i></span></div>
                                    <form id="asp_ng_form_065e81241e506b" class="asp-stripe-form" action=""
                                        method="POST"> <input type="hidden" name="asp_product_id" value="861">
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
        <div class="modal fade modal_code" id="join_member" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body login_bg_sec border-top-0">
                        <div class="join_member_box">
                            <div class="ftr_logo_sec text-center">
                                <a href="https://www.lionroaring.us" class="ftr_logo d-inline-block">
                                    <img src="uploads/2024/02/Group-2029.png" alt="">
                                </a>
                            </div>
                            <h2>Lion Roaring PMA (Private Members Association) Agreement</h2>
                            <p>It is the responsibility of the members to read and review the Articles of
                                Association of
                                Lion Roaring PMA in its entirety and agree to adopt and comply to its belief,
                                foundation
                                and purpose of the Lion Roaring PMA. </p>
                            <p> Click here to read the full document <a
                                    href="uploads/2024/01/Articles-of-Association-1.pdf">
                                    ARTICLES OF ASSOCIATION</a> (provide link to bring up the PMA to the screen)</p>
                            <p>Each member agrees to the following excerpt taken from the Articles of Association of
                                PMA:</p>
                            <ul>
                                <li>Lion Roaring PMA is a Private Members Association protected under the
                                    Constitution
                                    of the United States of America and the original constitution for these united
                                    States of America and the Maryland Constitution</li>
                                <li>Member agrees and is supportive to the mission and vision of the Lion Roaring
                                </li>
                                <li>Member strives to contribute to the purpose of the PMA to fulfill the God given
                                    call
                                    to the founding members as it is written in Section 4 through 15 in the Article
                                    of
                                    Association</li>
                                <li>Member will not hold Lion Roaring PMA liable for any materials or contents
                                    posted in
                                    the website or any paperwork, written articles, education materials or others
                                    created within the PMA for its members’ benefits and private usage</li>
                                <li>Member’s agreement does not entitle a member to any financial or other interest
                                    in
                                    the Private Members Association or management thereof</li>
                                <li>Information regarding details of the association, any materials produced or
                                    created
                                    by Lion Roaring PMA including all paperwork, agreements, articles, PowerPoints
                                    presentations, word parchments, coaching, and education materials are private
                                    intellectual property of the PMA and will not be shared, replicated, dispersed
                                    or
                                    distributed with anyone outside the PMA without explicit written permission from
                                    the
                                    founder</li>
                                <li>Member’s due diligence is expected and member will hold harmless any member or
                                    founder of Lion Roaring PMA and any dispute shall be handled by the founder(s)
                                    with
                                    final decision for remedy made by the founder(s) and shall be accepted as a
                                    settled
                                    matter. (Article III for disputes resolution &amp; Article IV for Sovereignty in
                                    the
                                    Private)</li>
                                <li>As a private member of the Lion Roaring PMA, member is invoking its united
                                    States
                                    constitutional rights specifically the 1st, 4th, 5th, 9th and 10th and the
                                    Maryland
                                    Constitutional rights included in the Maryland Declaration of Rights Sections 1,
                                    2,
                                    6, 10, 24, 26, 36, 40 and 45 and as such take full responsibility for his or her
                                    behavior, such that his or her actions shall never constitute anything that can
                                    be
                                    determined to be of a “clear and present danger of a substantial evil.” </li>
                                <li>Any actions by the member which are not consistent with the values of the PMA
                                    can
                                    result in the founder’s decision to ask the member to leave the PMA</li>
                                <li>Member is connected with each other and the actions affect one another,
                                    therefore,
                                    the Lion Roaring PMA encourages and supports one another as a family and
                                    community
                                </li>
                                <li>Member and those who are included in this member’s agreement and contract are
                                    solely
                                    responsible for member’s own outcome or results from participating or receiving
                                    any
                                    education materials, counsel, coaching, training, mentoring or other services
                                    provided by Lion Roaring PMA through its websites or any other resources made
                                    available to the members</li>
                                <li>The terminology used in these articles of organization and member’s agreement is
                                    used solely for clarification of the various usages for Private Members
                                    Association
                                    under universal contract law by and between free, spiritually free men and
                                    women,
                                    creations of nature and Natures God, whose lives and rights derive from God
                                    Almighty
                                    and unique Covenant of the man and/or woman with the Creator</li>
                                <li>Any reference within the Articles of Association to the man shall also include
                                    the
                                    woman and any reference to one people may include many people. This PMA shall be
                                    construed and interpreted in the private and all decisions or disputes will be
                                    final
                                    as settled by the founders in accordance with Article III </li>
                                <li>Member agrees that the elimination of one Item or segment of this Agreement does
                                    not
                                    eliminate the entirety of the Agreement but the Agreement will remain as Agreed
                                </li>
                            </ul>

                            <form action="#" method="post">
                                <input type="checkbox" id="checkbox" name="checkbox" value="checked">
                                <label for="checkbox">I have read and agreed to the Lion Roaring PMA Agreement
                                </label>
                                <!-- Remove the submit button -->
                            </form>
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
                                    <label for="pma_check">I have read and agreed to the above requirements to
                                        access
                                        Lion
                                        Roaring PMA website.</label>
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
                                    {{ Helper::getAgreements()['agreement_title'] ?? 'Lion Roaring PMA (Private Members Association) Agreement' }}
                                </h2>
                            </div>
                            <div class="member-text-div admin-srl" id="admin-srl_1">
                                <div class="member-text">
                                    {!! Helper::getAgreements()['agreement_description'] ??
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
    <script src="{{ asset('frontend_assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('frontend_assets/js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox-plus-jquery.js"
        integrity="sha512-0rYcJjaqTGk43zviBim8AEjb8cjUKxwxCqo28py38JFKKBd35yPfNWmwoBLTYORC9j/COqldDc9/d1B7dhRYmg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.getElementById("time_zone").value = timezone;
        });
    </script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en'
            }, 'google_translate_element');
        }
    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>

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

                }
            });

            // $(document).on('click', '#submit-btn', function(){
            //     $('#loading').addClass('loading');
            //     $('#loading-content').addClass('loading-content');
            // })
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
                            window.location.href = response.redirect;
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
                remainingSeconds = seconds || 600; // Default to 10 minutes (600 seconds)
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
                @if (!Session::has('agree'))

                    $('#onload_popup').modal('show');
                @endif
            @endif
        });
    </script>


    @stack('scripts')
</body>

</html>
