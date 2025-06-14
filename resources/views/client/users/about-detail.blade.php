@extends('client.layouts.default')

@section('title', 'Giới Thiệu')

@section('content')
    <section class="about-us-area section-padding-100-0">
        <div class="container">
            <div class="row align-items-center">

                <!-- Hình ảnh -->
                <div class="col-12 col-md-6">
                    <div class="about-us-thumbnail mb-100">
                        <img src="{{ asset('assets/img/bg-img/6.jpg') }}" alt="">
                    </div>
                </div>

                <!-- Nội dung giới thiệu -->
                <div class="col-12 col-md-6">
                    <div class="section-heading mb-5">
                        <h2>Về chúng tôi</h2>
                        <p>Chúng tôi là cửa hàng cây cảnh uy tín, chuyên cung cấp các loại cây trồng trong nhà, cây văn phòng và cây phong thủy.</p>
                    </div>
                    <p>Với nhiều năm kinh nghiệm trong lĩnh vực chăm sóc và cung cấp cây xanh, chúng tôi cam kết mang đến cho khách hàng những sản phẩm chất lượng nhất cùng dịch vụ tận tâm. Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn trong việc chọn lựa cây phù hợp với không gian và phong cách sống của bạn.</p>
                    <a href="{{ route('home') }}" class="btn alazea-btn mt-30">Quay lại trang chủ</a>
                </div>

            </div>
        </div>
    </section>
@endsection