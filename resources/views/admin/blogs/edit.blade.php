@extends('admin.layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="text-white text-capitalize ps-3">Chỉnh sửa bài viết</h6>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-light me-3">
              <i class="material-icons text-sm">Quay lại</i> 
            </a>
          </div>
        </div>
        <div class="card-body px-3 pb-2">
          @if($errors->any())
            <div class="alert alert-danger text-white">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          
          <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="col-md-8">
                <div class="input-group input-group-static mb-4">
                  <label>Tiêu đề</label>
                  <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
                </div>
                
                <div class="input-group input-group-static mb-4">
                  <label for="category_id">Danh mục</label>
                  <select name="category_id" id="category_id" class="form-control">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $category)
                      <option value="{{ $category->id }}" {{ (old('category_id', $blog->category_blog_id) == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="input-group input-group-static mb-4">
                  <label for="content">Nội dung</label>
                  <textarea name="content" id="content" rows="10" class="form-control">{{ old('content', $blog->content) }}</textarea>
                </div>
                
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ (old('is_active', $blog->is_active) ? 'checked' : '') }}>
                  <label class="form-check-label" for="is_active">Kích hoạt</label>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header p-3">
                    <h5 class="mb-0">Hình ảnh</h5>
                  </div>
                  <div class="card-body p-3">
                    <div class="form-group">
                      <label for="img" class="form-label">Chọn hình ảnh</label>
                      <input type="file" name="img" id="img" class="form-control border" accept="image/*">
                    </div>
                    
                    <div id="imagePreview" class="mt-3 text-center" style="{{ $blog->img ? '' : 'display: none;' }}">
                      <img id="preview" src="{{ $blog->img ? asset($blog->img) : '#' }}" alt="Preview" style="max-width: 100%; max-height: 200px;">
                    </div>
                    
                    @if($blog->img)
                      <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">
                          Xóa hình ảnh hiện tại
                        </label>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
              <a href="{{ route('admin.blogs.index') }}" class="btn btn-light ms-3">Hủy</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.getElementById('img').onchange = function(evt) {
    const [file] = this.files;
    if (file) {
      document.getElementById('preview').src = URL.createObjectURL(file);
      document.getElementById('imagePreview').style.display = 'block';
    }
  };

  // Thêm WYSIWYG Editor nếu có thể
  if(typeof ClassicEditor !== 'undefined') {
    ClassicEditor
      .create(document.querySelector('#content'))
      .catch(error => {
        console.error(error);
      });
  }
</script>
@endpush
@endsection