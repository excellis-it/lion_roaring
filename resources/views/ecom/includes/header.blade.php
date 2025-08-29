<div class="main_menu_hdr">
    <div class="container-fluid">
        <div class="main_menu">
            <div class="navigation navbar">
                <div class="left_top me-auto">
                    <div class="logo">
                        <a href="{{ route('e-store') }}" class="">
                            <img src="{{ asset('ecom_assets/images/logo.png') }}" alt="" />
                        </a>
                    </div>
                </div>
                <div class="right_btm">
                    <div>


                    </div>
                    <div id="cssmenu">
                        <ul>
                            <li>
                                @if (Auth::check())
                                    @if (Auth::user()->location_lat || Auth::user()->location_lng)
                                        <span onclick="changeLocation()" class="location-icon text-white back_main"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="{{ Auth::user()->location_address }}">
                                            <i class="fa fa-map-marker text-danger me-2"
                                                aria-hidden="true"></i>{{ Str::limit(Auth::user()->location_address ?? '', 20, '...') }}
                                        </span>
                                    @endif
                                @else
                                    @if (session()->has('location_lat') && session()->has('location_lng'))
                                        <span onclick="changeLocation()" class="location-icon text-white back_main"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="{{ session('location_address') }}">
                                            <i class="fa fa-map-marker text-danger me-2"
                                                aria-hidden="true"></i>{{ Str::limit(session('location_address') ?? '', 20, '...') }}
                                        </span>
                                    @endif
                                @endif
                            </li>

                            <li><a href="{{ route('e-store') }}">Home</a></li>
                            <li><a href="{{ route('e-store.all-products') }}">our collections</a></li>
                            <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="right_top">
                    <div class="right_login">
                        <div class="d-flex align-items-center justify-content-end">
                            <a href="{{ route('user.profile') }}" class="back_main">back to main page</a>

                            <a href="{{ route('e-store.cart') }}" class="back_main"><i class="fa fa-shopping-cart"
                                    aria-hidden="true"></i><span class="cart_count ms-1">{{ $cartCount }}</span></a>
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
                                            <li><a class="dropdown-item" href="{{ route('home') }}">Login</a></li>
                                            <li><a class="dropdown-item" href="{{ route('home') }}">Register</a>
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

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
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


@push('scripts')
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
@endpush
