@php
    use App\Helpers\Helper;
    use App\Models\EstorePromoCode;
    use App\Models\MembershipPromoCode;

    $now = \Carbon\Carbon::now();

    // Fetch active membership promo codes
    $membershipCoupons = MembershipPromoCode::where('status', 1)
        ->whereDate('end_date', '>=', $now->toDateString())
        ->get()
        ->map(function ($coupon) {
            return (object) [
                'code' => $coupon->code,
                'discount' => $coupon->is_percentage
                    ? $coupon->discount_amount . '% OFF'
                    : '$' . number_format($coupon->discount_amount, 2) . ' OFF',
                'end_date' => \Carbon\Carbon::parse($coupon->end_date)->format('M d, Y'),
                'type' => 'Membership',
            ];
        });

    $allCoupons = $membershipCoupons;
@endphp

@if ($allCoupons->count() > 0)
    <div class="coupon_slider" id="couponSliderBar">
        {{-- <div class="coupon-slider-close" id="couponSliderClose" title="Close">
            <i class="fa fa-times"></i>
        </div> --}}
        <div class="coupon-ticker-wrapper">
            <div class="coupon-ticker-track">
                @foreach ($allCoupons as $coupon)
                    <div class="coupon-ticker-item">
                        <span class="coupon-ticker-icon"><i class="fa-solid fa-tag"></i></span>
                        <span class="coupon-ticker-badge">{{ $coupon->type }}</span>
                        <span class="coupon-ticker-text">
                            Use code <strong class="coupon-ticker-code">{{ $coupon->code }}</strong>
                            to get <strong class="coupon-ticker-discount">{{ $coupon->discount }}</strong>
                        </span>
                        <span class="coupon-ticker-expiry">
                            <i class="fa-regular fa-clock"></i> Expires {{ $coupon->end_date }}
                        </span>
                    </div>
                @endforeach
                {{-- Duplicate for seamless loop --}}
                @foreach ($allCoupons as $coupon)
                    <div class="coupon-ticker-item">
                        <span class="coupon-ticker-icon"><i class="fa-solid fa-tag"></i></span>
                        <span class="coupon-ticker-badge">{{ $coupon->type }}</span>
                        <span class="coupon-ticker-text">
                            Use code <strong class="coupon-ticker-code">{{ $coupon->code }}</strong>
                            to get <strong class="coupon-ticker-discount">{{ $coupon->discount }}</strong>
                        </span>
                        <span class="coupon-ticker-expiry">
                            <i class="fa-regular fa-clock"></i> Expires {{ $coupon->end_date }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>




@endif

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

        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
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

            {{-- ── Account Type Indicator ──────────────────────────── --}}
            @php
                $displayCountry = Helper::getDisplayCountry();
                $isGlobalContext = $displayCountry && $displayCountry->is_global;
                $userType       = Auth::user()->user_type;
            @endphp

            <li class="nav-item d-none d-lg-flex align-items-center px-2 gap-2">
                @if ($isGlobalContext)
                    <span class="badge-global">
                        <i class="fa-solid fa-globe me-1"></i>Global
                    </span>
                @else
                    <span class="badge-regional">
                        @if ($displayCountry && $displayCountry->code)
                            <img src="{{ asset('frontend_assets/images/flags/' . strtolower($displayCountry->code) . '.png') }}" height="12px" alt="">
                        @endif
                        {{ $displayCountry->name ?? '' }}
                    </span>
                @endif

                {{-- User type chip --}}
                {{-- <span class="badge-user-type">
                    <i class="fa-solid fa-user-tag me-1"></i>User Type : {{ $userType }}
                </span> --}}
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover header-notif-link" href="javascript:void(0)" id="drop2"
                    aria-label="Notifications">
                    <i class="ti ti-bell-ringing"></i>
                    @php $headerNotifCount = (int) Helper::notificationCount(); @endphp
                    {{-- Do not use .round-note here — legacy CSS (margin-top:23px) pushes the count under the bell --}}
                    <span id="show-notification-count-{{ auth()->user()->id }}"
                        class="header-notif-badge{{ $headerNotifCount <= 0 ? ' is-empty' : '' }}"
                        data-count="{{ $headerNotifCount }}"
                        @if ($headerNotifCount <= 0) style="display:none" @endif>{{ Helper::formatBadgeCount($headerNotifCount) }}</span>
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
                            @php
                                $headerAvatarFallback = asset('user_assets/images/profile_dummy.png');
                                $headerAvatarUrl = Helper::publicStorageUrl(Auth::user()->profile_picture)
                                    ?: $headerAvatarFallback;
                            @endphp
                            <img src="{{ $headerAvatarUrl }}" class="rounded-circle" width="35" height="35"
                                alt=""
                                onerror="this.onerror=null;this.src='{{ $headerAvatarFallback }}';">
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up profile-menu-dd"
                    aria-labelledby="drop1">
                    <div class="profile-dropdown position-relatipve">
                        <div class="py-3 px-7 pb-0">
                            <h5 class="mb-0 fs-5 fw-semibold">
                                {{ Str::ucfirst(strtolower(Auth::user()->getFirstUserRoleName())) }} Profile</h5>
                        </div>
                        <div class="px-7 py-9 border-bottom profile-dropdown-user">
                            <div class="d-flex align-items-start gap-3">
                                @php
                                    $ddAvatarFallback = asset('user_assets/images/profile_dummy.png');
                                    $ddAvatarUrl = Helper::publicStorageUrl(Auth::user()->profile_picture)
                                        ?: $ddAvatarFallback;
                                @endphp
                                <img src="{{ $ddAvatarUrl }}" class="rounded-circle flex-shrink-0" width="72"
                                    height="72" alt=""
                                    onerror="this.onerror=null;this.src='{{ $ddAvatarFallback }}';">
                                <div class="min-w-0 flex-grow-1">
                                    <h5 class="mb-1 fs-5 lh-sm text-break">
                                        {{ Auth::user()->full_name }}
                                    </h5>
                                    <span
                                        class="d-block text-dark text-break">{{ Auth::user()->getFirstUserRoleName() }}</span>
                                </div>
                            </div>
                            <p class="mb-0 mt-3 d-flex text-dark align-items-start gap-2 profile-dropdown-email">
                                <i class="ti ti-mail fs-5 flex-shrink-0 mt-1"></i>
                                <span class="text-break">{{ Auth::user()->email }}</span>
                            </p>
                            <div class="d-flex align-items-center flex-wrap gap-1 mt-2 profile-dropdown-badges">
                                @if ($isGlobalContext)
                                    <span class="badge-global small-badge">
                                        <i class="fa-solid fa-globe me-1"></i>Global
                                    </span>
                                @else
                                    <span class="badge-regional small-badge">
                                        @if ($displayCountry && $displayCountry->code)
                                            <img src="{{ asset('frontend_assets/images/flags/' . strtolower($displayCountry->code) . '.png') }}"
                                                height="11px" alt="">
                                        @endif
                                        {{ $displayCountry->name ?? '' }}
                                    </span>
                                @endif
                                <span class="badge-user-type small-badge">
                                    <i class="fa-solid fa-user-tag me-1"></i>User Type : {{ $userType }}
                                </span>
                            </div>
                        </div>
                        <div class="message-body">
                            @if (Gate::check('Manage Profile'))
                                <a href="{{ route('user.profile') }}"
                                    class="py-8 px-7 mt-8 d-flex align-items-center">
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
