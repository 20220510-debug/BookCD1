@extends('layouts.app')

@section('content')
    <div class="hero-panel mb-4">
        <h1 class="section-title">Bảng điều khiển quản trị</h1>
        <p class="section-subtitle">Theo dõi tổng quan doanh thu, đơn hàng và những đầu sách đang hoạt động.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card">
                <div class="dashboard-label">Tổng đầu sách</div>
                <p class="dashboard-value">{{ $bookCount }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card">
                <div class="dashboard-label">Tổng đơn hàng</div>
                <p class="dashboard-value">{{ $orderCount }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card">
                <div class="dashboard-label">Người dùng</div>
                <p class="dashboard-value">{{ $userCount }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card">
                <div class="dashboard-label">Tổng doanh thu</div>
                <p class="dashboard-value">{{ number_format($revenue ?? 0) }}</p>
                <div class="mini-note">VNĐ</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap mb-4">
        <a href="{{ route('admin.books.index') }}" class="btn btn-primary">Quản lý sách</a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark">Quản lý thể loại</a>
        <a href="{{ route('admin.publishers.index') }}" class="btn btn-outline-dark">Quản lý nhà xuất bản</a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-dark">Quản lý đơn hàng</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="section-card p-4 h-100">
                <h4 class="fw-bold mb-3">Doanh thu theo tháng</h4>
                <div class="table-responsive">
                    <table class="table table-modern table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Tháng/Năm</th>
                                <th>Online</th>
                                <th>Offline</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyRevenue as $row)
                                <tr>
                                    <td>{{ sprintf('%02d/%d', $row->month, $row->year) }}</td>
                                    <td>{{ number_format($row->online_revenue) }}</td>
                                    <td>{{ number_format($row->offline_revenue) }}</td>
                                    <td class="fw-semibold">{{ number_format($row->online_revenue + $row->offline_revenue) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="section-card p-4 h-100">
                <h4 class="fw-bold mb-3">Doanh thu theo năm</h4>
                <div class="table-responsive">
                    <table class="table table-modern table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Năm</th>
                                <th>Online</th>
                                <th>Offline</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($yearlyRevenue as $row)
                                <tr>
                                    <td>{{ $row->year }}</td>
                                    <td>{{ number_format($row->online_revenue) }}</td>
                                    <td>{{ number_format($row->offline_revenue) }}</td>
                                    <td class="fw-semibold">{{ number_format($row->online_revenue + $row->offline_revenue) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="section-card p-4">
        <h4 class="fw-bold mb-3">Sách bán chạy nhất</h4>
        <div class="table-responsive">
            <table class="table table-modern table-sm mb-0">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Loại</th>
                        <th>Số lượng đã bán</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bestsellers as $book)
                        <tr>
                            <td class="fw-semibold">{{ $book->title }}</td>
                            <td>
                                <span class="book-badge {{ $book->book_type === 'online' ? 'book-badge-online' : 'book-badge-offline' }}">
                                    {{ strtoupper($book->book_type) }}
                                </span>
                            </td>
                            <td>{{ $book->total_sold }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

#chinh-qc