<div class="main_menu_hdr">
    <div class="container-fluid">
        <div class="main_menu">
            <div class="navigation navbar">
                <div class="left_top me-auto">
                    <div class="logo">
                        <a href="{{ route('e-learning') }}" class="">
                            <img src="{{ asset('ecom_assets/images/logo.png') }}" alt="" />
                        </a>
                    </div>
                </div>
                <div class="right_btm">
                    <div id="cssmenu">
                        <ul>
                            <li><a href="{{ route('e-learning') }}">Home</a></li>
                            @php
                                $elearningCategories = \App\Models\ElearningCategory::where('status', 1)
                                    ->orderBy('name', 'asc')
                                    ->get();
                            @endphp
                            @if (isset($elearningCategories) && count($elearningCategories) > 0)
                                <li class="has-sub">
                                    <a href="javascript:void(0);">Category</a>
                                    <ul>
                                        @foreach ($elearningCategories as $cat)
                                            @if (isset($cat->slug) && $cat->slug)
                                                <li><a
                                                        href="{{ url('e-learning/' . $cat->slug) }}">{{ $cat->name }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                            <li><a href="{{ route('e-learning.all-products') }}">our collections</a></li>
                            <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="right_top">
                    <div class="right_login">
                        <div class="d-flex align-items-center justify-content-end">
                            <a href="{{ route('user.profile') }}" class="back_main">back to main page</a>
                            <div class="profile_dropdown">
                                <div class="dropdown">
                                    <button class="profile_img_round dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span>
                                            @if (Auth::user()->profile_picture)
                                                <img src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                                    alt="" />
                                            @else
                                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}"
                                                    alt="" />
                                            @endif
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('user.profile') }}">Profile</a>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('user.change.password') }}">Change
                                                Password</a></li>
                                        <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- <div class="icon_c ms-2">
            <a href="" class=""><i class="fa-solid fa-user"></i></a>
          </div>
          <div class="icon_c ms-2">
            <a href="" class="add_cart_active"><i class="fa-solid fa-cart-shopping"></i></a>
          </div>
          <div class="icon_c ms-2">
            <a href="" class="add_cart_active"><i class="fa-solid fa-heart"></i></a>
          </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
