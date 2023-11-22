<nav class="navbar navbar-expand-lg main-navbar sticky">
    <div class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                    <svg xmlns="http: //www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-menu">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </a>
            </li>
            <li></li>
        </ul>
    </div>
    <ul class="navbar-nav navbar-right">

        <li class="dropdown">
            <a href="" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <i class="ph-bell"></i> <span class="notification_round">10</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-title">Notification</div>
                <div class="notification_scroll">
                    <div class="notify_me">
                        <a href="" class="notify_me_a">
                            <span class="number_notify">1</span>
                            <p>Today’s New Bookings</p>
                        </a>
                        <a href="" class="notify_me_a">
                            <span class="number_notify">2</span>
                            <p>Today’s New Bookings</p>
                        </a>
                        <a href="" class="notify_me_a">
                            <span class="number_notify">3</span>
                            <p>Today’s New Bookings</p>
                        </a>
                        <a href="" class="notify_me_a">
                            <span class="number_notify">4</span>
                            <p>Today’s New Bookings</p>
                        </a>
                        <a href="" class="notify_me_a">
                            <span class="number_notify">5</span>
                            <p>Today’s New Bookings</p>
                        </a>
                        <a href="" class="notify_me_a">
                            <span class="number_notify">6</span>
                            <p>Today’s New Bookings</p>
                        </a>

                    </div>
                </div>

            </div>
        </li>
        <!-- <li>
            <a href="" class="nav-link nav-link-lg fullscreen-btn">
                <i class="ph-gear"></i>
            </a>
        </li> -->
        <li class="dropdown">
            <a href="" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{asset('admin_assets/img/profile.png')}}" class="user-img-radious-style" /> {{Auth::user()->name}}
                <!---<span class="d-sm-none d-lg-inline-block"><i class="ph-caret-down"></i></span>---->
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-title">Hello {{Auth::user()->name}}</div>
                <div class="dropdown-divider"></div>
                <button type="submit" class="dropdown-item has-icon text-danger">
                    <i class="ph ph-sign-out"></i>
                    <a href="{{route('admin.logout')}}" class="has-icon text-danger">
                        Logout
                    </a>
                </button>
            </div>
        </li>
    </ul>
</nav>

