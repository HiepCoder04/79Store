@extends('client.layouts.default')

@section('title', $product->name)

@section('content')
<!-- ##### Breadcrumb Area Start ##### -->
<div class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center" style="background-image: url('{{ asset('assets/img/bg-img/24.jpg') }}');">
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
                @endphp
                <img class="d-block w-100" src="{{ asset(ltrim($image, '/')) }}" alt="{{ $product->name }}">
            </div>

            <!-- Product Info -->
            <div class="col-12 col-md-6">
                <h4 class="product-title mb-2 text-uppercase">{{ $product->name }}</h4>

                @php
                    $price = $product->variants->first()->price ?? 0;
                @endphp

                <h4 class="text-success mb-3">{{ number_format($price, 0, ',', '.') }}đ</h4>

                <p class="mb-4">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.add') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">

    <div class="form-group">
        <label for="pot">Chọn chậu:</label>
        <select name="pot" id="pot" class="form-control" required>
            @foreach ($product->variants->unique('pot') as $variant)
                <option value="{{ $variant->pot }}">{{ $variant->pot }}</option>
            @endforeach
        </select>
    </div>

    <div class="d-flex align-items-center mt-3 mb-3">
        <div class="quantity">
            <span class="qty-minus" onclick="document.getElementById('quantity').stepDown();"><i class="fa fa-minus"></i></span>
            <input type="number" id="quantity" name="quantity" value="1" min="1" class="qty-text mx-2" style="width: 60px;">
            <span class="qty-plus" onclick="document.getElementById('quantity').stepUp();"><i class="fa fa-plus"></i></span>
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
