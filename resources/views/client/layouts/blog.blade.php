<section class="alazea-blog-area section-padding-100-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Section Heading -->
                <div class="section-heading text-center">
                    <h2>TIN TỨC MỚI NHẤT</h2>
                    <p style="font-size: 20px; font-weight: 600;">
                        Các bài viết chia sẻ kiến thức mới nhất từ chúng tôi.
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            @if (isset($latest_blogs) && $latest_blogs->count() > 0)
                @foreach ($latest_blogs as $blog)
                    <!-- Single Blog Post Area -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="single-blog-post mb-100">
                            <div class="post-thumbnail mb-30">
                                <a href="{{ route('client.blogs.show', $blog->slug) }}">
                                    @if ($blog->img)
                                        <img src="{{ asset($blog->img) }}" alt="{{ $blog->title }}" loading="lazy">
                                    @else
                                        <img src="{{ asset('client/img/bg-img/6.jpg') }}" alt="{{ $blog->title }}">
                                    @endif
                                </a>
                            </div>
                            <div class="post-content">
                                <a href="{{ route('client.blogs.show', $blog->slug) }}" class="post-title">
                                    <h5>{{ Str::limit($blog->title, 60) }}</h5>
                                </a>
                                <div class="post-meta">
                                    <a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i>
                                        {{ $blog->created_at->format('d/m/Y') }}</a>
                                    @if ($blog->category)
                                        {{-- Đảm bảo luôn truyền tham số slug khi tạo URL --}}
                                        <a
                                            href="{{ route('client.blogs.category', ['slug' => $blog->category->slug]) }}">
                                            <i class="fa fa-folder" aria-hidden="true"></i> {{ $blog->category->name }}
                                        </a>
                                    @endif
                                </div>
                                <p class="post-excerpt">
                                    {{ Str::limit(strip_tags($blog->content), 100) }}
                                    <a href="{{ route('client.blogs.show', $blog->slug) }}" class="text-success">Đọc
                                        thêm</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center">
                    <p>Chưa có bài viết nào được đăng.</p>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12 text-center mb-100">
                <a href="{{ route('client.blogs.index') }}" class="btn alazea-btn" title="Xem tất cả tin tức">Xem tất
                    cả bài viết</a>

            </div>
        </div>
    </div>
</section>
<!-- ##### Blog Area End ##### -->
