<section class="new-arrivals-products-area bg-gray section-padding-100">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>SẢN PHẨM MỚI</h2>
                    <p style="font-size: 20px; font-weight: 600;">
                        Chúng tôi liên tục cập nhật những sản phẩm mới nhất cho bạn.</p>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($products as $index => $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-product-area mb-50 wow fadeInUp" data-wow-delay="{{ ($index + 1) * 100 }}ms">
                        <!-- Product Image -->
                        <div class="product-img ">
                            <a href="{{ route('shop-detail', $product->id) }}">
                                @php
                                    $image = optional($product->galleries->first())->image;
                                    $imagePath = $image
                                        ? asset(ltrim($image, '/'))
                                        : asset('assets/img/bg-img/default.jpg');
                                @endphp
                                <img src="{{ $imagePath }}" alt="{{ $product->name }}" class="img-fluid fixed-img">
                            </a>

                            <!-- Optional Tag -->
                            <div class="product-tag">
                                <a href="#">Mới</a>
                            </div>

                            {{-- <div class="product-meta d-flex">
                                <a href="#" class="wishlist-btn" title="Thêm vào yêu thích"><i
                                        class="icon_heart_alt"></i></a>

                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-to-cart-btn"
                                        title="Thêm sản phẩm vào giỏ hàng">Thêm vào giỏ</button>
                                </form>

                                <a href="#" class="compare-btn" title="So sánh sản phẩm"><i
                                        class="arrow_left-right_alt"></i></a>
                            </div> --}}

                        </div>

                        <!-- Product Info -->
                        <div class="product-info mt-15 text-center">
                            <a href="{{ route('shop-detail', $product->id) }}">
                                <p>{{ $product->name }}</p>
                            </a>

                            @php
                                $min = $product->variants->min('price');
                                $max = $product->variants->max('price');
                            @endphp
                            <h6 class="text-success fw-bold">
                                {{ number_format($min, 0, ',', '.') }}đ
                                @if ($min != $max)
                                    – {{ number_format($max, 0, ',', '.') }}đ
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-12 text-center">
                <a href="{{ route('shop') }}" class="btn alazea-btn">Xem tất cả</a>
            </div>
        </div>
    </div>
</section>
{{-- sp ban chay --}}
<section class="best-sellers-area section-padding-80-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>Sản phẩm bán chạy</h2>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach($bestSellers as $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-product-area mb-50">
                        <div class="product-img">
                            @php
                                $firstGallery = $product->galleries->first();
                                $imagePath = $firstGallery && $firstGallery->image
                                    ? asset(ltrim($firstGallery->image, '/'))
                                    : asset('assets/img/bg-img/default.jpg');
                            @endphp
                            <img src="{{ $imagePath }}" alt="{{ $product->name }}">
                        </div>
                        <div class="product-info mt-15 text-center">
                            <a href="{{ route('shop-detail', $product->id) }}">
                                <p>{{ $product->name }}</p>
                            </a>
                            @php
                                $min = $product->variants->min('price');
                                $max = $product->variants->max('price');
                            @endphp
                            <h6 class="text-success fw-bold">
                                {{ number_format($min, 0, ',', '.') }}đ
                                @if ($min != $max)
                                    – {{ number_format($max, 0, ',', '.') }}đ
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<style>
.best-sellers-area {
    padding-top: 20px; /* khoảng cách trên */
    padding-bottom: 20px; /* khoảng cách dưới */
    margin-top: 10px; /* cách phần trên ít nhất 10px */
    margin-bottom: 10px; /* cách phần dưới ít nhất 10px */
    background-color: #f9f9f9; /* nền nhẹ để tách biệt */
    border-radius: 8px;
}

.best-sellers-area .section-heading h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 25px;
    color: #333;
}

.best-sellers-area .single-product-area {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.best-sellers-area .single-product-area:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.best-sellers-area .product-img img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.best-sellers-area .product-info p {
    margin: 10px 0 5px;
    font-weight: 500;
    font-size: 16px;
    color: #555;
}

.best-sellers-area .product-info h6 {
    margin-bottom: 10px;
}

</style>