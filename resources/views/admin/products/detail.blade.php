@extends('admin.layouts.dashboard')

@section('content')
<style>
.product-detail-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    max-width: 900px;
    margin: 30px auto;
}

/* Headings */
.product-detail-container h2,
.product-detail-container h3 {
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Info block */
.product-meta p {
    margin-bottom: 8px;
    color: #475569;
}

.product-meta strong {
    color: #334155;
}

/* Description content (rich text) */
.product-description {
    margin-top: 8px;
    margin-bottom: 16px;
    line-height: 1.5;
    color: #334155;
}

.product-description p {
    margin-bottom: 0.75em;
}

/* Table */
.product-variant-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.product-variant-table-wrapper::-webkit-scrollbar {
    height: 6px;
}

.product-variant-table-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.product-variant-table-wrapper::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.product-variant-table.table {
    margin-top: 15px;
    border-radius: 8px;
    overflow: hidden;
    min-width: 450px;
    /* để tránh bóp quá nhỏ gây khó đọc */
}

.product-variant-table.table th {
    background-color: #f1f5f9;
    color: #334155;
    white-space: nowrap;
}

.product-variant-table.table td {
    background-color: #f9fafb;
    vertical-align: middle;
}

/* Currency alignment */
.product-variant-table.table td.price-cell {
    text-align: right;
}

.product-variant-table.table td.qty-cell {
    text-align: center;
    width: 1%;
    white-space: nowrap;
}

/* Gallery */
.product-gallery {
    --gap: 16px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: var(--gap);
    margin-top: 16px;
}

.product-gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.product-gallery-item img {
    width: 100%;
    height: auto;
    display: block;
}

.product-gallery-item:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

/* Responsive tweaks */
@media (max-width: 576px) {
    .product-detail-container {
        padding: 20px;
        margin-top: 16px;
    }

    .product-gallery {
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        --gap: 12px;
    }
}

/* Back button */
.btn-back-list {
    background-color: #64748b;
    border: none;
    padding: 8px 18px;
    font-weight: 500;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
}

.btn-back-list:hover {
    background-color: #475569;
    color: #fff;
}

/* Optional utility spacing if Bootstrap margin classes missing */
.mt-4 {
    margin-top: 1.5rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}
</style>

<div class="product-detail-container">
    <h2>Chi tiết sản phẩm: {{ $product->name }}</h2>

    <div class="product-meta mb-4">
        <p><strong>Tên:</strong> {{ $product->name }}</p>
        <p><strong>Danh mục:</strong> {{ $product->category->name ?? 'N/A' }}</p>
        <div class="product-description">
            <strong>Mô tả:</strong>
            <div>{!! $product->description !!}</div>
        </div>
        <p>
            <strong>Trạng thái:</strong>
            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $product->is_active ? 'Hoạt động' : 'Không hoạt động' }}
            </span>
        </p>
        <p><strong>Ngày tạo:</strong> {{ optional($product->created_at)->format('d/m/Y H:i') }}</p>
    </div>

    <h3>Biến thể sản phẩm</h3>
    <div class="product-variant-table-wrapper">
        <table class="table table-bordered product-variant-table">
            <thead>
                <tr>
                    <th>Chậu</th>
                    <th>Giá</th>
                    <th>Số lượng tồn kho</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->variants as $variant)
                <tr>
                    <td>{{ $variant->pot ?? 'Không có' }}</td>
                    <td class="price-cell">
                        {{ is_numeric($variant->price) ? number_format($variant->price, 0, ',', '.') . '₫' : 'N/A' }}
                    </td>
                    <td class="qty-cell">{{ $variant->stock_quantity ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">Chưa có biến thể nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="mt-4">Hình ảnh sản phẩm</h3>
    @if($product->galleries->count())
    <div class="product-gallery">
        @foreach($product->galleries as $gallery)
        <div class="product-gallery-item">
            <img src="{{ $gallery->image }}" alt="Ảnh sản phẩm">
        </div>
        @endforeach
    </div>
    @else
    <p>Chưa có hình ảnh.</p>
    @endif

    <div class="text-end mt-4">
        <a href="{{ route('admin.products.index') }}" class="btn-back-list">← Quay lại danh sách</a>
    </div>
</div>
@endsection