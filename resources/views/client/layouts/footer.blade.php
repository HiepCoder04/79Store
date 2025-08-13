<footer class="footer-area bg-img" style="background-image: url('{{ asset('img/bg-img/3.jpg') }}');">
    <!-- Khu vực footer chính -->
    <div class="main-footer-area">
        <div class="container">
            <div class="row">

                <!-- Cột: Logo và mô tả -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget">
                        <div class="footer-logo mb-30">
                            <a href="#"><img src="{{ asset('assets/img/core-img/leaf.png') }}" alt="Logo cửa hàng"></a>
                        </div>
                        <p>Website chuyên cung cấp các loại cây cảnh đẹp, giá tốt và giao hàng tận nơi trên toàn quốc.</p>
                        <div class="social-info">
                            <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                            <a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                            <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Cột: Liên kết nhanh -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget">
                        <div class="widget-title">
                            <h5>LIÊN KẾT NHANH</h5>
                        </div>
                        <nav class="widget-nav">
                            <ul>
                                <li><a href="#">Mua hàng</a></li>
                                <li><a href="#">Câu hỏi thường gặp</a></li>
                                <li><a href="#">Thanh toán</a></li>
                                <li><a href="#">Tin tức</a></li>
                                <li><a href="#">Đổi trả</a></li>
                                <li><a href="#">Hợp tác quảng cáo</a></li>
                                <li><a href="#">Vận chuyển</a></li>
                                <li><a href="#">Tuyển dụng</a></li>
                                <li><a href="#">Đơn hàng</a></li>
                                <li><a href="#">Chính sách</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

<!-- Cột: Sản phẩm bán chạy -->
<div class="col-12 col-sm-6 col-lg-3">
    <div class="single-footer-widget">
        <div class="widget-title">
            <h5>BÁN CHẠY</h5>
        </div>

        @if(isset($bestSellers) && $bestSellers->count())
           @foreach ($bestSellers->take(2) as $product) {{-- Lấy tối đa 2 sản phẩm --}}
                @php
                    $firstGallery = $product->galleries->first();
                    $imagePath = $firstGallery && $firstGallery->image
                        ? asset(ltrim($firstGallery->image, '/'))
                        : asset('assets/img/bg-img/default.jpg');

                    $min = $product->variants->min('price');
                    $max = $product->variants->max('price');
                @endphp

                <div class="single-best-seller-product d-flex align-items-center">
                    <div class="product-thumbnail">
                        <a href="{{ route('shop-detail', $product->id) }}">
                            <img src="{{ $imagePath }}" alt="{{ $product->name }}" class="footer-best-thumb">
                        </a>
                    </div>
                    <div class="product-info">
                        <a href="{{ route('shop-detail', $product->id) }}">{{ $product->name }}</a>
                        <p class="mb-0 text-success fw-bold">
                            {{ number_format($min, 0, ',', '.') }}đ
                            @if ($min != $max)
                                – {{ number_format($max, 0, ',', '.') }}đ
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        @else
            <p>Chưa có dữ liệu.</p>
        @endif
    </div>
</div>

                <!-- Cột: Thông tin liên hệ -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget">
                        <div class="widget-title">
                            <h5>LIÊN HỆ</h5>
                        </div>
                        <div class="contact-information">
                            <p><span>Địa chỉ:</span> Số 1 Trịnh Văn Bô</p>
                            <p><span>Điện thoại:</span> 0336994436</p>
                            <p><span>Email:</span> 79store@gmail.com</p>
                            <p><span>Giờ mở cửa:</span> Thứ 2 - CN: 8h - 21h</p>
                            <p><span>Giờ ưu đãi:</span> Thứ 7: 14h - 16h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khu vực bản quyền -->
    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="border-line"></div>
                </div>

                <!-- Bản quyền -->
                <div class="col-12 col-md-6">
                    <div class="copywrite-text">
                        <p>
                            &copy;
                            <script>
                                document.write(new Date().getFullYear());
                            </script> Bản quyền thuộc về cửa hàng 79Store |
                            Thiết kế bởi <a href="https://colorlib.com" target="_blank">Nhóm 79</a>
                        </p>
                    </div>
                </div>

                <!-- Điều hướng footer -->
                <div class="col-12 col-md-6">
                    <div class="footer-nav">
                        <nav>
                            <ul>
                                <li><a href="#">Trang chủ</a></li>
                                <li><a href="#">Giới thiệu</a></li>
                                <li><a href="#">Dịch vụ</a></li>
                                <li><a href="#">Bộ sưu tập</a></li>
                                <li><a href="#">Bài viết</a></li>
                                <li><a href="#">Liên hệ</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
