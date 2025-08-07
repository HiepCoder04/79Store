<style>
.sidenav-header .navbar-brand span {
    color: #333;
}

.navbar-nav .nav-item .nav-link .nav-link-text {
    font-size: 14px;
    color: #333;
}
</style>

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard "
            target="_blank">
            <!-- <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo"> -->
            <span class="ms-1 fs-4 text-dark">ADMIN</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="material-symbols-rounded opacity-5">dashboard</i>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <!-- Menu chính có collapse -->
                <a class="nav-link text-dark" data-bs-toggle="collapse" href="#submenuQuanLi" role="button"
                    aria-expanded="false" aria-controls="submenuQuanLi">
                    <i class="material-symbols-rounded opacity-5">table_view</i>
                    <span class="nav-link-text ms-1">QUẢN LÍ</span>
                </a>

                <!-- Submenu -->
                <div class="collapse" id="submenuQuanLi">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.orders.index') }}">
                                <span class="nav-link-text">Quản Lí Đơn hàng</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.products.index') }}">
                                <span class="nav-link-text">Quản Lí Sản phẩm</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.categories.index') }}">
                                <span class="nav-link-text">Quản lí Danh Mục</span>
                            </a>
                        </li>

            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.vouchers.index') }}">
                    <span class="nav-link-text">Quản Lí Voucher</span>
                </a>
            </li>
            {{-- quan li chậu --}}
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.pot.index') }}">
                    <span class="nav-link-text">Quản Lí Chậu</span>
                </a>
            </li>

            @if(Auth::user()->role === 'admin')
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.users.list') }}">
                    <span class="nav-link-text">Quản Lí Người dùng</span>
                </a>
            </li>
            @endif

            <li class="nav-item">
    <a class="nav-link text-dark" href="{{ route('admin.contacts.index') }}">
        <span class="nav-link-text">Quản Lí Liên hệ</span>
    </a>
</li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.banners.index') }}">
                    <span class="nav-link-text">Quản Lí Banner</span>
                </a>


            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.blogs.index') }}">
                    <span class="nav-link-text">Quản Lí Blog</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.category_blogs.index') }}">
                    <span class="nav-link-text">Quản Lí Danh Mục Blog</span>
                </a>
            </li>
     


        </ul>
    </div>
    </li>

    <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Account pages</h6>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="../pages/profile.html">
            <i class="material-symbols-rounded opacity-5">person</i>
            <span class="nav-link-text ms-1">Profile</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('auth.login') }}">
            <i class="material-symbols-rounded opacity-5">login</i>
            <span class="nav-link-text ms-1">Đăng Nhập</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('auth.register') }}">
            <i class="material-symbols-rounded opacity-5">assignment</i>
            <span class="nav-link-text ms-1">Đăng Ký</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('auth.logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="material-symbols-rounded opacity-5">logout</i>
            <span class="nav-link-text ms-1">Đăng Xuất</span>
        </a>

        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
    </ul>
    </div>
</aside>
