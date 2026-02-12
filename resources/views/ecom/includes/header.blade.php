@php
    use App\Helpers\Helper;
@endphp
<!--<div class="top-bar-header">-->
<!--    <div class="container-fluid">-->
<!--        <div class="top-bar-wrepper">-->
<!--            <div class="row align-items-center">-->
<!--                <div class="col-lg-4">-->

<!--                </div>-->
<!--                <div class="col-lg-4">-->
<!--                    <div class="input-group border-0">-->

<!--                        <input id="global-search" class="form-control" type="search" autocomplete="off"-->
<!--                            placeholder="Search products..." aria-autocomplete="list" aria-expanded="false"-->
<!--                            aria-haspopup="listbox">-->
<!--                        <span class="input-group-text"-->
<!--                            style="background-color: #ff6632;border-radius: 0px 5px 5px 0px;"><i-->
<!--                                class="fa fa-search"></i></span>-->
<!--                        <div id="search-suggestions" class="search-suggestions d-none" role="listbox"></div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-lg-4">-->

<!--                </div>-->
<!--            </div>-->




<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<div class="main_menu_hdr page-header">
    <div class="container-fluid">
        <div class="main_menu">
            <div class="navigation navbar">
                <div class="left_top">
                    <div class="logo">
                        <a href="{{ route('e-store') }}" class="">
                            <img src="{{ Helper::estoreHeaderLogoUrl() }}" alt="" />
                        </a>
                    </div>

                    <div class="top-bar-text">
                        <span class="d-block font-bold" onclick="changeLocation()" style="cursor: pointer;">Near My
                            Location</span>

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

                <div class="menu-with-login-right">
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
                    <div class="right_top order-3 order-lg-4">
                        <div class="right_login">
                            <div class="search-toggle">
                                <button class="search-icon icon-search"><i class="fa fa-fw fa-search"></i></button>
                                <button class="search-icon icon-close"><i class="fa fa-fw  fa-close"></i></button>
                            </div>
                            <div class="search-container">

                                <input type="text" name="q" id="search-terms" placeholder="Search products..."
                                    aria-autocomplete="list" aria-expanded="false" aria-haspopup="listbox"
                                    autocomplete="off" />
                                <button type="button" name="submit" value="Go" class="search-icon"><i
                                        class="fa fa-fw fa-search"></i></button>

                                <div id="search-suggestions" class="search-suggestions d-none"
                                    style="position: relative !important;" role="listbox"></div>
                            </div>




                            <div class="d-flex align-items-center justify-content-end">
                                {{-- <a href="{{ route('user.profile') }}" class="back_main">back to main page</a> --}}

                                <a href="{{ route('e-store.wishlist') }}" class="shoping_cart"><i
                                        class="fa-solid fa-heart"></i></a>

                                <a href="{{ route('e-store.cart') }}" class="shoping_cart"><i
                                        class="fa fa-shopping-cart" aria-hidden="true"></i><span
                                        class="cart_count">{{ Helper::cartCount() }}</span></a>
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
                                                <li><a class="dropdown-item"
                                                        href="{{ route('e-store.wishlist') }}">My
                                                        Wishlist</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('e-store.profile') }}">Profile</a>
                                                </li>
                                                @if (Auth::user() && Auth::user()->role !== 'ESTORE_USER')
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('user.profile') }}">Go PMA Panel</a></li>
                                                @endif
                                                <li><a class="dropdown-item"
                                                        href="{{ route('e-store.my-orders') }}">My
                                                        Orders</a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('e-store.change-password') }}">Change
                                                        Password</a></li>
                                                <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                                </li>
                                            </ul>
                                        @else
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        data-bs-toggle="modal" data-bs-target="#loginModalEstore"
                                                        href="{{ route('login') }}">Login</a></li>
                                                {{-- <li><a class="dropdown-item" href="javascrip:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#registerModalEstore">Register</a>
                                                </li> --}}
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
</div>

<!-- Location Modal -->
<div class="modal location-modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Choose Delivery Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="lr-address-modal">
                    <div class="row g-0">
                        <div class="col-md-4 lr-left">
                            <div class="lr-left-header">
                                <div class="fw-semibold">Saved Addresses</div>
                                <div class="text-muted small">Manage your delivery locations</div>
                            </div>
                            <div class="lr-left-body">
                                @if (Auth::check())
                                    <div id="savedAddresses"></div>
                                    <div id="savedAddressesEmpty" class="text-muted small d-none">No saved addresses
                                        yet.</div>
                                    <button id="addNewAddressBtn" type="button"
                                        class="btn btn-light red_btn w-100 mt-2">
                                        <span> + Add New Address</span>
                                    </button>
                                    <div class="text-muted small mt-2" hidden>Tip: select radio to set default.</div>
                                @else
                                    <div class="text-muted small">Login to manage multiple addresses.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8 lr-right">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-semibold" id="lrFormTitle">Add New Address</div>
                                <span id="locationBusy" class="text-muted small d-none">Working…</span>
                            </div>

                            <div class="mb-2">
                                <input type="text" id="lr_address_search" class="form-control"
                                    placeholder="Search address" autocomplete="off" />
                            </div>

                            <div class="lr-map-wrap mb-3">
                                <div id="lr-map" class="lr-map"></div>
                                <button id="getLocationBtn" type="button" class="btn btn-light lr-map-action">
                                    Use Current Location
                                </button>
                            </div>

                            <input type="hidden" id="lr_lat" value="">
                            <input type="hidden" id="lr_lng" value="">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Street Address</label>
                                    <input type="text" id="lr_address_line1" class="form-control"
                                        placeholder="House no, street" aria-required="true" required />
                                    <div class="invalid-feedback">Street address is required.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Apartment / Landmark (optional)</label>
                                    <input type="text" id="lr_address_line2" class="form-control"
                                        placeholder="Apartment, suite, landmark" />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" id="lr_city" class="form-control" placeholder="City" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State / Province</label>
                                    <input type="text" id="lr_state" class="form-control" placeholder="State" />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Zip Code</label>
                                    <input type="text" id="lr_postal_code" class="form-control" placeholder="ZIP"
                                        aria-required="true" required />
                                    <div class="invalid-feedback">ZIP / postal code is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <input type="text" id="lr_country" class="form-control" placeholder="Country"
                                        aria-required="true" required />
                                    <div class="invalid-feedback">Country is required.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Address Label</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lr_label"
                                                id="lr_label_home" value="Home" checked>
                                            <label class="form-check-label" for="lr_label_home">Home</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lr_label"
                                                id="lr_label_work" value="Work">
                                            <label class="form-check-label" for="lr_label_work">Work</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lr_label"
                                                id="lr_label_other" value="Other">
                                            <label class="form-check-label" for="lr_label_other">Other</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Full Address</label>
                                    <textarea id="lr_formatted_address" class="form-control" rows="2" placeholder="Auto-filled, editable"></textarea>
                                </div>

                                @if (Auth::check())
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="lr_make_default" checked>
                                            <label class="form-check-label" for="lr_make_default">Set as default
                                                delivery address</label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" class="btn btn-light btn-link"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button id="saveAddressBtn" type="button" class="btn btn-primary">Save
                                    Address</button>
                            </div>
                        </div>
                    </div>
                </div>
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
                    {{-- <p class="text-center join_member mt-3">

                        <a href="javascrip:void(0);" data-bs-toggle="modal" data-bs-target="#registerModalEstore">Not
                            a member? Register now</a>
                    </p> --}}
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
{{-- <div class="modal fade" id="registerModalEstore" tabindex="-1" aria-labelledby="registerModalLabel"
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
</div> --}}
{{-- Register Modal End --}}


@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @if (config('services.google.maps_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places">
        </script>
    @endif

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
        (function() {
            const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
            const routes = {
                store: "{{ route('user-update.location') }}",
                @if (Auth::check())
                    index: "{{ route('e-store.addresses.index') }}",
                    setDefault: "{{ route('e-store.addresses.default') }}",
                    updateTemplate: "{{ route('e-store.addresses.update', ['address' => '__ADDRESS__']) }}",
                    deleteTemplate: "{{ route('e-store.addresses.delete', ['address' => '__ADDRESS__']) }}",
                @endif
            };
            const csrf = "{{ csrf_token() }}";

            const modalEl = document.getElementById('locationModal');
            const busyEl = document.getElementById('locationBusy');
            const getLocationBtn = document.getElementById('getLocationBtn');
            const saveBtn = document.getElementById('saveAddressBtn');

            const formTitleEl = document.getElementById('lrFormTitle');
            const addNewAddressBtn = document.getElementById('addNewAddressBtn');

            const savedBox = document.getElementById('savedAddresses');
            const savedEmpty = document.getElementById('savedAddressesEmpty');

            const searchEl = document.getElementById('lr_address_search');

            const latEl = document.getElementById('lr_lat');
            const lngEl = document.getElementById('lr_lng');
            const line1El = document.getElementById('lr_address_line1');
            const line2El = document.getElementById('lr_address_line2');
            const cityEl = document.getElementById('lr_city');
            const stateEl = document.getElementById('lr_state');
            const zipEl = document.getElementById('lr_postal_code');
            const countryEl = document.getElementById('lr_country');
            const formattedEl = document.getElementById('lr_formatted_address');
            const makeDefaultEl = document.getElementById('lr_make_default');

            const labelRadios = document.querySelectorAll('input[name="lr_label"]');

            let map, marker;
            let googleGeocoder, googleAutocomplete;
            let mapProvider = null; // 'google' | 'leaflet'
            let editingId = null;
            let addressCache = [];
            let formattedTouched = false;

            function setBusy(isBusy) {
                if (!busyEl) return;
                busyEl.classList.toggle('d-none', !isBusy);
                if (getLocationBtn) getLocationBtn.disabled = isBusy;
                if (saveBtn) saveBtn.disabled = isBusy;
            }

            function setLatLng(lat, lng) {
                latEl.value = lat;
                lngEl.value = lng;
            }

            function getLatLng() {
                const lat = parseFloat(latEl.value);
                const lng = parseFloat(lngEl.value);
                return {
                    lat,
                    lng,
                    ok: Number.isFinite(lat) && Number.isFinite(lng)
                };
            }

            function getSelectedLabel() {
                const checked = document.querySelector('input[name="lr_label"]:checked');
                return checked ? checked.value : null;
            }

            function setSelectedLabel(value) {
                if (!value) return;
                const normalized = String(value).toLowerCase();
                let target = null;
                if (normalized === 'home') target = document.getElementById('lr_label_home');
                else if (normalized === 'work' || normalized === 'office') target = document.getElementById(
                    'lr_label_work');
                else target = document.getElementById('lr_label_other');
                if (target) target.checked = true;
            }

            function buildFormattedAddress() {
                const parts = [];
                const l1 = (line1El && line1El.value ? line1El.value.trim() : '');
                const l2 = (line2El && line2El.value ? line2El.value.trim() : '');
                const city = (cityEl && cityEl.value ? cityEl.value.trim() : '');
                const state = (stateEl && stateEl.value ? stateEl.value.trim() : '');
                const zip = (zipEl && zipEl.value ? zipEl.value.trim() : '');
                const country = (countryEl && countryEl.value ? countryEl.value.trim() : '');

                if (l1) parts.push(l1);
                if (l2) parts.push(l2);

                const cityState = [city, state].filter(Boolean).join(', ');
                const stateZip = [state, zip].filter(Boolean).join(' ');
                const cityStateZip = [city, stateZip].filter(Boolean).join(', ');

                if (cityStateZip) parts.push(cityStateZip);
                if (country) parts.push(country);

                return parts.filter(Boolean).join(', ');
            }

            function syncFormattedAddressFromFields() {
                if (!formattedEl) return;
                if (formattedTouched) return;
                formattedEl.value = buildFormattedAddress();
            }

            function initMap(lat, lng) {
                const hasGoogle = (typeof google !== 'undefined') && google.maps && google.maps.Map;
                if (hasGoogle) {
                    return initGoogleMap(lat, lng);
                }
                return initLeafletMap(lat, lng);
            }

            function initLeafletMap(lat, lng) {
                mapProvider = 'leaflet';
                if (map) {
                    map.setView([lat, lng], 15);
                    if (marker) marker.setLatLng([lat, lng]);
                    return;
                }

                map = L.map('lr-map', {
                    zoomControl: true
                }).setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', function() {
                    const p = marker.getLatLng();
                    setLatLng(p.lat, p.lng);
                    reverseGeocode(p.lat, p.lng);
                });

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    setLatLng(e.latlng.lat, e.latlng.lng);
                    reverseGeocode(e.latlng.lat, e.latlng.lng);
                });

                if (searchEl) {
                    searchEl.addEventListener('focus', function() {
                        toastr.info(
                            'Google Maps key not configured. You can still click the map or use current location.'
                        );
                    }, {
                        once: true
                    });
                }
            }

            function initGoogleMap(lat, lng) {
                mapProvider = 'google';

                if (!googleGeocoder) {
                    googleGeocoder = new google.maps.Geocoder();
                }

                if (!map) {
                    map = new google.maps.Map(document.getElementById('lr-map'), {
                        center: {
                            lat,
                            lng
                        },
                        zoom: 16,
                        streetViewControl: false,
                        mapTypeControl: false,
                        fullscreenControl: false,
                    });

                    marker = new google.maps.Marker({
                        position: {
                            lat,
                            lng
                        },
                        map,
                        draggable: true,
                    });

                    marker.addListener('dragend', function(e) {
                        const p = e.latLng;
                        const nlat = p.lat();
                        const nlng = p.lng();
                        setLatLng(nlat, nlng);
                        reverseGeocode(nlat, nlng);
                    });

                    map.addListener('click', function(e) {
                        const p = e.latLng;
                        const nlat = p.lat();
                        const nlng = p.lng();
                        marker.setPosition(p);
                        setLatLng(nlat, nlng);
                        reverseGeocode(nlat, nlng);
                    });

                    if (searchEl && !googleAutocomplete && google.maps.places && google.maps.places.Autocomplete) {
                        googleAutocomplete = new google.maps.places.Autocomplete(searchEl, {
                            fields: ['geometry', 'address_components', 'formatted_address', 'name'],
                            types: ['geocode'],
                        });

                        googleAutocomplete.addListener('place_changed', function() {
                            const place = googleAutocomplete.getPlace();
                            if (!place || !place.geometry || !place.geometry.location) {
                                toastr.error('Please select an address from suggestions.');
                                return;
                            }
                            const loc = place.geometry.location;
                            const nlat = loc.lat();
                            const nlng = loc.lng();
                            setLatLng(nlat, nlng);
                            map.panTo(loc);
                            marker.setPosition(loc);
                            applyGoogleAddress(place);
                        });
                    }
                } else {
                    map.setCenter({
                        lat,
                        lng
                    });
                    if (marker) marker.setPosition({
                        lat,
                        lng
                    });
                }
            }

            function applyGoogleAddress(placeOrResult) {
                const comps = (placeOrResult && placeOrResult.address_components) ? placeOrResult.address_components :
                [];
                const get = (type) => {
                    const c = comps.find(x => (x.types || []).includes(type));
                    return c ? (c.long_name || '') : '';
                };
                const streetNumber = get('street_number');
                const route = get('route');
                const sublocality = get('sublocality') || get('sublocality_level_1') || get('neighborhood');
                const city = get('locality') || get('postal_town') || get('administrative_area_level_2');
                const state = get('administrative_area_level_1');
                const postal = get('postal_code');
                const country = get('country');
                const line1 = [streetNumber, route].filter(Boolean).join(' ').trim() || (placeOrResult.name || '');

                // Marker move should update everything from map location
                if (line1El) line1El.value = line1;
                // keep user's landmark if present; otherwise fill with sublocality
                if (line2El && !line2El.value) line2El.value = sublocality || '';
                if (cityEl) cityEl.value = city || '';
                if (stateEl) stateEl.value = state || '';
                if (zipEl) zipEl.value = postal || '';
                if (countryEl) countryEl.value = country || '';

                formattedTouched = false;
                syncFormattedAddressFromFields();
            }

            async function reverseGeocode(lat, lng) {
                try {
                    setBusy(true);
                    if (mapProvider === 'google' && typeof google !== 'undefined' && googleGeocoder) {
                        await new Promise((resolve) => {
                            googleGeocoder.geocode({
                                location: {
                                    lat,
                                    lng
                                }
                            }, function(results, status) {
                                if (status === 'OK' && results && results[0]) {
                                    applyGoogleAddress(results[0]);
                                }
                                resolve(true);
                            });
                        });
                        return;
                    }

                    const url =
                        `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
                    const resp = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await resp.json();

                    const addr = data && data.address ? data.address : {};
                    const road = addr.road || addr.pedestrian || addr.path || '';
                    const house = addr.house_number || '';
                    const suburb = addr.suburb || addr.neighbourhood || '';
                    const city = addr.city || addr.town || addr.village || '';
                    const state = addr.state || '';
                    const postcode = addr.postcode || '';
                    const country = addr.country || '';

                    const line1 = [house, road].filter(Boolean).join(' ');

                    if (line1El) line1El.value = line1;
                    if (line2El && !line2El.value) line2El.value = suburb;
                    if (cityEl) cityEl.value = city;
                    if (stateEl) stateEl.value = state;
                    if (zipEl) zipEl.value = postcode;
                    if (countryEl) countryEl.value = country;

                    formattedTouched = false;
                    syncFormattedAddressFromFields();
                } catch (e) {
                    // ignore
                } finally {
                    setBusy(false);
                }
            }

            async function loadSavedAddresses() {
                if (!isLoggedIn || !savedBox || !routes.index) return;
                try {
                    const resp = await fetch(routes.index, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const json = await resp.json();
                    const list = json && json.status ? (json.data || []) : [];
                    addressCache = list;

                    savedBox.innerHTML = '';
                    if (!list.length) {
                        if (savedEmpty) savedEmpty.classList.remove('d-none');
                        return;
                    }
                    if (savedEmpty) savedEmpty.classList.add('d-none');

                    list.forEach(a => {
                        const text = a.formatted_address || [a.address_line1, a.address_line2, a.city, a
                                .state, a.postal_code, a.country
                            ]
                            .filter(Boolean)
                            .join(', ');

                        const wrap = document.createElement('div');
                        wrap.className = 'lr-address-item';
                        wrap.dataset.addressId = a.id;
                        wrap.innerHTML = `
                            <div class="d-flex align-items-start gap-2">
                                <input class="form-check-input mt-1" type="radio" name="saved_address" ${a.is_default ? 'checked' : ''} aria-label="Set as default">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="fw-semibold">${a.label ? a.label : 'Address'}</div>
                                        ${a.is_default ? '<span class="badge bg-primary">Default</span>' : ''}
                                    </div>
                                    <div class="small text-muted">${text}</div>
                                    <div class="small text-muted">Click to edit</div>
                                </div>
                               ${a.is_default ? '' : `<button type="button" class="btn btn-link p-0 text-danger lr-delete-btn" aria-label="Delete address" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>`}
                            </div>
                        `;

                        const radio = wrap.querySelector('input');
                        radio.addEventListener('change', function() {
                            setDefaultAddress(a.id);
                        });

                        const delBtn = wrap.querySelector('.lr-delete-btn');
                        if (delBtn) {
                            delBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                deleteAddress(a.id);
                            });
                        }

                        wrap.addEventListener('click', function(e) {
                            if (e.target && e.target.tagName === 'INPUT') return;
                            startEdit(a);
                        });
                        savedBox.appendChild(wrap);
                    });

                    // Auto-load default address into form (nice UX)
                    const def = list.find(x => x.is_default);
                    if (def) startEdit(def);
                } catch (e) {
                    // ignore
                }
            }

            function updateActiveCard() {
                if (!savedBox) return;
                const activeId = (editingId === null || editingId === undefined) ? null : parseInt(String(editingId),
                    10);
                [...savedBox.querySelectorAll('.lr-address-item')].forEach(el => {
                    const id = parseInt(el.dataset.addressId || '0', 10);
                    el.classList.toggle('active', activeId !== null && Number.isFinite(activeId) && id ===
                        activeId);
                });
            }

            function fillFormFromAddress(a) {
                if (!a) return;
                setSelectedLabel(a.label || 'Home');
                if (line1El) line1El.value = a.address_line1 || '';
                if (line2El) line2El.value = a.address_line2 || '';
                if (cityEl) cityEl.value = a.city || '';
                if (stateEl) stateEl.value = a.state || '';
                if (zipEl) zipEl.value = a.postal_code || '';
                if (countryEl) countryEl.value = a.country || '';
                if (formattedEl) formattedEl.value = a.formatted_address || '';
                if (makeDefaultEl) makeDefaultEl.checked = !!a.is_default;

                formattedTouched = !!(formattedEl && formattedEl.value);

                if (Number.isFinite(a.latitude) && Number.isFinite(a.longitude)) {
                    setLatLng(a.latitude, a.longitude);
                    initMap(a.latitude, a.longitude);
                    if (marker) {
                        if (mapProvider === 'google' && typeof marker.setPosition === 'function') {
                            marker.setPosition({
                                lat: a.latitude,
                                lng: a.longitude
                            });
                        } else if (typeof marker.setLatLng === 'function') {
                            marker.setLatLng([a.latitude, a.longitude]);
                        }
                    }
                }
            }

            function startNew() {
                editingId = null;
                if (formTitleEl) formTitleEl.textContent = 'Add New Address';
                if (saveBtn) saveBtn.textContent = 'Save Address';
                clearAddressFields();
                if (makeDefaultEl) makeDefaultEl.checked = true;
                setSelectedLabel('Home');
                if (searchEl) searchEl.value = '';
                updateActiveCard();
                seedInitialLatLng();
                setTimeout(() => {
                    if (mapProvider === 'leaflet' && map && typeof map.invalidateSize === 'function') map
                        .invalidateSize();
                    if (mapProvider === 'google' && typeof google !== 'undefined' && google.maps && google.maps
                        .event && map) {
                        google.maps.event.trigger(map, 'resize');
                    }
                }, 50);
            }

            function startEdit(a) {
                editingId = parseInt(String(a.id), 10);
                if (formTitleEl) formTitleEl.textContent = 'Edit Address';
                if (saveBtn) saveBtn.textContent = 'Save Changes';
                fillFormFromAddress(a);
                updateActiveCard();
                setTimeout(() => {
                    if (mapProvider === 'leaflet' && map && typeof map.invalidateSize === 'function') map
                        .invalidateSize();
                    if (mapProvider === 'google' && typeof google !== 'undefined' && google.maps && google.maps
                        .event && map) {
                        google.maps.event.trigger(map, 'resize');
                    }
                }, 50);
            }

            async function setDefaultAddress(addressId) {
                if (!routes.setDefault) return;
                try {
                    setBusy(true);
                    const resp = await $.ajax({
                        url: routes.setDefault,
                        type: 'POST',
                        data: {
                            _token: csrf,
                            address_id: addressId
                        }
                    });
                    if (resp && resp.status) {
                        toastr.success(resp.message || 'Default address updated');
                        window.location.reload();
                    } else {
                        toastr.error((resp && resp.message) || 'Failed to update default');
                    }
                } catch (e) {
                    toastr.error('Failed to update default');
                } finally {
                    setBusy(false);
                }
            }

            async function deleteAddress(addressId) {
                if (!isLoggedIn || !routes.deleteTemplate) return;
                if (!confirm('Delete this address?')) return;

                try {
                    setBusy(true);
                    const url = routes.deleteTemplate.replace('__ADDRESS__', String(addressId));
                    const resp = await $.ajax({
                        url,
                        type: 'POST',
                        data: {
                            _token: csrf,
                        }
                    });

                    if (resp && resp.status) {
                        toastr.success(resp.message || 'Address deleted');

                        // Update UI without full reload
                        addressCache = (addressCache || []).filter(x => String(x.id) !== String(addressId));
                        const el = savedBox ? savedBox.querySelector(
                            `.lr-address-item[data-address-id="${addressId}"]`) : null;
                        if (el) el.remove();
                        if (savedEmpty && (!addressCache || !addressCache.length)) savedEmpty.classList.remove(
                            'd-none');

                        if (editingId && String(editingId) === String(addressId)) {
                            startNew();
                        }

                        // If default was deleted, backend may switch default; refresh to update header location
                        window.location.reload();
                    } else {
                        toastr.error((resp && resp.message) || 'Failed to delete');
                    }
                } catch (e) {
                    toastr.error('Failed to delete');
                } finally {
                    setBusy(false);
                }
            }

            function seedInitialLatLng() {
                let lat = null;
                let lng = null;

                @if (Auth::check())
                    lat = {!! Auth::user()->location_lat ? json_encode((float) Auth::user()->location_lat) : 'null' !!};
                    lng = {!! Auth::user()->location_lng ? json_encode((float) Auth::user()->location_lng) : 'null' !!};
                @else
                    lat = {!! session()->has('location_lat') ? json_encode((float) session('location_lat')) : 'null' !!};
                    lng = {!! session()->has('location_lng') ? json_encode((float) session('location_lng')) : 'null' !!};
                @endif

                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    // Default to US center if nothing else is known
                    lat = 37.0902;
                    lng = -95.7129;
                }

                setLatLng(lat, lng);
                initMap(lat, lng);
            }

            function clearAddressFields() {
                if (line1El) line1El.value = '';
                if (line2El) line2El.value = '';
                if (cityEl) cityEl.value = '';
                if (stateEl) stateEl.value = '';
                if (zipEl) zipEl.value = '';
                if (countryEl) countryEl.value = '';
                if (formattedEl) formattedEl.value = '';
                formattedTouched = false;
            }

            async function useCurrentLocation() {
                if (!navigator.geolocation) {
                    toastr.error('Geolocation is not supported by this browser.');
                    return;
                }

                setBusy(true);
                navigator.geolocation.getCurrentPosition(async function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        clearAddressFields();
                        setLatLng(lat, lng);
                        initMap(lat, lng);
                        if (marker) {
                            if (mapProvider === 'google' && typeof marker.setPosition === 'function') {
                                marker.setPosition({
                                    lat,
                                    lng
                                });
                            } else if (typeof marker.setLatLng === 'function') {
                                marker.setLatLng([lat, lng]);
                            }
                        }
                        await reverseGeocode(lat, lng);
                        setBusy(false);
                    },
                    function() {
                        setBusy(false);
                        toastr.error('Location access denied.');
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000
                    });
            }

            async function saveAddress() {
                const p = getLatLng();
                if (!p.ok) {
                    toastr.error('Please choose a location on the map.');
                    return;
                }

                // Frontend validation for required fields
                const requiredFields = [{
                        el: line1El,
                        name: 'Street address'
                    },
                    {
                        el: zipEl,
                        name: 'ZIP / postal code'
                    },
                    {
                        el: countryEl,
                        name: 'Country'
                    },
                ];

                const missing = requiredFields.filter(f => !f.el || !String(f.el.value || '').trim());
                // clear previous invalid state
                requiredFields.forEach(f => f.el && f.el.classList.remove('is-invalid'));

                if (missing.length) {
                    missing.forEach(f => f.el && f.el.classList.add('is-invalid'));
                    const labels = missing.map(m => m.name).join(', ');
                    toastr.error('Please fill required fields: ' + labels);
                    if (missing[0] && missing[0].el) missing[0].el.focus();
                    return;
                }

                const payload = {
                    _token: csrf,
                    latitude: p.lat,
                    longitude: p.lng,
                    label: getSelectedLabel(),
                    address_line1: line1El ? line1El.value.trim() : null,
                    address_line2: line2El ? line2El.value.trim() : null,
                    city: cityEl ? cityEl.value.trim() : null,
                    state: stateEl ? stateEl.value.trim() : null,
                    postal_code: zipEl ? zipEl.value.trim() : null,
                    country: countryEl ? countryEl.value.trim() : null,
                    formatted_address: formattedEl ? (formattedEl.value || buildFormattedAddress()) :
                        buildFormattedAddress(),
                };

                if (isLoggedIn && makeDefaultEl) {
                    payload.make_default = makeDefaultEl.checked ? 1 : 0;
                }

                try {
                    setBusy(true);
                    let url = routes.store;
                    if (isLoggedIn && editingId && routes.updateTemplate) {
                        url = routes.updateTemplate.replace('__ADDRESS__', String(editingId));
                    }
                    const resp = await $.ajax({
                        url,
                        type: 'POST',
                        data: payload
                    });
                    if (resp && resp.status) {
                        toastr.success(resp.message || 'Address saved');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        window.location.reload();
                    } else {
                        toastr.error((resp && resp.message) || 'Failed to save');
                    }
                } catch (xhr) {
                    if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.values(xhr.responseJSON.errors).forEach(list => {
                            if (Array.isArray(list) && list[0]) toastr.error(list[0]);
                        });
                    } else {
                        toastr.error('Failed to save');
                    }
                } finally {
                    setBusy(false);
                }
            }

            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function() {
                    seedInitialLatLng();
                    setTimeout(() => {
                        if (map && mapProvider === 'leaflet') map.invalidateSize();
                    }, 50);
                    loadSavedAddresses();
                    if (!isLoggedIn) {
                        // Guest flow: just show new-address form
                        if (formTitleEl) formTitleEl.textContent = 'Add New Address';
                    }
                });
            }

            if (formattedEl) {
                formattedEl.addEventListener('input', function() {
                    formattedTouched = true;
                });
            }

            [line1El, line2El, cityEl, stateEl, zipEl, countryEl].forEach(el => {
                if (!el) return;
                el.addEventListener('input', function() {
                    // remove validation state when user types and keep full address in sync
                    el.classList.remove('is-invalid');
                    syncFormattedAddressFromFields();
                });
            });

            if (addNewAddressBtn) {
                addNewAddressBtn.addEventListener('click', startNew);
            }

            if (getLocationBtn) {
                getLocationBtn.addEventListener('click', useCurrentLocation);
            }
            if (saveBtn) {
                saveBtn.addEventListener('click', saveAddress);
            }
        })();
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
            const input = document.getElementById('search-terms');
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
