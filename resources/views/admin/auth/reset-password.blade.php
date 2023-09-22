<!DOCTYPE html>
<html lang="en">

<head>
    <meta content="width=device-width,  initial-scale=1,  maximum-scale=1,  shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Reset Password - {{ env('APP_NAME') }} Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('admin_assets/bootstrap-5.3/css/bootstrap.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/phosphor-icons"></script>
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css" /> -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/app.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}" />

    <link rel="stylesheet" href="{{ asset('admin_assets/css/morris.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .mainDiv {
            display: flex;
            min-height: 100%;
            align-items: center;
            justify-content: center;
            /* background-color: #f9f9f9; */
        }

        .cardStyle {
            width: 500px;
            border-color: white;
            background: #fff;
            padding: 36px 0;
            border-radius: 4px;
            margin: 30px 0;
            box-shadow: 0px 0 2px 0 rgba(0, 0, 0, 0.25);
        }

        #signupLogo {
            max-height: 100px;
            margin: auto;
            display: flex;
            flex-direction: column;
        }

        .formTitle {
            font-weight: 600;
            margin-top: 20px;
            color: #2F2D3B;
            text-align: center;
        }

        .inputLabel {
            /* font-size: 12px; */
            color: #555;
            margin-bottom: 6px;
            margin-top: 24px;
        }

        /* .inputDiv {
            width: 70%;
            display: flex;
            flex-direction: column;
            margin: auto;
        } */

        input {
            height: 40px;
            font-size: 16px;
            border-radius: 4px;
            border: none;
            border: solid 1px #ccc;
            padding: 0 11px;
        }

        input:disabled {
            cursor: not-allowed;
            border: solid 1px #eee;
        }

        .buttonWrapper {
            margin-top: 40px;
        }

        .submitButton {
            width: 70%;
            height: 40px;
            margin: auto;
            display: block;
            color: #fff;
            background-color: #065492;
            border-color: #065492;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.12);
            box-shadow: 0 2px 0 rgba(0, 0, 0, 0.035);
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .submitButton:disabled,
        button[disabled] {
            border: 1px solid #cccccc;
            background-color: #cccccc;
            color: #666666;
        }

        #loader {
            position: absolute;
            z-index: 1;
            margin: -2px 0 0 10px;
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #666666;
            width: 14px;
            height: 14px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        .field-icon {
            float: right;
            margin-left: -25px;
            margin-top: -25px;
            position: relative;
            z-index: 2;
        }

        .container {
            padding-top: 50px;
            margin: auto;
        }

        .eye {
            float: right;
            margin-left: -25px;
            margin-top: -30px;
            position: relative;
            z-index: 2;
            padding: 0px 30px 0px 0px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="mainDiv">
        <div class="cardStyle">
            <form action="{{ route('admin.change.password') }}" method="post" name="signupForm" id="signupForm">
                @csrf

                <img src="{{ asset('admin_assets/img/logo.png') }}" id="signupLogo" />

                <h2 class="formTitle">
                    Reset Password
                </h2>
                <input type="hidden" name="id" value="{{ $id }}">
                <div class="form-wrap" >
                    <div class="form-group">
                        <label class="col-md-4 control-label">Password</label>
                        <div class="col-md-12">
                            <input type="password" id="password-field1" class="form-control" name="password"
                                value="">
                            <span toggle="#password-field1" class="fa fa-fw fa-eye field-icon toggle-password eye"
                                id="ds"></span>
                            @if ($errors->has('password'))
                                <div class="error" style="color:red;">{{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Confirm Password</label>
                        <div class="col-md-12">
                            <input id="password-field" type="password" class="form-control" name="confirm_password"
                                value="secret">
                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password eye"
                                id="ps"></span>
                            @if ($errors->has('confirm_password'))
                                <div class="error" style="color:red;">{{ $errors->first('confirm_password') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="buttonWrapper">
                    <button type="submit" id="submitButton" onclick="validateSignupForm()"
                        class="submitButton btn btn-primary">
                        <span>Continue</span>
                        {{-- <span id="loader"></span> --}}
                    </button>
                </div>

            </form>
        </div>
    </div>
</body>


<script src="{{ asset('admin_assets/js/jquery-3.4.1.min.js') }}"></script>
<!-- <script src="js/jquery.min.js"></script> -->
<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> -->
<!-- <script src="js/bootstrap.min.js" async=""></script> -->
<script src="{{ asset('admin_assets/bootstrap-5.3/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/raphael-min.js') }}"></script>
<script src="{{ asset('admin_assets/js/morris.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/Chart.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/custom.js') }}" async=""></script>
<script src="{{ asset('admin_assets/js/app.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/scripts.js') }}" async=""></script>
<script src="{{ asset('admin_assets/js/jquery-ui.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
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
<script src="{{ asset('admin_assets/js/app.js') }}"></script>
<script>
    $("#ds").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $("#ps").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>

</html>
