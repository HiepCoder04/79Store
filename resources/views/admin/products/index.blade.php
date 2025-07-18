@extends('admin.layouts.dashboard')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📦 Danh sách sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">
            ➕ Thêm sản phẩm
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Tên</th>
                    <th scope="col">Danh mục</th>
                    <th scope="col">Biến thể</th>
                    <th scope="col">Khoảng giá</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr>
                    <td class="text-start">{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'Không rõ' }}</td>

                    {{-- Biến thể --}}
                    <td class="text-start small">
                        @if ($product->variants->count())
                            <ul class="list-unstyled mb-0">
                                @foreach ($product->variants->take(2) as $variant)
                                    <li>• {{ $variant->pot ?? 'Chậu ?' }}, {{ $variant->height ?? '?' }}cm</li>
                                @endforeach
                                @if ($product->variants->count() > 2)
                                    <li class="text-muted">+{{ $product->variants->count() - 2 }} biến thể</li>
                                @endif
                            </ul>
                        @else
                            <span class="text-muted fst-italic">Không có</span>
                        @endif
                    </td>

                    {{-- Khoảng giá --}}
                    <td>
                        @php
                            $prices = $product->variants->pluck('price')->filter(); // Lấy mảng giá
                        @endphp

                        @if ($prices->count() === 0)
                            <span class="text-muted fst-italic">Không có</span>
                        @elseif ($prices->count() === 1)
                            {{ number_format($prices->first(), 0, ',', '.') }} <span class="text-muted">VNĐ</span>
                        @else
                            {{ number_format($prices->min(), 0, ',', '.') }} - {{ number_format($prices->max(), 0, ',', '.') }}
                            <span class="text-muted">VNĐ</span>
                        @endif

                    </td>

                    {{-- Hành động --}}
                    <td>
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i> Xem
                        </a>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này?')">
                                <i class="bi bi-trash"></i> Xoá
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-muted fst-italic">Không có sản phẩm nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
