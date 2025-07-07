@extends('client.layouts.default')

@section('title', $product->name)

@section('content')
<!-- ##### Breadcrumb Area Start ##### -->
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
<!-- ##### Breadcrumb Area End ##### -->

<section class="single_product_details_area mb-50">
    <div class="container">
        <div class="row align-items-center">
            <!-- Product Image -->
            <div class="col-12 col-md-6">
                @php
                    $image = $product->galleries->first()->image ?? 'assets/img/bg-img/default.jpg';
                    $imagePath = Str::startsWith($image, ['http', 'assets/', 'img/'])
                        ? asset($image)
                        : asset('img/products/' . $image);
                @endphp
                <img class="d-block w-100" src="{{ $imagePath }}"
                    onerror="this.onerror=null;this.src='{{ asset('assets/img/default.jpg') }}';"
                    alt="{{ $product->name }}">
            </div>

            <!-- Product Info -->
            <div class="col-12 col-md-6">
                <h4 class="product-title mb-2 text-uppercase">{{ $product->name }}</h4>

                @php
                    $price = $product->variants->first()->price ?? 0;
                @endphp

                <h4 id="price-display" class="text-success mb-3">{{ number_format($price, 0, ',', '.') }}đ</h4>

                <p class="mb-4">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="form-group">
                        <label for="pot">Chọn chậu:</label>
                        <select name="pot" id="pot" class="form-control" required>
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->pot }}" data-price="{{ $variant->price }}">
                                    {{ $variant->pot }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex align-items-center mt-3 mb-3">
                        <div class="quantity">
                            <span class="qty-minus" onclick="document.getElementById('quantity').stepDown();">
                                <i class="fa fa-minus"></i>
                            </span>
                            <input type="number" id="quantity" name="quantity" value="1" min="1"
                                class="qty-text mx-2" style="width: 60px;">
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

        <!-- Tabs: Description, Info, Reviews -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="product_details_tab clearfix">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" role="tablist" id="product-details-tab">
                        <li class="nav-item">
                            <a href="#description" class="nav-link active" data-toggle="tab" role="tab">Description</a>
                        </li>
                        <li class="nav-item">
                            <a href="#addi-info" class="nav-link" data-toggle="tab" role="tab">Additional Info</a>
                        </li>
                        <li class="nav-item">
                            <a href="#reviews" class="nav-link" data-toggle="tab" role="tab">Reviews <span class="text-muted">({{ count($comments) }})</span></a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Description -->
                        <div role="tabpanel" class="tab-pane fade show active" id="description">
                            <p class="mt-3">{{ $product->description }}</p>
                            <div class="reviews_area mt-4">
                                <ul>
                                    @foreach ($comments as $value)
                                        <li>
                                            <strong>{{ $value->name }}</strong>: {{ $value->content }}<br>
                                            <small class="text-muted">{{ $value->created_at->format('H:i d/m/Y') }}</small>
                                            <hr>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <form action="{{ route('comment.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nickname</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ auth()->user()->name ?? '' }}" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                value="{{ auth()->user()->email ?? '' }}" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="comment">Comments</label>
                                            <textarea class="form-control" name="comment" id="comment" rows="5" required></textarea>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn alazea-btn">Gửi nhận xét</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Additional Info -->
                        <div role="tabpanel" class="tab-pane fade" id="addi-info">
                            <div class="additional_info_area mt-3">
                                <p>Thông tin bổ sung về quy trình giao hàng, đổi trả và chăm sóc cây sẽ được hiển thị tại đây.</p>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div role="tabpanel" class="tab-pane fade" id="reviews">
                            <div class="reviews_area mt-3">
                                <p>Hiện tại chưa có đánh giá nào.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectElement = document.getElementById('pot');
        const priceDisplay = document.getElementById('price-display');

        function updatePrice() {
            const selected = selectElement.options[selectElement.selectedIndex];
            const price = selected.getAttribute('data-price');
            if (price) {
                priceDisplay.textContent = Number(price).toLocaleString('vi-VN') + 'đ';
            }
        }

        selectElement.addEventListener('change', updatePrice);
        updatePrice(); // Gọi lần đầu để hiển thị đúng giá ban đầu
    });
</script>
@endsection
