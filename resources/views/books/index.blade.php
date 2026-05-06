@extends('layouts.app')

@section('content')
    <div class="hero-panel mb-4">
        <h1 class="section-title">Danh sách sách</h1>
        <p class="section-subtitle">Tìm kiếm, lọc và sắp xếp sách theo nhu cầu của bạn.</p>
    </div>

    <div class="section-card p-4 mb-4">
        <form method="GET" action="{{ route('books.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Từ khóa</label>
                    <input type="text" name="keyword" class="form-control filter-control" placeholder="Nhập tên sách..." value="{{ request('keyword') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Thể loại</label>
                    <select name="category_id" class="form-select filter-control">
                        <option value="">Tất cả thể loại</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nhà xuất bản</label>
                    <select name="publisher_id" class="form-select filter-control">
                        <option value="">Tất cả nhà xuất bản</option>
                        @foreach($publishers as $publisher)
                            <option value="{{ $publisher->id }}" {{ request('publisher_id') == $publisher->id ? 'selected' : '' }}>
                                {{ $publisher->publisher_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tác giả</label>
                    <select name="author_id" class="form-select filter-control">
                        <option value="">Tất cả tác giả</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                {{ $author->author_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Giá từ</label>
                    <input type="number" name="price_min" class="form-control filter-control" placeholder="0" value="{{ request('price_min') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Giá đến</label>
                    <input type="number" name="price_max" class="form-control filter-control" placeholder="500000" value="{{ request('price_max') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sắp xếp</label>
                    <select name="sort" class="form-select filter-control">
                        <option value="">Mặc định</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                    </select>
                </div>

                <div class="col-md-5 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary px-4 filter-action">Tìm kiếm</button>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-dark px-4 filter-action">Làm mới</a>
                </div>
            </div>
        </form>
    </div>

    @if(request()->hasAny(['keyword', 'category_id', 'publisher_id', 'author_id', 'price_min', 'price_max', 'sort']))
        <div class="alert alert-info mb-4">
            Tìm thấy <strong>{{ $books->total() }}</strong> sách phù hợp.
        </div>
    @endif

    <div class="row g-4">
        @forelse($books as $book)
            @php
                $isOnline = $book->book_type === 'online';
                $isOutOfStock = !$isOnline && $book->stock <= 0;
            @endphp

            <div class="col-sm-6 col-lg-3">
                <div class="book-card">
                    <div class="position-relative">
                        <img src="{{ $book->image }}" alt="{{ $book->title }}" class="w-100">

                        <div class="position-absolute top-0 start-0 p-3 d-flex gap-2 flex-wrap">
                            <span class="book-badge {{ $isOnline ? 'book-badge-online' : 'book-badge-offline' }}">
                                {{ $isOnline ? 'Sách online' : 'Sách offline' }}
                            </span>

                            @if($isOutOfStock)
                                <span class="book-badge book-badge-danger">Hết hàng</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 p-lg-4 d-flex flex-column" style="min-height: 320px;">
                        <h5 class="fw-bold mb-2">{{ $book->title }}</h5>
                        <div class="book-meta mb-1"><strong>Thể loại:</strong> {{ $book->category->category_name }}</div>
                        <div class="book-meta mb-1"><strong>NXB:</strong> {{ $book->publisher->publisher_name }}</div>
                        <div class="book-meta mb-2">
                            <strong>Tác giả:</strong>
                            @forelse($book->authors as $author)
                                <span>{{ $author->author_name }}</span>{{ !$loop->last ? ', ' : '' }}
                            @empty
                                <span>Chưa cập nhật</span>
                            @endforelse
                        </div>

                        <div class="fs-5 fw-bold text-primary mb-3">{{ number_format($book->price) }} VNĐ</div>

                        @if(!$isOnline)
                            <div class="book-meta mb-3">
                                {{ $isOutOfStock ? 'Sách offline đã hết hàng' : 'Tồn kho: ' . $book->stock }}
                            </div>
                        @endif

                        <div class="mt-auto">
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-outline-dark w-100">
                                {{ $isOutOfStock ? 'Xem thông tin' : 'Xem chi tiết' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Không tìm thấy sách phù hợp.</div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $books->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
@endsection
