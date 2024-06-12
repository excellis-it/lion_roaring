<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>

        <div class="d-block d-lg-none">
            <img src="{{ asset('user_assets/images/logo.png') }}" class="dark-logo" width="" alt="">
        </div>

        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-bell-ringing"></i>
                    <div class="notification bg-primary rounded-circle"></div>
                </a>
                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                    aria-labelledby="drop2">
                    <div class="d-flex align-items-center justify-content-between py-3 px-7">
                        <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                        <span class="badge bg-primary rounded-4 px-3 py-1 lh-sm">5 new</span>
                    </div>
                    <div class="message-body" data-simplebar="">
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                            <span class="me-3">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="user"
                                    class="rounded-circle" width="48" height="48">
                            </span>
                            <div class="w-75 d-inline-block v-middle">
                                <h6 class="mb-1 fw-semibold">Roman Joined the Team!</h6>
                                <span class="d-block">Congratulate him</span>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                            <span class="me-3">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="user"
                                    class="rounded-circle" width="48" height="48">
                            </span>
                            <div class="w-75 d-inline-block v-middle">
                                <h6 class="mb-1 fw-semibold">New message</h6>
                                <span class="d-block">Salma sent you new message</span>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                            <span class="me-3">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="user"
                                    class="rounded-circle" width="48" height="48">
                            </span>
                            <div class="w-75 d-inline-block v-middle">
                                <h6 class="mb-1 fw-semibold">Bianca sent payment</h6>
                                <span class="d-block">Check your earnings</span>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                            <span class="me-3">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="user"
                                    class="rounded-circle" width="48" height="48">
                            </span>
                            <div class="w-75 d-inline-block v-middle">
                                <h6 class="mb-1 fw-semibold">Jolly completed tasks</h6>
                                <span class="d-block">Assign her new tasks</span>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                            <span class="me-3">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="user"
                                    class="rounded-circle" width="48" height="48">
                            </span>
                            <div class="w-75 d-inline-block v-middle">
                                <h6 class="mb-1 fw-semibold">John received payment</h6>
                                <span class="d-block">$230 deducted from account</span>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                            <span class="me-3">
                                <img src="{{ asset('user_assets/images/logo.png') }}" alt="user"
                                    class="rounded-circle" width="48" height="48">
                            </span>
                            <div class="w-75 d-inline-block v-middle">
                                <h6 class="mb-1 fw-semibold">Roman Joined the Team!</h6>
                                <span class="d-block">Congratulate him</span>
                            </div>
                        </a>
                    </div>
                    <div class="py-6 px-7 mb-1">
                        <button class="btn btn-primary w-100"> See All Notifications </button>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="user-profile-img">
                            @if (Auth::user()->profile_picture)
                            <img src="{{ Storage::url(Auth::user()->profile_picture) }}" class="rounded-circle"
                            width="35" height="35" alt="">
                        @else
                        <img src="{{ asset('user_assets/images/logo.png') }}" class="rounded-circle"
                        width="35" height="35" alt="">
                        @endif

                        </div>
                    </div>
                </a>
                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                    aria-labelledby="drop1">
                    <div class="profile-dropdown position-relative" data-simplebar="">
                        <div class="py-3 px-7 pb-0">
                            <h5 class="mb-0 fs-5 fw-semibold">{{ Str::ucfirst(strtolower(Auth::user()->getRoleNames()->first())) }} Profile</h5>
                        </div>
                        <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                            @if (Auth::user()->profile_picture)
                                <img src="{{ Storage::url(Auth::user()->profile_picture) }}" class="rounded-circle"
                                    width="80" height="80" alt="">
                            @else
                                <img src="{{ asset('user_assets/images/logo.png') }}" class="rounded-circle"
                                    width="80" height="80" alt="">
                            @endif
                            <div class="ms-3">
                                <h5 class="mb-1 fs-3">{{ Auth::user()->full_name }}</h5>
                                <span class="mb-1 d-block text-dark">{{ Auth::user()->getRoleNames()->first() }}</span>
                                <p class="mb-0 d-flex text-dark align-items-center gap-2">
                                    <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                        <div class="message-body">
                            @if (Gate::check('Manage Profile'))
                            <a href="{{ route('user.profile') }}" class="py-8 px-7 mt-8 d-flex align-items-center">
                                <span class="d-flex align-items-center justify-content-center bg-light rounded-1 p-6">
                                    <img src="{{ asset('user_assets/images/icon-account.svg') }}" alt=""
                                        width="24" height="24">
                                </span>
                                <div class="w-75 d-inline-block v-middle ps-3">
                                    <h6 class="mb-1 bg-hover-primary fw-semibold"> My Profile </h6>
                                    <span class="d-block text-dark">Account Settings</span>
                                </div>
                            </a>
                            @endif

                            @if (Gate::check('Manage Password'))
                            <a href="{{ route('user.change.password') }}"
                                class="py-8 px-7 d-flex align-items-center">
                                <span class="d-flex align-items-center justify-content-center bg-light rounded-1 p-6">
                                    <img src="{{ asset('user_assets/images/icon-inbox.svg') }}" alt=""
                                        width="24" height="24">
                                </span>
                                <div class="w-75 d-inline-block v-middle ps-3">
                                    <h6 class="mb-1 bg-hover-primary fw-semibold">Change Password</h6>
                                    {{-- <span class="d-block text-dark">Messages &amp; Emails</span> --}}
                                </div>
                            </a>
                            @endif
                        </div>
                        <div class="d-grid py-4 px-7 pt-8">
                            <a href="{{ route('logout') }}" class="btn btn-primary">Log Out</a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

    </nav>
</header>
