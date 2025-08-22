@extends('client.layouts.default')

@section('title', $product->name)

@section('content')

<div class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url({{ asset('assets/img/bg-img/24.jpg') }});">
        <h2>{{ $product->name }}</h2>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="single_product_details_area mb-50">
    <div class="container">
        <div class="row align-items-center">
            <!-- Ảnh sản phẩm -->
            <div class="col-12 col-md-6">
                @php
                    $image = optional($product->galleries->first())->image;
                    $imagePath = $image ? asset(ltrim($image, '/')) : asset('assets/img/bg-img/default.jpg');
                @endphp
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                    <div class="carousel-inner">
                        @foreach ($product->galleries as $index => $gallery)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset(ltrim($gallery->image, '/')) }}" class="d-block w-100" alt="Ảnh {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="col-12 col-md-6">
                <h4 class="product-title mb-2 text-uppercase">{{ $product->name }}</h4>
                <h4 id="price-display" class="text-success mb-3">{{ number_format($product->variants->first()->price ?? 0, 0, ',', '.') }}đ</h4>
                <p class="mb-4">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="pot" id="selected-pot">
                    <input type="hidden" name="height" id="selected-height">

                    <div class="form-group" id="height-group">
                        <label>Chọn chiều cao:</label><br>
                        <div id="height-buttons"></div>
                        <p id="stock-display" class="text-muted mt-2"></p>
                    </div>

                    @if($potsToShow->isNotEmpty())
                        <div class="form-group">
                            <label>Chọn chậu:
                                <span style="font-size: 0.9em; color: #888;">(Không bắt buộc)</span>
                            </label><br>
                            <div id="pot-buttons"></div>
                        </div>
                    @endif

                    <div id="add-to-cart-form" data-url="{{ route('cart.add.ajax') }}">
                        <input type="hidden" id="product-id" value="{{ $product->id }}">
                        <div class="d-flex align-items-center mt-3 mb-3">
                            <div class="quantity">
                                <span class="qty-minus" onclick="document.getElementById('quantity').stepDown();"><i class="fa fa-minus"></i></span>
                                <input type="number" id="quantity" value="1" min="1" class="qty-text mx-2" style="width: 60px;">
                                <span class="qty-plus" onclick="document.getElementById('quantity').stepUp();"><i class="fa fa-plus"></i></span>
                            </div>
                            <button type="button" id="add-to-cart-btn" class="btn alazea-btn ml-3">THÊM VÀO GIỎ</button>
                        </div>
                    </div>
                </form>

                <ul class="list-unstyled">
                    <li><strong>Danh Mục:</strong> {{ $product->category->name }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="product_details_tab clearfix">
                    <ul class="nav nav-tabs" role="tablist" id="product-details-tab">
                        <li class="nav-item">
                            <a href="#reviews" class="nav-link active" data-toggle="tab" role="tab">Đánh giá</a>
                        </li>
                        <li class="nav-item">
                            <a href="#comment" class="nav-link" data-toggle="tab" role="tab">Bình luận</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Đánh giá -->
                        <div role="tabpanel" class="tab-pane fade show active" id="reviews">
                            <div class="container d-flex justify-content-center">
                                <div class="col-md-8 col-lg-6 mt-4">
                                    <h5 class="mb-3 fw-semibold">Đánh giá của khách hàng</h5>

                                    <div class="mb-3">
                                        <span class="h4 text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star{{ $i <= round($averageRating) ? '' : '-o' }}"></i>
                                            @endfor
                                        </span>
                                        <span class="ms-2">{{ number_format($averageRating,1) }}/5</span>
                                        <small class="text-muted">({{ $reviewCount }} đánh giá)</small>
                                    </div>

                                    @auth
                                        @php
                                            $orderDetailId = \App\Models\OrderDetail::where('product_id', $product->id)
                                                ->whereHas('order', fn($q) => 
                                                    $q->where('user_id', auth()->id())
                                                      ->where('status', 'delivered')
                                                )
                                                ->whereDoesntHave('review')
                                                ->value('id');
                                        @endphp

                                        @if($orderDetailId)
                                            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="order_detail_id" value="{{ $orderDetailId }}">
                                                <input type="hidden" name="rating" id="rating" required>

                                                <div class="mb-2">
                                                    <div id="star-rating" style="cursor: pointer; font-size: 20px; color: #ccc;">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="fa fa-star" data-value="{{ $i }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center border rounded p-2">
                                                    <textarea name="comment" rows="1" class="form-control border-0 flex-grow-1"
                                                              placeholder="Viết nhận xét..." minlength="5" required></textarea>
                                                    <button type="submit" class="btn btn-dark ms-2">Gửi đánh giá</button>
                                                </div>

                                                <div class="mt-2">
                                                    <input type="file" name="image_path" id="review-image" accept="image/*" 
                                                           style="display:none;" onchange="previewReviewImage(event)">
                                                    <label for="review-image" class="btn btn-outline-secondary btn-sm mt-2">
                                                        <i class="fa fa-camera"></i> Thêm ảnh
                                                    </label>
                                                    <div id="image-preview" class="mt-2"></div>
                                                </div>
                                            </form>
                                        @endif
                                    @endauth

                                    <h6 class="mt-4">{{ $reviewCount }} Đánh giá</h6>
                                    @forelse($product->reviews as $review)
                                        <div class="border rounded p-3 mb-3 bg-white shadow-sm">
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&size=36"
                                                    class="rounded-circle me-2" width="36" height="36" alt="avatar">
                                                <div>
                                                    <strong>{{ $review->user->name }}</strong>
                                                    <div class="text-warning small">
                                                        @for($i=1;$i<=5;$i++)
                                                            <i class="fa fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="mb-2">{{ $review->comment }}</p>
                                            @if ($review->image_path)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $review->image_path) }}" alt="Ảnh đánh giá" class="review-img">
                                                </div>
                                            @endif
                                            <small class="text-muted d-block mb-2">{{ $review->created_at->diffForHumans() }}</small>

                                            @if($review->admin_reply)
                                                <div class="mt-3 p-3 border rounded bg-light position-relative">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-primary me-2">
                                                            <i class="fa fa-shield"></i> 79Store
                                                        </span>
                                                        <small class="text-muted">đã phản hồi</small>
                                                    </div>
                                                    <p class="mb-1">{{ $review->admin_reply }}</p>
                                                    <small class="text-muted">Cảm ơn bạn đã tin tưởng và ủng hộ sản phẩm của chúng tôi</small>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Bình luận -->
                        <div role="tabpanel" class="tab-pane fade" id="comment">
                            <div class="container d-flex justify-content-center">
                                <div class="col-md-8 col-lg-6 mt-4">
                                    <h5 class="mb-3 fw-semibold text-center">Bình luận sản phẩm</h5>

                                    @auth
                                        <form action="{{ route('comments.store') }}" method="POST" class="mb-3">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <textarea name="content" rows="2" class="form-control form-control-sm mb-2" placeholder="Nhập bình luận..."></textarea>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-primary">Gửi</button>
                                            </div>
                                        </form>
                                    @else
                                        <p class="text-muted text-center">Vui lòng <a href="{{ route('auth.login') }}">đăng nhập</a> để bình luận.</p>
                                    @endauth

                                    @foreach($product->comments()->whereNull('parent_id')->latest()->get() as $comment)
                                        <div class="border rounded p-2 mb-2 bg-light-subtle small">
                                            <div class="d-flex justify-content-between mb-1">
                                                <strong>{{ $comment->user->name }}</strong>
                                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="text-muted">{{ $comment->content }}</div>

                                            @foreach($comment->replies as $reply)
                                                <div class="ms-3 mt-2 p-2 bg-white border rounded small">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small class="fw-semibold">{{ $reply->user->name }}</small>
                                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <div class="text-muted">{{ $reply->content }}</div>
                                                </div>
                                            @endforeach

                                            @auth
                                                <form action="{{ route('comments.store') }}" method="POST" class="mt-2 ms-3">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                    <textarea name="content" rows="1" class="form-control form-control-sm mb-1" placeholder="Trả lời..."></textarea>
                                                    <button class="btn btn-sm btn-outline-secondary">Trả lời</button>
                                                </form>
                                            @endauth
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div><!-- end tab-content -->
                </div>
            </div>
        </div>
    </div>
</section>

@if(isset($recommended) && $recommended->isNotEmpty())
    <!-- Gợi ý sản phẩm -->
    <section class="recommended-products-area section-padding-80">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-50">
                    <h3 class="section-title wow fadeInUp" data-wow-delay="100ms">Có thể bạn cũng thích</h3>
                    <p class="text-muted">Những sản phẩm thường được mua cùng sản phẩm này</p>
                </div>
            </div>

            <div class="row">
                @foreach ($recommended as $index => $product)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="single-product-area mb-50 wow fadeInUp" data-wow-delay="{{ ($index + 1) * 100 }}ms">
                            <div class="product-img">
                                <a href="{{ route('shop-detail', $product->id) }}">
                                    @php
                                        $image = optional($product->galleries->first())->image;
                                        $imagePath = $image ? asset(ltrim($image, '/')) : asset('assets/img/bg-img/default.jpg');
                                    @endphp
                                    <img src="{{ $imagePath }}" alt="{{ $product->name }}" class="img-fluid fixed-img">
                                </a>
                                <div class="product-tag"><a href="#">Hot</a></div>
                            </div>
                            <div class="product-info mt-15 text-center">
                                <a href="{{ route('shop-detail', $product->id) }}"><p>{{ $product->name }}</p></a>
                                @php
                                    $min = $product->variants->min('price');
                                    $max = $product->variants->max('price');
                                @endphp
                                <h6 class="text-success fw-bold">
                                    {{ number_format($min, 0, ',', '.') }}đ
                                    @if ($min != $max) – {{ number_format($max, 0, ',', '.') }}đ @endif
                                </h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-12 text-center">
                    <a href="{{ route('shop') }}" class="btn alazea-btn">Xem tất cả</a>
                </div>
            </div>
        </div>
    </section>
@endif

<style>
    .review-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
        margin-top: 5px;
    }
</style>
@endsection


@section('page_scripts')
<script>
    const allVariants = @json($variants) || [];
    const allPots = @json($allPots) || [];

    document.addEventListener('DOMContentLoaded', function () {
        const potContainer = document.getElementById('pot-buttons');
        const heightContainer = document.getElementById('height-buttons');
        const priceDisplay = document.getElementById('price-display');
        const potInput = document.getElementById('selected-pot');
        const heightInput = document.getElementById('selected-height');
        const addBtn = document.getElementById('add-to-cart-btn');
        const form = document.getElementById('add-to-cart-form');

        function renderPotButtons(height) {
            const variant = allVariants.find(v => v.height === height);
            const pots = variant?.pots || [];

            if (!potContainer) return;
            potContainer.innerHTML = '';

            pots.forEach(potId => {
                const pot = allPots[potId];
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-outline-dark m-1 pot-option';
                
                // Lúc render ban đầu chỉ hiện tên chậu
                button.textContent = pot?.name || 'Chậu';
                button.dataset.potId = potId;

                button.onclick = function () {
                    const isActive = button.classList.contains('active');

                    // Bỏ active tất cả nút khác
                    document.querySelectorAll('.pot-option').forEach(b => b.classList.remove('active'));

                    if (isActive) {
                        // Bỏ chọn → ẩn giá
                        potInput.value = '';
                        button.textContent = pot?.name || 'Chậu';
                        updatePrice(null, heightInput.value);
                    } else {
                        // Chọn chậu → hiện giá
                        button.classList.add('active');
                        potInput.value = potId;

                        // Hiện tên + giá khi click
                        const potPrice = pot?.price ? ` (+${Number(pot.price).toLocaleString('vi-VN')}đ)` : '';
                        button.textContent = (pot?.name || 'Chậu') + potPrice;

                        updatePrice(potId, heightInput.value);
                    }
                };

                potContainer.appendChild(button);
            });
        }

        function updatePrice(potId, height) {
            const variant = allVariants.find(v => v.height === height);
            const pot = potId ? allPots[potId] : null;
            const potPrice = pot ? Number(pot.price) : 0;
            const variantPrice = variant ? Number(variant.price) : 0;
            const total = potPrice + variantPrice;

            const formatted = total.toLocaleString('vi-VN') + 'đ';
            const stock = variant ? variant.stock_quantity : 0;

            priceDisplay.style.opacity = 0;
            setTimeout(() => {
                priceDisplay.textContent = formatted;
                priceDisplay.style.opacity = 1;
                document.getElementById('stock-display').textContent =
                    stock > 0 ? `Còn ${stock} sản phẩm` : 'Hết hàng';
            }, 150);
        }

        // Render chiều cao
        const heights = allVariants.map(v => v.height).filter((v, i, a) => a.indexOf(v) === i);
        heights.forEach(height => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-dark m-1 height-option';
            btn.dataset.height = height;
            btn.textContent = height;

            btn.onclick = function () {
                document.querySelectorAll('.height-option').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                heightInput.value = height;
                renderPotButtons(height);
                updatePrice(null, height); // Mặc định không chọn chậu khi đổi chiều cao
            };

            heightContainer.appendChild(btn);
        });

        // Auto chọn chiều cao đầu tiên
        if (heights.length > 0) {
            const defaultHeight = heights[0];
            heightInput.value = defaultHeight;
            const defaultHeightBtn = [...document.querySelectorAll('.height-option')].find(b => b.dataset.height == defaultHeight);
            defaultHeightBtn?.classList.add('active');
            renderPotButtons(defaultHeight);
            updatePrice(null, defaultHeight);
        }

        // Thêm vào giỏ AJAX
        addBtn?.addEventListener('click', function () {
            const productId = document.getElementById('product-id').value;
            const pot = potInput.value || null;
            const height = heightInput.value;
            const quantity = parseInt(document.getElementById('quantity').value);

            if (!height) {
                alert('Vui lòng chọn chiều cao!');
                return;
            }

            fetch(form.dataset.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, pot, height, quantity })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);

                // ✅ Cập nhật số giỏ hàng (thêm dấu ngoặc như giao diện hiện tại)
                if (typeof data.cart_count !== 'undefined') {
                    const cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) {
                        cartCountEl.textContent = `(${data.cart_count})`;
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert('Đã có lỗi xảy ra khi thêm vào giỏ hàng!');
            });
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll("#star-rating i");
    const ratingInput = document.getElementById("rating");
    let selected = 0;

    stars.forEach(star => {
        star.addEventListener("mouseover", function () {
            highlightStars(parseInt(this.dataset.value));
        });
        star.addEventListener("mouseout", function () {
            highlightStars(selected);
        });
        star.addEventListener("click", function () {
            selected = parseInt(this.dataset.value);
            ratingInput.value = selected;
            highlightStars(selected);
        });
    });

    function highlightStars(value) {
        stars.forEach(star => {
            star.style.color = parseInt(star.dataset.value) <= value ? "#f5c518" : "#ccc";
        });
    }
});

function previewReviewImage(event) {
    const previewContainer = document.getElementById('image-preview');
    previewContainer.innerHTML = "";

    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement("img");
            img.src = e.target.result;
            img.className = "img-thumbnail mt-2";
            img.style.maxHeight = "120px";
            img.style.borderRadius = "6px";
            previewContainer.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
}

</script>
@endsection
