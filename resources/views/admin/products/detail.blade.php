@extends('admin.layouts.dashboard')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header text-white">
            <h4 class="mb-0">Chi tiết sản phẩm: {{ $product->name }}</h4>
        </div>
        <div class="card-body">
            <p><strong>Tên:</strong> {{ $product->name }}</p>
            <p><strong>Danh mục:</strong> {{ $product->category->name }}</p>
            <p><strong>Mô tả:</strong> {!! $product->description !!}</p>
            <p><strong>Trạng thái:</strong> 
                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $product->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
            </p>
            <p><strong>Ngày tạo:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="card mt-4 shadow">
        <div class="card-header text-white">
            <h5 class="mb-0">Biến thể sản phẩm</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Chiều cao</th>
                        <th>Giá</th>
                        <th>Số lượng tồn kho</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->variants as $variant)
                    <tr>
                        <td>{{ $variant->height ? $variant->height . ' cm' : 'Không có' }}</td>
                        <td>{{ is_numeric($variant->price) ? number_format($variant->price, 0, ',', '.') . 'Đ' : 'N/A' }}</td>
                        <td>{{ $variant->stock_quantity ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chậu đã liên kết --}}
<div class="card mt-4 shadow">
    <div class="card-header text-white">
        <h5 class="mb-0">Chậu đã liên kết</h5>
    </div>
    <div class="card-body">
        @if(isset($allPots) && $allPots->isNotEmpty())
            <div class="row row-cols-1 row-cols-md-3 g-3">
                @foreach($allPots as $pot)
                    <div class="col">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $pot->name ?? 'Chậu' }}</div>
                                    @if(!empty($pot->code))
                                        <div class="text-muted small">Mã: {{ $pot->code }}</div>
                                    @endif
                                </div>
                                @if(is_numeric($pot->price))
                                    <div class="fw-semibold">
                                        {{ number_format($pot->price, 0, ',', '.') }}Đ
                                    </div>
                                @endif
                            </div>

                            @if(!empty($pot->description))
                                <div class="mt-2 small text-muted">{!! $pot->description !!}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">Chưa có chậu nào được liên kết.</p>
        @endif
    </div>
</div>

    {{-- Hình ảnh sản phẩm --}}
    <div class="card mt-4 shadow">
        <div class="card-header text-white">
            <h5 class="mb-0">Hình ảnh sản phẩm</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($product->galleries as $gallery)
                    <div class="col-md-3 mb-3">
                        <img src="{{ $gallery->image }}" class="img-fluid rounded border" alt="Product Image">
                    </div>
                @empty
                    <p class="text-muted">Không có hình ảnh nào cho sản phẩm này.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            ← Quay lại danh sách
        </a>
    </div>
</div>
@endsection
