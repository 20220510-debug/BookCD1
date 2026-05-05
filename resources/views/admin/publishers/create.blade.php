@extends('layouts.app')

@section('content')
    <h1>Thêm nhà xuất bản</h1>

    <div class="section-card p-4">
        <form action="{{ route('admin.publishers.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tên nhà xuất bản</label>
                <input type="text" name="publisher_name" class="form-control" value="{{ old('publisher_name') }}">
                @error('publisher_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('admin.publishers.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection
