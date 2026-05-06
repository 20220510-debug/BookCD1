@extends('layouts.app')

@section('content')
    <h1>Sửa sách</h1>

    <div class="section-card p-4">
        <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Tên sách</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}">
                @error('title')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Loại sách</label>
                    <select name="book_type" class="form-control" id="book_type">
                        <option value="offline" {{ old('book_type', $book->book_type) === 'offline' ? 'selected' : '' }}>Sách offline</option>
                        <option value="online" {{ old('book_type', $book->book_type) === 'online' ? 'selected' : '' }}>Sách online</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Giá</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $book->price) }}">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4 mb-3" id="stock-wrapper">
                    <label class="form-label">Số lượng</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $book->stock) }}">
                    @error('stock')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Thể loại</label>
                <select name="category_id" class="form-control">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nhà xuất bản</label>
                <select name="publisher_id" class="form-control">
                    @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" {{ old('publisher_id', $book->publisher_id) == $publisher->id ? 'selected' : '' }}>
                            {{ $publisher->publisher_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tác giả</label>
                <textarea name="author_names" class="form-control" rows="5" placeholder="Mỗi dòng là một tác giả">{{ old('author_names', $book->authors->pluck('author_name')->implode("\n")) }}</textarea>
                <small class="text-muted">Mỗi dòng tương ứng với một tác giả.</small>
                @error('author_names')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Link ảnh</label>
                <input type="url" name="image_url" class="form-control" value="{{ old('image_url', filter_var($book->image, FILTER_VALIDATE_URL) ? $book->image : '') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Hoặc tải file ảnh mới</label>
                <input type="file" name="image_file" class="form-control" accept="image/*">
                @if($book->image)
                    <img src="{{ $book->image }}" alt="{{ $book->title }}" style="max-width: 120px;" class="mt-2">
                @endif
            </div>

            <div class="mb-3" id="ebook-wrapper">
                <label class="form-label">Tải file sách online (PDF)</label>
                <input type="file" name="ebook_upload" class="form-control" accept="application/pdf">
                @if($book->ebook_file)
                    <div class="mt-2">
                        <a href="{{ $book->ebook_file }}" target="_blank">Xem file PDF hiện tại</a>
                    </div>
                @endif
                @error('ebook_upload')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $book->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script>
        const bookType = document.getElementById('book_type');
        const ebookWrapper = document.getElementById('ebook-wrapper');
        const stockWrapper = document.getElementById('stock-wrapper');

        function toggleBookFields() {
            const isOnline = bookType.value === 'online';
            ebookWrapper.style.display = isOnline ? 'block' : 'none';
            stockWrapper.style.opacity = isOnline ? '0.5' : '1';
        }

        bookType.addEventListener('change', toggleBookFields);
        toggleBookFields();
    </script>
@endsection
