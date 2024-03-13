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
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/testimonials*') ? 'active' : ' ' }}">
                    <i class="ph ph-hand-fist"></i>
                    <span> Testimonials</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/testimonials') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('testimonials.index') }}"> Testimonials List</a></li>
                    <li class="{{ Request::is('admin/testimonials/create') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('testimonials.create') }}">Testimonials Create</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/our-governances*') ? 'active' : ' ' }}">
                    <i class="ph ph-scales"></i>
                    <span> Our Governance</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/our-governances') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('our-governances.index') }}"> Our Governance List</a></li>
                    <li class="{{ Request::is('admin/our-governances/create') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('our-governances.create') }}">Our Governance Create</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/our-organizations*') ? 'active' : ' ' }}">
                    <i class="ph ph-graduation-cap"></i>
                    <span> Our Organizations</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/our-organizations') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('our-organizations.index') }}"> Our Organizations List</a></li>
                    <li class="{{ Request::is('admin/our-organizations/create') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('our-organizations.create') }}">Our Organizations Create</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/organization-centers*') ? 'active' : ' ' }}">
                    <i class="ph ph-chalkboard-teacher"></i>
                    <span> Organization Center</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('admin/organization-centers') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('organization-centers.index') }}"> Organization Center List</a></li>
                    <li class="{{ Request::is('admin/organization-centers/create') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('organization-centers.create') }}">Organization Center Create</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown {{ Request::is('admin/pages*') ? 'active' : ' ' }}">
                    <i class="ph ph-newspaper"></i>
                    <span> Pages </span>
                </a>
                <ul class="dropdown-menu">
                    {{-- <li class="dropdown {{ Request::is('admin/pages/organizations*') ? 'active' : ' ' }}">
                        <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown">
                            <span> Organization</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="{{ Request::is('admin/pages/organizations') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('organizations.index') }}"> Organization CMS</a></li>
                        </ul>
                    </li> --}}
                    <li class="{{ Request::is('admin/pages/home-cms') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('home-cms.index') }}"> Home </a></li>
                    {{-- <li class="{{ Request::is('admin/pages/details') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('details.index') }}"> Details </a></li> --}}
                    <li class="dropdown {{ Request::is('admin/pages/organizations*') ? 'active' : ' ' }}">
                        <a href="javascript:void(0);" class="menu-toggle nav-link has-dropdown">
                            <span> Organization</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="{{ Request::is('admin/pages/organizations') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('organizations.index') }}"> Organization CMS</a></li>
                        </ul>
                    </li>
                    <li class="{{ Request::is('admin/pages/about-us') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('about-us.index') }}"> About Us</a></li>
                    <li class="{{ Request::is('admin/pages/faq') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('faq.index') }}"> FAQS</a></li>
                    <li class="{{ Request::is('admin/pages/gallery') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('gallery.index') }}"> GALLERY </a></li>
                    <li class="{{ Request::is('admin/pages/ecclesia-associations') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('ecclesia-associations.index') }}"> ECCLESIA ASSOCIATION</a></li>
                    <li class="{{ Request::is('admin/pages/principle-and-business') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('principle-and-business.index') }}">PRINCIPLE AND BUSINESS MODEL
                    </a></li>
                    <li class="{{ Request::is('admin/contact-us-cms') ? 'active' : ' ' }}"><a class="nav-link" href="{{ route('contact-us-cms.index') }}">CONTACT US</a></li>
                </ul>
            </li>
        </ul>
    </aside>
</div>
