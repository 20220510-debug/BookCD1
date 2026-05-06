@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Quản lý sách</h1>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">+ Thêm sách</a>
    </div>

    <div class="section-card p-4">
        <div class="table-responsive">
            <table class="table table-modern table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sách</th>
                        <th>Loại</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>PDF</th>
                        <th>Thể loại</th>
                        <th>Tác giả</th>
                        <th>Nhà xuất bản</th>
                        <th>Mô tả</th>
                        <th width="160">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>{{ $book->id }}</td>
                            <td>
                                @if($book->image)
                                    <img src="{{ $book->image }}" alt="{{ $book->title }}" width="60">
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $book->title }}</td>
                            <td>
                                <span class="badge {{ $book->book_type === 'online' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $book->book_type === 'online' ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                            <td>{{ number_format($book->price) }} VNĐ</td>
                            <td>{{ $book->stock }}</td>
                            <td>
                                @if($book->ebook_file)
                                    <a href="{{ $book->ebook_file }}" target="_blank">Có file</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $book->category->category_name }}</td>
                            <td>
                                @forelse($book->authors as $author)
                                    <span class="badge bg-light text-dark border">{{ $author->author_name }}</span>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>{{ $book->publisher->publisher_name }}</td>
                            <td style="max-width: 260px;">
                                <span class="d-inline-block text-truncate" style="max-width: 240px;" title="{{ $book->description }}">
                                    {{ $book->description ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.books.edit', $book->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                                <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa sách này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">Chưa có sách nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $books->links() }}
    </div>
@endsection
