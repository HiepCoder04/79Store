<header class="header-area">

    <!-- Top Header Area -->
    <div class="top-header-area py-2  text-white">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="top-header-content d-flex align-items-center justify-content-between">

                        <!-- Thông tin liên hệ -->
                        <div class="top-header-meta">
                            <a href="#"><i class="fa fa-envelope-o"></i> <span>Email: lienhe@79store.com</span></a>
                            <a href="#"><i class="fa fa-phone"></i> <span>Gọi: 0123 456 789</span></a>
                        </div>

                        <!-- User + Ngôn ngữ + Cart -->
                        <div class="top-header-meta d-flex align-items-center gap-3">

                            <!-- Ngôn ngữ -->
                            {{-- <div class="language-dropdown dropdown mr-3">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="languageDropdown" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Ngôn ngữ
                                </button>
                                <div class="dropdown-menu" aria-labelledby="languageDropdown">
                                    <a class="dropdown-item" href="#">Tiếng Việt</a>
                                    <a class="dropdown-item" href="#">English</a>
                                </div>
                            </div> --}}

                            <!-- Đăng nhập / User -->
                            <div class="top-header-right d-flex align-items-center justify-content-end gap-1">

                                <div class="login">
                                    @if (Auth::check())
                                        <div class="user-dropdown">
                                            <a href="#" class="user-toggle">
                                                <i class="fa fa-user"></i>
                                                @if (Auth::user()->avatar)
                                                    <img id="header-avatar"
                                                        src="{{ asset('img/avatars/' . Auth::user()->avatar) }}"
                                                        alt="Avatar"
                                                        style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                                @endif
                                                <span>{{ Auth::user()->name }}</span>
                                            </a>
                                            <ul class="user-dropdown-menu">
                                                <li>
                                                    <a href="{{ route('client.account.index') }}">
                                                        <i class="fa fa-user-circle"></i> Tài khoản của tôi
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('auth.logout') }}"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        <i class="fa fa-sign-out-alt"></i> Đăng xuất
                                                    </a>
                                                    <form id="logout-form" action="{{ route('auth.logout') }}"
                                                        method="POST" style="display: none;">@csrf</form>
                                                </li>
                                            </ul>

                                        </div>
                                    @else
                                        <a href="{{ route('auth.login') }}">
                                            <i class="fa fa-user"></i> <span>Đăng nhập</span>
                                        </a>
                                        <span class="mx-1"></span>
                                        <a href="{{ route('auth.register') }}">
                                            <span>Đăng ký</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Giỏ hàng -->
                                <div class="cart ml-3">
                                    <a href="{{ route('cart.index') }}" id="cart-icon">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span>Giỏ hàng <span id="cart-count">({{ $cartItemCount ?? 0 }})</span></span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <div class="alazea-main-menu">
        <div class="classy-nav-container breakpoint-off">
            <div class="container">
                <nav class="classy-navbar justify-content-between" id="alazeaNav">

                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="nav-brand">
                        <img src="{{ asset('assets/img/core-img/leaf.png') }}" alt="79Store Logo"
                            style="height: 50px;">
                    </a>

                    <!-- Toggle (Responsive) -->
                    <div class="classy-navbar-toggler">
                        <span class="navbarToggler">
                            <span></span><span></span><span></span>
                        </span>
                    </div>

                    <!-- Menu -->
                    <div class="classy-menu">
                        <div class="classycloseIcon">
                            <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                        </div>

                        <div class="classynav">
                            <ul>
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><a href="{{ route('about') }}">Giới thiệu</a></li>
                                <li><a href="{{ route('shop') }}">Sản phẩm</a></li>
                                <li><a href="{{ route('client.blogs.index') }}">Tin tức</a></li>
                                <li><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                                <li><a href="{{ route('client.contact.form') }}">Liên hệ</a></li>

                            </ul>

                            <!-- Search -->
                            <div id="searchIcon" class="ml-3">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Form tìm kiếm -->
                <div class="search-form">
                    <form action="{{ route('shop') }}" method="GET">
                        <input type="search" name="keyword" placeholder="Tìm sản phẩm..."
                            value="{{ request('keyword') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Style cho user dropdown và responsive -->
<style>
    .user-dropdown {
        position: relative;
        display: inline-block;
    }

    .user-dropdown .user-toggle {
        color: #5c5c5c;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }

    .user-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        list-style: none;
        padding: 10px 0;
        margin: 0;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        min-width: 140px;
        background-color: rgb(245, 233, 233);
        z-index: 999;
    }

    .user-dropdown:hover .user-dropdown-menu {
        display: block;
    }

    .user-dropdown-menu li {
        padding: 5px 20px;
    }

    .user-dropdown-menu li a {
        color: #333;
        text-decoration: none;
        display: block;
        text-align: center;
        padding: 8px 10px;
        transition: background-color 0.2s ease;
    }

    .user-dropdown-menu li a:hover {
        background-color: #13e68b;
        color: #000 !important;
    }

    .user-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: rgb(17, 148, 26);
        border: 1px solid #ccc;
        padding: 10px;
        list-style: none;
        z-index: 999;
    }

    .user-dropdown:hover .user-dropdown-menu {
        display: block;
    }

    .user-toggle {
        text-decoration: none;
        color: #333;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .login a,
    .cart a {
        text-decoration: none;
        color: #fff;
    }

    .cart-quantity {
        font-weight: bold;
        color: #ffc107;
    }
</style>

@include('client.layouts.thongbao')
