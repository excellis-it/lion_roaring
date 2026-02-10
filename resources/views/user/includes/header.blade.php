@php
    use App\Helpers\Helper;
@endphp
<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>

        <div class="d-block d-lg-none">

            @if (isset(Helper::getSettings()->PMA_PANEL_LOGO))
                <img class="dark-logo" src="{{ asset(Helper::getSettings()->PMA_PANEL_LOGO) }}" alt="">
            @else
                <img class="dark-logo" src="{{ asset('user_assets/images/logo.png') }}" alt="">
            @endif
        </div>

        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
            <li class="nav-item m-3">
                <style>
                    .blink {
                        animation: blinker 1s linear infinite;
                        color: #ffffff;
                        background-color: #ff0000;
                        padding: 3px 8px;
                        border-radius: 4px;
                        margin-right: 5px;
                        font-weight: bold;
                        font-size: 11px;
                        text-transform: uppercase;
                        display: inline-block;
                        vertical-align: middle;
                    }

                    @keyframes blinker {
                        50% {
                            opacity: 0;
                        }
                    }
                </style>
                <span id="siteUpdateBlink" class="blink"
                    style="{{ isset(Helper::getSettings()->SITE_UPDATE) && Helper::getSettings()->SITE_UPDATE == 1 ? '' : 'display: none;' }}">
                    update is in progress
                </span>

                <a href="{{ route('e-learning') }}" class="btn btn-primary"> <img
                        src="{{ asset('user_assets/images/e-learning.png') }}" alt="e-Learning">
                    {{ Helper::getMenuName('elearning', 'e-Learning') }}</a>
            </li>

            <li class="nav-item m-3">

                <a href="{{ route('e-store') }}" class="btn btn-primary"> <img
                        src="{{ asset('user_assets/images/bag.png') }}" alt="e-store">
                    {{ Helper::getMenuName('estore', 'e-Store') }}</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2">
                    <i class="ti ti-bell-ringing"></i>
                    {{-- <span class="round-note"><p>5</p>5</span> --}}
                    <div class="notification round-note">
                        <p id="show-notification-count-{{ auth()->user()->id }}">{{ Helper::notificationCount() }}</p>
                    </div>
                </a>
                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up notification-dropdown"
                    aria-labelledby="drop2" data-bs-popper="static">
                    <div class="d-flex align-items-center justify-content-between py-3 px-7">
                        <h5 class="mb-0 fs-5 fw-semibold">{{ Helper::getMenuName('notifications', 'Notifications') }}
                        </h5>
                        <span class="badge bg-primary rounded-4 px-3 py-1 lh-sm clear-all-notification"
                            style="color: red; font-size:medium; cursor:pointer">Clear All</span>
                    </div>
                    <ul class="message-body srl" data-simplebar="" id="show-notification-{{ auth()->user()->id }}">
                        @include('user.includes.notification')
                    </ul>
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
                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}" class="rounded-circle"
                                    width="35" height="35" alt="">
                            @endif

                        </div>
                    </div>
                </a>
                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                    aria-labelledby="drop1">
                    <div class="profile-dropdown position-relative" data-simplebar="">
                        <div class="py-3 px-7 pb-0">
                            <h5 class="mb-0 fs-5 fw-semibold">
                                {{ Str::ucfirst(strtolower(Auth::user()->getFirstUserRoleName())) }} Profile</h5>
                        </div>
                        <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                            @if (Auth::user()->profile_picture)
                                <img src="{{ Storage::url(Auth::user()->profile_picture) }}" class="rounded-circle"
                                    width="80" height="80" alt="">
                            @else
                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}" class="rounded-circle"
                                    width="80" height="80" alt="">
                            @endif
                            <div class="ms-3">
                                <h5 class="mb-1 fs-3">{{ Auth::user()->full_name }}</h5>
                                <span class="mb-1 d-block text-dark">{{ Auth::user()->getFirstUserRoleName() }}</span>
                                <p class="mb-0 d-flex text-dark align-items-center gap-2">
                                    <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                        <div class="message-body">
                            @if (Gate::check('Manage Profile'))
                                <a href="{{ route('user.profile') }}" class="py-8 px-7 mt-8 d-flex align-items-center">
                                    <span
                                        class="d-flex align-items-center justify-content-center bg-light rounded-1 p-6">
                                        <img src="{{ asset('user_assets/images/icon-account.svg') }}" alt=""
                                            width="24" height="24">
                                    </span>
                                    <div class="w-75 d-inline-block v-middle ps-3">
                                        <h6 class="mb-1 bg-hover-primary fw-semibold">
                                            {{ Helper::getMenuName('my_profile', 'My Profile') }} </h6>
                                        <span class="d-block text-dark">Account Settings</span>
                                    </div>
                                </a>
                            @endif

                            @if (Gate::check('Manage Password'))
                                <a href="{{ route('user.change.password') }}"
                                    class="py-8 px-7 d-flex align-items-center">
                                    <span
                                        class="d-flex align-items-center justify-content-center bg-light rounded-1 p-6">
                                        <img src="{{ asset('user_assets/images/icon-inbox.svg') }}" alt=""
                                            width="24" height="24">
                                    </span>
                                    <div class="w-75 d-inline-block v-middle ps-3">
                                        <h6 class="mb-1 bg-hover-primary fw-semibold">
                                            {{ Helper::getMenuName('change_password', 'Change Password') }}</h6>
                                        {{-- <span class="d-block text-dark">Messages &amp; Emails</span> --}}
                                    </div>
                                </a>
                            @endif
                        </div>
                        <div class="d-grid py-4 px-7 pt-8">
                            <a href="{{ route('logout') }}"
                                class="btn btn-primary">{{ Helper::getMenuName('logout', 'Log Out') }}</a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

    </nav>
</header>
