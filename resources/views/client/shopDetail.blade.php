@extends('client.layouts.default')

@section('title', $product->name)

@section('content')
@php
    $variants = $product->variants->map(fn ($v) => [
        'pot' => (string)$v->pot,
        'height' => (string)$v->height,
        'price' => $v->price,
         'stock_quantity' => $v->stock_quantity,
    ]);
@endphp

<div class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url('{{ asset('assets/img/bg-img/24.jpg') }}');">
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
                <img  class="d-block w-100" src="{{ $imagePath }}" alt="{{ $product->name }}">
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

                    <div class="form-group">
                        <label>Chọn chậu:</label><br>
                        <div id="pot-buttons">
                            @foreach ($product->variants->pluck('pot')->unique() as $pot)
                                <button type="button" class="btn btn-outline-dark m-1 pot-option" data-pot="{{ $pot }}">{{ $pot }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group" id="height-group" style="display:none;">
                        <label>Chọn chiều cao:</label><br>
                        <div id="height-buttons"></div>
                         <p id="stock-display" class="text-muted mt-2"></p>
                    </div>

                    <div id="add-to-cart-form" data-url="{{ route('cart.add.ajax') }}">
                        <input type="hidden" id="product-id" value="{{ $product->id }}">
                        <input type="hidden" id="selected-pot">
                        <input type="hidden" id="selected-height">

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
            <p class="text-muted text-center">Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</p>
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
    const allVariants = @json($variants);

    document.addEventListener('DOMContentLoaded', function () {
        const potContainer = document.getElementById('pot-buttons');
        const heightContainer = document.getElementById('height-buttons');
        const priceDisplay = document.getElementById('price-display');
        const potInput = document.getElementById('selected-pot');
        const heightInput = document.getElementById('selected-height');
        const heightGroup = document.getElementById('height-group');

        const addBtn = document.getElementById('add-to-cart-btn');
        const form = document.getElementById('add-to-cart-form');

        // --- Biến thể: chọn chậu + chiều cao ---
        function renderHeightButtons(pot) {
            heightGroup.style.display = 'block';
            const heights = allVariants
                .filter(v => v.pot.toLowerCase().trim() === pot.toLowerCase().trim())
                .map(v => String(v.height).trim())
                .filter((v, i, arr) => arr.indexOf(v) === i);

            heightContainer.innerHTML = '';
            heights.forEach((height) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-dark m-1 height-option';
                btn.dataset.height = height;
                btn.textContent = height;
                btn.onclick = function () {
                    document.querySelectorAll('.height-option').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    heightInput.value = height;
                    updatePrice(pot, height);
                };
                heightContainer.appendChild(btn);
            });

            if (heights.length > 0) {
                const firstBtn = heightContainer.querySelector('.height-option');
                firstBtn?.click();
            } else {
                heightInput.value = '';
                updatePrice('', '');
            }
        }

        function updatePrice(pot, height) {
            const variant = allVariants.find(v =>
                v.pot.toLowerCase().trim() === pot.toLowerCase().trim() &&
                v.height.toLowerCase().trim() === height.toLowerCase().trim()
            );

            const price = variant ? Number(variant.price).toLocaleString('vi-VN') + 'đ' : '0đ';
            const stock = variant ? variant.stock_quantity : 0;

            priceDisplay.style.opacity = 0;
            setTimeout(() => {
                priceDisplay.textContent = price;
                priceDisplay.style.opacity = 1;
                document.getElementById('stock-display').textContent =
                    stock > 0 ? `Còn ${stock} sản phẩm` : 'Hết hàng';
            }, 150);
        }

        document.querySelectorAll('.pot-option').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.pot-option').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                potInput.value = btn.dataset.pot;
                renderHeightButtons(btn.dataset.pot);
            });
        });

        heightGroup.style.display = 'none';

        // --- AJAX thêm vào giỏ ---
        addBtn?.addEventListener('click', function () {
            const productId = document.getElementById('product-id').value;
            const pot = potInput.value;
            const height = heightInput.value;
            const quantity = parseInt(document.getElementById('quantity').value);

            if (!pot || !height) {
                alert('Vui lòng chọn chậu và chiều cao!');
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
                if (data.success) {
                    flyToCart();
                    updateCartCount();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Đã có lỗi xảy ra khi thêm vào giỏ hàng!');
            });
        });

        function updateCartCount() {
            fetch('/cart/count')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = `(${data.count})`;
                });
        }

        function flyToCart() {
            const img = document.getElementById('product-image');
            const cartIcon = document.getElementById('cart-icon');
            if (!img || !cartIcon) return;

            const imgRect = img.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            const flyingImg = img.cloneNode(true);
            flyingImg.style.position = 'fixed';
            flyingImg.style.top = imgRect.top + 'px';
            flyingImg.style.left = imgRect.left + 'px';
            flyingImg.style.width = img.offsetWidth + 'px';
            flyingImg.style.height = img.offsetHeight + 'px';
            flyingImg.style.transition = 'all 1s ease-in-out';
            flyingImg.style.zIndex = 9999;
            document.body.appendChild(flyingImg);

            setTimeout(() => {
                flyingImg.style.top = cartRect.top + 'px';
                flyingImg.style.left = cartRect.left + 'px';
                flyingImg.style.width = '20px';
                flyingImg.style.height = '20px';
                flyingImg.style.opacity = 0.3;
            }, 10);

            setTimeout(() => {
                document.body.removeChild(flyingImg);
            }, 1000);
        }
    });
</script>
@endsection
