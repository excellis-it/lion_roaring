<div class="main_menu_hdr">
    <div class="container-fluid">
        <div class="main_menu">
            <div class="navigation navbar">
                <div class="left_top">
                    <div class="logo">
                        <a href="{{ route('home') }}" class="">
                        </a>
                    </div>
                </div>
                <div class="right_top d-flex align-items-center">
                    <div class="menu-wrepper d-flex">
                        <div class="right_btm">
                            <div class="overlay" id="overlay">
                                <nav class="overlay-menu">
                                    <ul id="menu-header-menu" class="menu">
                                        <li class="active">
                                            <a href="{{ route('home') }}">Home</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('organization') }}">Organization</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('ecclesia-associations') }}">Ecclesia Association</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('principle-and-business') }}">Principle and Business
                                                Model</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('gallery') }}">Gallery</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('faq') }}">FAQ</a>
                                        </li>
                                        {{-- <li>
                                        <a href="{{ route('contact-us') }}">Contact Us</a>
                                    </li> --}}
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="right_tran ms-2">
                            <div id="google_translate_element"></div>
                        </div>
                    </div>
                    <div class="right-site-btns d-flex">
                        <div class="login_prf">
                            @if (auth()->check())
                                @php

                                    $user = auth()
                                        ->user()
                                        ->roles()
                                        ->whereIn('type', [1, 2, 3])
                                        ->first();
                                @endphp
                                @if ($user)
                                    <a href="{{ route('user.profile') }}"><span><i class="fa-solid fa-user"></i></span>
                                        Profile</a>
                                @else
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#loginModal"
                                        href="{{ route('login') }}"><span><i class="fa-solid fa-user"></i></span>
                                        Login</a>
                                @endif
                            @else
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#loginModal"
                                    href="{{ route('login') }}"><span><i class="fa-solid fa-user"></i></span> Login</a>
                            @endif
                        </div>
                        <div class="login_prf">
                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                data-bs-target="#exampleModal1"><span><i
                                        class="fa-solid fa-hand-holding-medical"></i></span> Gift/Seed</a>
                        </div>
                        {{-- <div class="login_prf e-store-btn">
                            <a href="{{ route('e-store') }}"><span><i class="fa-solid fa-store"></i></span> E-Store</a>
                        </div> --}}

                        <div class="button_container" id="toggle">
                            <span class="top"></span>
                            <span class="middle"></span>
                            <span class="bottom"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
