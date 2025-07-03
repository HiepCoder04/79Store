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
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                            </li>

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

                    <h4 class="text-success mb-3">{{ number_format($price, 0, ',', '.') }}đ</h4>
                    @endphp
                    <img class="d-block w-100" src="{{ asset(ltrim($image, '/')) }}" alt="{{ $product->name }}">
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
                                @foreach ($product->variants->unique('pot') as $variant)
                                    <option value="{{ $variant->pot }}">{{ $variant->pot }}</option>
                                    =======
                                    @foreach ($product->variants as $variant)
                                        <option value="{{ $variant->pot }}" data-price="{{ $variant->price }}">
                                            {{ $variant->pot }}
                                        </option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center mt-3 mb-3">
                            <div class="quantity">
                                <span class="qty-minus" onclick="document.getElementById('quantity').stepDown();"><i
                                        class="fa fa-minus"></i></span>
                                <input type="number" id="quantity" name="quantity" value="1" min="1"
                                    class="qty-text mx-2" style="width: 60px;">
                                <input type="number" id="quantity" name="quantity" value="1" min="1"
                                    class="qty-text mx-2" style="width: 60px;">
                                <span class="qty-plus" onclick="document.getElementById('quantity').stepUp();"><i
                                        class="fa fa-plus"></i></span>
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
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="product_details_tab clearfix">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" role="tablist" id="product-details-tab">
                            <li class="nav-item">
                                <a href="#description" class="nav-link active" data-toggle="tab"
                                    role="tab">Description</a>
                            </li>
                            <li class="nav-item">
                                <a href="#addi-info" class="nav-link" data-toggle="tab" role="tab">Additional
                                    Information</a>
                            </li>
                            <li class="nav-item">
                                <a href="#reviews" class="nav-link" data-toggle="tab" role="tab">Reviews <span
                                        class="text-muted">(1)</span></a>
                            </li>
                        </ul>
                        <!-- Tab Content -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="description">
                                <div class="description_area">
                                    <div class="reviews_area">
                                        <ul>
                                            @foreach ($comments as $value)
                                                <li><strong>{{ $value->name }}</strong> : {{ $value->content }}</li>
                                                <li><strong>{{ $value->name }}</strong> : {{ $value->content }}<br>
                                                    <small class="text-muted">
                                                        {{ $value->created_at->format('H:i d/m/Y') }}
                                                    </small>
                                                    <hr>
                                                </li>

                                            @endforeach
                                        </ul>
                                    </div>
                                    <form action="{{ route('comment.store') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">

                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Nickname</label>
                                                    <input type="name" class="form-control" id="name"
                                                        name="name" placeholder="Nazrul"
                                                        value="{{ auth()->user()->name ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Email</label>
                                                    <input type="email" class="form-control" id="name"
                                                        name="email" placeholder="Nazrul"
                                                        value="{{ auth()->user()->email ?? '' }}">
                                                    <input type="email" class="form-control" id="name"
                                                        name="email" placeholder="Nazrul"
                                                        value="{{ auth()->user()->email ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="comments">Comments</label>
                                                    <textarea class="form-control" id="comments" name="comment" rows="5" data-max-length="150"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <input type="hidden" value="{{ $product->id }}" name="product_id">
                                                <button type="submit" class="btn alazea-btn">Submit Your Review</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="addi-info">
                                <div class="additional_info_area">
                                    <p>What should I do if I receive a damaged parcel?
                                        <br> <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reprehenderit
                                            impedit similique qui, itaque delectus labore.</span>
                                    </p>
                                    <p>I have received my order but the wrong item was delivered to me.
                                        <br> <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis quam
                                            voluptatum beatae harum tempore, ab?</span>
                                    </p>
                                    <p>Product Receipt and Acceptance Confirmation Process
                                        <br> <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorum
                                            ducimus, temporibus soluta impedit minus rerum?</span>
                                    </p>
                                    <p>How do I cancel my order?
                                        <br> <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum eius
                                            eum, minima!</span>
                                    </p>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="reviews">
                                <div class="reviews_area">
                                    <ul>
                                        <li>
                                            <div class="single_user_review mb-15">
                                                <div class="review-rating">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <span>for Quality</span>
                                                </div>
                                                <div class="review-details">
                                                    <p>by <a href="#">Colorlib</a> on <span>12 Sep 2018</span></p>
                                                </div>
                                            </div>
                                            <div class="single_user_review mb-15">
                                                <div class="review-rating">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <span>for Design</span>
                                                </div>
                                                <div class="review-details">
                                                    <p>by <a href="#">Colorlib</a> on <span>12 Sep 2018</span></p>
                                                </div>
                                            </div>
                                            <div class="single_user_review">
                                                <div class="review-rating">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <span>for Value</span>
                                                </div>
                                                <div class="review-details">
                                                    <p>by <a href="#">Colorlib</a> on <span>12 Sep 2018</span></p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="submit_a_review_area mt-50">
                                    <h4>Submit A Review</h4>
                                    <form action="#" method="post">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group d-flex align-items-center">
                                                    <span class="mr-15">Your Ratings:</span>
                                                    <div class="stars">
                                                        <input type="radio" name="star" class="star-1"
                                                            id="star-1">
                                                        <label class="star-1" for="star-1">1</label>
                                                        <input type="radio" name="star" class="star-2"
                                                            id="star-2">
                                                        <label class="star-2" for="star-2">2</label>
                                                        <input type="radio" name="star" class="star-3"
                                                            id="star-3">
                                                        <label class="star-3" for="star-3">3</label>
                                                        <input type="radio" name="star" class="star-4"
                                                            id="star-4">
                                                        <label class="star-4" for="star-4">4</label>
                                                        <input type="radio" name="star" class="star-5"
                                                            id="star-5">
                                                        <label class="star-5" for="star-5">5</label>
                                                        <span></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Nickname</label>
                                                    <input type="email" class="form-control" id="name"
                                                        placeholder="Nazrul">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="options">Reason for your rating</label>
                                                    <select class="form-control" id="options">
                                                        <option>Quality</option>
                                                        <option>Value</option>
                                                        <option>Design</option>
                                                        <option>Price</option>
                                                        <option>Others</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="comments">Comments</label>
                                                    <textarea class="form-control" id="comments" rows="5" data-max-length="150"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn alazea-btn">Submit Your Review</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('pot');
        const priceDisplay = document.getElementById('price-display');

        function updatePrice() {
            const selected = selectElement.options[selectElement.selectedIndex];
            const price = selected.getAttribute('data-price');
            priceDisplay.textContent = Number(price).toLocaleString('vi-VN') + 'đ';
        }

        // Lắng nghe khi user thay đổi select
        selectElement.addEventListener('change', updatePrice);

        // Gọi lần đầu nếu cần
        updatePrice();
    });
</script>
@endsection
