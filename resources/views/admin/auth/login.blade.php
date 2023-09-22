<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta content="width=device-width,  initial-scale=1,  maximum-scale=1,  shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Admin Login</title>
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
</head>

<body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">

                <div class="account-logo">
                    <a href="admin-dashboard.html"><img src="{{ asset('admin_assets/img/logo.png') }}"
                            alt="Dreamguy's Technologies"></a>
                </div>

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Login</h3>
                        <p class="account-subtitle">Access to our dashboard</p>

                        <form action="{{ route('admin.login.check') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control" name="email" id="inputEmailAddress"
                                    placeholder="Email Address">
                                @if ($errors->has('email'))
                                    <div class="error" style="color:red;">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label>Password</label>
                                    </div>
                                    {{-- <div class="col-auto">
                                                <a class="text-muted" href="forgot-password.html">
                                                    Forgot password?
                                                </a>
                                            </div> --}}
                                </div>
                                <div class="position-relative" id="show_hide_password">
                                    <input type="password" class="form-control border-end-0" name="password"
                                        id="inputChoosePassword" placeholder="Enter Password">
                                    <a href="javascript:;" class=""><span class="fa fa-eye-slash"
                                            id="toggle-password"></span></a>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="error" style="color:red;">{{ $errors->first('password') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Login</button>
                            </div>
                            <div class="account-footer">
                                <p><a href="{{ route('admin.forget.password.show') }}">Forgot Password?</a></p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
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
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });
    });
</script>

</html>
