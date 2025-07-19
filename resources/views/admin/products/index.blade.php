@extends('admin.layouts.dashboard')

@section('content')
{{-- Thêm meta tag CSRF token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Thêm sản phẩm
        </a>
    </div>

    {{-- Thống kê --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Tổng sản phẩm</h5>
                    <h3>{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Đang hoạt động</h5>
                    <h3>{{ $stats['active'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Đã xóa</h5>
                    <h3>{{ $stats['deleted'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc trạng thái --}}
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.products.index') }}"
               class="btn {{ request('status', 'active') === 'active' ? 'btn-primary' : 'btn-outline-primary' }}">
                Đang hoạt động
            </a>
            <a href="{{ route('admin.products.index', ['status' => 'deleted']) }}"
               class="btn {{ request('status') === 'deleted' ? 'btn-danger' : 'btn-outline-danger' }}">
                Đã xóa
            </a>
            <a href="{{ route('admin.products.index', ['status' => 'all']) }}"
               class="btn {{ request('status') === 'all' ? 'btn-info' : 'btn-outline-info' }}">
                Tất cả
            </a>
        </div>
    </div>

    {{-- Hiển thị thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bảng sản phẩm --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->deleted_at ? 'table-secondary' : '' }}">
                        <td>{{ $product->id }}</td>
                        <td>
                            {{ $product->name }}
                            @if($product->deleted_at)
                                <span class="badge bg-danger ms-2">Đã xóa</span>
                            @endif
                        </td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ number_format($product->variants->first()->price ?? 0) }}đ</td>
                        <td>
                            @if($product->deleted_at)
                                <span class="badge bg-danger">Đã xóa</span>
                                <small class="text-muted d-block">{{ $product->deleted_at->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-success">Hoạt động</span>
                            @endif
                        </td>
                        <td>
                            @if($product->deleted_at)
                                {{-- Sản phẩm đã bị xóa mềm --}}
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-success"
                                            onclick="restoreProduct({{ $product->id }})"
                                            title="Khôi phục sản phẩm">
                                        <i class="fa fa-undo"></i> Khôi phục
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="forceDeleteProduct({{ $product->id }})"
                                            title="Xóa vĩnh viễn - Không thể hoàn tác">
                                        <i class="fa fa-trash-alt"></i> Xóa vĩnh viễn
                                    </button>
                                </div>
                            @else
                                {{-- Sản phẩm đang hoạt động --}}
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       class="btn btn-sm btn-info text-white"
                                       title="Xem chi tiết sản phẩm">
                                        <i class="fa fa-eye"></i> Chi tiết
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-sm btn-warning text-white"
                                       title="Chỉnh sửa thông tin sản phẩm">
                                        <i class="fa fa-edit"></i> Sửa
                                    </a>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="deleteProduct({{ $product->id }})"
                                            title="Xóa sản phẩm (có thể khôi phục)">
                                        <i class="fa fa-trash"></i> Xóa
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có sản phẩm nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>

{{-- JavaScript đặt ngay trong section --}}
<script>
// Đảm bảo DOM đã load xong
document.addEventListener('DOMContentLoaded', function() {
    // Lấy CSRF token từ meta tag hoặc từ Laravel global
    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        // Fallback: tìm từ hidden input trong form
        const hiddenInput = document.querySelector('input[name="_token"]');
        if (hiddenInput) {
            return hiddenInput.value;
        }
        // Fallback cuối: từ window object nếu có
        return window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : '';
    }

    // Định nghĩa functions trong global scope
    window.deleteProduct = function(id) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            const csrfToken = getCSRFToken();

            if (!csrfToken) {
                alert('Lỗi: Không tìm thấy CSRF token');
                return;
            }

            fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, data: data };
                });
            })
            .then(result => {
                if (result.status === 200 && result.data.success) {
                    alert('✅ ' + (result.data.message || 'Xóa sản phẩm thành công'));
                    location.reload();
                } else {
                    // Hiển thị thông báo lỗi từ server một cách thân thiện
                    const message = result.data.message || 'Không thể xóa sản phẩm';
                    alert('⚠️ ' + message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Có lỗi xảy ra khi kết nối đến server');
            });
        }
    };

    window.restoreProduct = function(id) {
        if (confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này?')) {
            const csrfToken = getCSRFToken();

            if (!csrfToken) {
                alert('Lỗi: Không tìm thấy CSRF token');
                return;
            }

            fetch(`/admin/products/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, data: data };
                });
            })
            .then(result => {
                if (result.status === 200 && result.data.success) {
                    alert('✅ ' + (result.data.message || 'Khôi phục sản phẩm thành công'));
                    location.reload();
                } else {
                    alert('⚠️ ' + (result.data.message || 'Không thể khôi phục sản phẩm'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Có lỗi xảy ra khi kết nối đến server');
            });
        }
    };

    window.forceDeleteProduct = function(id) {
        if (confirm('⚠️ CẢNH BÁO: Bạn có chắc chắn muốn xóa VĨNH VIỄN sản phẩm này?\n\n\nNhấn OK để tiếp tục hoặc Cancel để hủy.')) {
            const csrfToken = getCSRFToken();

            if (!csrfToken) {
                alert('Lỗi: Không tìm thấy CSRF token');
                return;
            }

            fetch(`/admin/products/${id}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, data: data };
                });
            })
            .then(result => {
                if (result.status === 200 && result.data.success) {
                    alert('✅ ' + (result.data.message || 'Xóa vĩnh viễn sản phẩm thành công'));
                    location.reload();
                } else {
                    alert('⚠️ ' + (result.data.message || 'Không thể xóa vĩnh viễn sản phẩm'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Có lỗi xảy ra khi kết nối đến server');
            });
        }
    };
});
</script>

{{-- Thêm CSS để cải thiện giao diện --}}
<style>
.btn-group .btn {
    margin-right: 2px;
    border-radius: 4px !important;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    min-width: 80px;
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

/* Responsive cho mobile */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .btn-sm {
        min-width: 70px;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .btn-sm i {
        margin-right: 3px;
    }
}

/* Hiệu ứng cho trạng thái */
tr.table-secondary {
    background-color: #f8f9fa !important;
    opacity: 0.8;
}

tr.table-secondary:hover {
    background-color: #e9ecef !important;
}

/* Badge cải thiện */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

/* Icon cải thiện */
.btn i {
    margin-right: 4px;
}
</style>
@endsection
