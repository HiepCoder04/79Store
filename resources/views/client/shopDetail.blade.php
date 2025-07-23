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
                <img class="d-block w-100" src="{{ $imagePath }}" alt="{{ $product->name }}">
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

                    <div class="d-flex align-items-center mt-3 mb-3">
                        <div class="quantity">
                            <span class="qty-minus" onclick="document.getElementById('quantity').stepDown();">
                                <i class="fa fa-minus"></i>
                            </span>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="qty-text mx-2" style="width: 60px;">
                            <span class="qty-plus" onclick="document.getElementById('quantity').stepUp();">
                                <i class="fa fa-plus"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn alazea-btn ml-3">THÊM VÀO GIỎ</button>
                    </div>
                </form>

                <ul class="list-unstyled">
                    <li><strong>Danh Mục:</strong> {{ $product->category->name }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page_scripts')
<script>
    const allVariants = @json($variants);

    window.addEventListener('DOMContentLoaded', function () {
        const potContainer = document.getElementById('pot-buttons');
        const heightContainer = document.getElementById('height-buttons');
        const priceDisplay = document.getElementById('price-display');

        const potInput = document.getElementById('selected-pot');
        const heightInput = document.getElementById('selected-height');
        const heightGroup = document.getElementById('height-group');

        function renderHeightButtons(pot) {
            heightGroup.style.display = 'block';
            const heights = allVariants
                .filter(v => v.pot.toLowerCase().trim() === pot.toLowerCase().trim())
                .map(v => String(v.height).trim())
                .filter((v, i, arr) => arr.indexOf(v) === i); // loại bỏ trùng

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

            // Auto chọn chiều cao đầu tiên nếu có
            if (heights.length > 0) {
                const firstBtn = heightContainer.querySelector('.height-option');
                if (firstBtn) {
                    firstBtn.classList.add('active');
                    heightInput.value = heights[0];
                    updatePrice(pot, heights[0]);
                }
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
        const stockText = stock > 0 ? `Còn ${stock} sản phẩm` : 'Hết hàng';
        document.getElementById('stock-display').textContent = stockText;
    }, 150);
}

        // Bắt sự kiện click chậu
        document.querySelectorAll('.pot-option').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.pot-option').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const pot = btn.dataset.pot;
                potInput.value = pot;
                renderHeightButtons(pot);
            });
        });

        // Mặc định ẩn chiều cao và không chọn sẵn
        heightGroup.style.display = 'none';
    });
</script>
@endsection

