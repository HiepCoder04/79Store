@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="text-white text-capitalize ps-3">Chỉnh sửa bài viết</h6>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-light me-3">
              <i class="material-icons text-sm">arrow_back</i> Quay lại
            </a>
          </div>
        </div>
        <div class="card-body px-3 pb-2">
          <form action="{{ route('admin.blogs.update', $blog) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label for="title" class="form-label">Tiêu đề</label>
              <input type="text" class="form-control border px-2 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $blog->title) }}">
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="category_blog_id" class="form-label">Danh mục</label>
              <select class="form-select border px-2 @error('category_blog_id') is-invalid @enderror" id="category_blog_id" name="category_blog_id">
                <option value="">Chọn danh mục</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ old('category_blog_id', $blog->category_blog_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
              @error('category_blog_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="content" class="form-label">Nội dung</label>
              <textarea class="form-control border px-2 @error('content') is-invalid @enderror" id="content" name="content" rows="6">{{ old('content', $blog->content) }}</textarea>
              @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="text-center">
              <button type="submit" class="btn bg-gradient-primary">Cập nhật bài viết</button>
              <a href="{{ route('admin.blogs.index') }}" class="btn bg-gradient-secondary">Hủy</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#content',
    height: 500,
    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    toolbar_mode: 'floating',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image'
  });
</script>
@endpush