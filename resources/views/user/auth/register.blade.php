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
    <style>
        .eye-btn-1 {
            top: 29px;
        }

        /* Validation Error Styles */
        .input.is-invalid,
        .form-control.is-invalid,
        select.is-invalid {
            border-color: #dc3545 !important;
            border-width: 2px;
            background-color: #fff5f5;
        }

        .input.is-invalid:focus,
        .form-control.is-invalid:focus,
        select.is-invalid:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>

    <style>
        /* Premium Modal Styles */
        .modal-content.premium-modal {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            background: #fff;
        }

        .premium-modal .modal-header {
            background: linear-gradient(135deg, #643271 0%, #4a2454 100%);
            color: #fff;
            padding: 20px 30px;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .premium-modal .modal-title {
            font-family: 'EB Garamond', serif;
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 1px;
            color: #d98b1c;
            /* Gold */
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin: 0;
        }

        .premium-modal .close {
            position: absolute;
            right: 20px;
            top: 20px;
            color: rgba(255, 255, 255, 0.7);
            opacity: 1;
            font-size: 1.5rem;
            transition: all 0.3s;
            background: none;
            border: none;
        }

        .premium-modal .close:hover {
            color: #d98b1c;
            transform: rotate(90deg);
        }

        .premium-modal .modal-body {
            padding: 40px;
            background: #fdfdfd;
        }

        /* Tier Cards */
        .tier-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .tier-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            position: relative;
            transition: all 0.4s ease;
            border: 1px solid #eee;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .tier-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(100, 50, 113, 0.15);
            border-color: #d98b1c;
            /* Gold border on hover */
        }

        .tier-card.featured {
            border: 2px solid #d98b1c;
            background: linear-gradient(to bottom, #fff, #fffbf2);
        }

        .tier-card .tier-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #643271;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tier-card .tier-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 20px;
            font-family: 'Figtree', sans-serif;
        }

        .tier-card .tier-price span {
            font-size: 1rem;
            color: #777;
            font-weight: 400;
        }

        .tier-card .tier-desc {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .tier-benefits {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
            text-align: left;
            flex-grow: 1;
        }

        .tier-benefits li {
            margin-bottom: 12px;
            color: #444;
            display: flex;
            align-items: flex-start;
        }

        .tier-benefits li i {
            color: #d98b1c;
            /* Gold checkmarks */
            font-size: 0.9rem;
            margin-right: 10px;
            margin-top: 5px;
        }

        .select-tier-btn {
            width: 100%;
            padding: 12px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            background: #643271;
            color: #fff;
            box-shadow: 0 4px 15px rgba(100, 50, 113, 0.3);
        }

        .select-tier-btn:hover {
            background: #4a2454;
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(100, 50, 113, 0.4);
        }

        .tier-card.featured .select-tier-btn {
            background: linear-gradient(135deg, #d98b1c 0%, #b57012 100%);
            box-shadow: 0 4px 15px rgba(217, 139, 28, 0.4);
        }

        .tier-card.featured .select-tier-btn:hover {
            background: linear-gradient(135deg, #b57012 0%, #945a0e 100%);
        }

        /* Payment Styles */
        .payment-amount-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .payment-amount-box h4 {
            margin: 0;
            color: #555;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .payment-amount-box .amount {
            display: block;
            font-size: 2.5rem;
            font-weight: 700;
            color: #643271;
            margin-top: 5px;
        }

        .stripe-container {
            background: #fff;
            padding: 15px !important;
            border-radius: 8px;
            border: 1px solid #ced4da;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.02);
            margin-bottom: 10px;
        }

        .StripeElement {
            width: 100%;
            padding: 5px 0;
        }

        .stripe-container.focused {
            border-color: #643271;
            box-shadow: 0 0 0 3px rgba(100, 50, 113, 0.1);
        }

        .payment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .back-btn {
            background: none;
            border: none;
            color: #777;
            font-weight: 600;
            padding: 10px 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .back-btn:hover {
            color: #333;
        }

        .pay-btn {
            background: #643271;
            color: white;
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        }

        .pay-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
@php
    use App\Helpers\Helper;
@endphp

<body style="background: #643271">
    <main>
        <section class="log-main">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-8">
                        <div class="login_bg_sec border-top-0">
                            <div class="logo-admin">
                                @if (isset(Helper::getFooter()['footer_logo']))
                                    <img src="{{ Storage::url(Helper::getFooter()['footer_logo']) }}" alt="">
                                @else
                                    <img src="{{ asset('user_assets/images/logo.png') }}" alt="">
                                @endif
                            </div>
                            <div class="heading_hp">
                                <h2 id="greeting">Request Private Member Access </h2>
                                <div class="admin-form">
                                    <form name="login-form" id="login-form" action="{{ route('register.check') }}"
                                        method="post">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Username</label>
                                                    <input type="text" name="user_name" id="user_login"
                                                        class="input" value="{{ old('user_name') }}">
                                                    @if ($errors->has('user_name'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('user_name') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="lion_roaring_id">Lion Roaring ID *</label>
                                                    <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                                                        <span class="input-group-text" id="basic-addon1"
                                                            style="border-radius: 4px 0 0 4px; border-right: 0; background-color: #f8f9fa; padding: 10px 15px; white-space: nowrap;">{{ $generated_id_part }}</span>
                                                        <input type="text" name="lion_roaring_id_suffix"
                                                            id="lion_roaring_id" class="input"
                                                            value="{{ old('lion_roaring_id_suffix') }}" maxlength="4"
                                                            placeholder="Enter last 4 digit ID/SSN"
                                                            style="border-radius: 0 4px 4px 0; border-left: 0; flex: 1;">
                                                    </div>
                                                    @if ($errors->has('lion_roaring_id_suffix'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('lion_roaring_id_suffix') }}</div>
                                                    @endif
                                                    <input type="hidden" name="generated_id_part"
                                                        value="{{ $generated_id_part }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="roar_id">Roar ID</label>
                                                    <input type="text" name="roar_id" id="roar_id" class="input"
                                                        value="{{ old('roar_id') }}">
                                                    @if ($errors->has('roar_id'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('roar_id') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Country</label>
                                                    <select name="country" id="country" class="input">
                                                        <option value="">Select Country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                {{ old('country', $country->code == 'US' ? $country->id : '') == $country->id ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('country'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('country') }}</div>
                                                    @endif
                                                </div>
                                            </div>


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

                                            {{-- eclessias --}}
                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Ecclesia</label>
                                                    <select name="ecclesia_id" id="ecclesia_id" class="input">
                                                        <option value=""></option>
                                                        @foreach ($eclessias as $item)
                                                            <option value="{{ $item->id }}"
                                                                {{ old('ecclesia_id') == $item->id ? 'selected' : '' }}>
                                                                {{ $item->name . ' (' . $item->countryName->name . ')' ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('ecclesia_id'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('ecclesia_id') }}</div>
                                                    @endif
                                                </div>
                                            </div>


                                            {{-- state --}}
                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">State</label>
                                                    <select name="state" id="state" class="input">
                                                        <option value="">Select State</option>
                                                    </select>
                                                    @if ($errors->has('state'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('state') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- city --}}
                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">City</label>
                                                    <input type="text" name="city" id="user_login"
                                                        class="input" value="{{ old('city') }}">
                                                    @if ($errors->has('city'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('city') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- address --}}
                                            <div class="col-lg-12 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Address Line 1</label>
                                                    <input type="text" name="address" id="user_login"
                                                        class="input" value="{{ old('address') }}">
                                                    @if ($errors->has('address'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('address') }}</div>
                                                    @endif

                                                </div>
                                            </div>
                                            {{-- address2 --}}
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Address Line 2</label>
                                                    <input type="text" name="address2" id="user_login"
                                                        class="input" value="{{ old('address2') }}">
                                                    @if ($errors->has('address2'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('address2') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- zip --}}
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Zip</label>
                                                    <input type="text" name="zip" id="user_login"
                                                        class="input" value="{{ old('zip') }}">
                                                    @if ($errors->has('zip'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('zip') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Phone Number</label>
                                                    <input type="text" name="phone_number" id="mobile_code"
                                                        placeholder="Enter Phone Number" class="input"
                                                        value="{{ old('full_phone_number') }}">
                                                    @if ($errors->has('phone_number'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('phone_number') }}</div>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="col-lg-4 mb-3">
                                                <div class="login-username">
                                                    <label for="user_login">Email ID</label>
                                                    <input type="email" name="email" id="user_login"
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
                                                    <input type="email" name="email_confirmation" id="user_login"
                                                        class="input" value="{{ old('email_confirmation') }}">
                                                    @if ($errors->has('email_confirmation'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('email_confirmation') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                        {{-- E-Signature Section --}}
                                        <div class="row">
                                            <div class="col-lg-12 mb-3">
                                                <div class="login-username">
                                                    <label for="signature_pad"
                                                        style="font-weight: 600; font-size: 15px;">
                                                        <i class="fa fa-pencil-alt" style="color: #643271;"></i>
                                                        E-Signature *
                                                    </label>
                                                    <p class="text-muted small mb-2" style="font-size: 13px;">
                                                        <i class="fa fa-info-circle"></i> Please sign below using
                                                        your
                                                        mouse or finger
                                                    </p>
                                                    <div class="signature-container"
                                                        style="
                                                        border: 2px dashed #643271;
                                                        border-radius: 12px;
                                                        background: linear-gradient(to bottom, #ffffff 0%, #f9f9f9 100%);
                                                        position: relative;
                                                        box-shadow: 0 2px 8px rgba(100, 50, 113, 0.1);
                                                        padding: 10px;
                                                    ">
                                                        <canvas id="signature-pad"
                                                            style="
                                                            width: 100%;
                                                            height: 200px;
                                                            cursor: crosshair;
                                                            touch-action: none;
                                                            border-radius: 8px;
                                                            background: white;
                                                        "></canvas>
                                                        <div style="
                                                            position: absolute;
                                                            bottom: 20px;
                                                            left: 20px;
                                                            color: #ddd;
                                                            font-style: italic;
                                                            pointer-events: none;
                                                            font-size: 14px;
                                                        "
                                                            id="signature-placeholder">Sign here</div>
                                                    </div>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center mt-3">
                                                        <button type="button" id="clear-signature"
                                                            class="btn btn-sm"
                                                            style="
                                                            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                                                            color: white;
                                                            border: none;
                                                            padding: 8px 16px;
                                                            border-radius: 6px;
                                                            font-weight: 500;
                                                            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
                                                            transition: all 0.3s ease;
                                                        "
                                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(220, 53, 69, 0.4)';"
                                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(220, 53, 69, 0.3)';">
                                                            <i class="fa fa-eraser"></i> Clear & Redraw
                                                        </button>
                                                        <small class="text-muted" style="font-size: 12px;">
                                                            <i class="fa fa-hand-pointer"></i> Use mouse, trackpad,
                                                            or
                                                            touch to sign
                                                        </small>
                                                    </div>
                                                    <input type="hidden" name="signature" id="signature-data"
                                                        value="{{ old('signature') }}">
                                                    @if ($errors->has('signature'))
                                                        <div class="error mt-2"
                                                            style="
                                                            color: #dc3545;
                                                            background: #ffe6e6;
                                                            padding: 10px;
                                                            border-radius: 6px;
                                                            border-left: 4px solid #dc3545;
                                                            font-size: 13px;
                                                        ">
                                                            <i class="fa fa-exclamation-circle"></i>
                                                            {{ $errors->first('signature') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username position-relative">
                                                    <label for="password">Password</label>
                                                    <input type="password" name="password" id="password"
                                                        class="input" value="{{ old('password') }}">
                                                    <span class="eye-btn-1" id="eye-button-1">
                                                        <i class="fa fa-eye-slash" aria-hidden="true"
                                                            id="togglePassword"></i>
                                                    </span>
                                                    @if ($errors->has('password'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('password') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="login-username position-relative">
                                                    <label for="confirm_password">Confirm Password</label>
                                                    <input type="password" name="password_confirmation"
                                                        id="confirm_password" class="input"
                                                        value="{{ old('password_confirmation') }}">
                                                    <span class="eye-btn-1" id="eye-button-2">
                                                        <i class="fa fa-eye-slash" aria-hidden="true"
                                                            id="togglePassword"></i>
                                                    </span>
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
    <script src="{{ asset('user_assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('user_assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('user_assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--  core files -->
    <script src="{{ asset('user_assets/js/app.min.js') }}"></script>
    <script src="{{ asset('user_assets/js/app.init.js') }}"></script>
    <script src="{{ asset('user_assets/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('user_assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('user_assets/js/custom.js') }}"></script>
    <!--  current page js files -->
    <script src="{{ asset('user_assets/js/owl.carousel.min.js') }}"></script>
    <!-- <script src="js/apexcharts.min.js"></script> -->
    <!-- <script src="js/dashboard.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    {{-- trippy cdn link --}}
    <script src="https://unpkg.com/popper.js@1"></script>
    <script src="https://unpkg.com/tippy.js@5"></script>
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
        $('#back-login').click(function() {
            window.location.href = "{{ route('home') }}";
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initially load states and ecclesias for the selected country
            var initialCountry = $('#country').val();
            getStates(initialCountry);
            getEcclesias(initialCountry);

            // Fetch states and ecclesias when the country changes
            $('#country').change(function() {
                var country = $(this).val();
                getStates(country);
                getEcclesias(country);
            });

            function getStates(country) {
                if (!country) {
                    $('#state').html('<option value="">Select State</option>');
                    return;
                }
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "GET",
                    data: {
                        country: country
                    },
                    success: function(response) {
                        var states = response;
                        var selectedState = "{{ old('state') }}";
                        var html = '<option value="">Select State</option>';

                        states.forEach(function(state) {
                            html += '<option value="' + state.id + '"';
                            if (selectedState == state.id) {
                                html += ' selected';
                            }
                            html += '>' + state.name + '</option>';
                        });

                        $('#state').html(html);
                    }
                });
            }

            function getEcclesias(country) {
                if (!country) {
                    $('#ecclesia_id').html('<option value=""></option>');
                    return;
                }
                $.ajax({
                    url: "{{ route('get.ecclesias') }}",
                    type: "GET",
                    data: {
                        country: country
                    },
                    success: function(response) {
                        var ecclesias = response;
                        var selectedEcclesia = "{{ old('ecclesia_id') }}";
                        var html = '<option value=""></option>';

                        ecclesias.forEach(function(ecclesia) {
                            html += '<option value="' + ecclesia.id + '"';
                            if (selectedEcclesia == ecclesia.id) {
                                html += ' selected';
                            }
                            var countryName = (ecclesia.country_name && ecclesia.country_name
                                .name) || (ecclesia.countryName && ecclesia.countryName
                                .name) || "";
                            // Note: Laravel default JSON serialization for relationships is usually snake_case.
                            html += '>' + ecclesia.name + (countryName ? ' (' + countryName +
                                ')' : '') + '</option>';
                        });

                        $('#ecclesia_id').html(html);
                    }
                });
            }
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

            let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils
                .numberFormat.NATIONAL);
            maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

            let mask;
            if (dialCode && dialCode.length > 2) {
                // Use a fixed mask pattern for countries with dial codes of length greater than 2
                mask = '999 999 999';
                maskNumber = '999 999 999';
            } else {
                // Dynamically create a mask by replacing digits with 0 for shorter dial codes
                mask = maskNumber.replace(/[0-9+]/g, '0');
            }

            // Apply the mask with the placeholder
            phoneInput.mask(mask, {
                placeholder: 'Enter Phone Number',
            });

            phoneInput.on('countrychange', function() {
                $(this).val('');
                const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                const newDialCode = newSelectedCountry.dialCode;
                const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

                let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2,
                    intlTelInputUtils.numberFormat.NATIONAL);
                newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');
                let newMask;
                if (newDialCode.length > 2) {
                    // If dial code length is more than 2, use a 999 999 999 mask (or a similar format)
                    newMask = '999 999 999';
                    newMaskNumber = '999 999 999';
                } else {
                    // Otherwise, replace all digits with 0
                    newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                }

                phoneInput.mask(newMask, {
                    placeholder: 'Enter Phone Number',
                });
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
                const countryData = phoneInput.intlTelInput('getSelectedCountryData');
                const countryCodeName = countryData.iso2;

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

                $('<input>').attr({
                    type: 'hidden',
                    name: 'phone_country_code_name',
                    value: countryCodeName
                }).appendTo('form');
            });
        });
    </script>

    {{-- Signature Pad Library --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            const placeholder = $('#signature-placeholder');

            // Resize canvas to fit container
            function resizeCanvas(restoreData = false) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const oldData = restoreData ? signaturePad.toData() : null;

                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();

                if (oldData && oldData.length > 0) {
                    signaturePad.fromData(oldData);
                }
            }

            // Initial resize
            resizeCanvas();

            // Restore old signature if exists (from validation error)
            const oldSignature = $('#signature-data').val();
            if (oldSignature && oldSignature.trim() !== '') {
                try {
                    signaturePad.fromDataURL(oldSignature);
                    placeholder.hide();
                } catch (e) {
                    console.log('Could not restore signature:', e);
                }
            }

            // Resize on window resize
            window.addEventListener('resize', function() {
                resizeCanvas(true);
            });

            // Hide placeholder when drawing starts
            signaturePad.addEventListener('beginStroke', function() {
                placeholder.fadeOut(200);
            });

            // Show placeholder if signature is empty
            signaturePad.addEventListener('endStroke', function() {
                if (signaturePad.isEmpty()) {
                    placeholder.fadeIn(200);
                }
            });

            // Clear button
            $('#clear-signature').on('click', function() {
                signaturePad.clear();
                $('#signature-data').val('');
                placeholder.fadeIn(200);
            });

            // On form submit, save signature data
            $('#login-form').on('submit', function(e) {
                if (!signaturePad.isEmpty()) {
                    const dataURL = signaturePad.toDataURL('image/png');
                    $('#signature-data').val(dataURL);
                } else {
                    // If signature is empty, prevent form submission
                    e.preventDefault();
                    swal({
                        title: "Signature Required",
                        text: "Please provide your signature before submitting the form.",
                        type: "warning",
                        confirmButtonText: "OK"
                    });
                    return false;
                }
            });
        });
    </script>


    <!-- Membership Tier Modal -->
    <div class="modal fade" id="tierModal" tabindex="-1" role="dialog" aria-labelledby="tierModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content premium-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="tierModalLabel">Choose Your Member Tier</h5>
                </div>
                <div class="modal-body">
                    <div class="tier-grid">
                        @foreach ($tiers as $index => $tier)
                            @php $is_featured = ($index == 1) ? 'featured' : ''; @endphp
                            <div class="tier-card {{ $is_featured }}">
                                <div class="tier-name">{{ $tier->name }}</div>
                                <div class="tier-price">
                                    @if (($tier->pricing_type ?? 'amount') === 'token')
                                        {{ $tier->life_force_energy_tokens ?? 0 }}<span> Life Force Energy</span>
                                    @else
                                        ${{ number_format((float) $tier->cost, 0) }}<span>/yr</span>
                                    @endif
                                </div>
                                <div class="tier-desc">
                                    {{ $tier->description }}
                                </div>
                                <ul class="tier-benefits">
                                    @foreach ($tier->benefits as $benefit)
                                        <li><i class="fas fa-check-circle"></i> {{ $benefit->benefit }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="select-tier-btn" data-id="{{ $tier->id }}"
                                    data-cost="{{ (float) ($tier->cost ?? 0) }}"
                                    data-pricing-type="{{ $tier->pricing_type ?? 'amount' }}"
                                    data-tier-name="{{ $tier->name }}"
                                    data-agree-description="{{ e($tier->agree_description) }}"
                                    data-tokens="{{ $tier->life_force_energy_tokens ?? '' }}">
                                    @if (($tier->pricing_type ?? 'amount') === 'token')
                                        Subscribe (Tokens)
                                    @else
                                        {{ (float) ($tier->cost ?? 0) > 0 ? 'Subscribe' : 'Get Started' }}
                                    @endif
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Tier Agreement Modal -->
    <div class="modal fade" id="tokenAgreeModal" tabindex="-1" role="dialog" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content premium-modal" style="max-width: 650px; margin: 0 auto;">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenAgreeModalTitle">Tier - Agreement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#tokenAgreeModal').modal('hide'); $('#tierModal').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-2" style="color:#666; font-size: 0.95rem;">Please review and accept to
                        continue.</div>
                    <div class="border rounded p-3" style="white-space: pre-wrap; max-height: 320px; overflow:auto;"
                        id="tokenAgreeModalBody">
                    </div>
                </div>
                <div class="modal-footer"
                    style="display:flex; justify-content:flex-end; gap:10px; padding: 15px 30px;">
                    <button type="button" class="back-btn" id="token-agree-reject-btn">
                        Reject
                    </button>
                    <button type="button" class="pay-btn" id="token-agree-accept-btn">
                        Accept & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content premium-modal" style="max-width: 500px; margin: 0 auto;">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Secure Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#paymentModal').modal('hide');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Promo Code Discount Display -->
                    <div id="promo-discount-section"
                        style="display: none; background: #e7f4e7; border: 1px solid #4caf50; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <i class="fa fa-tag" style="color: #4caf50; margin-right: 8px;"></i>
                                <strong style="color: #2e7d32;">Promo Code Applied:</strong>
                                <span id="applied-promo-code" style="color: #555;"></span>
                            </div>
                            <button type="button" id="remove-promo"
                                style="background: none; border: none; color: #d32f2f; cursor: pointer; font-size: 14px;">
                                <i class="fa fa-times"></i> Remove
                            </button>
                        </div>
                    </div>

                    <div class="payment-amount-box">
                        <div id="original-amount-section" style="display: none; margin-bottom: 10px;">
                            <div
                                style="display: flex; justify-content: space-between; color: #999; text-decoration: line-through;">
                                <span>Original Amount:</span>
                                <span id="original-amount-display">$0.00</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; color: #4caf50; font-weight: 600;">
                                <span>Discount:</span>
                                <span id="discount-amount-display">-$0.00</span>
                            </div>
                            <hr style="margin: 10px 0;">
                        </div>
                        <h4>Total Amount</h4>
                        <span class="amount" id="payment-amount-display">$0.00</span>
                    </div>

                    <form id="payment-form">
                        <!-- Promo Code Field -->
                        <div class="form-group mb-4">
                            <label for="promo_code"
                                style="font-weight: 600; color: #555; margin-bottom: 10px; display: block;">
                                <i class="fa fa-tag" style="color: #643271; margin-right: 5px;"></i>
                                Promo Code (Optional)
                            </label>
                            <div style="display: flex; gap: 10px; align-items: stretch;">
                                <input type="text" name="promo_code" id="promo_code" class="form-control"
                                    placeholder="Enter promo code"
                                    style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 15px;">
                                <button type="button" id="validate-promo-btn" class="btn"
                                    style="background: #643271; color: white; padding: 0 20px; border: none; border-radius: 6px; white-space: nowrap; font-weight: 500;"
                                    disabled>
                                    Apply
                                </button>
                            </div>
                            <div id="promo-code-feedback" class="mt-2" style="font-size: 13px;"></div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="card-element"
                                style="font-weight: 600; color: #555; margin-bottom: 10px; display: block;">Credit
                                or
                                Debit Card</label>
                            <div id="card-element" class="stripe-container">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <!-- Used to display form errors. -->
                            <div id="card-errors" role="alert" class="text-danger mt-2"
                                style="font-size: 0.9rem;"></div>
                        </div>

                        <div class="payment-actions">
                            <button type="button" class="back-btn"
                                onclick="$('#paymentModal').modal('hide'); $('#tierModal').modal('show');">
                                <i class="fas fa-arrow-left"></i> Change Member Tier
                            </button>
                            <button type="button" class="pay-btn" id="confirm-payment-btn">
                                Complete Payment <i class="fas fa-lock"
                                    style="font-size: 0.8em; margin-left: 5px;"></i>
                            </button>
                        </div>
                        <div style="text-align: center; margin-top: 20px; color: #999; font-size: 0.8rem;">
                            <i class="fas fa-shield-alt"></i> Payments are secure and encrypted.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $(document).ready(function() {
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            var elements = stripe.elements();
            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            var card = elements.create('card', {
                style: style
            });
            card.mount('#card-element');

            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Reset validation errors and promo code when modals are opened/closed
            $('#tierModal').on('show.bs.modal shown.bs.modal', function() {
                // Clear all validation errors when tier modal opens
                $('.error').remove();
                $('.input, .form-control, select').removeClass('is-invalid');
            });

            $('#paymentModal').on('show.bs.modal shown.bs.modal', function() {
                // Clear validation errors
                $('.error').remove();
                $('.input, .form-control, select').removeClass('is-invalid');

                // Reset promo code feedback (but keep the value if already validated)
                // Only clear if user hasn't validated yet
                if (!promoCodeData) {
                    $('#promo-code-feedback').html('');
                }
            });

            // When going back from payment to tier modal, reset promo validation state
            $('.back-btn').on('click', function() {
                // Clear promo code validation state (user can re-validate after choosing new tier)
                $('#promo-code-feedback').html('');
                var promoCode = $('#promo_code').val().trim();
                $('#validate-promo-btn').text('Apply').prop('disabled', promoCode === '');
            });

            $('#login-form').on('submit', function(e) {
                if ($(this).data('final-submit') === true) {
                    return true;
                }
                // If signature validation failed in previous handler, stop here
                if (e.isDefaultPrevented()) {
                    return;
                }

                var selectedPricingType = $('#tier_pricing_type').val() || 'amount';

                // If token-tier and agreement already accepted, submit without Stripe
                if ($('#tier_id').length > 0 && selectedPricingType === 'token') {
                    if ($('#agree_accepted').val() === '1') {
                        return true;
                    }

                    // Tier selected but agreement not accepted yet
                    e.preventDefault();
                    $('#tokenAgreeModal').modal('show');
                    return false;
                }

                // Amount-based tiers: if we already have stripeToken, let it submit
                if ($('#tier_id').length > 0 && $('#stripeToken').length > 0) {
                    return true;
                }

                e.preventDefault();

                // Apply Signature
                if ($('#signature-data').val() === '') {
                    if (!document.getElementById('login-form').checkValidity()) {
                        document.getElementById('login-form').reportValidity();
                        return;
                    }
                }

                // Perform Backend Validation via AJAX
                var form = $(this);
                // Create FormData for file uploads (if any) and regular data
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('register.validate') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).text('Validating...');
                    },
                    success: function(response) {
                        $('button[type="submit"]').prop('disabled', false).text('Register');
                        if (response.status === true) {
                            // Validation Success, Show Tier Modal
                            // Check if Tier is selected
                            if ($('#tier_id').length === 0) {
                                $('#tierModal').modal('show');
                            } else {
                                // Tier selected; token tiers need agreement, amount tiers need payment
                                var type = $('#tier_pricing_type').val() || 'amount';
                                if (type === 'token') {
                                    $('#tokenAgreeModal').modal('show');
                                } else {
                                    $('#paymentModal').modal('show');
                                }
                            }
                        } else {
                            // Validation Failed
                            var errors = response.errors;

                            // Clear all previous error messages
                            $('.error').remove();
                            $('.input, .form-control, select').removeClass('is-invalid');

                            // Display errors inline next to fields
                            $.each(errors, function(field, messages) {
                                var $field = $('[name="' + field + '"]');
                                if ($field.length) {
                                    $field.addClass('is-invalid');
                                    var errorHtml =
                                        '<div class="error" style="color:red; font-size: 13px; margin-top: 5px;">' +
                                        messages[0] + '</div>';
                                    $field.closest('.login-username, .form-group')
                                        .append(errorHtml);
                                }
                            });

                            // Scroll to first error
                            var firstError = $('.is-invalid').first();
                            if (firstError.length) {
                                $('html, body').animate({
                                    scrollTop: firstError.offset().top - 100
                                }, 500);
                            }

                            // Show summary alert
                            if (typeof swal !== 'undefined') {
                                swal({
                                    title: 'Validation Error',
                                    text: 'Please check the highlighted fields and correct the errors.',
                                    type: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(
                                    'Please check the highlighted fields and correct the errors.'
                                );
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $('button[type="submit"]').prop('disabled', false).text('Register');
                        alert('An error occurred during validation. Please try again.');
                    }
                });
            });

            // Clear error state when user starts typing
            $('input, select, textarea').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).closest('.login-username, .form-group').find('.error').remove();
            });

            // Handle Tier Selection
            $('.select-tier-btn').click(function() {
                var tierId = $(this).data('id');
                var cost = parseFloat($(this).data('cost'));
                var pricingType = $(this).data('pricing-type') || 'amount';
                var tierName = $(this).data('tier-name') || 'Tier';
                var agree = $(this).data('agree-description') || '';

                // Remove existing input if any
                $('#tier_id').remove();
                $('<input>').attr({
                    type: 'hidden',
                    id: 'tier_id',
                    name: 'tier_id',
                    value: tierId
                }).appendTo('#login-form');

                $('#tier_pricing_type').remove();
                $('<input>').attr({
                    type: 'hidden',
                    id: 'tier_pricing_type',
                    name: 'tier_pricing_type',
                    value: pricingType
                }).appendTo('#login-form');

                // Reset previous state
                $('#agree_accepted').remove();
                $('#stripeToken').remove();

                $('#tierModal').modal('hide');

                if (pricingType === 'token') {
                    $('#tokenAgreeModalTitle').text(tierName + ' Tier - Agreement');
                    $('#tokenAgreeModalBody').text(agree);
                    $('#tokenAgreeModal').modal('show');
                    return;
                }

                if (cost > 0) {
                    $('#payment-amount-display').text('$' + cost.toFixed(2));
                    $('#paymentModal').modal('show');
                    // Re-mount card to ensure it renders correctly if modal was hidden
                    // card.unmount(); card.mount('#card-element'); // Sometimes needed
                } else {
                    // Free Tier
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'stripeToken',
                        name: 'stripeToken',
                        value: 'free_tier'
                    }).appendTo('#login-form');
                    $('#login-form').data('final-submit', true);
                    $('#login-form').submit();
                }
            });

            // Token agreement buttons
            $('#token-agree-reject-btn').on('click', function() {
                $('#agree_accepted').remove();
                $('#tokenAgreeModal').modal('hide');
                $('#tierModal').modal('show');
            });

            $('#token-agree-accept-btn').on('click', function() {
                $('#agree_accepted').remove();
                $('<input>').attr({
                    type: 'hidden',
                    id: 'agree_accepted',
                    name: 'agree_accepted',
                    value: '1'
                }).appendTo('#login-form');
                $('#tokenAgreeModal').modal('hide');
                $('#login-form').data('final-submit', true);
                $('#login-form').submit();
            });

            // Handle Payment
            $('#confirm-payment-btn').click(function() {
                // Disable button to prevent multiple clicks
                $(this).prop('disabled', true).text('Processing...');

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $('#card-errors').text(result.error.message);
                        $('#confirm-payment-btn').prop('disabled', false).text('Pay Now');
                    } else {
                        // Send the token to your server.
                        $('#stripeToken').remove();
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'stripeToken',
                            name: 'stripeToken',
                            value: result.token.id
                        }).appendTo('#login-form');

                        // Add promo code if validated
                        $('#promo_code_input').remove();
                        if (promoCodeData && promoCodeData.code) {
                            $('<input>').attr({
                                type: 'hidden',
                                id: 'promo_code_input',
                                name: 'promo_code',
                                value: promoCodeData.code
                            }).appendTo('#login-form');
                        }

                        $('#paymentModal').modal('hide');
                        $('#login-form').data('final-submit', true);
                        $('#login-form').submit();
                    }
                });
            });

            // Promo Code Validation System
            var promoCodeData = null;
            var selectedTierId = null;
            var originalAmount = 0;
            var promoWarningTimeout = null; // Track delayed warning message

            // Enable validate button when promo code is entered
            $('#promo_code').on('input', function() {
                var code = $(this).val().trim();
                if (code.length > 0) {
                    $('#validate-promo-btn').prop('disabled', false);
                    // Reset promo data if code changed
                    if (promoCodeData && promoCodeData.code !== code) {
                        promoCodeData = null;
                        $('#promo-code-feedback').html('');
                    }
                } else {
                    $('#validate-promo-btn').prop('disabled', true);
                    promoCodeData = null;
                    $('#promo-code-feedback').html('');
                }
            });

            // Apply Promo Code
            $('#validate-promo-btn').on('click', function() {
                var code = $('#promo_code').val().trim();
                if (!code) return;

                // Clear any pending warning timeout
                if (promoWarningTimeout) {
                    clearTimeout(promoWarningTimeout);
                    promoWarningTimeout = null;
                }

                var btn = $(this);
                btn.prop('disabled', true).text('Applying...');

                $.ajax({
                    url: '{{ route('user.promo-codes.validate') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        code: code,
                        tier_id: selectedTierId
                    },
                    success: function(response) {
                        console.log('Promo validation response:', response);

                        if (response.valid) {
                            promoCodeData = {
                                code: code,
                                discount: response.discount,
                                final_price: response.final_price,
                                is_percentage: response.is_percentage,
                                discount_amount: response.discount_amount
                            };

                            var discountText = response.is_percentage ?
                                response.discount_amount + '%' :
                                '$' + parseFloat(response.discount_amount).toFixed(2);

                            $('#promo-code-feedback').html(
                                '<div style="color: #4caf50; background: #e7f4e7; padding: 8px; border-radius: 4px; border-left: 3px solid #4caf50;">' +
                                '<i class="fa fa-check-circle"></i> <strong>Valid!</strong> You\'ll save ' +
                                discountText + ' on this membership.' +
                                '</div>'
                            );

                            // Update payment modal if already open
                            updatePaymentDisplay();

                            console.log('Success message should be visible now');
                        } else {
                            promoCodeData = null;
                            $('#promo-code-feedback').html(
                                '<div style="color: #f44336; background: #ffebee; padding: 8px; border-radius: 4px; border-left: 3px solid #f44336;">' +
                                '<i class="fa fa-times-circle"></i> ' + (response.message ||
                                    'Invalid promo code') +
                                '</div>'
                            );
                        }

                        // Reset button state
                        btn.prop('disabled', false).text('Apply');
                        console.log('Button reset to: Apply');
                    },
                    error: function(xhr) {
                        promoCodeData = null;
                        var errorMsg = 'Unable to validate promo code. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#promo-code-feedback').html(
                            '<div style="color: #f44336; background: #ffebee; padding: 8px; border-radius: 4px; border-left: 3px solid #f44336;">' +
                            '<i class="fa fa-times-circle"></i> ' + errorMsg +
                            '</div>'
                        );
                        btn.prop('disabled', false).text('Apply');
                    }
                });
            });

            // Remove promo code
            $('#remove-promo').on('click', function() {
                promoCodeData = null;
                $('#promo_code').val('');
                $('#promo-code-feedback').html('');
                $('#validate-promo-btn').prop('disabled', true);
                updatePaymentDisplay();
            });

            // Update payment display with promo code
            function updatePaymentDisplay() {
                if (originalAmount > 0) {
                    if (promoCodeData && promoCodeData.discount > 0) {
                        $('#original-amount-display').text('$' + originalAmount.toFixed(2));
                        $('#discount-amount-display').text('-$' + promoCodeData.discount.toFixed(2));
                        $('#payment-amount-display').text('$' + promoCodeData.final_price.toFixed(2));
                        $('#applied-promo-code').text(promoCodeData.code);
                        $('#original-amount-section').show();
                        $('#promo-discount-section').show();
                    } else {
                        $('#payment-amount-display').text('$' + originalAmount.toFixed(2));
                        $('#original-amount-section').hide();
                        $('#promo-discount-section').hide();
                    }
                }
            }

            // Modified tier selection to track tier and validate promo
            $('.select-tier-btn').off('click').on('click', function() {
                var tierId = $(this).data('id');
                var cost = parseFloat($(this).data('cost'));
                var pricingType = $(this).data('pricing-type') || 'amount';
                var tierName = $(this).data('tier-name') || 'Tier';
                var agree = $(this).data('agree-description') || '';

                selectedTierId = tierId;
                originalAmount = cost;

                // Remove existing input if any
                $('#tier_id').remove();
                $('<input>').attr({
                    type: 'hidden',
                    id: 'tier_id',
                    name: 'tier_id',
                    value: tierId
                }).appendTo('#login-form');

                $('#tier_pricing_type').remove();
                $('<input>').attr({
                    type: 'hidden',
                    id: 'tier_pricing_type',
                    name: 'tier_pricing_type',
                    value: pricingType
                }).appendTo('#login-form');

                // Reset previous state
                $('#agree_accepted').remove();
                $('#stripeToken').remove();

                // Clear promo code validation if tier changed
                // User needs to re-validate promo for new tier
                var promoCode = $('#promo_code').val().trim();
                if (promoCodeData || promoCode) {
                    // Reset validation data
                    promoCodeData = null;

                    // Clear all promo visual feedback
                    $('#promo-code-feedback').html('');
                    $('#promo-discount-section').hide();
                    $('#original-amount-section').hide();
                    $('#applied-promo-code').text('');

                    // If user had entered a code, show message to re-validate
                    if (promoCode) {
                        // Clear any existing timeout first
                        if (promoWarningTimeout) {
                            clearTimeout(promoWarningTimeout);
                        }

                        promoWarningTimeout = setTimeout(function() {
                            $('#promo-code-feedback').html(
                                '<div style="color: #ff9800; background: #fff3e0; padding: 8px; border-radius: 4px; border-left: 3px solid #ff9800;">' +
                                '<i class="fa fa-info-circle"></i> You changed the membership tier. Please validate your promo code again for this tier.' +
                                '</div>'
                            );
                            promoWarningTimeout = null; // Clear reference after execution
                        }, 500); // Delay so it shows after modal opens
                    }

                    // Reset validate button text and state
                    $('#validate-promo-btn').text('Apply').prop('disabled', !promoCode);
                }


                $('#tierModal').modal('hide');

                if (pricingType === 'token') {
                    $('#tokenAgreeModalTitle').text(tierName + ' Tier - Agreement');
                    $('#tokenAgreeModalBody').text(agree);
                    $('#tokenAgreeModal').modal('show');
                    return;
                }

                if (cost > 0) {
                    updatePaymentDisplay();
                    $('#paymentModal').modal('show');
                } else {
                    // Free Tier
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'stripeToken',
                        name: 'stripeToken',
                        value: 'free_tier'
                    }).appendTo('#login-form');
                    $('#login-form').data('final-submit', true);
                    $('#login-form').submit();
                }
            });
        });
    </script>
</body>

</html>
