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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
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
                        <div class="widget-desc">
                            <div class="slider-range" data-min="8" data-max="30" data-unit="$"
                                data-value-min="8" data-value-max="30" data-label-result="Price:">
                                <div class="range-price">Price: $8 - $30</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Widget -->
                    <div class="shop-widget catagory mb-50">
                        <h4 class="widget-title">Categories</h4>
                        <div class="widget-desc">
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck1" name="category[]" value="all">
                                <label class="custom-control-label" for="customCheck1">All plants <span class="text-muted">(72)</span></label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck2" name="category[]" value="outdoor">
                                <label class="custom-control-label" for="customCheck2">Outdoor plants <span class="text-muted">(20)</span></label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck3" name="category[]" value="indoor">
                                <label class="custom-control-label" for="customCheck3">Indoor plants <span class="text-muted">(15)</span></label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck4" name="category[]" value="office">
                                <label class="custom-control-label" for="customCheck4">Office Plants <span class="text-muted">(20)</span></label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck5" name="category[]" value="potted">
                                <label class="custom-control-label" for="customCheck5">Potted <span class="text-muted">(15)</span></label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck6" name="category[]" value="others">
                                <label class="custom-control-label" for="customCheck6">Others <span class="text-muted">(2)</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Widget -->
                    <div class="shop-widget sort-by mb-50">
                        <h4 class="widget-title">Sort by</h4>
                        <div class="widget-desc">
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck7" name="sort[]" value="new">
                                <label class="custom-control-label" for="customCheck7">New arrivals</label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck8" name="sort[]" value="a-z">
                                <label class="custom-control-label" for="customCheck8">Alphabetically, A-Z</label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck9" name="sort[]" value="z-a">
                                <label class="custom-control-label" for="customCheck9">Alphabetically, Z-A</label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center mb-2">
                                <input type="checkbox" class="custom-control-input" id="customCheck10" name="sort[]" value="low-high">
                                <label class="custom-control-label" for="customCheck10">Price: low to high</label>
                            </div>
                            <!-- Single Checkbox -->
                            <div class="custom-control custom-checkbox d-flex align-items-center">
                                <input type="checkbox" class="custom-control-input" id="customCheck11" name="sort[]" value="high-low">
                                <label class="custom-control-label" for="customCheck11">Price: high to low</label>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Widget -->
                    <div class="shop-widget best-seller mb-50">
                        <h4 class="widget-title">Best Seller</h4>
                        <div class="widget-desc">
                            <!-- Single Best Seller Products -->
                            <div class="single-best-seller-product d-flex align-items-center">
                                <div class="product-thumbnail">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/4.jpg') }}" alt="Cactus Flower"></a>
                                </div>
                                <div class="product-info">
                                    <a href="{{ route('shop-detail') }}">Cactus Flower</a>
                                    <p>$10.99</p>
                                    <div class="ratings">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Best Seller Products -->
                            <div class="single-best-seller-product d-flex align-items-center">
                                <div class="product-thumbnail">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/5.jpg') }}" alt="Tulip Flower"></a>
                                </div>
                                <div class="product-info">
                                    <a href="{{ route('shop-detail') }}">Tulip Flower</a>
                                    <p>$11.99</p>
                                    <div class="ratings">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Best Seller Products -->
                            <div class="single-best-seller-product d-flex align-items-center">
                                <div class="product-thumbnail">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/34.jpg') }}" alt="Recuerdos Plant"></a>
                                </div>
                                <div class="product-info">
                                    <a href="{{ route('shop-detail') }}">Recuerdos Plant</a>
                                    <p>$9.99</p>
                                    <div class="ratings">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Products Area -->
            <div class="col-12 col-md-8 col-lg-9">
                <div class="shop-products-area">
                    <div class="row">
                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/40.png') }}" alt="Cactus Flower"></a>
                                    <!-- Product Tag -->
                                    <div class="product-tag">
                                        <a href="#">Hot</a>
                                    </div>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/41.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/42.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/43.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/44.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/45.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/46.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/47.png') }}" alt="Cactus Flower"></a>
                                    <!-- Product Tag -->
                                    <div class="product-tag sale-tag">
                                        <a href="#">Sale</a>
                                    </div>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Single Product Area -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-area mb-50">
                                <!-- Product Image -->
                                <div class="product-img">
                                    <a href="{{ route('shop-detail') }}"><img src="{{ asset('assets/img/bg-img/48.png') }}" alt="Cactus Flower"></a>
                                    <div class="product-meta d-flex">
                                        <a href="#" class="wishlist-btn"><i class="icon_heart_alt"></i></a>
                                        <a href="#" class="add-to-cart-btn">Add to cart</a>
                                        <a href="#" class="compare-btn"><i class="arrow_left-right_alt"></i></a>
                                    </div>
                                </div>
                                <!-- Product Info -->
                                <div class="product-info mt-15 text-center">
                                    <a href="{{ route('shop-detail') }}">
                                        <p>Cactus Flower</p>
                                    </a>
                                    <h6>$10.99</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i class="fa fa-angle-right"></i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection