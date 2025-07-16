<section class="hero-area">
    <div class="hero-post-slides owl-carousel">

        @foreach ($banners as $banner)
            @if ($banner->is_active)
                <div class="single-hero-post bg-overlay">
                    <!-- Ảnh nền -->
                    <div class="slide-img bg-img" style="background-image: url('{{ asset($banner->image) }}'); position: relative;">
                        <!-- Logo nhỏ nổi góc trái -->
                        {{-- <img src="{{ asset('http://127.0.0.1:8000/assets/img/core-img/leaf.png') }}" alt="Logo"
                             style="position: absolute; top: 20px; left: 20px; width: 80px; z-index: 10;">
                    </div> --}}

                    <div class="container h-100">
                        <div class="row h-100 align-items-center justify-content-center">
                            <div class="col-12 text-center">
                                <!-- Nội dung banner -->
                                <div class="hero-slides-content">
                                    <h2 class="text-white" style="font-size: 36px; font-weight: bold;">{{ $banner->description }}</h2>
                                    {{-- Có thể thêm dòng phụ ở đây nếu muốn --}}
                                    <div class="welcome-btn-group mt-4">
                                        <a href="{{ $banner->link }}" class="btn alazea-btn active">XEM NGAY</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif
        @endforeach

    </div>
</section>

