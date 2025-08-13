<header class="header-area">

    <!-- Top Header Area -->
    <div class="top-header-area py-2  text-white">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="top-header-content d-flex align-items-center justify-content-between">

                        <!-- Thông tin liên hệ -->
                        <div class="top-header-meta">
                            <a href="#"><i class="fa fa-envelope-o"></i> <span>Email: 79store@gmail.com</span></a>
                            <a href="#"><i class="fa fa-phone"></i> <span>Gọi: 0336994436</span></a>
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
                                            <i class="fa fa-caret-down ml-1"></i>
                                        </a>

                                        <ul class="user-dropdown-menu">
                                            <li>
                                                <a href="{{ route('client.account.index') }}">
                                                     Tài khoản của tôi
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('client.orders.index') }}">
                                                     Đơn hàng
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('auth.logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                     Đăng xuất
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

              <!-- Form tìm kiếm + dropdown  -->
<div class="search-form">
  <div id="searchWrap" class="search-wrap">
    <form action="{{ route('shop') }}" method="GET" onsubmit="return gotoFirstResult(event)">
      <input
        id="searchInput"
        type="search"
        name="keyword"
        placeholder="Tìm sản phẩm..."
        value="{{ request('keyword') }}"
        autocomplete="off"
        data-url="{{ route('search.suggest') }}"
      >
    </form>
    <div id="searchDropdown" class="search-dropdown"></div>
  </div>
</div>
            </div>
        </div>
    </div>
</header>
<script>
(function(){
  const input = document.getElementById('searchInput');
  const box   = document.getElementById('searchDropdown');
  if (!input || !box) return;

  const url   = input.dataset.url;
  const DETAIL_PREFIX_BY_SLUG = "{{ url('/shopDetail') }}";
  let timer = null, lastItems = [];

  function debounce(fn, delay=250){
    return function(...args){ clearTimeout(timer); timer=setTimeout(()=>fn.apply(this,args), delay); }
  }

  // Đặt dropdown NGAY DƯỚI ô tìm kiếm, NGANG đúng bằng ô
  function placeDropdown(){
    const r = input.getBoundingClientRect();
    box.style.left  = r.left + 'px';
    box.style.top   = (r.bottom + 6) + 'px';   // cách ô 6px
    box.style.width = r.width + 'px';
  }
    //lay id sp
  function buildDetailUrl(item){
    return item.slug ? `${DETAIL_PREFIX_BY_SLUG}/${encodeURIComponent(item.id)}` : '#';
  }

  function render(items){
    if (!Array.isArray(items) || items.length === 0){
      box.innerHTML = `<div class="search-empty">Không tìm thấy sản phẩm</div>`;
      placeDropdown();
      box.style.display = 'block';
      return;
    }
    lastItems = items;
    box.innerHTML = items.map(it => `
      <a class="search-item" href="${buildDetailUrl(it)}">
        <img src="${it.thumb}" alt="">
        <span class="title">${it.name}</span>
      </a>
    `).join('');
    placeDropdown();
    box.style.display = 'block';
  }

  async function fetchSuggest(q){
    if (!q || q.trim()===''){ box.style.display='none'; return; }
    try{
      const res = await fetch(`${url}?q=${encodeURIComponent(q)}`, { headers: {'X-Requested-With':'XMLHttpRequest'} });
      const data = await res.json();
      render(data);
    }catch(e){
      console.error(e);
      box.innerHTML = `<div class="search-empty">Có lỗi xảy ra</div>`;
      placeDropdown();
      box.style.display = 'block';
    }
  }

  input.addEventListener('input', debounce(e => fetchSuggest(e.target.value), 250));

  // Luôn căn lại khi resize/scroll (nav cố định, theme animation…)
  window.addEventListener('resize', placeDropdown);
  window.addEventListener('scroll', placeDropdown, true);

  // Ẩn khi click ra ngoài hoặc nhấn ESC
  document.addEventListener('click', (e) => { if (!box.contains(e.target) && e.target !== input) box.style.display = 'none'; });
  input.addEventListener('keydown', (e) => { if (e.key === 'Escape') box.style.display = 'none'; });

  // Enter -> mở kết quả đầu tiên nếu có
  window.gotoFirstResult = function(ev){
    if (lastItems.length > 0) {
      const href = buildDetailUrl(lastItems[0]);
      if (href && href !== '#'){ window.location.href = href; ev.preventDefault(); return false; }
    }
    return true;
  };
})();
</script>



<!-- Style cho user dropdown và responsive -->
<style>
    .user-dropdown {
        position: relative;
        display: inline-block;
    }

    .user-dropdown .user-toggle {
        color: #333;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }

    .user-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        list-style: none;
        padding: 8px 0;
        min-width: 180px;
        z-index: 999;
    }

    .user-dropdown:hover .user-dropdown-menu {
        display: block;
    }

    .user-dropdown-menu li {
        width: 100%;
    }

    .user-dropdown-menu li a {
        color: #333;
        text-decoration: none;
        display: block;
        padding: 10px 15px;
        font-size: 14px;
        transition: background-color 0.2s ease, color 0.2s ease;
        color: #000 !important;
    }

    .user-dropdown-menu li a:hover {
        background-color: #f5f5f5;
        color: #000;
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
/* Dropdown: bám theo toạ độ của input (viewport) */
#searchDropdown{
  position: fixed;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,.12);
  display: none;
  z-index: 100000;
  max-height: 280px;
  overflow-y: auto;
}

/* Item: ảnh + tên cùng hàng */
#searchDropdown .search-item{
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  text-decoration: none;
  color: #222;
}
#searchDropdown .search-item:hover{ background: #f6f6f6; }

#searchDropdown .search-item img{
  width: 40px; height: 40px; object-fit: cover; border-radius: 6px;
  flex: 0 0 40px;
}

/* TÊN to hơn, đậm hơn, cùng hàng với ảnh */
#searchDropdown .search-item .title{
  font-weight: 700;
  font-size: 16px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

#searchDropdown .search-empty{
  padding: 8px 10px; color: #777; font-size: 14px;
}


</style>

@include('client.layouts.thongbao')
