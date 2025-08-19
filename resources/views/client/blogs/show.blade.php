@extends('client.layouts.default')

@section('title', $blog->title)

@section('extra-css')
<style>
    /* Xóa thuộc tính white-space: pre-line để tránh xung đột với HTML */
    .post-text {
        line-height: 1.8;
        font-size: 16px;
    }
    
    /* Định dạng các thẻ HTML phổ biến trong nội dung */
    .post-text p {
        margin-bottom: 1.5rem;
        text-align: justify;
    }
    
    .post-text img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem auto;
        display: block;
    }
    
    .post-text h1, .post-text h2, .post-text h3, .post-text h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
        color: #333;
    }
    
    .post-text ul, .post-text ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .post-text li {
        margin-bottom: 0.5rem;
    }
    
    .post-text blockquote {
        border-left: 4px solid #70c745;
        padding: 1rem;
        background-color: #f9f9f9;
        margin: 1.5rem 0;
        font-style: italic;
    }
    
    /* Định dạng bảng trong nội dung */
    .post-text table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        display: block;
    }
    
    .post-text table td, 
    .post-text table th {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    /* Định dạng code và pre */
    .post-text pre {
        background-color: #f5f5f5;
        padding: 1rem;
        border-radius: 4px;
        overflow-x: auto;
        margin-bottom: 1.5rem;
    }
    
    .post-text code {
        background-color: #f5f5f5;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-family: monospace;
    }
    
    /* Định dạng liên kết */
    .post-text a {
        color: #70c745;
        text-decoration: none;
    }
    
    .post-text a:hover {
        text-decoration: underline;
    }
    
    /* Giữ nguyên các CSS khác */
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
        background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.7) 100%);
        z-index: 1;
    }
    
    .top-breadcrumb-area h2 {
        position: relative;
        z-index: 2;
        font-size: 48px;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        letter-spacing: 2px;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .top-breadcrumb-area .subtitle {
        color: #ffffff;
        font-size: 20px;
        font-weight: 500;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
        padding: 0 15px;
        line-height: 1.5;
    }
    
    .subtitle-wrapper {
        background-color: rgba(0,0,0,0.5);
        display: inline-block;
        padding: 8px 20px;
        border-radius: 30px;
    }
    
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
        border-bottom: 1px solid rgba(0,0,0,0.1);
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
    
    .post-content h4.post-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .post-share-area a {
        display: inline-block;
        width: 34px;
        height: 34px;
        line-height: 34px;
        text-align: center;
        background-color: #f2f4f5;
        color: #70c745;
        border-radius: 50%;
        margin-left: 8px;
    }
    
    .post-share-area a:hover {
        background-color: #70c745;
        color: #ffffff;
    }
    
    /* Thêm định dạng đặc biệt cho đoạn đầu tiên */
    .post-text p:first-of-type {
        font-size: 18px;
        color: #444;
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
            <h2>Chi tiết Blog</h2>
            <div class="subtitle-wrapper">
                <p class="subtitle">{{ Str::limit($blog->title, 70) }}</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.blogs.index') }}">Blog</a></li>
                        @if($blog->category)
                            <li class="breadcrumb-item"><a href="{{ route('client.blogs.category', ['slug' => $blog->category->slug]) }}">{{ $blog->category->name }}</a></li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($blog->title, 30) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- ##### Breadcrumb Area End ##### -->

<!-- ##### Blog Content Area Start ##### -->
<section class="blog-content-area section-padding-0-100">
    <div class="container">
        <div class="row">
            <!-- Blog Posts Area -->
            <div class="col-12 col-md-8">
                <div class="blog-posts-area">

                    <!-- Post Details Area -->
                    <div class="single-post-details-area">
                        <div class="post-content">
                            <h2 class="post-title mb-3">{{ $blog->title }}</h2>
                            <div class="post-meta mb-30">
                                <a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $blog->created_at->format('d M Y') }}</a>
                                @if($blog->category)
                                    <a href="{{ route('client.blogs.category', ['slug' => $blog->category->slug]) }}"><i class="fa fa-folder" aria-hidden="true"></i> {{ $blog->category->name }}</a>
                                @endif
                            </div>
                            
                            @if($blog->img)
                                <div class="post-thumbnail mb-30">
                                    <img src="{{ asset($blog->img) }}" alt="{{ $blog->title }}" class="img-fluid">
                                </div>
                            @endif
                            
                            <div class="post-text">
                                {!! $blog->content !!}
                            </div>
                        </div>
                    </div>

                    <!-- Post Share Area -->
                    <div class="post-share-comment-area d-flex justify-content-between">
                        <!-- Share Area -->
                        <div class="post-share-area d-flex align-items-center">
                            <span>Chia sẻ:</span>
                            <a href="javascript:void(0)" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank')"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href="javascript:void(0)" onclick="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent('{{ $blog->title }}'), '_blank')"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                            <a href="javascript:void(0)" onclick="window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(window.location.href) + '&title=' + encodeURIComponent('{{ $blog->title }}'), '_blank')"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                            <a href="mailto:?subject={{ urlencode($blog->title) }}&body={{ urlencode('Đọc bài viết tại: ' . url()->current()) }}"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blog Sidebar Area -->
            <div class="col-12 col-md-4">
                <div class="post-sidebar-area">

                    <!-- ##### Single Widget Area ##### -->
                    <div class="single-widget-area">
                        <form action="#" method="get" class="search-form">
                            <input type="search" name="search" id="widgetsearch" placeholder="Tìm kiếm...">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>

                    <!-- ##### Single Widget Area ##### -->
                    <div class="single-widget-area">
                        <!-- Title -->
                        <div class="widget-title">
                            <h4>Bài viết gần đây</h4>
                        </div>

                        @if(isset($recent_posts) && $recent_posts->count() > 0)
                            @foreach($recent_posts as $post)
                                <!-- Single Latest Posts -->
                                <div class="single-latest-post d-flex align-items-center">
                                    <div class="post-thumb">
                                        @if($post->img)
                                            <img src="{{ asset($post->img) }}" alt="{{ $post->title }}">
                                        @else
                                            <img src="{{ asset('client/img/bg-img/30.jpg') }}" alt="{{ $post->title }}">
                                        @endif
                                    </div>
                                    <div class="post-content">
                                        <a href="{{ route('client.blogs.show', $post->slug) }}" class="post-title">
                                            <h6>{{ Str::limit($post->title, 45) }}</h6>
                                        </a>
                                        <a href="#" class="post-date">{{ $post->created_at->format('d M Y') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Không có bài viết gần đây.</p>
                        @endif
                    </div>

                    <!-- ##### Single Widget Area ##### -->
                    @if(isset($blog->category))
                    <div class="single-widget-area">
                        <!-- Title -->
                        <div class="widget-title">
                            <h4>Cùng chuyên mục</h4>
                        </div>
                        
                        <?php
                        $related_posts = \App\Models\Blog::where('category_blog_id', $blog->category_blog_id)
                            ->where('id', '!=', $blog->id)
                            ->where('is_active', 1)
                            ->latest()
                            ->take(4)
                            ->get();
                        ?>
                        
                        @if($related_posts->count() > 0)
                            @foreach($related_posts as $post)
                                <!-- Single Latest Posts -->
                                <div class="single-latest-post d-flex align-items-center">
                                    <div class="post-thumb">
                                        @if($post->img)
                                            <img src="{{ asset($post->img) }}" alt="{{ $post->title }}">
                                        @else
                                            <img src="{{ asset('client/img/bg-img/30.jpg') }}" alt="{{ $post->title }}">
                                        @endif
                                    </div>
                                    <div class="post-content">
                                        <a href="{{ route('client.blogs.show', $post->slug) }}" class="post-title">
                                            <h6>{{ Str::limit($post->title, 45) }}</h6>
                                        </a>
                                        <a href="#" class="post-date">{{ $post->created_at->format('d M Y') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Không có bài viết cùng chuyên mục.</p>
                        @endif
                    </div>
                    @endif

                    <!-- ##### Single Widget Area ##### -->
                    <div class="single-widget-area">
                        <!-- Title -->
                        <div class="widget-title">
                            <h4>Tags</h4>
                        </div>
                        <!-- Tags -->
                        <ol class="popular-tags d-flex flex-wrap">
                            @if(isset($blog->category))
                                <li><a href="{{ route('client.blogs.category', ['slug' => $blog->category->slug]) }}">{{ $blog->category->name }}</a></li>
                            @endif
                            <li><a href="{{ route('client.blogs.index') }}">BLOG</a></li>
                            <li><a href="#">TIN TỨC</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ##### Blog Content Area End ##### -->
@endsection