@extends('admin.layouts.dashboard')

@section('title', 'Quản lý đánh giá sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách đánh giá</h5>
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterBox"
                aria-expanded="{{ request()->hasAny(['search','rating','user_id','product_id']) ? 'true' : 'false' }}">
                <i class="fa fa-filter me-1"></i> Bộ lọc
            </button>
        </div>

        <!-- Filter collapse -->
        <!-- Filter collapse -->
<div class="collapse {{ request()->hasAny(['search','rating','user_name','product_name']) ? 'show' : '' }}" id="filterBox">
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="form-control" placeholder="Tìm theo bình luận...">
            </div>

            <div class="col-md-2">
                <select name="rating" class="form-select">
                    <option value="">-- Chọn số sao --</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} sao
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2">
                <input type="text" name="user_name" value="{{ request('user_name') }}" 
                    class="form-control" placeholder="Tên người dùng">
            </div>

            <div class="col-md-2">
                <input type="text" name="product_name" value="{{ request('product_name') }}" 
                    class="form-control" placeholder="Tên sản phẩm">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search me-1"></i> Lọc
                </button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-times me-1"></i> Xóa lọc
                </a>
            </div>
        </form>
    </div>
</div>


        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Đánh giá</th>
                            <th>Bình luận</th>
                            <th>Ảnh</th>
                            <th>Phản hồi</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $key=>$review)
                            <tr>
                                <td>{{  $key +1}}</td>
                                <td>{{ $review->user->name ?? 'Ẩn danh' }}</td>
                                <td>{{ $review->product->name ?? 'Sản phẩm đã xoá' }}</td>
                                <td>
                                    <div class="star-rating" data-rating="{{ $review->rating }}"></div>
                                </td>
                                <td style="max-width: 200px">{{ $review->comment }}</td>
                                <td>
                                    @if($review->image_path)
                                        <img src="{{ asset('storage/' . $review->image_path) }}" 
                                             alt="Ảnh đánh giá" width="80" height="80" class="rounded border">
                                    @else
                                        <span class="text-muted small">Không có</span>
                                    @endif
                                </td>
                                <td>
                                    @if($review->admin_reply)
                                        <span class="text-success">{{ $review->admin_reply }}</span>
                                    @else
                                        <span class="text-muted">Chưa phản hồi</span>
                                    @endif
                                </td>
                                <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <!-- Phản hồi -->
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#replyModal{{ $review->id }}">
                                        Phản hồi
                                    </button>
                                    <!-- Xoá -->
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn chắc chắn muốn xoá đánh giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Xoá
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal phản hồi -->
                            <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Phản hồi đánh giá #{{ $review->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                        <textarea name="admin_reply" class="form-control" rows="3"
                                                    placeholder="Nhập phản hồi...">{{ $review->admin_reply }}</textarea>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary">Lưu</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Chưa có đánh giá nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            {{-- <div class="mt-3">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div> --}}

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Hiển thị {{ $reviews->firstItem() ?? 0 }} - {{ $reviews->lastItem() ?? 0 }} 
                    trong tổng số {{ $reviews->total() }} đánh giá
                </div>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $reviews->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const starContainers = document.querySelectorAll(".star-rating");
    starContainers.forEach(container => {
        const rating = parseInt(container.dataset.rating) || 0;
        container.innerHTML = "";
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement("i");
            if (i <= rating) {
                star.className = "fas fa-star text-warning"; // ⭐
            } else {
                star.className = "far fa-star text-muted";  // ☆
            }
            star.style.marginRight = "2px";
            container.appendChild(star);
        }
    });
});
</script>

<style>
#filterBox {
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    padding: 15px;
    border-radius: 0 0 10px 10px;
}
</style>
@endsection
