@extends('admin.layouts.dashboard')

@section('title', 'Sửa danh mục | 79Store')

@section('content')
<div class="card">
  <div class="card-header">
    <h4>Sửa danh mục</h4>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label">Tên danh mục</label>
        <input type="text" name="name" value="{{ $category->name }}" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Danh mục cha</label>
        <select name="parent_id" class="form-select">
          <option value="">-- Không chọn --</option>
          @foreach ($parents as $p)
          <option value="{{ $p->id }}" {{ $p->id == $category->parent_id ? 'selected' : '' }}>
            {{ $p->name }}
          </option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Cập nhật</button>
      <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
  </div>
</div>
@endsection
