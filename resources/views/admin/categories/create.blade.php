@extends('admin.layouts.dashboard')

@section('title', 'Thêm danh mục | 79Store')

@section('content')
<div class="card-body">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh mục cha</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- Không chọn --</option>
                        @foreach ($parents as $p)
                            <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <button type="submit" class="btn btn-success">Lưu</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
