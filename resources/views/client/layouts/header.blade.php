<header class="header-area">

    <!-- ***** Top Header Area ***** -->
    <div class="top-header-area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="top-header-content d-flex align-items-center justify-content-between">
                        <!-- Top Header Content -->
                        <div class="top-header-meta">
                            <a href="#" data-toggle="tooltip" data-placement="bottom"
                                title="infodeercreative@gmail.com"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <span>Email: infodeercreative@gmail.com</span></a>
                            <a href="#" data-toggle="tooltip" data-placement="bottom" title="+1 234 122 122"><i
                                    class="fa fa-phone" aria-hidden="true"></i> <span>Call Us: +1 234 122 122</span></a>
                        </div>

                        <!-- Top Header Content -->
                        <div class="top-header-meta d-flex">
                            <!-- Language Dropdown -->
                            <div class="language-dropdown">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle mr-30" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">Language</button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#">USA</a>
                                        <a class="dropdown-item" href="#">UK</a>
                                        <a class="dropdown-item" href="#">Bangla</a>
                                        <a class="dropdown-item" href="#">Hindi</a>
                                        <a class="dropdown-item" href="#">Spanish</a>
                                        <a class="dropdown-item" href="#">Latin</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Login -->
                            <div class="login">
                                @if (Auth::check())
                                <div class="user-dropdown">
                                    <a href="#" class="user-toggle">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <span>{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="user-dropdown-menu">
                                        <li>
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                Đăng Xuất
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @else
                                <a href="{{ route('login') }}">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    <span>Login</span>
                                </a>
                                @endif
                            </div>


                            <!-- Cart -->
                            <div class="cart">
                                <a href="#"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <span>Cart <span
                                            class="cart-quantity">(1)</span></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ***** Navbar Area ***** -->
    <div class="alazea-main-menu">
        <div class="classy-nav-container breakpoint-off">
            <div class="container">
                <!-- Menu -->
                <nav class="classy-navbar justify-content-between" id="alazeaNav">

                    <!-- Nav Brand -->
                    <a href="index.html" class="nav-brand"><img src="img/core-img/logo.png" alt=""></a>

                    <!-- Navbar Toggler -->
                    <div class="classy-navbar-toggler">
                        <span class="navbarToggler"><span></span><span></span><span></span></span>
                    </div>

                    <!-- Menu -->
                    <div class="classy-menu">

                        <!-- Close Button -->
                        <div class="classycloseIcon">
                            <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                        </div>

                        <!-- Navbar Start -->
                        <div class="classynav">
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{route('about')}}">Giới Thiệu</a></li>
                                <li><a href="#">Pages</a>
                                    <ul class="dropdown">
                                        <li><a href="{{route('home')}}">Home</a></li>
                                        <li><a href="{{route('about')}}">Giới Thiệu</a></li>
                                        <li><a href="shop.html">Shop</a>
                                            <ul class="dropdown">
                                                <li><a href="{{ route('shop') }}">Shop</a></li>
                                                <li><a href="{{ route('shop-detail') }}">Shop Details</a></li>
                                                <li><a href="cart.html">Shopping Cart</a></li>
                                                <li><a href="checkout.html">Checkout</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="blog.html">Blog</a>
                                            <ul class="dropdown">
                                                <li><a href="blog.html">Blog</a></li>
                                                <li><a href="single-post.html">Blog Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ route('shop') }}">Shop</a></li>
                                <li><a href="portfolio.html">Portfolio</a></li>
                                <li><a href="contact.html">Contact</a></li>
                            </ul>

                            <!-- Search Icon -->
                            <div id="searchIcon">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </div>

                        </div>
                        <!-- Navbar End -->
                    </div>
                </nav>

                <!-- Search Form -->
                <div class="search-form">
                    <form action="#" method="get">
                        <input type="search" name="search" id="search" placeholder="Type keywords &amp; press enter...">
                        <button type="submit" class="d-none"></button>
                    </form>
                    <!-- Close Icon -->
                    <div class="closeIcon"><i class="fa fa-times" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
.user-dropdown {
    position: relative;
    display: inline-block;
}

.user-dropdown .user-toggle {
    color: #5c5c5c;
    /* màu chữ cho tên user */
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
    /* background-color: #ffffff; */
    list-style: none;
    padding: 10px 0;
    margin: 0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    min-width: 140px;
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
    /* 👉 căn giữa chữ */
    padding: 8px 10px;
    /* 👉 khoảng cách đều hơn */
    transition: background-color 0.2s ease;
}


.user-dropdown-menu li a:hover {
    /* background-color: #f0f0f0; */
    color: #000;
    /* vẫn giữ màu chữ khi hover */
}
</style>
@include('client.layouts.thongbao')