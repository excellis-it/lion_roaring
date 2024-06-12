<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                <img src="{{ asset('user_assets/images/logo.png') }}" class="dark-logo" alt="">
            </a>
            <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8 text-muted"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Request::is('user/dashboard') ? 'active' : '' }}" href=""
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                @if (Gate::check('View Activity'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-notebook"></i>
                        </span>
                        <span class="hide-menu">Activity</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View Team'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-note"></i>
                        </span>
                        <span class="hide-menu">Team</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View File'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-cpu"></i>
                        </span>
                        <span class="hide-menu">Files</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View Chat'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Request::is('user/chats') ? 'active' : '' }}"
                        href="{{ route('chats.index') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-checkup-list"></i>
                        </span>
                        <span class="hide-menu">Messaging</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View Education'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-checkup-list"></i>
                        </span>
                        <span class="hide-menu">Education</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('Manage Bulletin'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-list-numbers"></i>
                        </span>
                        <span class="hide-menu">Bulletin</span>
                    </a>
                </li>
                @endif
                @if (Auth::user()->hasRole('ADMIN'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Request::is('user/roles') ? 'active' : '' }}" href="{{route('roles.index')}}" aria-expanded="false">
                        <span>
                            <i class="ti ti-shield"></i>
                        </span>
                        <span class="hide-menu">Role Permission</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View Partners'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Request::is('user/partners') ? 'active' : '' }}" href="{{route('partners.index')}}" aria-expanded="false">
                        <span>
                            <i class="ti ti-playlist"></i>
                        </span>
                        <span class="hide-menu">Partners</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View Sovereigns'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-certificate"></i>
                        </span>
                        <span class="hide-menu">Sovereign</span>
                    </a>
                </li>
                @endif
                @if (Gate::check('View Help'))
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" aria-expanded="false">
                        <span>
                            <i class="ti ti-logout"></i>
                        </span>
                        <span class="hide-menu">Help</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
