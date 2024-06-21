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
                @if (Gate::check('Manage Chat') || Gate::check('Manage Calendar') || Gate::check('Manage Email'))
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/chats') ? 'active' : '' }}"
                            href="javascript:void(0);" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample">
                            <span>
                                <img src="{{ asset('user_assets/images/Messaging.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Messaging</span>
                        </a>
                        {{-- collapse --}}
                        <div class="collapse" id="collapseExample">
                            <div class="menu_bb">
                                @if (Gate::check('Manage Chat'))
                                    <a href="{{ route('chats.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Chat.png') }}" alt="">
                                        </span>
                                        <span>Messaging</span>
                                    </a>
                                @endif
                                @if (Gate::check('Manage Team'))
                                    <a href="{{ route('user.page', ['name' => 'Team', 'permission' => 'Manage Team']) }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Team.png') }}" alt="">

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
                                    <a
                                        href="{{ route('mail.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Mail.png') }}" alt="">
                                        </span>
                                        <span>Mail</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endif
                @if (Gate::check('Manage Education'))
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExampleEducation">
                            <span>
                                <img src="{{ asset('user_assets/images/Education.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Education</span>
                        </a>
                        {{-- Collapse content --}}
                        <div class="collapse" id="collapseExampleEducation">
                            <div class="menu_bb">
                                <a
                                    href="{{ route('user.page', ['name' => 'Becoming Sovereign', 'permission' => 'Manage Education']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Becoming Sovereign.png') }}"
                                            alt="">
                                    </span>
                                    <span>Becoming Sovereign</span>
                                </a>
                                <a
                                    href="{{ route('user.page', ['name' => 'Becoming Christ Like', 'permission' => 'Manage Education']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Becoming Christ Like.png') }}"
                                            alt="">
                                    </span>
                                    <span>Becoming Christ Like</span>
                                </a>
                                <a
                                    href="{{ route('user.page', ['name' => 'Leadership Development', 'permission' => 'Manage Education']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Leadership Development.png') }}"
                                            alt="">
                                    </span>
                                    <span>Leadership Development</span>
                                </a>
                                @if (Gate::check('Manage File'))
                                    <a href="{{ route('file.index') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/Files.png') }}" alt="">
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
                @if (Gate::check('Manage Bulletin'))
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample4">
                            <span>
                                <img src="{{ asset('user_assets/images/Bulletine.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Bulletin</span>
                        </a>
                        {{-- Collapse content --}}
                        <div class="collapse" id="collapseExample4">
                            <div class="menu_bb">
                                <a
                                    href="{{ route('user.page', ['name' => 'Job Posting', 'permission' => 'Manage Bulletin']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Job Posting.png') }}" alt="">
                                    </span>
                                    <span>Job Posting</span>
                                </a>
                                <a
                                    href="{{ route('user.page', ['name' => 'Meeting Schedule', 'permission' => 'Manage Bulletin']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Meeting Schedule.png') }}"
                                            alt="">
                                    </span>
                                    <span>Meeting Schedule</span>
                                </a>
                                {{-- <a href="{{ route('user.page', ['name' => 'Lion Roaring Store', 'permission' => 'Manage Bulletin']) }}">
                                <span>
                                    <img src="{{ asset('user_assets/images/Lion Roaring Store.png') }}" alt="">
                                </span>
                                <span>Lion Roaring Store</span>
                            </a> --}}
                                {{-- <a href="{{ route('user.page', ['name' => 'Communities of interest', 'permission' => 'Manage Bulletin']) }}">
                                <span>
                                    <img src="{{ asset('user_assets/images/Communities of interest.png') }}" alt="">
                                </span>
                                <span>Communities of interest</span>
                            </a> --}}
                                <a
                                    href="{{ route('user.page', ['name' => 'Live Events', 'permission' => 'Manage Bulletin']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Live Events.png') }}" alt="">
                                    </span>
                                    <span>Live Events</span>
                                </a>
                            </div>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->hasRole('ADMIN'))
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/roles') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Team.png') }}" alt="">
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
                                <img src="{{ asset('user_assets/images/Partners.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Partners</span>
                        </a>
                    </li>
                @endif
                {{-- @if (Gate::check('Manage Sovereigns'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('user.page', ['name' => 'Sovereigns', 'permission' => 'Manage Sovereigns']) }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Sovereign.png') }}" alt="">
                            </span>
                            <span class="hide-menu">Sovereign</span>
                        </a>
                    </li>
                @endif --}}
                @if (Gate::check('Manage Help'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('user.page', ['name' => 'Help', 'permission' => 'Manage Help']) }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Help.png') }}" alt="">
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
