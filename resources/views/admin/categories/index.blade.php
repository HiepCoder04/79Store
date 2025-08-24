@extends('admin.layouts.dashboard')

@section('title', 'Danh sách danh mục | 79Store')

@section('content')
<style>
    .table-container {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-top: 25px;
    }
    .table thead {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .table th, .table td {
        vertical-align: middle;
        padding: 12px 15px;
    }
    .table-hover tbody tr:hover {
        background-color: #f5f7fa;
        transition: all 0.2s ease;
    }
    .btn-add {
        border-radius: 8px;
        font-weight: 500;
    }
    .badge-parent {
        background-color: #0d6efd;
        color: #fff;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 999px;
    }
    .badge-none {
        background: #adb5bd;
        color: #fff;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
    }
    .action-btn {
        display: flex;
        gap: 6px;
        justify-content: center;
    }
    .alert {
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>

@if (session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
@endif

<div class="container table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-list-task me-2 text-primary"></i> Quản lý danh mục
        </h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-add">
            <i class="bi bi-plus-circle me-1"></i> Thêm danh mục
        </a>
    </div>

    {{-- Form tìm kiếm & lọc --}}
 <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 mb-3 ">
    {{-- Ô tìm kiếm --}}
    <div class="col-md-6">
        <label for="search" class="form-label fw-semibold">Tìm kiếm danh mục</label>
        <input type="text" name="search" id="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nhập tên danh mục...">
    </div>

    {{-- Dropdown danh mục cha --}}
    <div class="col-md-4 mt-3">
        <label for="parent_id" class="form-label fw-semibold">Lọc theo cấp</label>
        <select name="parent_id" id="parent_id" class="form-select">
            <option value="">-- Tất cả danh mục --</option>
            <option value="root" {{ request('parent_id') === 'root' ? 'selected' : '' }}>
                📁 Chỉ danh mục gốc
            </option>
            @foreach ($allParents as $parent)
                <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                    📂 Con của: {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Nút lọc --}}
    <div class="col-md-2 mt-5">
        <button type="submit" class="btn btn-success w-100 ">
            <i class="bi bi-funnel-fill "></i> Lọc
        </button>
    </div>
</form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px">ID</th>
                    <th class="text-start">Tên danh mục</th>
                    <th>Cấp độ</th>
                    <th>Danh mục cha</th>
                    <th style="width: 150px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                    <tr class="{{ $cat->parent_id ? 'table-light' : '' }}">
                        <td>{{ $cat->id }}</td>
                        <td class="text-start fw-medium">
                            @if($cat->parent_id)
                                <span class="text-muted me-2">└─</span>
                            @else
                                <span class="text-primary me-2">📁</span>
                            @endif
                            {{ $cat->name }}
                        </td>
                        <td>
                            @if($cat->parent_id)
                                <span class="badge bg-info">Cấp 2 (Con)</span>
                            @else
                                <span class="badge bg-primary">Cấp 1 (Gốc)</span>
                            @endif
                        </td>
                        <td>
                            @if($cat->parent)
                                <span class="badge-parent">{{ $cat->parent->name }}</span>
                            @else
                                <span class="badge-none">---</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btn">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-warning">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirmDelete('{{ $cat->name }}', {{ $cat->products_count ?? 0 }}, {{ $cat->children_count ?? 0 }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4"></i> <br> 
                            @if(request('search') || request('parent_id'))
                                Không tìm thấy danh mục nào phù hợp
                            @else
                                Chưa có danh mục nào
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>



        <div class="d-flex justify-content-between align-items-center">
        <div>
            Hiển thị {{$categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
            trong tổng số {{ $categories->total() }} danh mục
        </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $categories->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<script>
function confirmDelete(categoryName, productCount, childrenCount) {
    let message = `Bạn có chắc chắn muốn xóa danh mục "${categoryName}"?`;
    
    if (childrenCount > 0) {
        message += `\n\nCảnh báo: Danh mục này có ${childrenCount} danh mục con!`;
        message += `\nVui lòng xóa danh mục con trước.`;
        return false; // Không cho xóa
    }
    
    if (productCount > 0) {
        message += `\n\nCảnh báo: Danh mục này có ${productCount} sản phẩm!`;
        message += `\nVui lòng di chuyển sản phẩm sang danh mục khác trước.`;
        return false; // Không cho xóa
    }
    
    return confirm(message);
}
</script>
@endsection
