@extends('layouts.app')

@section('content')
    <div class="hero-panel mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="section-title">Không gian sách cho cả bản in và ebook</div>
                <p class="section-subtitle">
                    Chọn sách online để đọc PDF sau khi giao dịch thành công, hoặc đặt sách offline để giao tận nơi.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <a href="{{ route('books.index') }}" class="btn btn-dark btn-lg px-4">Khám phá tất cả sách</a>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Sách mới cập nhật</h2>
            <p class="text-muted mb-0">Giao diện gọn gàng, dễ nhìn, tách rõ từng loại sách.</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($books as $book)
            @php
                $isOnline = $book->book_type === 'online';
                $isOutOfStock = !$isOnline && $book->stock <= 0;
            @endphp

            <div class="col-md-6 col-xl-3">
                <div class="book-card">
                    <div class="position-relative">
                        <img src="{{ $book->image }}" class="w-100" alt="{{ $book->title }}">

                        <div class="position-absolute top-0 start-0 p-3 d-flex gap-2 flex-wrap">
                            <span class="book-badge {{ $isOnline ? 'book-badge-online' : 'book-badge-offline' }}">
                                {{ $isOnline ? 'Sách online' : 'Sách offline' }}
                            </span>

                            @if($isOutOfStock)
                                <span class="book-badge book-badge-danger">Hết hàng</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 p-lg-4 d-flex flex-column" style="min-height: 250px;">
                        <h5 class="fw-bold mb-2">{{ $book->title }}</h5>
                        <div class="book-meta mb-2">{{ $book->category->category_name ?? '-' }}</div>
                        <div class="fs-5 fw-bold text-primary mb-3">{{ number_format($book->price) }} VNĐ</div>

                        @if(!$isOnline)
                            <div class="book-meta mb-3">
                                {{ $isOutOfStock ? 'Sách offline đã hết hàng' : 'Tồn kho: ' . $book->stock }}
                            </div>
                        @endif

                        <div class="mt-auto">
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-outline-dark w-100">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Chưa có sách nào trong hệ thống.</div>
            </div>
        @endforelse
    </div>
@endsection
