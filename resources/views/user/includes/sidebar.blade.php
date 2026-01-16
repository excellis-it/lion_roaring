 @php
     use App\Helpers\Helper;
 @endphp
 <aside class="left-sidebar">
     <div style="background: #7851A9">
         <div class="brand-logo d-flex align-items-center justify-content-between">
             <a href="javascript:void(0);" class="text-nowrap logo-img">

                 @if (isset(Helper::getSettings()->PMA_PANEL_LOGO))
                     <img class="dark-logo" src="{{ asset(Helper::getSettings()->PMA_PANEL_LOGO) }}" alt="">
                 @else
                     <img class="dark-logo" src="{{ asset('user_assets/images/logo.png') }}" alt="">
                 @endif
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
                            <span class="hide-menu">{{ Helper::getMenuName('activity', 'Activity') }}</span>
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
                            <span class="hide-menu">{{ Helper::getMenuName('team', 'Team') }}</span>
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
                            <span class="hide-menu">{{ Helper::getMenuName('files', 'Files') }}</span>
                        </a>
                    </li>
                @endif --}}
                 @if (Gate::check('Manage Chat') || Gate::check('Manage Team') || Gate::check('Manage Email'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/chats') ? 'active' : '' }}"
                             href="javascript:void(0);" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseExample">

                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Messaging/Messaging.svg') }}"
                                     alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('messaging', 'Messaging') }}</span>
                             <div class="count_chat_sidebar count_chat_sidebar_count_all" style="display: none;"></div>
                         </a>
                         {{-- collapse --}}
                         <div class="collapse {{ Request::is('user/chats*') || Request::is('user/page/Team*') || Request::is('user/mail*') ? 'show' : '' }}"
                             id="collapseExample">
                             <div class="menu_bb">
                                 @if (Gate::check('Manage Chat'))
                                     <a href="{{ route('chats.index') }}">
                                         <div class="count_chat_sidebar count_chat_sidebar_count_chat"
                                             style="display: none;"></div>
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Messaging/chat.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('chats', 'Chats') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Team'))
                                     <a href="{{ route('team-chats.index') }}">
                                         <div class="count_chat_sidebar count_chat_sidebar_count_team"
                                             style="display: none;"></div>
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Messaging/Team.svg') }}"
                                                 alt="">

                                         </span>
                                         <span class="hide-menu">{{ Helper::getMenuName('team', 'Team') }}</span>
                                     </a>
                                 @endif



                                 @if (Gate::check('Manage Email'))
                                     <a href="{{ route('mail.index') }}">
                                         <div class="count_chat_sidebar count_chat_sidebar_count_mail"
                                             style="display: none;"></div>
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Messaging/Mail.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('mail', 'Mail') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif
                 @if (Gate::check('Manage Becoming Sovereigns') ||
                         Gate::check('Manage Becoming Christ Like') ||
                         Gate::check('Manage Becoming a Leader') ||
                         Gate::check('Manage File') ||
                         Gate::check('Manage Topic') ||
                         Auth::user()->hasNewRole('SUPER ADMIN'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseExampleEducation">
                             <span>
                                 <img src="{{ asset('user_assets/images/Education.svg') }}" alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('education', 'Education') }}</span>
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
                                         <span>{{ Helper::getMenuName('topics', 'Topics') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Becoming Sovereigns'))
                                     <a href="{{ route('becoming-sovereign.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/Becoming Sovereign.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('becoming_sovereign', 'Becoming Sovereign') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Becoming Christ Like'))
                                     <a href="{{ route('becoming-christ-link.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/Becoming Christ Like.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('becoming_christ_like', 'Becoming Christ Like') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Becoming a Leader'))
                                     <a href="{{ route('leadership-development.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/Leadership Development.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('becoming_leader', 'Becoming a Leader') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage File'))
                                     <a href="{{ route('file.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/Files.svg') }}" alt="">
                                         </span>
                                         <span class="hide-menu">{{ Helper::getMenuName('files', 'Files') }}</span>
                                     </a>
                                 @endif
                                 {{-- <a href="{{ route('user.page', ['name' => 'Communities of interest', 'permission' => 'Manage Education']) }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/Live Events.png') }}" alt="">
                                    </span>
                                    <span>{{ Helper::getMenuName('live_events', 'Live Events') }}</span>
                                </a> --}}
                             </div>
                         </div>
                     </li>
                 @endif
                 @if (Gate::check('Manage Job Postings') ||
                         Gate::check('Manage Meeting Schedule') ||
                         Gate::check('Manage Private Collaboration') ||
                         Gate::check('Manage Event') ||
                         Gate::check('Manage Bulletin'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseExample4">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Bulletin/Bulletin.svg') }}"
                                     alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('bulletins', 'Bulletins') }}</span>
                         </a>
                         {{-- Collapse content --}}
                         <div class="collapse {{ Request::is('user/bulletins*') || Request::is('user/view-calender*') || Request::is('user/jobs*') || Request::is('user/meetings*') || Request::is('user/private-collaborations*') || Request::is('user/bulletin-board*') || Request::is('user/events*') ? 'show' : '' }}"
                             id="collapseExample4">
                             <div class="menu_bb">
                                 @if (Gate::check('Manage Bulletin'))
                                     {{-- bulletins --}}
                                     <a href="{{ route('bulletin-board.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Bulletin/Meeting_Schedule.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('bulletin_board', 'Bulletins Board') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Bulletin'))
                                     <a href="{{ route('bulletins.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Bulletin/Create_Bulletins.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('create_bulletins', 'Create Bulletins') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Job Postings'))
                                     <a href="{{ route('jobs.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Bulletin/Job_Posting.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('job_posting', 'Job Posting') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Meeting Schedule'))
                                     <a href="{{ route('meetings.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Bulletin/Meeting_Schedule.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('meeting_schedule', 'Meeting Schedule') }}</span>
                                     </a>
                                 @endif

                                 {{-- <a href="{{ route('user.page', ['name' => 'Communities of interest', 'permission' => 'Manage Bulletin']) }}">
                                <span>
                                    <img src="{{ asset('user_assets/images/Communities of interest.png') }}" alt="">
                                </span>
                                <span>{{ Helper::getMenuName('communities_of_interest', 'Communities of interest') }}</span>
                            </a> --}}
                                 @if (Gate::check('Manage Event'))
                                     <a href="{{ route('events.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Bulletin/Live_Event.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('live_events', 'Live Events') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Private Collaboration'))
                                     <a href="{{ route('private-collaborations.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Bulletin/lecture.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('private_collaboration', 'Private Collaboration') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @else
                     {{-- <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('bulletin-board.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/Meeting Schedule.png') }}" alt="">
                            </span>
                            <span class="hide-menu">{{ Helper::getMenuName('bulletin_board', 'Bulletin Board') }}</span>
                        </a>
                    </li> --}}
                 @endif
                 @if (Gate::check('Manage Estore CMS') ||
                         Gate::check('Manage Estore Users') ||
                         Gate::check('Manage Estore Category') ||
                         Gate::check('Manage Estore Sizes') ||
                         Gate::check('Manage Estore Colors') ||
                         Gate::check('Manage Estore Products') ||
                         Gate::check('Manage Estore Settings') ||
                         Gate::check('Manage Estore Warehouse') ||
                         Gate::check('Manage Estore Orders') ||
                         Gate::check('Manage Order Status') ||
                         Gate::check('Manage Email Template'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseExample10">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Store/Store.svg') }}" alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('estore', 'E-Store') }}</span>
                         </a>
                         {{-- Collapse content --}}
                         <div class="collapse {{ Request::is('user/products*') ||
                         Request::is('user/order-status*') ||
                         Request::is('user/order-email-templates*') ||
                         Request::is('user/estore-users-list*') ||
                         Request::is('user/store-orders*') ||
                         Request::is('user/ware-houses*') ||
                         Request::is('user/store-settings*') ||
                         Request::is('user/sizes*') ||
                         Request::is('user/colors*') ||
                         Request::is('user/store-promo-codes*') ||
                         Request::is('user/warehouse-admins*') ||
                         Request::is('user/store-cms*') ||
                         Request::is('user/store-cms-page/home*') ||
                         Request::is('user/store-cms-page/footer*') ||
                         Request::is('user/categories*')
                             ? 'show'
                             : '' }}"
                             id="collapseExample10">
                             <div class="menu_bb">
                                 {{-- bulletins --}}
                                 @if (Gate::check('Manage Estore CMS'))
                                     <a href="{{ route('user.store-cms.dashboard') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/E-store_Dashboard.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('e_store_dashboard', 'E-store Dashboard') }}</span>
                                     </a>
                                 @endif

                                 {{-- @if (Gate::check('Manage Estore Users'))
                                    <a href="{{ route('estore-users.list') }}">
                                        <span>
                                            <img src="{{ asset('user_assets/images/ICON/All_Member.svg') }}"
                                                alt="">
                                        </span>
                                        <span>{{ Helper::getMenuName('e_store_users', 'E-store Users') }}</span>
                                    </a>
                                @endif --}}

                                 @if (Gate::check('Manage Estore Category'))
                                     <a href="{{ route('categories.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/Product_Categories.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('product_categories', 'Product Categories') }}</span>
                                     </a>
                                 @endif


                                 {{-- product size/colors management --}}
                                 @if (Gate::check('Manage Estore Sizes'))
                                     <a href="{{ route('sizes.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/size.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('manage_sizes', 'Manage Sizes') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Estore Colors'))
                                     <a href="{{ route('colors.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/color-wheel.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('manage_colors', 'Manage Colors') }}</span>
                                     </a>
                                 @endif





                                 @if (Gate::check('Manage Estore Settings'))
                                     <a href="{{ route('store-promo-codes.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/coupon.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('promo_codes', 'Promo Codes') }}</span>
                                     </a>

                                     <a href="{{ route('store-settings.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/settings.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('e_store_settings', 'E-store Settings') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Order Status'))
                                     <a href="{{ route('order-status.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/o-status.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('order_status', 'Order Status') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Email Template'))
                                     <a href="{{ route('order-email-templates.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/o-e-template.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('orders_email_templates', 'Orders Email Templates') }}</span>
                                     </a>
                                 @endif



                                 @if (Gate::check('Manage Estore Products'))
                                     <a href="{{ route('products.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/Products.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('products', 'Products') }}</span>
                                     </a>
                                 @endif
                                 {{-- warehouse management --}}
                                 @if (Gate::check('Manage Estore Warehouse'))
                                     <a href="{{ route('ware-houses.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/wharehouse.png') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('warehouses', 'Warehouses') }}</span>
                                     </a>
                                 @endif



                                 {{-- warehouse admin management --}}
                                 {{-- <a href="{{ route('warehouse-admins.index') }}">
                                    <span>
                                        <img src="{{ asset('user_assets/images/ICON/Store/warehouse_admin.png') }}"
                                            alt="">
                                    </span>
                                    <span>{{ Helper::getMenuName('warehouse_admins', 'Warehouse Admins') }}</span>
                                </a> --}}

                                 {{-- Orders List --}}
                                 @if (Gate::check('Manage Estore Orders'))
                                     <a href="{{ route('user.store-orders.list') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/Orders.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('orders', 'Orders') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Auth::user()->warehouses->count() > 0)
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseExampleWarehouseStore">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Store/store-wa.svg') }}" alt="">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('warehouse_store', 'Warehouse Store') }}</span>
                         </a>
                         {{-- Collapse content --}}
                         <div class="collapse {{ Request::is('user/products*') || Request::is('user/store-orders*') || Request::is('user/ware-houses*') || Request::is('user/store-settings*') || Request::is('user/sizes*') || Request::is('user/colors*') || Request::is('user/store-promo-codes*') || Request::is('user/warehouse-admins*') || Request::is('user/store-cms*') || Request::is('user/store-cms-page/home*') || Request::is('user/store-cms-page/footer*') || Request::is('user/categories*') ? 'show' : '' }}"
                             id="collapseExampleWarehouseStore">
                             <div class="menu_bb">

                                 {{-- warehouse management --}}
                                 <a href="{{ route('ware-houses.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/ICON/Store/wharehouse.png') }}"
                                             alt="">
                                     </span>
                                     <span>{{ Helper::getMenuName('warehouses', 'Warehouses') }}</span>
                                 </a>

                                 <a href="{{ route('products.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/ICON/Store/Products.svg') }}"
                                             alt="">
                                     </span>
                                     <span>{{ Helper::getMenuName('warehouse_products', 'Warehouse Products') }}</span>
                                 </a>



                                 {{-- Orders List --}}
                                 <a href="{{ route('user.store-orders.list') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/ICON/Store/Orders.svg') }}"
                                             alt="">
                                     </span>
                                     <span>{{ Helper::getMenuName('warehouse_orders', 'Warehouse Orders') }}</span>
                                 </a>
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Elearning CMS') ||
                         Gate::check('Manage Elearning Category') ||
                         Gate::check('Manage Elearning Product'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseExample11">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/elearning.png') }}" alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('elearning', 'E-Learning') }}</span>
                         </a>
                         {{-- Collapse content --}}
                         <div class="collapse {{ Request::is('user/elearning*') || Request::is('user/elearning-cms*') || Request::is('user/elearning-cms-page/home*') || Request::is('user/elearning-cms-page/footer*') || Request::is('user/elearning-categories*') ? 'show' : '' }}"
                             id="collapseExample11">
                             <div class="menu_bb">
                                 {{-- bulletins --}}
                                 @if (Gate::check('Manage Elearning CMS'))
                                     <a href="{{ route('user.elearning-cms.dashboard') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/E-store_Dashboard.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('e_learning_dashboard', 'E-learning Dashboard') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Elearning Category'))
                                     <a href="{{ route('elearning-categories.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/Product_Categories.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('e_learning_categories', 'E-learning Categories') }}</span>
                                     </a>
                                 @endif
                                 {{-- elearning topics  --}}
                                 @if (Gate::check('Manage Elearning Topic'))
                                     <a href="{{ route('elearning-topics.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/Product_Categories.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('e_learning_topics', 'E-learning Topics') }}</span>
                                     </a>
                                 @endif



                                 @if (Gate::check('Manage Elearning Product'))
                                     <a href="{{ route('elearning.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/ICON/Store/Products.svg') }}"
                                                 alt="">
                                         </span>
                                         <span>{{ Helper::getMenuName('e_learning_products', 'E-learning Products') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif
                 @if (Gate::check('Manage Role Permission'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/roles') ? 'active' : '' }}"
                             href="{{ route('roles.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Role_Permission.svg') }}"
                                     alt="">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('role_permission', 'Role Permission') }}</span>
                         </a>
                     </li>
                 @endif

                 @if (Gate::check('Manage Membership') ||
                         Gate::check('View Membership Members') ||
                         Gate::check('View Membership Payments') ||
                         Gate::check('View Membership Settings'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseMembershipManagement">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/membership-manage.png') }}"
                                     alt="">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('membership_management', 'Membership Management') }}</span>
                         </a>
                         {{-- Collapse content --}}
                         <div class="collapse {{ Request::is('user/membership/manage*') || Request::is('user/membership/create*') || Request::is('user/membership/edit*') || Request::is('user/membership/members*') || Request::is('user/membership/payments*') || Request::is('user/membership/settings*') ? 'show' : '' }}"
                             id="collapseMembershipManagement">
                             <div class="menu_bb">
                                 @if (Gate::check('Manage Membership'))
                                     <a href="{{ route('user.membership.manage') }}">
                                         <span>
                                             <i class="ti ti-list"></i>
                                         </span>
                                         <span>{{ Helper::getMenuName('membership_plan_list', 'Plan List') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Create Membership'))
                                     <a href="{{ route('user.membership.create') }}">
                                         <span>
                                             <i class="ti ti-plus"></i>
                                         </span>
                                         <span>{{ Helper::getMenuName('membership_create_plan', 'Create Plan') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('View Membership Members'))
                                     <a href="{{ route('user.membership.members') }}">
                                         <span>
                                             <i class="ti ti-users"></i>
                                         </span>
                                         <span>{{ Helper::getMenuName('membership_members', 'Members') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('View Membership Payments'))
                                     <a href="{{ route('user.membership.payments') }}">
                                         <span>
                                             <i class="ti ti-credit-card"></i>
                                         </span>
                                         <span>{{ Helper::getMenuName('membership_all_payments', 'All Payments') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('View Membership Settings'))
                                     <a href="{{ route('user.membership.settings') }}">
                                         <span>
                                             <i class="ti ti-settings"></i>
                                         </span>
                                         <span>{{ Helper::getMenuName('membership_settings', 'Settings') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Partners'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/partners/*') ? 'active' : '' }}"
                             href="{{ route('partners.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/All_Member.svg') }}" alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('all_members', 'All Members') }}</span>
                         </a>
                     </li>
                 @endif
                 @if (Gate::check('Manage User Activity'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/user-activity*') ? 'active' : '' }}"
                             href="javascript:void(0);" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseUserActivity">

                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/activity.png') }}" alt="">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('user_activity', 'User Activity') }}</span>
                         </a>

                         <div class="collapse {{ Request::is('user/user-activity*') ? 'show' : '' }}"
                             id="collapseUserActivity">
                             <div class="menu_bb">
                                 <a href="{{ route('user-activity.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/ICON/visual-data.png') }}"
                                             alt="">
                                     </span>
                                     <span>{{ Helper::getMenuName('activity_dashboard', 'Activity Dashboard') }}</span>
                                 </a>
                                 <a href="{{ route('user-activity-get-list') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/ICON/completed-task.png') }}"
                                             alt="">
                                     </span>
                                     <span>{{ Helper::getMenuName('activity_list', 'Activity List') }}</span>
                                 </a>
                             </div>
                         </div>
                     </li>
                 @endif
                 {{-- @if (Auth::user()->hasNewRole('SUPER ADMIN'))
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ Request::is('user/ecclesias/*') ? 'active' : '' }}"
                            href="{{ route('ecclesias.index') }}" aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Ecclesias.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">{{ Helper::getMenuName('ecclesias', 'Ecclesias') }}</span>
                        </a>
                    </li>
                 @endif
                 {{-- Signup Rules Management --}}
                 @if (Auth::user()->hasNewRole('SUPER ADMIN'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/signup-rules*') ? 'active' : '' }}"
                             href="{{ route('user.signup-rules.index') }}" aria-expanded="false">
                             <span>
                                 <i class="ti ti-clock-check fs-6"></i>
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('signup_rules', 'Signup Rules') }}</span>
                         </a>
                     </li>
                 @endif
                 @if (Gate::check('Manage Strategy'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="{{ route('strategy.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Strategy.svg') }}" alt="">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('strategy', 'Strategy') }}</span>
                         </a>
                     </li>
                 @endif

                 @if (Gate::check('Manage Policy'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="{{ route('policy-guidence.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/policy_and_guidance.png') }}"
                                     alt="">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('policy_guidance', 'Policy & Guidance') }}</span>
                         </a>
                     </li>
                 @endif
                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ route('user.membership.index') }}" aria-expanded="false">
                         <span>
                             <img src="{{ asset('user_assets/images/ICON/membership.png') }}" alt="">
                         </span>
                         <span class="hide-menu">{{ Helper::getMenuName('membership', 'Membership') }}</span>
                     </a>
                 </li>


                 {{-- //*********************************************** Admin Portal menu --}}


                 @if (Gate::check('Manage Donations'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/admin/donations*') ? 'active' : '' }}"
                             href="{{ route('user.admin.donations.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/donation.png') }}"
                                     alt="Donations">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('donations', 'Donations') }}</span>
                         </a>
                     </li>
                 @endif

                 {{-- @if (Gate::check('Manage Contact Us Messages'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/admin/contact-us*') ? 'active' : '' }}"
                             href="{{ route('user.admin.contact-us.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/contact-massage.png') }}"
                                     alt="Contact Us Messages">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('contact_us_messages', 'Contact Us Messages') }}</span>
                         </a>
                     </li>
                 @endif --}}

                 @if (Gate::check('Manage Newsletters'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/admin/newsletters*') ? 'active' : '' }}"
                             href="{{ route('user.admin.newsletters.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/news-letters.png') }}"
                                     alt="Newsletters">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('newsletters', 'Newsletters') }}</span>
                         </a>
                     </li>
                 @endif

                 @if (Gate::check('Manage Testimonials'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseTestimonials">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/testimonials.png') }}"
                                     alt="Testimonials">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('testimonials', 'Testimonials') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/testimonials*') ? 'show' : '' }}"
                             id="collapseTestimonials">
                             <div class="menu_bb">
                                 <a href="{{ route('user.admin.testimonials.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/testimonial-list.png') }}"
                                             alt="Testimonials List">
                                     </span>
                                     <span>{{ Helper::getMenuName('testimonials_list', 'Testimonials List') }}</span>
                                 </a>
                                 @if (Gate::check('Create Testimonials'))
                                     <a href="{{ route('user.admin.testimonials.create') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/create-testimonials.png') }}"
                                                 alt="Create Testimonial">
                                         </span>
                                         <span>{{ Helper::getMenuName('testimonials_create', 'Testimonials Create') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Our Governance'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseOurGovernance">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/our-governance.png') }}"
                                     alt="Our Governance">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('our_governance', 'Our Governance') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/our-governances*') ? 'show' : '' }}"
                             id="collapseOurGovernance">
                             <div class="menu_bb">
                                 <a href="{{ route('user.admin.our-governances.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/our-governance-list.png') }}"
                                             alt="Our Governance List">
                                     </span>
                                     <span>{{ Helper::getMenuName('our_governance_list', 'Our Governance List') }}</span>
                                 </a>
                                 @if (Gate::check('Create Our Governance'))
                                     <a href="{{ route('user.admin.our-governances.create') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/our-governance-create.png') }}"
                                                 alt="Our Governance Create">
                                         </span>
                                         <span>{{ Helper::getMenuName('our_governance_create', 'Our Governance Create') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Our Organization'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseOurOrganizations">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/our-organisations.png') }}"
                                     alt="Our Organizations">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('our_organizations', 'Our Organizations') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/our-organizations*') ? 'show' : '' }}"
                             id="collapseOurOrganizations">
                             <div class="menu_bb">
                                 <a href="{{ route('user.admin.our-organizations.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/our-organisation-list.png') }}"
                                             alt="Our Organizations List">
                                     </span>
                                     <span>{{ Helper::getMenuName('our_organizations_list', 'Our Organizations List') }}</span>
                                 </a>
                                 @if (Gate::check('Create Our Organization'))
                                     <a href="{{ route('user.admin.our-organizations.create') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/our-organisation-list.png') }}"
                                                 alt="Plus">
                                         </span>
                                         <span>{{ Helper::getMenuName('our_organizations_create', 'Our Organizations Create') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Organization Center'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseOrganizationCenter">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/organisation-center.png') }}"
                                     alt="Organization Center">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('organization_center', 'Organization Center') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/organization-centers*') ? 'show' : '' }}"
                             id="collapseOrganizationCenter">
                             <div class="menu_bb">
                                 <a href="{{ route('user.admin.organization-centers.index') }}">
                                     <span>
                                         <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/organisation-center-list.png') }}"
                                             alt="Organization Center List">
                                     </span>
                                     <span>{{ Helper::getMenuName('organization_center_list', 'Organization Center List') }}</span>
                                 </a>
                                 @if (Gate::check('Create Organization Center'))
                                     <a href="{{ route('user.admin.organization-centers.create') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/organisation-center-list.png') }}"
                                                 alt="Plus">
                                         </span>
                                         <span>{{ Helper::getMenuName('organization_center_create', 'Organization Center Create') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Services'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseServices">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/services.png') }}"
                                     alt="Services">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('services', 'Services') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/services*') ? 'show' : '' }}"
                             id="collapseServices">
                             <div class="menu_bb">
                                 @if (count(Helper::getOrganzations()) > 0)
                                     @foreach (Helper::getOrganzations() as $key => $organization)
                                         <a
                                             href="{{ route('user.admin.services.index', ['slug' => $organization->slug]) }}">
                                             <span>
                                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/education-center.png') }}"
                                                     alt="Service">
                                             </span>
                                             <span>{{ $organization->name }}</span>
                                         </a>
                                     @endforeach
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Home Page') ||
                         Gate::check('Manage Details Page') ||
                         Gate::check('Manage Organizations Page') ||
                         Gate::check('Manage About Us Page') ||
                         Gate::check('Manage Faq') ||
                         Gate::check('Manage Gallery') ||
                         Gate::check('Manage Ecclesia Association Page') ||
                         Gate::check('Manage Principle and Business Page') ||
                         //  Gate::check('Manage Contact Us Page') ||
                         Gate::check('Manage Article of Association Page') ||
                         Gate::check('Manage Footer') ||
                         Gate::check('Manage Register Page Agreement Page') ||
                         Gate::check('Manage Member Privacy Policy Page') ||
                         Gate::check('Manage PMA Terms Page') ||
                         Gate::check('Manage Privacy Policy Page') ||
                         Gate::check('Manage Terms and Conditions Page'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapsePages">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/pages.png') }}"
                                     alt="Pages">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('pages', 'Pages') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/pages*') ? 'show' : '' }}"
                             id="collapsePages">
                             <div class="menu_bb">
                                 @if (Gate::check('Manage Home Page'))
                                     <a href="{{ route('user.admin.home-cms.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/home.png') }}"
                                                 alt="Home">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_home', 'Home') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Details Page'))
                                     <a href="{{ route('user.admin.details.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/details.png') }}"
                                                 alt="Details">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_details', 'Details') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Organizations Page'))
                                     <a href="{{ route('user.admin.organizations.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/organisation-cms.png') }}"
                                                 alt="Organization CMS">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_organization_cms', 'Organization CMS') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage About Us Page'))
                                     <a href="{{ route('user.admin.about-us.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/about-us.png') }}"
                                                 alt="About Us">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_about_us', 'About Us') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Faq'))
                                     <a href="{{ route('user.admin.faq.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/faqs.png') }}"
                                                 alt="FAQ">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_faqs', 'FAQS') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Gallery'))
                                     <a href="{{ route('user.admin.gallery.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/gallery.png') }}"
                                                 alt="Gallery">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_gallery', 'Gallery') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Ecclesia Association Page'))
                                     <a href="{{ route('user.admin.ecclesia-associations.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/principle-and-business-modal.png') }}"
                                                 alt="Ecclesia Association">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_ecclesia_association', 'ECCLESIA ASSOCIATION') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Principle and Business Page'))
                                     <a href="{{ route('user.admin.principle-and-business.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/principle-and-business-modal.png') }}"
                                                 alt="Principle and Business">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_principle_and_business', 'PRINCIPLE AND BUSINESS MODEL') }}</span>
                                     </a>
                                 @endif

                                 {{-- @if (Gate::check('Manage Contact Us Page'))
                                     <a href="{{ route('user.admin.contact-us-cms.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/contact-us.png') }}"
                                                 alt="Contact Us">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_contact_us', 'CONTACT US') }}</span>
                                     </a>
                                 @endif --}}

                                 @if (Gate::check('Manage Article of Association Page'))
                                     <a href="{{ route('user.admin.articles-of-association.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/artailes-of-assosiations.png') }}"
                                                 alt="Article of Association">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_articles_of_association', 'ARTICLES OF ASSOCIATION') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Footer'))
                                     <a href="{{ route('user.admin.footer.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/footer.png') }}"
                                                 alt="Footer">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_footer', 'Footer') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Register Page Agreement Page'))
                                     <a href="{{ route('user.admin.register-agreements.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/register-page-agreement.png') }}"
                                                 alt="Register Page Agreement">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_register_agreements', 'REGISTER PAGE AGREEMENTS') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage PMA Terms Page'))
                                     <a href="{{ route('user.admin.pma-terms.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/pma-terms.png') }}"
                                                 alt="PMA Terms">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_pma_terms', 'PMA Terms') }}</span>
                                     </a>
                                 @endif

                                 @if (Gate::check('Manage Privacy Policy Page'))
                                     <a href="{{ route('user.admin.privacy-policy.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/privacy-policy.png') }}"
                                                 alt="Privacy Policy">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_privacy_policy', 'Privacy Policy') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Terms and Conditions Page'))
                                     <a href="{{ route('user.admin.terms-and-condition.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/terms-and-condition.png') }}"
                                                 alt="Terms and Condition">
                                         </span>
                                         <span>{{ Helper::getMenuName('pages_terms_and_conditions', 'Terms and Conditions') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 @if (Gate::check('Manage Countries'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/admin/admin-countries*') ? 'active' : '' }}"
                             href="{{ route('user.admin.admin-countries.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/countries.png') }}"
                                     alt="Countries">
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('countries', 'Countries') }}</span>
                         </a>
                     </li>
                 @endif

                 @if (Gate::check('Manage Site Settings') || Gate::check('Manage Menu Settings'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseSiteSettings">
                             <span>
                                 <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/site-setting.png') }}"
                                     alt="Site Settings">
                             </span>
                             <span
                                 class="hide-menu">{{ Helper::getMenuName('site_settings', 'Site Settings') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/settings*') || Request::is('user/admin/menu*') ? 'show' : '' }}"
                             id="collapseSiteSettings">
                             <div class="menu_bb">
                                 @if (Gate::check('Manage Site Settings'))
                                     <a href="{{ route('user.admin.settings.edit') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/setting.png') }}"
                                                 alt="Settings">
                                         </span>
                                         <span>{{ Helper::getMenuName('site_settings_settings', 'Settings') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('Manage Menu Settings'))
                                     <a href="{{ route('user.admin.menu.index') }}">
                                         <span>
                                             <img src="{{ asset('user_assets/images/lion-roring-icon/lion-roring-icon/menu-names.png') }}"
                                                 alt="Menu Names">
                                         </span>
                                         <span>{{ Helper::getMenuName('site_settings_menu_names', 'Menu Names') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif


                 {{-- @if (Gate::check('Manage Help'))
                    <li class="sidebar-item">
                        <a class="sidebar-link"
                            href="{{ route('user.page', ['name' => 'Help', 'permission' => 'Manage Help']) }}"
                            aria-expanded="false">
                            <span>
                                <img src="{{ asset('user_assets/images/ICON/Help.svg') }}" alt="">
                            </span>
                            <span class="hide-menu">{{ Helper::getMenuName('help', 'Help') }}</span>
                        </a>
                    </li>
                @endif --}}
                 @if (Gate::check('Manage Chatbot'))
                     <li class="sidebar-item">
                         <a class="sidebar-link" href="#" aria-expanded="false" data-bs-toggle="collapse"
                             data-bs-target="#collapseChatbot">
                             <span>
                                 <i class="fas fa-robot fs-4"></i>
                             </span>
                             <span class="hide-menu">{{ Helper::getMenuName('chatbot', 'Chatbot Assistant') }}</span>
                         </a>
                         <div class="collapse {{ Request::is('user/admin/chatbot*') ? 'show' : '' }}"
                             id="collapseChatbot">
                             <div class="menu_bb">
                                 <a href="{{ route('user.admin.chatbot.index') }}">
                                     <span><i class="fas fa-tachometer-alt"></i></span>
                                     <span>{{ Helper::getMenuName('chatbot_dashboard', 'Dashboard') }}</span>
                                 </a>
                                 @if (Gate::check('Manage Chatbot Keywords'))
                                     <a href="{{ route('user.admin.chatbot.keywords') }}">
                                         <span><i class="fas fa-key"></i></span>
                                         <span>{{ Helper::getMenuName('chatbot_keywords', 'Keywords') }}</span>
                                     </a>
                                 @endif
                                 @if (Gate::check('View Chatbot History'))
                                     <a href="{{ route('user.admin.chatbot.conversations') }}">
                                         <span><i class="fas fa-history"></i></span>
                                         <span>{{ Helper::getMenuName('chatbot_history', 'History') }}</span>
                                     </a>
                                 @endif
                             </div>
                         </div>
                     </li>
                 @endif

                 <br>
                 <br>
                 <br>
             </ul>
         </nav>

         <!-- End Sidebar navigation -->
     </div>
     <!-- End Sidebar scroll-->
 </aside>
