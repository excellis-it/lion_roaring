<aside class="left-sidebar">
    <div style="background: #7851A9">
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="javascript:void(0);" class="text-nowrap logo-img">
                <img src="{{ asset('user_assets/images/logo.png') }}" class="dark-logo" alt="">
            </a>
            <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8 text-muted"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                {{-- @if (Gate::check('Manage Activity'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('user.page', ['name' => 'Activity', 'permission' => 'Manage Activity']) }}"
                            role="button" >
                            <span>
                                <img src="{{ asset('user_assets/images/Activity.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Activity</span>
                        </a>
                    </li>
                @endif --}}
                {{-- @if (Gate::check('Manage Team'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('teams.index') }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Team.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Team</span>
                        </a>
                    </li>
                @endif --}}
                {{-- @if (Gate::check('Manage File'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('file.index') }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Files.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Files</span>
                        </a>
                    </li>
                @endif --}}
                @if (Gate::check('Manage Chat') || Gate::check('Manage Team') || Gate::check('Manage Email'))
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/chats') ? 'active' : '' }}"
                            href="javascript:void(0);" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Messaging/Messaging.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Messaging</span>
                        </a>
                        {{-- collapse --}}
                        <div class="collapse {{ Request::is('user/chats*') || Request::is('user/page/Team*') || Request::is('user/mail*') ? 'show' : '' }}"
                            id="collapseExample">
                            <div class="menu_bb">
                                @if (Gate::check('Manage Chat'))
                                    <a href="{{ route('chats.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Messaging/chat.svg') }}" alt="">
                                        </span>
                                        <span>Chats</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Team'))
                                    <a
                                        href="{{ route('team-chats.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Messaging/Team.svg') }}" alt="">

                                        </span>
                                        <span class="hide-menu">Team</span>
                                    </a>
                                @endif


                                {{-- @if (Gate::check('Manage Calendar'))
                                <a href="{{ route('user.page', ['name' => 'Calendar', 'permission' => 'Manage Calendar']) }} ">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Calendar.png') }}" alt="">
                                    </span>
                                    <span>Calendar</span>
                                </a>
                                @endif --}}
                                {{-- <a href="{{ route('user.page', ['name' => 'Calls', 'permission' => 'Manage Chat']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Calls.png') }}" alt="">
                                    </span>
                                    <span>Calls</span>
                                </a> --}}
                                @if (Gate::check('Manage Email'))
                                    <a href="{{ route('mail.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Messaging/Mail.svg') }}" alt="">
                                        </span>
                                        <span>Mail</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endif
                @if (Gate::check('Manage Becoming Sovereigns') ||
                        Gate::check('Manage Becoming Christ Like') ||
                        Gate::check('Manage Becoming a Leader') ||
                        Gate::check('Manage File') || Gate::check('Manage Topic') ||
                        Auth::user()->hasRole('ADMIN'))
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExampleEducation">
                            <span>
                                <img src="{{ asset('user_assets/images/Education.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Education</span>
                        </a>
                        {{-- Collapse content --}}
                        <div class="collapse {{ Request::is('user/topics*') || Request::is('user/becoming-sovereign*') || Request::is('user/becoming-christ-link*') || Request::is('user/leadership-development*') || Request::is('user/file*') ? 'show' : '' }}"
                            id="collapseExampleEducation">
                            <div class="menu_bb">
                                @if (Gate::check('Manage Topic'))
                                    <a href="{{ route('topics.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Education.svg') }}" alt="">
                                        </span>
                                        <span>Topics</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Becoming Sovereigns'))
                                    <a href="{{ route('becoming-sovereign.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Becoming Sovereign.svg') }}"
                                                alt="">
                                        </span>
                                        <span>Becoming Sovereign</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Becoming Christ Like'))
                                    <a href="{{ route('becoming-christ-link.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Becoming Christ Like.svg') }}"
                                                alt="">
                                        </span>
                                        <span>Becoming Christ Like</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Becoming a Leader'))
                                    <a href="{{ route('leadership-development.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Leadership Development.svg') }}"
                                                alt="">
                                        </span>
                                        <span>Becoming a Leader</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage File'))
                                    <a href="{{ route('file.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Files.svg') }}" alt="">
                                        </span>
                                        <span class="hide-menu">Files</span>
                                    </a>
                                @endif
                                {{-- <a href="{{ route('user.page', ['name' => 'Communities of interest', 'permission' => 'Manage Education']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Live Events.png') }}" alt="">
                                    </span>
                                    <span>Live Events</span>
                                </a> --}}
                            </div>
                        </div>
                    </li>
                @endif
                @if (Gate::check('Manage Job Postings') || Gate::check('Manage Meeting Schedule') || Gate::check('Manage Event'))
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample4">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Bulletin/Bulletin.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Bulletins</span>
                        </a>
                        {{-- Collapse content --}}
                        <div class="collapse {{ Request::is('user/bulletins*') || Request::is('user/view-calender*') || Request::is('user/jobs*') || Request::is('user/meetings*') || Request::is('user/bulletin-board*') || Request::is('user/events*') ? 'show' : '' }}"
                            id="collapseExample4">
                            <div class="menu_bb">
                                {{-- bulletins --}}
                                <a href="{{ route('bulletin-board.index') }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/ICON/Bulletin/Meeting_Schedule.svg') }}"
                                            alt="">
                                    </span>
                                    <span>Bulletins Board</span>
                                </a>
                                @if (Gate::check('Manage Bulletin'))
                                    <a href="{{ route('bulletins.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Bulletin/Create_Bulletins.svg') }}" alt="">
                                        </span>
                                        <span>Create Bulletins</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Job Postings'))
                                    <a href="{{ route('jobs.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Bulletin/Job_Posting.svg') }}"
                                                alt="">
                                        </span>
                                        <span>Job Posting</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Meeting Schedule'))
                                    <a href="{{ route('meetings.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Bulletin/Meeting_Schedule.svg') }}"
                                                alt="">
                                        </span>
                                        <span>Meeting Schedule</span>
                                    </a>
                                @endif
                                {{-- <a href="{{ route('user.page', ['name' => 'Communities of interest', 'permission' => 'Manage Bulletin']) }}">
                                <span>
                                    <img src="{{ asset('user_assets/images/Communities of interest.png') }}" alt="">
                                </span>
                                <span>Communities of interest</span>
                            </a> --}}
                                @if (Gate::check('Manage Event'))
                                    <a href="{{ route('events.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/Bulletin/Live_Event.svg') }}"
                                                alt="">
                                        </span>
                                        <span>Live Events</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @else
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('bulletin-board.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Meeting Schedule.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Bulletin Board</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasRole('ADMIN'))
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample10">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Store/Store.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Store</span>
                        </a>
                        {{-- Collapse content --}}
                        <div class="collapse {{ Request::is('user/products*') || Request::is('user/cms*') || Request::is('user/cms-page/home*') || Request::is('user/cms-page/footer*') || Request::is('user/categories*') ? 'show' : '' }}"
                            id="collapseExample10">
                            <div class="menu_bb">
                                {{-- bulletins --}}
                                <a href="{{ route('user.cms.dashboard') }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/ICON/Store/E-store_Dashboard.svg') }}"
                                            alt="">
                                    </span>
                                    <span>E-store Dashboard</span>
                                </a>

                                <a href="{{ route('categories.index') }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/ICON/Store/Product_Categories.svg') }}"
                                            alt="">
                                    </span>
                                    <span>Product Categories</span>
                                </a>
                                <a href="{{ route('products.index') }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/ICON/Store/Products.svg') }}" alt="">
                                    </span>
                                    <span>Products</span>
                                </a>
                            </div>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->hasRole('ADMIN') || Auth::user()->hasRole('LEADER'))
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/roles') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Role_Permission.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Role Permission</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('Manage Partners'))
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/partners/*') ? 'active' : '' }}"
                            href="{{ route('partners.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/All_Member.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">All Members</span>
                        </a>
                    </li>
                @endif
                {{-- @if (Auth::user()->hasRole('ADMIN')) 
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/ecclesias/*') ? 'active' : '' }}"
                            href="{{ route('ecclesias.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Ecclesias.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Ecclesias</span>
                        </a>
                    </li>
                @endif --}}
                @if (Gate::check('Manage Strategy'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('strategy.index') }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Strategy.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">Strategy</span>
                        </a>
                    </li>
                @endif
                @if (Gate::check('Manage Help'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('user.page', ['name' => 'Help', 'permission' => 'Manage Help']) }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Help.svg') }}" alt="">
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
