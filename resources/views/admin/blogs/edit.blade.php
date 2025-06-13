@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="text-white text-capitalize ps-3">Chỉnh sửa danh mục blog</h6>
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-sm btn-light me-3">
              <i class="material-icons text-sm">arrow_back</i> Quay lại
            </a>
          </div>
        </div>
        <div class="card-body px-3 pb-2">
          <form action="{{ route('admin.blog-categories.update', $blogCategory) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label for="name" class="form-label">Tên danh mục</label>
              <input type="text" class="form-control border px-2 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $blogCategory->name) }}">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Mô tả</label>
              <textarea class="form-control border px-2 @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $blogCategory->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="parent_id" class="form-label">Danh mục cha</label>
              <select class="form-select border px-2" id="parent_id" name="parent_id">
                <option value="0">Không có danh mục cha</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ old('parent_id', $blogCategory->parent_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="text-center">
              <button type="submit" class="btn bg-gradient-primary">Cập nhật danh mục</button>
              <a href="{{ route('admin.blog-categories.index') }}" class="btn bg-gradient-secondary">Hủy</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection