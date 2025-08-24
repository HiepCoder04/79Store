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
                {{-- Hiển thị lỗi validation cho variants --}}
                @error('variants')
                    <div class="alert alert-danger small mb-3">{{ $message }}</div>
                @enderror

                {{-- ITEM MẪU (index 0) --}}
                <div class="variant border rounded p-3 position-relative mb-3">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant position-absolute top-0 end-0 m-2" title="Xoá biến thể">
                        X
                    </button>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Chiều cao (cm)</label>
                            <input type="text" name="variants[0][height]" 
                                   value="{{ old('variants.0.height') }}"
                                   class="form-control @error('variants.0.height') is-invalid @enderror" 
                                   placeholder="VD: 20 / 30 / 40">
                            @error('variants.0.height')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="variants[0][price]" 
                                   value="{{ old('variants.0.price') }}"
                                   class="form-control @error('variants.0.price') is-invalid @enderror" 
                                   min="1000" step="1000" placeholder="VD: 120000">
                            @error('variants.0.price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                            <input type="number" name="variants[0][stock_quantity]" 
                                   value="{{ old('variants.0.stock_quantity') }}"
                                   class="form-control @error('variants.0.stock_quantity') is-invalid @enderror" 
                                   min="0" step="1" placeholder="VD: 50">
                            @error('variants.0.stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Hiển thị các variant đã thêm từ old() --}}
                @if(old('variants'))
                    @foreach(old('variants') as $index => $oldVariant)
                        @if($index > 0)
                            <div class="variant border rounded p-3 position-relative mb-3">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-variant position-absolute top-0 end-0 m-2" title="Xoá biến thể">X</button>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Chiều cao (cm)</label>
                                        <input type="text" name="variants[{{ $index }}][height]" 
                                               value="{{ $oldVariant['height'] ?? '' }}"
                                               class="form-control @error("variants.$index.height") is-invalid @enderror" 
                                               placeholder="VD: 20 / 30 / 40">
                                        @error("variants.$index.height")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[{{ $index }}][price]" 
                                               value="{{ $oldVariant['price'] ?? '' }}"
                                               class="form-control @error("variants.$index.price") is-invalid @enderror" 
                                               min="1000" step="1000" placeholder="VD: 120000">
                                        @error("variants.$index.price")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[{{ $index }}][stock_quantity]" 
                                               value="{{ $oldVariant['stock_quantity'] ?? '' }}"
                                               class="form-control @error("variants.$index.stock_quantity") is-invalid @enderror" 
                                               min="0" step="1" placeholder="VD: 50">
                                        @error("variants.$index.stock_quantity")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

            </div>
        </div>

        {{-- CHỌN CHẬU --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <span class="fw-semibold">Chọn chậu liên kết</span>
            </div>
            <div class="card-body">
                {{-- Hiển thị lỗi validation cho selected_pots --}}
                @error('selected_pots')
                    <div class="alert alert-danger small mb-3">{{ $message }}</div>
                @enderror
                @error('selected_pots.*')
                    <div class="alert alert-danger small mb-3">{{ $message }}</div>
                @enderror
                
                @if($pots->count())
                    <div class="row g-3">
                        @foreach($pots as $pot)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-check d-flex align-items-start shadow-sm border rounded p-3 h-100">
                                    <input class="form-check-input mt-1 me-2" type="checkbox" name="selected_pots[]" 
                                           value="{{ $pot->id }}" id="pot_{{ $pot->id }}"
                                           {{ in_array($pot->id, old('selected_pots', [])) ? 'checked' : '' }}>
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
                <span class="fw-semibold">Hình ảnh sản phẩm <span class="text-warning">*</span></span>
                <label class="btn btn-light btn-sm mb-0" for="imageInput">+ Thêm ảnh</label>
            </div>
            <div class="card-body">
                <input type="file" name="images[]" id="imageInput" class="d-none @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" multiple accept="image/*" required>
                <div id="imagePreview" class="row g-3"></div>
                <div id="imageError" class="text-danger small mt-1" style="display: none;">
                    Vui lòng chọn ít nhất 1 ảnh sản phẩm.
                </div>
                <div class="form-text mt-2">Chấp nhận JPEG/PNG/GIF, tối đa 5MB/ảnh, tối đa 10 ảnh. <span class="text-danger fw-bold">Bắt buộc có ít nhất 1 ảnh.</span></div>
                
                {{-- Hiển thị lỗi validation cho images --}}
                @error('images')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('images.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
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
    let variantIndex = {{ old('variants') ? count(old('variants')) : 1 }};

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
                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control" min="1000" step="1000" placeholder="VD: 120000" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                    <input type="number" name="variants[${variantIndex}][stock_quantity]" class="form-control" min="0" step="1" placeholder="VD: 50" required>
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

    // ===== Enhanced Image Preview with Validation =====
    document.getElementById('imageInput').addEventListener('change', function () {
        const preview = document.getElementById('imagePreview');
        const errorDiv = document.getElementById('imageError');
        const files = Array.from(this.files);
        
        // Reset
        preview.innerHTML = '';
        selectedImages = [];
        errorDiv.style.display = 'none';
        this.classList.remove('is-invalid');
        
        if (files.length === 0) {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Vui lòng chọn ít nhất 1 ảnh sản phẩm.';
            this.classList.add('is-invalid');
            return;
        }
        
        if (files.length > 10) {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Chỉ được chọn tối đa 10 ảnh.';
            this.classList.add('is-invalid');
            return;
        }
        
        files.forEach((file, index) => {
            if (!file.type.startsWith('image/')) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = `File "${file.name}" không phải là ảnh hợp lệ.`;
                this.classList.add('is-invalid');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = `Ảnh "${file.name}" vượt quá 5MB.`;
                this.classList.add('is-invalid');
                return;
            }
            
            selectedImages.push(file);
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                col.innerHTML = `
                    <div class="ratio ratio-1x1 border rounded overflow-hidden position-relative">
                        <img src="${e.target.result}" class="w-100 h-100 object-fit-cover" alt="preview">
                        <div class="position-absolute top-0 end-0 m-1">
                            <button type="button" class="btn btn-sm btn-danger rounded-circle remove-image" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white text-center py-1">
                            <small>${file.name}</small>
                        </div>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });

    // Remove image from preview
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-image') || e.target.closest('.remove-image')) {
            const button = e.target.closest('.remove-image');
            const index = parseInt(button.dataset.index);
            
            // Remove from selectedImages array
            selectedImages.splice(index, 1);
            
            // Remove preview element
            button.closest('.col-6').remove();
            
            // Update validation
            const imageInput = document.getElementById('imageInput');
            const errorDiv = document.getElementById('imageError');
            
            if (selectedImages.length === 0) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'Vui lòng chọn ít nhất 1 ảnh sản phẩm.';
                imageInput.classList.add('is-invalid');
            } else {
                errorDiv.style.display = 'none';
                imageInput.classList.remove('is-invalid');
            }
        }
    });

    // ===== Enhanced Client Validation =====
    document.getElementById('productForm').addEventListener('submit', function (e) {
        const name = this.querySelector('[name="name"]');
        const cate = this.querySelector('[name="category_id"]');
        const desc = this.querySelector('[name="description"]');
        const imageInput = this.querySelector('#imageInput');
        const errorDiv = document.getElementById('imageError');
        let ok = true;

        // Validate basic fields
        [name, cate, desc].forEach(i => {
            if(!i.value || (i.tagName==='SELECT' && !i.value)) {
                i.classList.add('is-invalid');
                ok = false;
            } else {
                i.classList.remove('is-invalid');
            }
        });

        // Validate images
        if (selectedImages.length === 0 && !imageInput.files.length) {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Vui lòng chọn ít nhất 1 ảnh sản phẩm.';
            imageInput.classList.add('is-invalid');
            ok = false;
        } else {
            errorDiv.style.display = 'none';
            imageInput.classList.remove('is-invalid');
        }

        // Validate variants
        const variants = this.querySelectorAll('.variant');
        variants.forEach((variant, index) => {
            const price = variant.querySelector(`[name="variants[${index}][price]"]`);
            const stock = variant.querySelector(`[name="variants[${index}][stock_quantity]"]`);
            
            if (price && (!price.value || price.value < 1000)) {
                price.classList.add('is-invalid');
                ok = false;
            } else if (price) {
                price.classList.remove('is-invalid');
            }
            
            if (stock && (!stock.value || stock.value < 0)) {
                stock.classList.add('is-invalid');
                ok = false;
            } else if (stock) {
                stock.classList.remove('is-invalid');
            }
        });

        if(!ok){
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
@endsection
