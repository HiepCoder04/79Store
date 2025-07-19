@extends('client.layouts.default')

@section('title', 'Giới Thiệu')

@section('content')
    <!-- ##### Breadcrumb Area Start ##### -->
    <div class="breadcrumb-area">
        <!-- Top Breadcrumb Area -->
        <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
            style="background-image: url({{ asset('assets/img/bg-img/24.jpg') }});">
            <h2>Shop</h2>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Shop</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ##### Breadcrumb Area End ##### -->

    <!-- ##### Shop Area Start ##### -->
    <section class="shop-page section-padding-0-100">
        <div class="container">
            <div class="row">
                <!-- Shop Sorting Data -->
                <div class="col-12">
                    <div class="shop-sorting-data d-flex flex-wrap align-items-center justify-content-between">
                        <!-- Shop Page Count -->
                        <div class="shop-page-count">
                            <p>Showing 1–9 of 72 results</p>
                        </div>
                        <!-- Search by Terms -->
                        <div class="search_by_terms">
                            <form action="#" method="get" class="form-inline">
                                <select class="custom-select widget-title" name="sort_by">
                                    <option value="popularity" selected>Short by Popularity</option>
                                    <option value="newest">Short by Newest</option>
                                    <option value="sales">Short by Sales</option>
                                    <option value="ratings">Short by Ratings</option>
                                </select>
                                <select class="custom-select widget-title" name="per_page">
                                    <option value="9" selected>Show: 9</option>
                                    <option value="12">12</option>
                                    <option value="18">18</option>
                                    <option value="24">24</option>
                                </select>
                                <button type="submit" class="btn btn-sm alazea-btn ml-2">Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sidebar Area -->
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="shop-sidebar-area">

                        <!-- Shop Widget -->
                        <div class="shop-widget price mb-50">
                            <h4 class="widget-title">Prices</h4>
                            <!-- Shop Widget -->
<div class="shop-widget sort-by mb-50">
    <h4 class="widget-title">Sắp xếp theo</h4>
    <div class="widget-desc">
        <form method="GET" action="{{ route('shop') }}">
            @foreach (request()->except('sort') as $key => $value)
                @if (is_array($value))
                    @foreach ($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            @php
                $currentSorts = request('sort', []);
            @endphp

            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                <input type="checkbox" class="custom-control-input" id="sort-new" name="sort[]" value="new"
                    {{ in_array('new', $currentSorts) ? 'checked' : '' }}>
                <label class="custom-control-label" for="sort-new">Hàng mới</label>
            </div>

            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                <input type="checkbox" class="custom-control-input" id="sort-az" name="sort[]" value="a-z"
                    {{ in_array('a-z', $currentSorts) ? 'checked' : '' }}>
                <label class="custom-control-label" for="sort-az">Tên: A-Z</label>
            </div>

            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                <input type="checkbox" class="custom-control-input" id="sort-za" name="sort[]" value="z-a"
                    {{ in_array('z-a', $currentSorts) ? 'checked' : '' }}>
                <label class="custom-control-label" for="sort-za">Tên: Z-A</label>
            </div>

            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                <input type="checkbox" class="custom-control-input" id="sort-low" name="sort[]" value="low-high"
                    {{ in_array('low-high', $currentSorts) ? 'checked' : '' }}>
                <label class="custom-control-label" for="sort-low">Giá: thấp đến cao</label>
            </div>

            <div class="custom-control custom-checkbox d-flex align-items-center mb-3">
                <input type="checkbox" class="custom-control-input" id="sort-high" name="sort[]" value="high-low"
                    {{ in_array('high-low', $currentSorts) ? 'checked' : '' }}>
                <label class="custom-control-label" for="sort-high">Giá: cao đến thấp</label>
            </div>

            <button type="submit" class="btn btn-sm alazea-btn mt-2">Áp dụng</button>
        </form>
    </div>
</div>

                        </div>

                        <!-- Shop Widget -->
                        <div class="shop-widget catagory mb-50">
                            <h4 class="widget-title">Danh Mục</h4>
                            <div class="widget-desc">
                                <!-- Single Checkbox -->
                                <form method="GET" action="{{ route('shop') }}">
                                    @foreach ($categories as $category)
                                        <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                            <input type="checkbox" class="custom-control-input"
                                                id="category-{{ $category->id }}" name="category[]"
                                                value="{{ $category->id }}"
                                                {{ in_array($category->id, $selectedCategories ?? []) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="category-{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                    <button type="submit" class="btn btn-sm alazea-btn mt-2">Lọc</button>
                                </form>
                            </div>
                        </div>

                        <!-- Shop Widget -->
                        <div class="shop-widget sort-by mb-50">
                            <h4 class="widget-title">Sort by</h4>
                            <div class="widget-desc">
                                <!-- Single Checkbox -->
                                <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                    <input type="checkbox" class="custom-control-input" id="customCheck7" name="sort[]"
                                        value="new">
                                    <label class="custom-control-label" for="customCheck7">New arrivals</label>
                                </div>
                                <!-- Single Checkbox -->
                                <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                    <input type="checkbox" class="custom-control-input" id="customCheck8" name="sort[]"
                                        value="a-z">
                                    <label class="custom-control-label" for="customCheck8">Alphabetically, A-Z</label>
                                </div>
                                <!-- Single Checkbox -->
                                <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                    <input type="checkbox" class="custom-control-input" id="customCheck9" name="sort[]"
                                        value="z-a">
                                    <label class="custom-control-label" for="customCheck9">Alphabetically, Z-A</label>
                                </div>
                                <!-- Single Checkbox -->
                                <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                    <input type="checkbox" class="custom-control-input" id="customCheck10" name="sort[]"
                                        value="low-high">
                                    <label class="custom-control-label" for="customCheck10">Price: low to high</label>
                                </div>
                                <!-- Single Checkbox -->
                                <div class="custom-control custom-checkbox d-flex align-items-center">
                                    <input type="checkbox" class="custom-control-input" id="customCheck11"
                                        name="sort[]" value="high-low">
                                    <label class="custom-control-label" for="customCheck11">Price: high to low</label>
                                </div>
                            </div>
                        </div>

                        <!-- Shop Widget -->

                    </div>
                </div>

                <!-- All Products Area -->
                
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="shop-products-area">
                   @if (request('keyword'))
    <h4>
        Tìm thấy {{ $products->total() }} sản phẩm cho từ khóa: "{{ request('keyword') }}"
    </h4>
@endif
                        <div class="row">
                        
                            @foreach ($products as $product)
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="single-product-area mb-50">
                                        <!-- Product Image -->
                                        <div class="product-img">
                                            <a href="{{ route('shop-detail', $product->id) }}">
                                                @php
                                    $image = optional($product->galleries->first())->image;
                                    $imagePath = $image
                                        ? asset(ltrim($image, '/'))  // Loại bỏ dấu / đầu nếu có
                                        : asset('assets/img/bg-img/default.jpg');
                                @endphp

                                                <img src="{{ $imagePath }}"                                                    onerror="this.onerror=null;this.src='{{ asset('assets/img/default.jpg') }}';"
                                                    alt="{{ $product->name }}" width="100%">

                                            </a>

                                            <div class="product-tag">
                                                <a href="#">Hot</a>
                                            </div>

                                            <div class="product-meta d-flex">
                                                <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>

                                                <form action="{{ route('cart.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <button type="submit" class="add-to-cart-btn">Add to cart</button>
                                                </form>

                                                <a href="#" class="compare-btn"><i
                                                        class="arrow_left-right_alt"></i></a>
                                            </div>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="product-info mt-15 text-center">
                                            <a href="{{ route('shop-detail', $product->id) }}">
                                                <p>{{ $product->name }}</p>
                                            </a>

                                            @php
                                                $minPrice = $product->variants->min('price');
                                                $maxPrice = $product->variants->max('price');
                                            @endphp

                                            <h6 class="text-success fw-bold">
                                                {{ number_format($minPrice, 0, ',', '.') }}đ
                                                @if ($minPrice != $maxPrice)
                                                    – {{ number_format($maxPrice, 0, ',', '.') }}đ
                                                @endif
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="pagination"></ul>
                        </nav>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const totalPages = {{ $products->lastPage() }};
                                let currentPage = {{ $products->currentPage() }};
                                const baseUrl = "{{ url()->current() }}";

                                const pagination = document.getElementById("pagination");

                                function renderPagination() {
                                    pagination.innerHTML = "";

                                    // Prev
                                    const prev = document.createElement("li");
                                    prev.className = "page-item" + (currentPage === 1 ? " disabled" : "");
                                    prev.innerHTML = <a class="page-link" href="#"><span>&laquo;</span></a>;
                                    prev.addEventListener("click", () => {
                                        if (currentPage > 1) {
                                            window.location.href = baseUrl + '?page=' + (currentPage - 1);
                                        }
                                    });
                                    pagination.appendChild(prev);

                                    // Pages
                                    for (let i = 1; i <= totalPages; i++) {
                                        const li = document.createElement("li");
                                        li.className = "page-item" + (i === currentPage ? " active" : "");
                                        li.innerHTML = <a class="page-link" href="#">${i}</a>;
                                        li.addEventListener("click", () => {
                                            window.location.href = baseUrl + '?page=' + i;
                                        });
                                        pagination.appendChild(li);
                                    }

                                    // Next
                                    const next = document.createElement("li");
                                    next.className = "page-item" + (currentPage === totalPages ? " disabled" : "");
                                    next.innerHTML = <a class="page-link" href="#"><span>&raquo;</span></a>;
                                    next.addEventListener("click", () => {
                                        if (currentPage < totalPages) {
                                            window.location.href = baseUrl + '?page=' + (currentPage + 1);
                                        }
                                    });
                                    pagination.appendChild(next);
                                }

                                renderPagination();
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 
sửa cho tôi "@php
    $minPrice = $product->variants->min('price');
    $maxPrice = $product->variants->max('price');
@endphp