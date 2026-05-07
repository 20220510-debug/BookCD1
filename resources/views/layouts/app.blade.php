<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-dark: #14304a;
            --brand-blue: #2f6fed;
            --brand-gold: #f4c95d;
            --surface: #ffffff;
            --surface-soft: #f8fbff;
            --line: #dde7f3;
            --text-main: #1f2a37;
            --text-muted: #5b6b7f;
            --success-soft: #e9f9ee;
            --danger-soft: #fdeeee;
            --warning-soft: #fff7e6;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(47, 111, 237, 0.12), transparent 28%),
                linear-gradient(180deg, #f5f8fc 0%, #eef3f9 100%);
            color: var(--text-main);
            min-height: 100vh;
        }

        .site-nav {
            background: linear-gradient(135deg, #10273d 0%, #1e466b 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .site-brand {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .nav-pill {
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            padding: 0.48rem 0.95rem;
            font-size: 0.92rem;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .nav-pill:hover {
            background: linear-gradient(135deg, rgba(244, 201, 93, 0.9), rgba(255, 255, 255, 0.25));
            border-color: rgba(244, 201, 93, 0.95);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(244, 201, 93, 0.18);
        }

        .nav-pill:focus-visible {
            outline: 2px solid rgba(244, 201, 93, 0.85);
            outline-offset: 2px;
            color: #fff;
        }

        .nav-pill-active {
            background: #fff;
            color: var(--brand-dark);
            border-color: #fff;
            box-shadow: 0 10px 22px rgba(255, 255, 255, 0.18);
        }

        .nav-pill-active:hover {
            background: #fff;
            color: var(--brand-dark);
            transform: none;
        }

        .page-shell {
            padding: 32px 0 48px;
        }

        .hero-panel,
        .section-card,
        .info-card,
        .order-card,
        .auth-card,
        .dashboard-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(19, 42, 70, 0.08);
        }

        .hero-panel {
            background:
                linear-gradient(135deg, rgba(47, 111, 237, 0.09), rgba(244, 201, 93, 0.12)),
                var(--surface);
            padding: 28px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .section-subtitle {
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .book-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 14px 32px rgba(14, 36, 59, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 36px rgba(14, 36, 59, 0.12);
        }

        .book-card img {
            height: 300px;
            object-fit: cover;
        }

        .book-meta {
            font-size: 0.92rem;
            color: var(--text-muted);
        }

        .book-badge {
            border-radius: 999px;
            padding: 0.45rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .book-badge-online {
            background: #dfeafe;
            color: #2056b8;
        }

        .book-badge-offline {
            background: #fff0c8;
            color: #8b5a00;
        }

        .book-badge-danger {
            background: #ffe1e1;
            color: #b42318;
        }

        .soft-panel {
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        .table-modern {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--line);
            vertical-align: middle;
        }

        .table-modern thead th {
            background: #f5f8fd;
            color: var(--text-muted);
            font-size: 0.86rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom-width: 1px;
        }

        .table-modern tbody tr:hover {
            background: rgba(47, 111, 237, 0.04);
        }

        .btn {
            border-radius: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2f6fed 0%, #2259c8 100%);
            border-color: #2f6fed;
        }

        .btn-dark {
            background: linear-gradient(135deg, #14304a 0%, #0f2437 100%);
            border-color: #14304a;
        }

        .btn-outline-dark {
            border-color: #183752;
            color: #183752;
        }

        .btn-outline-dark:hover {
            background: #183752;
            color: #fff;
        }

        .filter-control {
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease, background-color 0.18s ease;
        }

        .filter-control:hover {
            border-color: #7da8ff;
            background: #f8fbff;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(47, 111, 237, 0.08);
        }

        .filter-action {
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease, color 0.18s ease;
        }

        .filter-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(20, 48, 74, 0.12);
        }

        .pagination {
            gap: 0.45rem;
        }

        .pagination .page-link {
            border: 1px solid var(--line);
            border-radius: 12px;
            color: var(--brand-dark);
            padding: 0.65rem 0.95rem;
            font-weight: 600;
            background: #fff;
            box-shadow: 0 8px 18px rgba(20, 48, 74, 0.05);
            transition: 0.18s ease;
        }

        .pagination .page-link:hover {
            background: var(--brand-blue);
            border-color: var(--brand-blue);
            color: #fff;
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #2f6fed 0%, #2259c8 100%);
            border-color: #2f6fed;
            color: #fff;
            box-shadow: 0 12px 24px rgba(47, 111, 237, 0.22);
        }

        .pagination .page-item.disabled .page-link {
            background: #f5f8fd;
            color: #9aa9bc;
            border-color: var(--line);
            box-shadow: none;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border-color: var(--line);
            padding: 0.72rem 0.9rem;
            background: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #7da8ff;
            box-shadow: 0 0 0 0.2rem rgba(47, 111, 237, 0.12);
        }

        .alert {
            border: 1px solid transparent;
            border-radius: 18px;
        }

        .alert-success {
            background: var(--success-soft);
            border-color: #ccefd8;
            color: #18613a;
        }

        .alert-danger {
            background: var(--danger-soft);
            border-color: #f4c4c4;
            color: #9f1f1f;
        }

        .alert-warning {
            background: var(--warning-soft);
            border-color: #f4ddb2;
            color: #8a5b00;
        }

        .alert-info {
            background: #eaf4ff;
            border-color: #cfe2ff;
            color: #204b7a;
        }

        .dashboard-card {
            padding: 22px;
            height: 100%;
        }

        .dashboard-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .dashboard-value {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0;
        }

        .mini-note {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .floating-chat-launcher {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 1080;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.85rem 1.1rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #2f6fed 0%, #2259c8 100%);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 16px 32px rgba(47, 111, 237, 0.28);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .floating-chat-launcher:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 20px 36px rgba(47, 111, 237, 0.35);
            background: linear-gradient(135deg, #3b7cff 0%, #245fd5 100%);
        }

        .floating-chat-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .floating-chat-text {
            line-height: 1.2;
        }

        .floating-chat-text small {
            display: block;
            opacity: 0.82;
            font-size: 0.76rem;
        }

        .floating-chat-widget {
            position: fixed;
            right: 22px;
            bottom: 88px;
            width: min(320px, calc(100vw - 28px));
            z-index: 1075;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(20, 48, 74, 0.18);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(18px) scale(0.98);
            transform-origin: bottom right;
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
        }

        .floating-chat-widget.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .floating-chat-header {
            background: linear-gradient(135deg, #14304a 0%, #1e466b 100%);
            color: #fff;
            padding: 0.75rem 0.85rem;
        }

        .floating-chat-header h3 {
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0;
        }

        .floating-chat-header p {
            margin: 0.25rem 0 0;
            font-size: 0.74rem;
            opacity: 0.82;
        }

        .floating-chat-top-actions {
            display: flex;
            gap: 0.5rem;
        }

        .floating-chat-icon-button {
            width: 30px;
            height: 30px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.18s ease;
        }

        .floating-chat-icon-button:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        .floating-chat-body {
            height: 300px;
            overflow-y: auto;
            background: linear-gradient(180deg, #f9fbff 0%, #f3f7fd 100%);
            padding: 0.75rem;
        }

        .floating-chat-row {
            display: flex;
            margin-bottom: 0.7rem;
        }

        .floating-chat-row-user {
            justify-content: flex-end;
        }

        .floating-chat-row-assistant {
            justify-content: flex-start;
        }

        .floating-chat-bubble {
            max-width: 90%;
            border-radius: 15px;
            padding: 0.7rem 0.8rem;
            box-shadow: 0 8px 20px rgba(20, 48, 74, 0.08);
        }

        .floating-chat-bubble-user {
            background: linear-gradient(135deg, #2f6fed 0%, #2259c8 100%);
            color: #fff;
            border-bottom-right-radius: 8px;
        }

        .floating-chat-bubble-assistant {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--line);
            border-bottom-left-radius: 8px;
        }

        .floating-chat-message {
            white-space: normal;
            line-height: 1.5;
            font-size: 0.86rem;
        }

        .floating-chat-book {
            margin-top: 0.6rem;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 0.65rem;
            display: flex;
            gap: 0.7rem;
            align-items: flex-start;
        }

        .floating-chat-book-media {
            width: 64px;
            min-width: 64px;
            height: 88px;
            object-fit: cover;
            border-radius: 10px;
            background: #eef3f9;
            box-shadow: 0 6px 14px rgba(20, 48, 74, 0.1);
        }

        .floating-chat-book-content {
            min-width: 0;
            flex: 1;
        }

        .floating-chat-book-title {
            font-weight: 700;
            margin-bottom: 0.15rem;
            font-size: 0.86rem;
        }

        .floating-chat-book-meta {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-bottom: 0.45rem;
        }

        .floating-chat-quick-actions {
            display: flex;
            gap: 0.45rem;
            flex-wrap: wrap;
            margin-top: 0.6rem;
        }

        .floating-chat-quick-actions .btn {
            border-radius: 999px;
            font-size: 0.72rem;
            padding: 0.28rem 0.58rem;
        }

        .floating-chat-footer {
            border-top: 1px solid var(--line);
            padding: 0.7rem;
            background: #fff;
        }

        .floating-chat-footer textarea {
            min-height: 44px;
            resize: none;
            font-size: 0.85rem;
            padding: 0.55rem 0.7rem;
        }

        @media (max-width: 991px) {
            .site-nav .container {
                gap: 12px;
            }

            .nav-group {
                width: 100%;
            }

            .nav-group .d-flex {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 576px) {
            .floating-chat-launcher {
                right: 14px;
                bottom: 14px;
                padding: 0.8rem 0.95rem;
            }

            .floating-chat-widget {
                right: 14px;
                bottom: 80px;
                width: calc(100vw - 28px);
            }

            .floating-chat-body {
                height: 280px;
            }

            .floating-chat-text span,
            .floating-chat-text small {
                display: none;
            }
        }
    </style>
</head>
@php
    $chatbotHistory = app(\App\Services\Chatbot\BookstoreChatbotService::class)->history();
@endphp
<body>
<nav class="navbar navbar-expand-lg site-nav shadow-sm">
    <div class="container py-2">
        <a class="navbar-brand text-white site-brand" href="{{ route('home') }}">Book Store</a>

        <div class="nav-group ms-auto">
            <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end">
                <a href="{{ route('books.index') }}" class="nav-pill {{ request()->routeIs('books.*') ? 'nav-pill-active' : '' }}">Danh sách sách</a>

                @auth
                    <a href="{{ route('cart.index') }}" class="nav-pill {{ request()->routeIs('cart.*') ? 'nav-pill-active' : '' }}">Giỏ hàng</a>
                    <a href="{{ route('orders.history') }}" class="nav-pill {{ request()->routeIs('orders.history') || request()->routeIs('orders.checkout') || request()->routeIs('orders.store') || request()->routeIs('orders.confirm-*') ? 'nav-pill-active' : '' }}">Đơn hàng</a>
                    <a href="{{ route('orders.library') }}" class="nav-pill {{ request()->routeIs('orders.library') || request()->routeIs('orders.books.read') ? 'nav-pill-active' : '' }}">Kho sách của tôi</a>

                    @if(auth()->user()->role_id == 1)
                        <a href="{{ route('admin.index') }}" class="nav-pill {{ request()->routeIs('admin.*') ? 'nav-pill-active' : '' }}">Quản trị</a>
                    @endif

                    <span class="text-white-50 small ms-2">Xin chào, {{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}" class="ms-1">
                        @csrf
                        <button class="nav-pill border-0" type="submit">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-pill {{ request()->routeIs('login') ? 'nav-pill-active' : '' }}">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="nav-pill {{ request()->routeIs('register') ? 'nav-pill-active' : '' }}">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="page-shell">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<div id="floatingChatWidget" class="floating-chat-widget" aria-hidden="true">
    <div class="floating-chat-header d-flex justify-content-between align-items-start gap-3">
        <div>
            <h3>Chatbot tư vấn sách</h3>
            <p>Hỏi về sách, đơn hàng hoặc khiếu nại.</p>
        </div>
        <div class="floating-chat-top-actions">
            <button type="button" class="floating-chat-icon-button" id="floatingChatReset" title="Làm mới">↺</button>
            <button type="button" class="floating-chat-icon-button" id="floatingChatClose" title="Đóng">×</button>
        </div>
    </div>

    <div id="floatingChatBody" class="floating-chat-body">
        @foreach($chatbotHistory as $item)
            <div class="floating-chat-row {{ $item['role'] === 'user' ? 'floating-chat-row-user' : 'floating-chat-row-assistant' }}">
                <div class="floating-chat-bubble {{ $item['role'] === 'user' ? 'floating-chat-bubble-user' : 'floating-chat-bubble-assistant' }}">
                    <div class="floating-chat-message">{!! nl2br(e($item['message'])) !!}</div>

                    @if(!empty($item['data']['books']))
                        @foreach($item['data']['books'] as $book)
                            <div class="floating-chat-book">
                                @if(!empty($book['image']))
                                    <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" class="floating-chat-book-media">
                                @endif
                                <div class="floating-chat-book-content">
                                    <div class="floating-chat-book-title">{{ $book['title'] }}</div>
                                    <div class="floating-chat-book-meta">
                                        {{ $book['category'] ?? 'Chưa cập nhật thể loại' }} · {{ number_format($book['price'], 0, ',', '.') }} VNĐ
                                    </div>
                                    <a href="{{ $book['detail_url'] }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if(!empty($item['data']['quick_actions']))
                        <div class="floating-chat-quick-actions">
                            @foreach($item['data']['quick_actions'] as $quickAction)
                                <button type="button" class="btn btn-sm btn-outline-dark floating-chat-quick-prompt" data-message="{{ $quickAction }}">{{ $quickAction }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="floating-chat-footer">
        <form id="floatingChatForm">
            @csrf
            <div class="mb-2">
                <textarea id="floatingChatMessage" class="form-control" placeholder="Nhập câu hỏi của bạn..."></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center gap-2">
                <div class="mini-note">Nhấn Enter để gửi, Shift + Enter để xuống dòng.</div>
                <button type="submit" class="btn btn-primary">Gửi</button>
            </div>
        </form>
    </div>
</div>

<button type="button" id="floatingChatLauncher" class="floating-chat-launcher" aria-label="Mở chatbot tư vấn sách">
    <span class="floating-chat-icon">💬</span>
    <span class="floating-chat-text">
        <span>Chatbot</span>
        <small>Tư vấn sách</small>
    </span>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const widget = document.getElementById('floatingChatWidget');
        const launcher = document.getElementById('floatingChatLauncher');
        const closeButton = document.getElementById('floatingChatClose');
        const resetButton = document.getElementById('floatingChatReset');
        const form = document.getElementById('floatingChatForm');
        const textarea = document.getElementById('floatingChatMessage');
        const body = document.getElementById('floatingChatBody');
        const csrfToken = document.querySelector('#floatingChatForm input[name="_token"]').value;
        const storageKey = 'bookstore_chat_widget_open';

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderBooks(books) {
            if (!books || !books.length) {
                return '';
            }

            return books.map(book => `
                <div class="floating-chat-book">
                    ${book.image ? `<img src="${book.image}" alt="${escapeHtml(book.title)}" class="floating-chat-book-media">` : ''}
                    <div class="floating-chat-book-content">
                        <div class="floating-chat-book-title">${escapeHtml(book.title)}</div>
                        <div class="floating-chat-book-meta">${escapeHtml(book.category || 'Chưa cập nhật thể loại')} · ${new Intl.NumberFormat('vi-VN').format(book.price)} VNĐ</div>
                        <a href="${book.detail_url}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            `).join('');
        }

        function renderQuickActions(actions) {
            if (!actions || !actions.length) {
                return '';
            }

            return `
                <div class="floating-chat-quick-actions">
                    ${actions.map(action => `<button type="button" class="btn btn-sm btn-outline-dark floating-chat-quick-prompt" data-message="${escapeHtml(action)}">${escapeHtml(action)}</button>`).join('')}
                </div>
            `;
        }

        function appendMessage(role, message, data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = `floating-chat-row ${role === 'user' ? 'floating-chat-row-user' : 'floating-chat-row-assistant'}`;
            wrapper.innerHTML = `
                <div class="floating-chat-bubble ${role === 'user' ? 'floating-chat-bubble-user' : 'floating-chat-bubble-assistant'}">
                    <div class="floating-chat-message">${escapeHtml(message).replace(/\n/g, '<br>')}</div>
                    ${renderBooks(data.books)}
                    ${renderQuickActions(data.quick_actions)}
                </div>
            `;
            body.appendChild(wrapper);
            body.scrollTop = body.scrollHeight;
        }

        function bindQuickPrompts() {
            document.querySelectorAll('.floating-chat-quick-prompt').forEach(button => {
                button.onclick = function () {
                    textarea.value = this.getAttribute('data-message') || '';
                    textarea.focus();
                };
            });
        }

        function openWidget() {
            widget.classList.add('is-open');
            widget.setAttribute('aria-hidden', 'false');
            launcher.setAttribute('aria-expanded', 'true');
            localStorage.setItem(storageKey, '1');
            setTimeout(() => {
                body.scrollTop = body.scrollHeight;
                textarea.focus();
            }, 120);
        }

        function closeWidget() {
            widget.classList.remove('is-open');
            widget.setAttribute('aria-hidden', 'true');
            launcher.setAttribute('aria-expanded', 'false');
            localStorage.setItem(storageKey, '0');
        }

        launcher.addEventListener('click', function () {
            if (widget.classList.contains('is-open')) {
                closeWidget();
                return;
            }

            openWidget();
        });

        closeButton.addEventListener('click', closeWidget);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && widget.classList.contains('is-open')) {
                closeWidget();
            }
        });

        textarea.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const message = textarea.value.trim();
            if (!message) {
                return;
            }

            textarea.value = '';
            appendMessage('user', message);

            const response = await fetch('{{ route('chatbot.message') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message }),
            });

            if (!response.ok) {
                appendMessage('assistant', 'Xin lỗi, tôi đang gặp lỗi tạm thời. Bạn thử lại sau ít phút nhé.');
                return;
            }

            const payload = await response.json();
            appendMessage('assistant', payload.assistant.message, payload.assistant.data || {});
            bindQuickPrompts();
        });

        resetButton.addEventListener('click', async function () {
            const response = await fetch('{{ route('chatbot.reset') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            body.innerHTML = '';

            payload.history.forEach(item => {
                appendMessage(item.role, item.message, item.data || {});
            });

            bindQuickPrompts();
        });

        bindQuickPrompts();
        body.scrollTop = body.scrollHeight;

        if (localStorage.getItem(storageKey) === '1') {
            openWidget();
        }
    });
</script>
</body>
</html>
