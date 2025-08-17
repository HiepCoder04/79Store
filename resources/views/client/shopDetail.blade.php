@extends('client.layouts.default')

@section('title', $product->name)

@section('content')


<div class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url('https://i.imgur.com/T6dAash.jpeg');">
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

                    <!-- Chọn chậu -->
                    @if($potsToShow->isNotEmpty())
                    <div class="form-group">
                    <label>Chọn chậu:
                         <span style="font-size: 0.9em; color: #888;">(Không bắt buộc)</span>
                    </label><br>
                    <div id="pot-buttons"></div> <!-- GIỮ LẠI DÒNG NÀY để JS render vào -->
                    </div>
                    @endif

                    <div id="add-to-cart-form" data-url="{{ route('cart.add.ajax') }}">
                        <input type="hidden" id="product-id" value="{{ $product->id }}">

                        <div class="d-flex align-items-center mt-3 mb-3">
                            <div class="quantity">
                                <span class="qty-minus" onclick="document.getElementById('quantity').stepDown();">
                                    <i class="fa fa-minus"></i>
                                </span>
                                <input type="number" id="quantity" value="1" min="1" class="qty-text mx-2" style="width: 60px;">
                                <span class="qty-plus" onclick="document.getElementById('quantity').stepUp();">
                                    <i class="fa fa-plus"></i>
                                </span>
                            </div>
                            <!-- ✅ Nút AJAX -->
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

     <!-- ==== Bình luận sản phẩm ==== -->
<div class="container d-flex justify-content-center">
    <div class="col-md-8 col-lg-6 mt-4">
        <h5 class="mb-3 fw-semibold text-center">Bình luận sản phẩm</h5>

        {{-- Form gửi bình luận --}}
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

        {{-- Danh sách bình luận --}}
        @foreach($product->comments()->whereNull('parent_id')->latest()->get() as $comment)
            <div class="border rounded p-2 mb-2 bg-light-subtle small">
                <div class="d-flex justify-content-between mb-1">
                    <strong>{{ $comment->user->name }}</strong>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <div class="text-muted">{{ $comment->content }}</div>

                {{-- Trả lời --}}
                @foreach($comment->replies as $reply)
                    <div class="ms-3 mt-2 p-2 bg-white border rounded small">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-semibold">{{ $reply->user->name }}</small>
                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-muted">{{ $reply->content }}</div>
                    </div>
                @endforeach

                {{-- Form trả lời --}}
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


</section>
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
</script>
@endsection
