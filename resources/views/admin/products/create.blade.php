@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0 fw-bold">Thêm sản phẩm mới</h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            ← Quay lại danh sách
        </a>
    </div>

    {{-- ALERTS --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
        @csrf

        {{-- THÔNG TIN CƠ BẢN --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Thông tin cơ bản</span>
                    <span class="small opacity-75">Điền đầy đủ các trường bắt buộc *</span>
                </div>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" placeholder="VD: Tên sản phẩm">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Mô tả ngắn gọn về sản phẩm, chất liệu, cách chăm...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- BIẾN THỂ --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Biến thể theo chiều cao</span>
                <button type="button" id="addVariant" class="btn btn-light btn-sm">
                    + Thêm biến thể
                </button>
            </div>
            <div class="card-body" id="variants">

                {{-- ITEM MẪU (index 0) --}}
                <div class="variant border rounded p-3 position-relative mb-3">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant position-absolute top-0 end-0 m-2" title="Xoá biến thể">
                        X
                    </button>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Chiều cao (cm)</label>
                            <input type="text" name="variants[0][height]" class="form-control" placeholder="VD: 20 / 30 / 40">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input type="number" name="variants[0][price]" class="form-control" min="0" step="1000" placeholder="VD: 120000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số lượng tồn</label>
                            <input type="number" name="variants[0][stock_quantity]" class="form-control" min="0" step="1" placeholder="VD: 50">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- CHỌN CHẬU --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <span class="fw-semibold">Chọn chậu liên kết</span>
            </div>
            <div class="card-body">
                @if($pots->count())
                    <div class="row g-3">
                        @foreach($pots as $pot)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-check d-flex align-items-start shadow-sm border rounded p-3 h-100">
                                    <input class="form-check-input mt-1 me-2" type="checkbox" name="selected_pots[]" value="{{ $pot->id }}" id="pot_{{ $pot->id }}">
                                    <label class="form-check-label w-100" for="pot_{{ $pot->id }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="fw-semibold">{{ $pot->name }}</div>
                                            @if(is_numeric($pot->price))
                                                <span class="badge text-bg-light border">
                                                    {{ number_format($pot->price, 0, ',', '.') }}Đ
                                                </span>
                                            @endif
                                        </div>
                                        @if(!empty($pot->description))
                                            <div class="text-muted small mt-1">{!! Str::limit(strip_tags($pot->description), 120) !!}</div>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">Chưa có chậu nào trong hệ thống.</div>
                @endif
            </div>
        </div>

        {{-- ẢNH SẢN PHẨM --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Hình ảnh sản phẩm</span>
                <label class="btn btn-light btn-sm mb-0" for="imageInput">+ Thêm ảnh</label>
            </div>
            <div class="card-body">
                <input type="file" name="images[]" id="imageInput" class="d-none" multiple accept="image/*">
                <div id="imagePreview" class="row g-3"></div>
                <div class="form-text mt-2">Chấp nhận JPEG/PNG/GIF, tối đa 5MB/ảnh.</div>
                @error('images.*') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="card shadow-sm sticky-bottom z-1">
            <div class="card-body d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                <button class="btn btn-success">Lưu sản phẩm</button>
            </div>
        </div>
    </form>
</div>

{{-- CUSTOM CSS (nhẹ) --}}
<style>
    .sticky-bottom{ position: sticky; bottom: 0; background: #fff; }
    .variant:hover{ box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,.06); }
</style>

{{-- SCRIPTS --}}
<script>
    // ===== Variants Add/Remove =====
    let variantIndex = 1;
    document.getElementById('addVariant').addEventListener('click', function () {
        const wrap = document.getElementById('variants');
        const el = document.createElement('div');
        el.className = 'variant border rounded p-3 position-relative mb-3';
        el.innerHTML = `
            <button type="button" class="btn btn-outline-danger btn-sm remove-variant position-absolute top-0 end-0 m-2" title="Xoá biến thể">X</button>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Chiều cao (cm)</label>
                    <input type="text" name="variants[${variantIndex}][height]" class="form-control" placeholder="VD: 20 / 30 / 40">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Giá (VNĐ)</label>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control" min="0" step="1000" placeholder="VD: 120000">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Số lượng tồn</label>
                    <input type="number" name="variants[${variantIndex}][stock_quantity]" class="form-control" min="0" step="1" placeholder="VD: 50">
                </div>
            </div>
        `;
        wrap.appendChild(el);
        variantIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant').remove();
        }
    });

    // ===== Image Preview =====
    document.getElementById('imageInput').addEventListener('change', function () {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        const files = Array.from(this.files);
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                col.innerHTML = `
                    <div class="ratio ratio-1x1 border rounded overflow-hidden">
                        <img src="${e.target.result}" class="w-100 h-100 object-fit-cover" alt="preview">
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });

    // ===== Basic Client Validation (optional) =====
    document.getElementById('productForm').addEventListener('submit', function (e) {
        // kiểm tra tên, danh mục, mô tả
        const name = this.querySelector('[name="name"]');
        const cate = this.querySelector('[name="category_id"]');
        const desc = this.querySelector('[name="description"]');
        let ok = true;

        [name, cate, desc].forEach(i => {
            if(!i.value || (i.tagName==='SELECT' && !i.value)) {
                i.classList.add('is-invalid');
                ok = false;
            } else {
                i.classList.remove('is-invalid');
            }
        });

        if(!ok){
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
@endsection
