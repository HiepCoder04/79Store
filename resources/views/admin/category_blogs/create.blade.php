@extends('admin.layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="text-white text-capitalize ps-3">Thêm danh mục blog mới</h6>
            <a href="{{ route('admin.category_blogs.index') }}" class="btn btn-sm btn-light me-3">
              <i class="material-icons text-sm">Quay lại</i> 
            </a>
          </div>
        </div>
        <div class="card-body px-3 pb-2">
          <form action="{{ route('admin.category_blogs.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Tên danh mục</label>
              <input type="text" class="form-control border px-2 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="text-center">
              <button type="submit" class="btn bg-gradient-primary">Lưu danh mục</button>
              <a href="{{ route('admin.category_blogs.index') }}" class="btn bg-gradient-secondary">Hủy</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection