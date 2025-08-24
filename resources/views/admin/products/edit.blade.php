@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0 fw-bold">Chỉnh sửa sản phẩm</h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            ← Quay lại danh sách
        </a>
    </div>

    {{-- FORM --}}
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
        @csrf
        @method('PUT')

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
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror" placeholder="VD: Trầu bà thanh xuân" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Mô tả ngắn gọn về sản phẩm, chất liệu, cách chăm..." required>{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- BIẾN THỂ --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Biến thể theo chiều cao</span>
                <button type="button" id="addVariant" class="btn btn-light btn-sm">+ Thêm biến thể</button>
            </div>
            <div class="card-body" id="variants">
                {{-- Hiển thị lỗi validation cho variants --}}
                @error('variants')
                    <div class="alert alert-danger small mb-3">{{ $message }}</div>
                @enderror

                {{-- LIST VARIANTS HIỆN CÓ --}}
                @php 
                    $vIndex = 0; 
                    $oldVariants = old('variants', []);
                @endphp
                @foreach ($product->variants as $variant)
                    <div class="variant border rounded p-3 position-relative mb-3">
                        {{-- hidden id để update --}}
                        <input type="hidden" name="variants[{{ $vIndex }}][id]" value="{{ old("variants.$vIndex.id", $variant->id) }}">

                        {{-- Nút xoá biến thể --}}
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-2">
                            <a href="{{ route('admin.products.variants.deleteVariant', $variant->id) }}"
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Bạn có chắc muốn xoá biến thể này?');"
                               title="Xoá biến thể">X</a>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Chiều cao (cm)</label>
                                <input type="text" name="variants[{{ $vIndex }}][height]" 
                                       value="{{ old("variants.$vIndex.height", $variant->height) }}" 
                                       class="form-control @error("variants.$vIndex.height") is-invalid @enderror" 
                                       placeholder="VD: 20 / 30 / 40">
                                @error("variants.$vIndex.height")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="variants[{{ $vIndex }}][price]" 
                                       value="{{ old("variants.$vIndex.price", $variant->price) }}" 
                                       class="form-control @error("variants.$vIndex.price") is-invalid @enderror" 
                                       min="1000" step="1000" placeholder="VD: 120000">
                                @error("variants.$vIndex.price")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                                <input type="number" name="variants[{{ $vIndex }}][stock_quantity]" 
                                       value="{{ old("variants.$vIndex.stock_quantity", $variant->stock_quantity) }}" 
                                       class="form-control @error("variants.$vIndex.stock_quantity") is-invalid @enderror" 
                                       min="0" step="1" placeholder="VD: 50">
                                @error("variants.$vIndex.stock_quantity")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @php $vIndex++; @endphp
                @endforeach

                {{-- Thêm các variant mới từ old() nếu có lỗi validation --}}
                @if(count($oldVariants) > $product->variants->count())
                    @for($i = $product->variants->count(); $i < count($oldVariants); $i++)
                        <div class="variant border rounded p-3 position-relative mb-3">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-variant position-absolute top-0 end-0 m-2" title="Xoá biến thể">X</button>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Chiều cao (cm)</label>
                                    <input type="text" name="variants[{{ $i }}][height]" 
                                           value="{{ old("variants.$i.height") }}" 
                                           class="form-control @error("variants.$i.height") is-invalid @enderror" 
                                           placeholder="VD: 20 / 30 / 40">
                                    @error("variants.$i.height")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="variants[{{ $i }}][price]" 
                                           value="{{ old("variants.$i.price") }}" 
                                           class="form-control @error("variants.$i.price") is-invalid @enderror" 
                                           min="1000" step="1000" placeholder="VD: 120000">
                                    @error("variants.$i.price")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Số lượng tồn <span class="text-danger">*</span></label>
                                    <input type="number" name="variants[{{ $i }}][stock_quantity]" 
                                           value="{{ old("variants.$i.stock_quantity") }}" 
                                           class="form-control @error("variants.$i.stock_quantity") is-invalid @enderror" 
                                           min="0" step="1" placeholder="VD: 50">
                                    @error("variants.$i.stock_quantity")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor
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
                            @php
                                // Ưu tiên old() trước, nếu không có thì dùng dữ liệu từ database
                                $isChecked = old('selected_pots') 
                                    ? in_array($pot->id, old('selected_pots', [])) 
                                    : $selectedPotIds->contains($pot->id);
                            @endphp
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-check d-flex align-items-start shadow-sm border rounded p-3 h-100">
                                    <input class="form-check-input mt-1 me-2" type="checkbox" name="selected_pots[]" 
                                           value="{{ $pot->id }}" id="pot_{{ $pot->id }}" 
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="pot_{{ $pot->id }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="fw-semibold">{{ $pot->name }}</div>
                                            @if(is_numeric($pot->price))
                                                <span class="badge text-bg-light border">{{ number_format($pot->price, 0, ',', '.') }}Đ</span>
                                            @endif
                                        </div>
                                        @if(!empty($pot->description))
                                            <div class="text-muted small mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($pot->description), 120) }}</div>
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
                <span class="fw-semibold">Hình ảnh sản phẩm @if(!$product->galleries->count()) <span class="text-warning">*</span> @endif</span>
                <label class="btn btn-light btn-sm mb-0" for="imageInput">+ Thêm ảnh</label>
            </div>
            <div class="card-body">
                {{-- ảnh hiện có + checkbox xoá --}}
                @if($product->galleries->count())
                    <div class="row g-3 mb-3" id="existingImages">
                        @foreach($product->galleries as $gallery)
                            <div class="col-6 col-md-4 col-lg-3 existing-image">
                                <div class="ratio ratio-1x1 border rounded overflow-hidden mb-2">
                                    <img src="{{ asset($gallery->image) }}" class="w-100 h-100 object-fit-cover" alt="gallery">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input delete-image-checkbox" type="checkbox" name="delete_images[]" value="{{ $gallery->id }}" id="delete_{{ $gallery->id }}">
                                    <label class="form-check-label" for="delete_{{ $gallery->id }}">Xoá ảnh này</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- upload ảnh mới + preview --}}
                <input type="file" name="images[]" id="imageInput" class="d-none @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" multiple accept="image/*">
                <div id="imagePreview" class="row g-3"></div>
                <div id="imageError" class="text-danger small mt-1" style="display: none;"></div>
                <div class="form-text mt-2">
                    Chấp nhận JPEG/PNG/GIF, tối đa 5MB/ảnh, tối đa 10 ảnh. 
                    @if(!$product->galleries->count())
                        <span class="text-danger fw-bold">Sản phẩm phải có ít nhất 1 ảnh.</span>
                    @endif
                </div>
                
                {{-- Hiển thị lỗi validation cho images --}}
                @error('images')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('images.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('delete_images')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('delete_images.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="card shadow-sm sticky-bottom z-1">
            <div class="card-body d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                <button class="btn btn-success">Cập nhật</button>
            </div>
        </div>
    </form>
</div>

{{-- CUSTOM CSS --}}
<style>
    .sticky-bottom{ position: sticky; bottom: 0; background: #fff; }
    .variant:hover{ box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,.06); }
</style>

{{-- SCRIPTS --}}
<script>
    // ===== Variants Add/Remove =====
    let variantIndex = {{ count($oldVariants) > 0 ? count($oldVariants) : $vIndex ?? 0 }};
    let selectedImages = [];

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

    // Xoá biến thể vừa thêm (client-side)
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant').remove();
        }
    });

    // ===== Image Preview =====
    document.getElementById('imageInput').addEventListener('change', function () {
        const preview = document.getElementById('imagePreview');
        const errorDiv = document.getElementById('imageError');
        const files = Array.from(this.files);
        
        // Reset
        preview.innerHTML = '';
        selectedImages = [];
        errorDiv.style.display = 'none';
        this.classList.remove('is-invalid');
        
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
                            <button type="button" class="btn btn-sm btn-danger rounded-circle remove-new-image" data-index="${index}">
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
        
        validateImages();
    });

    // Remove new image from preview
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-new-image') || e.target.closest('.remove-new-image')) {
            const button = e.target.closest('.remove-new-image');
            button.closest('.col-6').remove();
            validateImages();
        }
    });

    // Monitor delete checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('delete-image-checkbox')) {
            validateImages();
        }
    });

    function validateImages() {
        const existingImages = document.querySelectorAll('.existing-image:not(:has(.delete-image-checkbox:checked))').length;
        const newImages = document.querySelectorAll('#imagePreview .col-6').length;
        const errorDiv = document.getElementById('imageError');
        const imageInput = document.getElementById('imageInput');
        
        if (existingImages + newImages === 0) {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Sản phẩm phải có ít nhất 1 ảnh.';
            imageInput.classList.add('is-invalid');
        } else {
            errorDiv.style.display = 'none';
            imageInput.classList.remove('is-invalid');
        }
    }

    // ===== Enhanced Client Validation =====
    document.getElementById('productForm').addEventListener('submit', function (e) {
        const name = this.querySelector('[name="name"]');
        const cate = this.querySelector('[name="category_id"]');
        const desc = this.querySelector('[name="description"]');
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
        const existingImages = document.querySelectorAll('.existing-image:not(:has(.delete-image-checkbox:checked))').length;
        const newImages = document.querySelectorAll('#imagePreview .col-6').length;
        
        if (existingImages + newImages === 0) {
            const errorDiv = document.getElementById('imageError');
            const imageInput = document.getElementById('imageInput');
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Sản phẩm phải có ít nhất 1 ảnh.';
            imageInput.classList.add('is-invalid');
            ok = false;
        }

        // Validate variants with dynamic indices
        const variants = this.querySelectorAll('.variant');
        variants.forEach((variant) => {
            const priceInput = variant.querySelector('input[name*="[price]"]');
            const stockInput = variant.querySelector('input[name*="[stock_quantity]"]');
            
            if (priceInput && (!priceInput.value || priceInput.value < 1000)) {
                priceInput.classList.add('is-invalid');
                ok = false;
            } else if (priceInput) {
                priceInput.classList.remove('is-invalid');
            }
            
            if (stockInput && (!stockInput.value || stockInput.value < 0)) {
                stockInput.classList.add('is-invalid');
                ok = false;
            } else if (stockInput) {
                stockInput.classList.remove('is-invalid');
            }
        });

        if(!ok){
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
@endsection
