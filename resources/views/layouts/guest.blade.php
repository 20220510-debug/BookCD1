<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Book Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.35), transparent 28%),
                linear-gradient(135deg, #0f2840 0%, #245c8f 45%, #d9b650 100%);
        }

        .auth-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 520px;
            border: 0;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(10, 23, 38, 0.22);
            background: rgba(255, 255, 255, 0.98);
        }

        .auth-top {
            padding: 24px 28px 18px;
            background: linear-gradient(135deg, #12304a 0%, #2f6fed 100%);
            color: #fff;
        }

        .auth-body {
            padding: 26px 28px 28px;
        }

        .form-control {
            border-radius: 14px;
            padding: 0.78rem 0.95rem;
        }

        .btn {
            border-radius: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card">
            @yield('content')
        </div>
    </div>
</body>
</html>
