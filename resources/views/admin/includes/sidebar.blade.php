{{-- <div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="{{ Request::is('admin/dashboard*') ? 'active' : ' ' }}">
                    <a href="{{ route('admin.dashboard') }}" ><i class="la la-dashboard"></i> <span>Dashboard</span></a>
                </li>

                <li class="menu-title">
                    <span>Main</span>
                </li>

                <li class="submenu">
                    <a href="#" class="{{ Request::is('admin/profile*') || Request::is('admin/password*') || Request::is('admin/detail*') ? 'active' : ' ' }}"><i class="la la-user-cog"></i> <span>Manage Account </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="{{ Request::is('admin/profile*') ? 'active' : ' ' }}">
                            <a href="{{ route('admin.profile') }}">My Profile</a>
                        </li>
                        <li class="{{ Request::is('admin/password*') ? 'active' : ' ' }}">
                            <a href="{{ route('admin.password') }}">Change Password</a>
                        </li>
                        <li class="{{ Request::is('admin/detail*') ? 'active' : ' ' }}">
                            <a href="{{ route('admin.index') }}">Admin List</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Request::is('admin/customers*') ? 'active' : ' ' }}">
                    <a href="{{ route('customers.index') }}" ><i class="la la-users"></i> <span>Manage Customers</span></a>
                </li>

                <li class="{{ Request::is('admin/sellers*') ? 'active' : ' ' }}">
                    <a href="{{ route('sellers.index') }}" ><i class="la la-user"></i> <span>Manage Sellers</span></a>
                </li> --}}


{{-- <li class="{{ Request::is('admin/members*') ? 'active' : ' ' }}">
                    <a href="{{ route('user.index') }}"><i class="la la-users"></i> <span>Members</span></a>
                </li>
                <li class="{{ Request::is('admin/group*') ? 'active' : ' ' }}">
                    <a href="{{ route('group.index') }}"><i class="la la-list"></i> <span>Groups</span></a>
                </li> --}}

{{-- <li class="menu-title">
                    <span>Content Management System</span>
                </li>
                <li class="submenu">
                    <a href="#" class="{{ Request::is('admin/cms/sub-admin*') ? 'active' : ' ' }}"><i class="la la-address-card"></i> <span>Admin Panel </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="{{ Request::is('admin/cms/sub-admin*') ? 'active' : ' ' }}">
                            <a href="{{ route('cms.sub-admin.get-started') }}">Get Started Page</a>
                        </li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#" class="{{ Request::is('admin/cms/user*') ? 'active' : ' '}}"><i class="la la-newspaper"></i> <span>Member Panel </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="{{ Request::is('admin/cms/user*') ? 'active' : ' ' }}">
                            <a href="{{ route('cms.user.get-started') }}">Get Started Page</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>

 --}}

<div class="main-sidebar sidebar-style-2" tabindex="1">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}"><span class="logo-name"><img src="{{asset('admin_assets/img/logo.png')}}" /></span> </a>
            <a href="{{ route('admin.dashboard') }}"><span class="logo-fm"><img src="{{asset('admin_assets/img/logo_fm.png')}}" /></span> </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header"></li>
            <li class="dropdown {{ Request::is('admin/dashboard*') ? 'active' : ' ' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="ph-gauge"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/profile*') || Request::is('admin/password*') || Request::is('admin/detail*') ? 'active' : ' ' }}" >
                    <i class="ph-identification-card"></i>
                    <span>Manage Account</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/profile*') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('admin.profile') }}">My Profile</a></li>
                    <li class="{{ Request::is('admin/password*') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('admin.password') }}">Change Password</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/customers*') ? 'active' : ' ' }}">
                    <i class="ph ph-user-list"></i>
                    <span> User Management</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/customers/create') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('customers.create') }}">Create  User</a></li>
                    <li class="{{ Request::is('admin/customers') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('customers.index') }}"> User List</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/customers*') ? 'active' : ' ' }}">
                    <i class="ph ph-users-three"></i>
                    <span>B2B User Management</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/sellers/create') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('sellers.create') }}"> Create B2B User</a></li>
                    <li class="{{ Request::is('admin/sellers') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('sellers.index') }}">B2B User List</a></li>
                </ul>
            </li>
            {{-- <li class="dropdown">
                <a href="" class="menu-toggle nav-link has-dropdown">
                    <i class="ph-file"></i>
                    <span>B2B Markup Management</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="admin-markup.html">Admin Markup</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="" class="menu-toggle nav-link has-dropdown">
                    <i class="ph-wallet"></i>
                    <span>B2B Deposits</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="">Manage / Add Deposits</a></li>
                    <li><a class="nav-link" href="">Manage New Deposits
                            Request</a></li>
                    <li><a class="nav-link" href="">Approved Deposits Request</a>
                    </li>
                    <li><a class="nav-link" href="">Deposit History</a></li>
                    <li><a class="nav-link" href="">Refund Deposit</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="">
                    <i class="ph-ticket"></i>
                    <span>All Cancel Ticket</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="">
                    <i class="ph-user-circle"></i>
                    <span>Regional Manager</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="">
                    <i class="ph-fingerprint"></i>
                    <span>Itinerary</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="">
                    <i class="ph-fingerprint"></i>
                    <span>Upload Ticket</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="">
                    <i class="ph-fingerprint"></i>
                    <span>Customer Database</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="">
                    <i class="ph-fingerprint"></i>
                    <span>My Operator Log Details</span>
                </a>
            </li> --}}
        </ul>
    </aside>
</div>
