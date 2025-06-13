@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="text-white text-capitalize ps-3">Danh sách bài viết</h6>
            <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-light me-3">
              <i class="material-icons text-sm">add</i> Thêm mới
            </a>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          @if(session('success'))
            <div class="alert alert-success mx-3">
              {{ session('success') }}
            </div>
          @endif
          
          @if(session('error'))
            <div class="alert alert-danger mx-3">
              {{ session('error') }}
            </div>
          @endif
          
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tiêu đề</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Danh mục</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày tạo</th>
                  <th class="text-secondary opacity-7">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                @foreach($blogs as $blog)
                <tr>
                  <td class="ps-4">{{ $blog->id }}</td>
                  <td>{{ $blog->title }}</td>
                  <td>{{ optional($blog->category)->name ?? 'Không có danh mục' }}</td>
                  <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                  <td class="align-middle">
                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit blog">
                      Sửa
                    </a>
                    <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                        Xóa
                      </button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @if(method_exists($blogs, 'links'))
            <div class="px-3 mt-3">
              {{ $blogs->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection