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
