@extends('layouts.guest')

@section('content')
    <div class="auth-top">
        <div class="small text-white-50 mb-2">Book Store</div>
        <h3 class="mb-1 fw-bold">Đăng nhập tài khoản</h3>
        <p class="mb-0 text-white-50">Tiếp tục mua sách, theo dõi đơn hàng và truy cập kho sách của bạn.</p>
    </div>

    <div class="auth-body">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if(!empty($savedAccounts))
            <div class="mb-4">
                <label class="form-label fw-semibold">Tài khoản đã lưu</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($savedAccounts as $account)
                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            onclick="document.getElementById('login-email').value='{{ $account['email'] }}';"
                        >
                            {{ $account['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input id="login-email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập trên thiết bị này</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Đăng nhập</button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Chưa có tài khoản?</span>
            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Đăng ký ngay</a>
        </div>
    </div>
@endsection
