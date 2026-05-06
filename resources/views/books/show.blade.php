@extends('layouts.app')

@section('content')
    @php
        $isOnline = $book->book_type === 'online';
        $isOutOfStock = !$isOnline && $book->stock <= 0;
    @endphp

    <div class="section-card p-4 p-lg-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                @if($book->image)
                    <img src="{{ $book->image }}" class="img-fluid rounded-4 shadow-sm" alt="{{ $book->title }}">
                @endif
            </div>

            <div class="col-lg-8">
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span class="book-badge {{ $isOnline ? 'book-badge-online' : 'book-badge-offline' }}">
                        {{ $isOnline ? 'Sách online' : 'Sách offline' }}
                    </span>

                    @if($isOutOfStock)
                        <span class="book-badge book-badge-danger">Hết hàng</span>
                    @endif
                </div>

                <h1 class="fw-bold mb-3">{{ $book->title }}</h1>
                <div class="fs-4 fw-bold text-primary mb-4">{{ number_format($book->price) }} VNĐ</div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="soft-panel p-3 h-100">
                            <div class="mini-note">Thể loại</div>
                            <div class="fw-semibold">{{ $book->category->category_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="soft-panel p-3 h-100">
                            <div class="mini-note">Nhà xuất bản</div>
                            <div class="fw-semibold">{{ $book->publisher->publisher_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="soft-panel p-3 h-100">
                            <div class="mini-note">Tác giả</div>
                            <div class="fw-semibold">
                                @forelse($book->authors as $author)
                                    <span>{{ $author->author_name }}</span>{{ !$loop->last ? ', ' : '' }}
                                @empty
                                    <span>Chưa cập nhật</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="soft-panel p-3 h-100">
                            <div class="mini-note">{{ $isOnline ? 'Hình thức' : 'Tồn kho' }}</div>
                            <div class="fw-semibold">
                                @if($isOnline)
                                    Ebook PDF
                                @else
                                    {{ $isOutOfStock ? 'Đã hết hàng' : $book->stock . ' cuốn' }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="soft-panel p-4 mb-4">
                    <div class="fw-semibold mb-2">Mô tả sách</div>
                    <div class="text-muted">{{ $book->description ?: 'Chưa có mô tả.' }}</div>
                </div>

                @if($isOnline)
                    <div class="alert alert-info mb-4">
                        Sách online thanh toán bằng chuyển khoản. Sau khi xác nhận giao dịch thành công, file PDF sẽ mở trong kho sách của bạn.
                    </div>
                @else
                    <div class="alert {{ $isOutOfStock ? 'alert-danger' : 'alert-warning' }} mb-4">
                        @if($isOutOfStock)
                            Sách offline này tạm thời đã hết hàng.
                        @else
                            Sách offline hỗ trợ thanh toán khi nhận hàng và chuyển khoản trực tiếp.
                        @endif
                    </div>
                @endif

                @if(!$isOutOfStock)
                    <form action="{{ route('cart.add', $book->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg px-4">Thêm vào giỏ hàng</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
