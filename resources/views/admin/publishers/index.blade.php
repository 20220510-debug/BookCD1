@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Quản lý nhà xuất bản</h1>
        <a href="{{ route('admin.publishers.create') }}" class="btn btn-primary">+ Thêm nhà xuất bản</a>
    </div>

    <div class="section-card p-4">
        <div class="table-responsive">
            <table class="table table-modern table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên nhà xuất bản</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th width="160">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($publishers as $publisher)
                        <tr>
                            <td>{{ $publisher->id }}</td>
                            <td class="fw-semibold">{{ $publisher->publisher_name }}</td>
                            <td>{{ $publisher->address ?: '-' }}</td>
                            <td>{{ $publisher->phone ?: '-' }}</td>
                            <td>
                                <a href="{{ route('admin.publishers.edit', $publisher->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                                <form action="{{ route('admin.publishers.destroy', $publisher->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa nhà xuất bản này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Chưa có nhà xuất bản nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $publishers->links() }}
    </div>
@endsection
