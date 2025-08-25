@extends('admin.layouts.dashboard')

@section('content')
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

    {{-- Form search + filter --}}
    {{-- Form search + filter đẹp và cân đối --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-bold"><i class="fa fa-filter me-1 text-primary"></i> Bộ lọc tìm kiếm</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="row g-3">
                {{-- Tìm kiếm --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold mb-1">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nhập tên sản phẩm..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Danh mục --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold mb-1">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Tất cả danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Kích hoạt</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tắt</option>
                    </select>
                </div>

                {{-- Tình trạng --}}
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Tình trạng</label>
                    <select name="status" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Đã xóa</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>

                {{-- Nút lọc --}}
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label> {{-- giữ chỗ cho label --}}
                    <button type="submit" class="btn btn-primary w-100 filter-btn">
                        <i class="fa fa-search me-1"></i> Lọc
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


    {{-- Thông báo --}}
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
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $key=>$product)
                    <tr class="{{ $product->deleted_at ? 'table-secondary' : '' }}">
                        <td>{{  $key +1 }}</td>
                        <td>
                            {{ $product->name }}
                            @if($product->deleted_at)
                                <span class="badge bg-danger ms-2">Đã xóa</span>
                            @endif
                        </td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
<td>
    {{ number_format($product->variants_min_price) }}đ - {{ number_format($product->variants_max_price) }}đ
</td>
                        <td>
                            <input 
                                type="checkbox" 
                                class="toggle-status" 
                                data-id="{{ $product->id }}" 
                                {{ $product->is_active ? 'checked' : '' }}
                            >
                        </td>
                        <td>
                            @if($product->deleted_at)
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-success" onclick="restoreProduct({{ $product->id }})">
                                        <i class="fa fa-undo"></i> Khôi phục
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="forceDeleteProduct({{ $product->id }})">
                                        <i class="fa fa-trash-alt"></i> Xóa vĩnh viễn
                                    </button>
                                </div>
                            @else
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fa fa-eye"></i> Chi tiết
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning text-white">
                                        <i class="fa fa-edit"></i> Sửa
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct({{ $product->id }})">
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
    {{-- <div class="d-flex justify-content-between align-items-center">
        <div>
            Hiển thị {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
            trong tổng số {{ $products->total() }} sản phẩm
        </div>
        {{ $products->appends(request()->query())->links() }}
    </div> --}}

        <div class="d-flex justify-content-between align-items-center">
        <div>
            Hiển thị {{$products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
            trong tổng số {{ $products->total() }} sản phẩm
        </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $products->onEachSide(1)->links('pagination::bootstrap-4') }}
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
    .toggle-status {
    width: 40px;
    height: 20px;
    -webkit-appearance: none;
    appearance: none;
    background-color: #ccc;
    outline: none;
    border-radius: 20px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s;
}
.toggle-status:checked {
    background-color: #28a745;
}
.toggle-status::before {
    content: "";
    width: 18px;
    height: 18px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 1px;
    left: 1px;
    transition: transform 0.3s;
}
.toggle-status:checked::before {
    transform: translateX(20px);
}
.filter-btn {
    margin-top: 2px; /* chỉnh bao nhiêu px tùy thích */
}
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-status').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            let id = this.dataset.id;
            let status = this.checked ? 1 : 0;

            fetch(`/admin/products/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: status })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert('Lỗi: ' + (data.message || 'Không thể thay đổi trạng thái'));
                    this.checked = !this.checked; // rollback nếu lỗi
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi kết nối server');
                this.checked = !this.checked;
            });
        });
    });
});
</script>
@endsection

