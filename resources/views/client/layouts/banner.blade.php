<section class="hero-area">
    <div class="hero-post-slides owl-carousel">

        @forelse ($banners as $banner)
            @if ($banner->is_active)
                <div class="single-hero-post bg-overlay">
                    <!-- Ảnh nền -->
                    <div class="slide-img bg-img" style="background-image: url('{{ asset($banner->image) }}'); position: relative;">

                        <!-- Logo nhỏ góc trái -->
                        <img src="{{ asset('assets/img/core-img/leaf.png') }}" alt="Logo"
                             style="position: absolute; top: 20px; left: 20px; width: 80px; z-index: 10;">

                        <!-- Nội dung banner -->
                        <div class="container h-100">
                            <div class="row h-100 align-items-center justify-content-center">
                                <div class="col-12 text-center">
                                    <div class="hero-slides-content">
                                        <h2 class="text-white fw-bold" style="font-size: 36px;">{{ $banner->description }}</h2>
                                        <div class="welcome-btn-group mt-4">
                                            <a href="{{ $banner->link }}" class="btn alazea-btn active">XEM NGAY</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- Kết thúc slide-img -->
                </div> <!-- Kết thúc single-hero-post -->
            @endif
        @empty
            <div class="text-center py-5">
                <p class="text-muted">Hiện chưa có banner nào được hiển thị.</p>
            </div>
        @endforelse

    </div>
</section>
