@extends('layouts.guest')

@section('content')
    <div class="auth-top">
        <div class="small text-white-50 mb-2">Book Store</div>
        <h3 class="mb-1 fw-bold">Tạo tài khoản mới</h3>
        <p class="mb-0 text-white-50">Đăng ký để mua sách, lưu lịch sử đơn hàng và truy cập kho sách online.</p>
    </div>

    <div class="auth-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2">Đăng ký</button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Đã có tài khoản?</span>
            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Đăng nhập</a>
        </div>
    </div>
@endsection
