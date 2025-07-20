@extends('client.layouts.default')

@section('title', 'Blog')

@section('extra-css')
    <style>
        .top-breadcrumb-area {
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center center;
            height: 350px !important;
            transition: all 0.3s ease;
        }

        .top-breadcrumb-area:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* Tăng độ đậm của lớp overlay để text hiển thị rõ hơn */
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.7) 100%);
            z-index: 1;
        }

        .top-breadcrumb-area h2 {
            position: relative;
            z-index: 2;
            font-size: 48px;
            color: #ffffff;
            /* Đảm bảo màu trắng cho tiêu đề */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            /* Tăng đậm bóng đổ */
            letter-spacing: 2px;
            margin-bottom: 15px;
            /* Tăng khoảng cách với subtitle */
            font-weight: 700;
            /* Làm đậm chữ hơn */
        }

        .top-breadcrumb-area .subtitle {
            color: #ffffff;
            /* Đảm bảo màu trắng cho subtitle */
            font-size: 20px;
            /* Tăng kích thước chữ */
            font-weight: 500;
            /* Làm đậm chữ hơn */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            /* Tăng đậm bóng đổ */
            position: relative;
            z-index: 2;
            max-width: 800px;
            /* Giới hạn chiều rộng để dễ đọc */
            margin: 0 auto;
            /* Căn giữa */
            padding: 0 15px;
            /* Thêm đệm hai bên */
            line-height: 1.5;
            /* Tăng khoảng cách dòng */
        }

        /* Thêm một lớp nền bán trong suốt cho subtitle */
        .subtitle-wrapper {
            background-color: rgba(0, 0, 0, 0.5);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
        }

        /* CSS thích ứng với màn hình nhỏ */
        @media (max-width: 768px) {
            .top-breadcrumb-area h2 {
                font-size: 36px;
            }

            .top-breadcrumb-area .subtitle {
                font-size: 16px;
            }
        }

        .breadcrumb {
            background-color: transparent;
            padding: 1rem 0;
            margin-bottom: 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .breadcrumb-item a {
            color: #70c745;
        }

        .breadcrumb-item a:hover {
            color: #5a9d37;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #333;
        }
    </style>
@endsection

@section('content')
    <!-- ##### Breadcrumb Area Start ##### -->
    <div class="breadcrumb-area">
        <!-- Top Breadcrumb Area -->
        <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
            style="background-image: url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?q=80&w=2062&auto=format&fit=crop');">
            <div class="container text-center">
                <h2>Blog</h2>
                <div class="subtitle-wrapper">
                    <p class="subtitle" style="color: white;">
                        @if (isset($category))
                            {{ $category->name }}
                        @else
                            <strong>Khám phá kiến thức & cập nhật tin tức mới nhất</strong>
                        @endif
                    </p>
                </div>

            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" style="font-size: 20px;">
                                <a href="{{ route('home') }}"><i class="fa fa-home"></i> Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page" style="font-size: 20px;">Blog</li>
                            @if (isset($category))
                                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ##### Breadcrumb Area End ##### -->

    <!-- ##### Blog Area Start ##### -->
    <!-- ##### Blog Area Start ##### -->
    <section class="alazea-blog-area mb-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="row">
                        @if ($blogs->count() > 0)
                            @foreach ($blogs as $blog)
                                <!-- Single Blog Post Area -->
                                <div class="col-12 col-lg-6">
                                    <div class="single-blog-post mb-50">
                                        <div class="post-thumbnail mb-30">
                                            <a href="{{ route('client.blogs.show', $blog->slug) }}">
                                                @if ($blog->img)
                                                    <img src="{{ asset($blog->img) }}" alt="{{ $blog->title }}">
                                                @else
                                                    <img src="{{ asset('client/img/bg-img/6.jpg') }}"
                                                        alt="{{ $blog->title }}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="post-content">
                                            <a href="{{ route('client.blogs.show', $blog->slug) }}" class="post-title">
                                                <h5>{{ $blog->title }}</h5>
                                            </a>
                                            <div class="post-meta">
                                                <a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    {{ $blog->created_at->format('d M Y') }}</a>
                                                @if ($blog->category)
                                                    <a
                                                        href="{{ route('client.blogs.category', ['slug' => $blog->category->slug]) }}"><i
                                                            class="fa fa-folder" aria-hidden="true"></i>
                                                        {{ $blog->category->name }}</a>
                                                @endif
                                            </div>
                                            <p class="post-excerpt">{{ Str::limit(strip_tags($blog->content), 120) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Không có bài viết nào.
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <nav aria-label="Page navigation">
                                {{ $blogs->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="post-sidebar-area">

                        <!-- ##### Single Widget Area ##### -->
                        <div class="single-widget-area">
                            <form action="#" method="get" class="search-form">
                                <input type="search" name="search" id="widgetsearch" placeholder="Tìm kiếm..."
                                    style="color: black; font-size: 18px; padding: 10px 15px; border-radius: 6px; border: 1px solid #ccc;">

                                <button type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>

                        <!-- ##### Single Widget Area ##### -->
                        <div class="single-widget-area">
                            <!-- Title -->
                            <div class="widget-title">
                                <h4>Bài viết gần đây</h4>
                            </div>

                            @if (isset($recent_posts) && $recent_posts->count() > 0)
                                @foreach ($recent_posts as $post)
                                    <!-- Single Latest Posts -->
                                    <div class="single-latest-post d-flex align-items-center">
                                        <div class="post-thumb">
                                            @if ($post->img)
                                                <img src="{{ asset($post->img) }}" alt="{{ $post->title }}">
                                            @else
                                                <img src="{{ asset('client/img/bg-img/30.jpg') }}"
                                                    alt="{{ $post->title }}">
                                            @endif
                                        </div>
                                        <div class="post-content">
                                            <a href="{{ route('client.blogs.show', $post->slug) }}" class="post-title">
                                                <h6>{{ Str::limit($post->title, 45) }}</h6>
                                            </a>
                                            <a href="#"
                                                class="post-date">{{ $post->created_at->format('d M Y') }}</a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p style="color: #333; font-size: 20px; font-weight: normal; padding: 12px 0;">
                                    Không có bài viết gần đây.
                                </p>
                            @endif
                        </div>

                        <!-- ##### Single Widget Area ##### -->
                        <div class="single-widget-area">
                            <!-- Title -->
                            <div class="widget-title">
                                <h4>Danh mục</h4>
                            </div>
                            <!-- Tags -->
                            <ol class="popular-tags d-flex flex-wrap">
                                @if (isset($categories) && $categories->count() > 0)
                                    @foreach ($categories as $category)
                                        <li><a href="{{ route('client.blogs.category', ['slug' => $category->slug]) }}">{{ $category->name }}
                                                ({{ $category->blogs_count }})
                                            </a></li>
                                    @endforeach
                                @else
                                    <li style="font-size: 20px; font-weight: normal; color: #333;">
                                        Chưa có danh mục
                                    </li>
                                @endif
                            </ol>
                        </div>

                        <!-- ##### Single Widget Area ##### -->
                        <div class="single-widget-area">
                            <!-- Title -->
                            <div class="widget-title">
                                <h4>SẢN PHẨM NỔI BẬT</h4>
                            </div>

                            @if (isset($featured_products) && $featured_products->count() > 0)
                                @foreach ($featured_products as $product)
                                    <!-- Single Best Seller Products -->
                                    <div class="single-best-seller-product d-flex align-items-center">
                                        <div class="product-thumbnail">
                                            @if ($product->image)
                                                <a href="{{ route('client.products.show', $product->slug) }}"><img
                                                        src="{{ asset($product->image) }}" alt="{{ $product->name }}"></a>
                                            @else
                                                <a href="{{ route('client.products.show', $product->slug) }}"><img
                                                        src="{{ asset('client/img/bg-img/4.jpg') }}"
                                                        alt="{{ $product->name }}"></a>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <a
                                                href="{{ route('client.products.show', $product->slug) }}">{{ $product->name }}</a>
                                            <p>{{ number_format($product->price) }} VNĐ</p>
                                            @if (isset($product->rating))
                                                <div class="ratings">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $product->rating)
                                                            <i class="fa fa-star"></i>
                                                        @else
                                                            <i class="fa fa-star-o"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p style="font-size: 20px; font-weight: normal; color: #333;">
                                    Chưa có sản phẩm nổi bật.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ##### Blog Area End ##### -->
    <!-- ##### Blog Area End ##### -->
@endsection
<style>
    .search-widget input[type="search"] {
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        width: 100%;
        color: black !important;
        transition: all 0.3s ease-in-out;
    }

    .search-widget input[type="search"]:focus {
        border-color: #28a745;
        outline: none;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
    }
</style>
