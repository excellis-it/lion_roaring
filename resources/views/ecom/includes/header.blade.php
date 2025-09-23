@php
    use App\Helpers\Helper;
@endphp
<div class="top-bar-header">
    <div class="container-fluid">
        <div class="top-bar-wrepper">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="top-bar-text">
                        <span class="d-block font-bold" onclick="changeLocation()" style="cursor: pointer;">My
                            Location </span>

                        @if (Auth::check())
                            @if (Auth::user()->location_lat || Auth::user()->location_lng)
                                <span onclick="changeLocation()" class="location-icon location_btn"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="{{ Auth::user()->location_address }}">
                                    <i class="fa fa-map-marker text-danger me-2"
                                        aria-hidden="true"></i>{{ Str::limit(Auth::user()->location_address ?? '', 20, '...') }}
                                </span>
                            @endif
                        @else
                            @if (session()->has('location_lat') && session()->has('location_lng'))
                                <span onclick="changeLocation()" class="location-icon location_btn"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="{{ session('location_address') }}">
                                    <i class="fa fa-map-marker text-danger me-2"
                                        aria-hidden="true"></i>{{ Str::limit(session('location_address') ?? '', 20, '...') }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="input-group border-0">

                        <input id="global-search" class="form-control" type="search" autocomplete="off"
                            placeholder="Search products..." aria-autocomplete="list" aria-expanded="false"
                            aria-haspopup="listbox">
                        <span class="input-group-text"
                            style="background-color: #ff6632;border-radius: 0px 5px 5px 0px;"><i
                                class="fa fa-search"></i></span>
                        <div id="search-suggestions" class="search-suggestions d-none" role="listbox"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="right_login">
                        <div class="d-flex align-items-center justify-content-end">
                            {{-- <a href="{{ route('user.profile') }}" class="back_main">back to main page</a> --}}

                            <a href="{{ route('e-store.wishlist') }}" class="shoping_cart"><i
                                    class="fa-solid fa-heart"></i></a>

                            <a href="{{ route('e-store.cart') }}" class="shoping_cart"><i class="fa fa-shopping-cart"
                                    aria-hidden="true"></i><span class="cart_count">{{ Helper::cartCount() }}</span></a>
                            <div class="profile_dropdown">
                                <div class="dropdown">
                                    <button class="profile_img_round dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span>
                                            @if (Auth::user() && Auth::user()->profile_picture)
                                                <img src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                                    alt="" />
                                            @else
                                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}"
                                                    alt="" />
                                            @endif
                                        </span>
                                    </button>
                                    @if (Auth::user())
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('e-store.wishlist') }}">My
                                                    Wishlist</a>
                                            <li><a class="dropdown-item" href="{{ route('user.profile') }}">Profile</a>
                                            </li>
                                            <li><a class="dropdown-item" href="{{ route('e-store.my-orders') }}">My
                                                    Orders</a></li>
                                            <li><a class="dropdown-item"
                                                    href="{{ route('user.change.password') }}">Change
                                                    Password</a></li>
                                            <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                            </li>
                                        </ul>
                                    @else
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0);"
                                                    data-bs-toggle="modal" data-bs-target="#loginModalEstore"
                                                    href="{{ route('login') }}">Login</a></li>
                                            <li><a class="dropdown-item" href="javascrip:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#registerModalEstore">Register</a>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <!-- <div class="icon_c ms-2">
                                <a href="" class=""><i class="fa-solid fa-user"></i></a>
                              </div>
                              <div class="icon_c ms-2">
                                <a href="" class="add_cart_active"><i class="fa-solid fa-cart-shopping"></i></a>
                              </div>
                              <div class="icon_c ms-2">
                                <a href="" class="add_cart_active"><i class="fa-solid fa-heart"></i></a>
                              </div> -->
                        </div>
                    </div>
                </div>
            </div>




        </div>
    </div>
</div>
<div class="main_menu_hdr page-header">
    <div class="container-fluid">
        <div class="main_menu">
            <div class="navigation navbar">
                <div class="left_top order-1 order-lg-1">
                    <div class="logo">
                        <a href="{{ route('e-store') }}" class="">
                            <img src="{{ asset('ecom_assets/images/estore_logo.png') }}" alt="" />
                        </a>
                    </div>
                </div>

                <div class="right_btm order-4 order-lg-3">
                    <div id="cssmenu">
                        <ul>
                            <li><a href="{{ route('e-store') }}">Home</a></li>
                            <li><a href="{{ route('e-store') }}">Category</a>
                                {!! Helper::renderCategoryTree() !!}
                            </li>
                            <li><a href="{{ route('e-store.all-products') }}">Shop</a></li>
                            <li><a href="{{ route('e-store.contact') }}">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <!--<div class="right_top order-3 order-lg-4">-->

                <!--</div>-->
            </div>
        </div>
    </div>
</div>

<!-- Location Modal -->
<div class="modal location-modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Allow Location Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>We need your location to provide better service near you.</p>
                <button id="getLocationBtn" class="back_main">Share My Location</button>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade modal_code" id="loginModalEstore" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body login_bg_sec border-top-0">
                <div class="heading_hp">
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
                    <h4 class="text-center">Sign on to Lion Roaring E-store</h4>
                    <form name="login-form" id="sign-in-form" action="{{ route('login.check') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="user_login" class="form-label">Username or Email Address</label>
                            <input type="text" name="user_name" id="user_login" autocomplete="username"
                                @if (isset($_COOKIE['email_user_name'])) value="{{ $_COOKIE['email_user_name'] }}" @endif
                                class="form-control" value="" size="20">
                            <div class="text-danger"></div>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="user_password" class="form-label">Password</label>
                            <input type="password" name="password" id="user_password"
                                autocomplete="current-password" spellcheck="false" class="form-control"
                                @if (isset($_COOKIE['password'])) value="{{ $_COOKIE['password'] }}" @endif
                                size="20">
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3" id="eye-button-1"
                                style="margin-top: 12px; cursor: pointer;">
                                <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                            </span>
                            <div class="text-danger"></div>
                        </div>
                        <div class="mb-3 d-flex justify-content-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="d-grid">
                            <input type="submit" name="wp-submit" id="login-submit" class="back_main"
                                value="Log In">
                            <input type="hidden" name="redirect_to" value="">
                        </div>
                    </form>
                    <p class="text-center join_member mt-3">

                        <a href="javascrip:void(0);" data-bs-toggle="modal" data-bs-target="#registerModalEstore">Not
                            a member? Register now</a>
                    </p>
                    <p class="text-center join_member">

                        <a href="{{ route('member-privacy-policy') }}" class="login_privacy text-dark">Privacy,
                            Cookies, and Legal </a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Modal End -->

<!-- Email OTP Modal -->
<div class="modal fade" id="otpModalEstore" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
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
                        <input placeholder="Enter Code" type="text" class="form-control input" id="otp"
                            name="otp" maxlength="4" required
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

{{-- Register Modal Start --}}
<div class="modal fade" id="registerModalEstore" tabindex="-1" aria-labelledby="registerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Register</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="register-form-estore" action="{{ route('estore.register') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="register-first-name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="register-first-name" name="first_name">
                    </div>
                    <div class="mb-3">
                        <label for="register-middle-name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="register-middle-name" name="middle_name">
                    </div>
                    <div class="mb-3">
                        <label for="register-last-name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="register-last-name" name="last_name">
                    </div>
                    <div class="mb-3">
                        <label for="register-email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="register-email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="register-phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="register-phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label">Password</label>
                        <input autocomplete="new-password" type="password" class="form-control"
                            id="register-password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="register-confirm-password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="register-confirm-password"
                            name="password_confirmation">
                    </div>
                    <button type="submit" class="back_main w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Register Modal End --}}


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.getElementById("time_zone").value = timezone;
            console.log("User's Timezone:", timezone);
        });

        // togglePassword
        const togglePassword = document.querySelector('#togglePassword');
        togglePassword.addEventListener('click', function() {
            const passwordField = document.querySelector('#user_password');
            const passwordFieldType = passwordField.getAttribute('type');
            passwordField.setAttribute('type', passwordFieldType === 'password' ? 'text' : 'password');
        });
    </script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // changeLocation
        function changeLocation() {
            // Your logic to change location
            var locationModal = new bootstrap.Modal(document.getElementById('locationModal'), {
                backdrop: 'static',
                keyboard: false
            });
            locationModal.show();

        }
    </script>
    @if (Auth::check())
        @if (is_null(Auth::user()->location_lat) || is_null(Auth::user()->location_lng))
            <script>
                var locationModal = new bootstrap.Modal(document.getElementById('locationModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                locationModal.show();
            </script>
        @endif
    @else
        @if (!session()->has('location_lat') && !session()->has('location_lng'))
            <script>
                var locationModal = new bootstrap.Modal(document.getElementById('locationModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                locationModal.show();
            </script>
        @endif
    @endif

    @if (session()->has('location_lat') && session()->has('location_lng'))
        <script>
            var locationLat = {{ session('location_lat') }};
            var locationLng = {{ session('location_lng') }};
            //  alert("Location is already set. and data is: " + locationLat + ", " + locationLng);
        </script>
    @endif


    <script>
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    let lat = position.coords.latitude;
                    let lng = position.coords.longitude;

                    // Send via AJAX to Laravel
                    $.ajax({
                        url: "{{ route('user-update.location') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            latitude: lat,
                            longitude: lng
                        },
                        success: function(response) {
                            console.log("Location updated:", response);
                            toastr.success("Location saved successfully!");
                            var locationModal = bootstrap.Modal.getInstance(document
                                .getElementById('locationModal'));
                            locationModal.hide();
                            window.location.reload();
                        },
                        error: function(xhr) {
                            console.error("Error:", xhr.responseText);
                            alert("Failed to save location");
                        }
                    });

                }, function(error) {
                    alert("Location access denied.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });
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
                                $('#otpModalEstore').modal('show');
                            } else {
                                //  window.location.href = response.redirect;
                                window.location.reload();
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
                            // window.location.href = response.redirect;
                            window.location.reload();
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

    {{-- // register user --}}
    <script>
        $(document).ready(function() {
            $('#register-form-estore').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var url = $(this).attr('action');
                $.ajax({
                    url: url,
                    type: $(this).attr('method'),
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Optionally redirect or perform other actions
                            // setTimeout(() => {
                            // //    window.location.reload();
                            // }, 1000);
                            // open login modal with set the email
                            $('#registerModalEstore').modal('hide');
                            $('#user_password').val('');
                            $('#user_login').val(response.email);
                            $('#loginModalEstore').modal('show');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    }
                });
            });
        });
    </script>
    <script>
        (function() {
            const input = document.getElementById('global-search');
            const box = document.getElementById('search-suggestions');
            if (!input) return;
            let timer, controller;

            function hideBox() {
                box.classList.add('d-none');
                box.setAttribute('aria-expanded', 'false');
            }

            input.addEventListener('input', function() {
                const q = this.value.trim();
                clearTimeout(timer);
                if (!q) {
                    box.innerHTML = '';
                    hideBox();
                    return;
                }
                timer = setTimeout(() => {
                    if (controller) controller.abort();
                    controller = new AbortController();
                    fetch("{{ route('e-store.live-search') }}?q=" + encodeURIComponent(q), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            signal: controller.signal
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (!Array.isArray(data) || !data.length) {
                                box.innerHTML =
                                    '<div class="ss-empty p-2 text-muted">No matches</div>';
                            } else {
                                box.innerHTML = data.map(p => `
<a class="ss-item d-flex align-items-center" href="${p.url}" role="option">
    <img src="${p.image}" alt="${p.name}">
    <div class="flex-grow-1">
        <div class="ss-name">${p.name}</div>
        <div class="ss-price">$${p.price}</div>
    </div>
</a>`).join('');
                            }
                            box.classList.remove('d-none');
                            box.setAttribute('aria-expanded', 'true');
                        })
                        .catch(() => {
                            /* ignore abort */
                        });
                }, 250);
            });

            document.addEventListener('click', e => {
                if (!box.contains(e.target) && e.target !== input) hideBox();
            });

            input.addEventListener('keydown', e => {
                const items = [...box.querySelectorAll('.ss-item')];
                if (!items.length) return;
                const current = document.activeElement;
                let idx = items.indexOf(current);
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    idx = (idx + 1) % items.length;
                    items[idx].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    idx = (idx - 1 + items.length) % items.length;
                    items[idx].focus();
                } else if (e.key === 'Escape') {
                    hideBox();
                }
            });
        })();
    </script>
@endpush
