<section class="hero-area">
    <div class="hero-post-slides owl-carousel">

        @foreach ($banners as $banner)
            @if ($banner->is_active)
                <!-- Single Hero Post -->
                <div class="single-hero-post bg-overlay">
                    <!-- Post Image -->
                    <div class="slide-img bg-img" style="background-image: url('{{ asset($banner->image) }}');"></div>
                    <div class="container h-100">
                        <div class="row h-100 align-items-center">
                            <div class="col-12">
                                <!-- Post Content -->
                                <div class="hero-slides-content text-center">
                                    <h2>{{ $banner->description }}</h2>
                                    <p></p> {{-- Nếu có nội dung thêm thì bổ sung tại đây --}}
                                    <div class="welcome-btn-group">
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
