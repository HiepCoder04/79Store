@extends('admin.layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
            <h6 class="text-white text-capitalize ps-3">Danh sách danh mục blog</h6>
            <a href="{{ route('admin.category_blogs.create') }}" class="btn btn-sm btn-info me-3">
              <i class="material-icons text-sm">Thêm mới</i> 
            </a>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          @if(session('success'))
            <div class="alert alert-success mx-4">
              {{ session('success') }}
            </div>
          @endif
          
          @if(session('error'))
            <div class="alert alert-danger mx-4">
              {{ session('error') }}
            </div>
          @endif
          
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tên danh mục</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Số lượng bài viết</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                @foreach($categories as $index => $category)
                <tr>
                  <td>
                    <p class="text-xs font-weight-bold mb-0 ms-3">{{ $index + 1 }}</p>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $category->name }}</p>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">{{ $category->blogs_count }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <a href="{{ route('admin.category_blogs.edit', $category) }}" class="btn btn-sm bg-gradient-info text-white px-3 mb-0">
                      <i class="material-icons text-sm me-2">Sửa</i>
                    </a>
                    
                    <form action="{{ route('admin.category_blogs.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm bg-gradient-danger text-white px-3 mb-0">
                        <i class="material-icons text-sm me-2">Xóa</i>
                      </button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection