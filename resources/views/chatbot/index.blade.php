@extends('layouts.app')

@section('content')
    <div class="hero-panel mb-4">
        <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between">
            <div>
                <h1 class="section-title mb-2">Chatbot tư vấn sách</h1>
                <p class="section-subtitle">Hỏi về sách, tìm sách, tra đơn hàng hoặc gửi khiếu nại ngay trong một cửa sổ chat.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-dark quick-prompt" data-message="Gợi ý cho tôi vài sách kỹ năng hay">Gợi ý sách</button>
                <button type="button" class="btn btn-outline-dark quick-prompt" data-message="Tìm sách Đắc Nhân Tâm">Tìm sách</button>
                <button type="button" class="btn btn-outline-dark quick-prompt" data-message="Kiểm tra đơn hàng">Tra đơn hàng</button>
                <button type="button" class="btn btn-outline-dark quick-prompt" data-message="Tôi muốn khiếu nại đơn hàng">Khiếu nại</button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="section-card p-0 overflow-hidden">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h5 mb-1">Hội thoại</h2>
                        <p class="mini-note mb-0">Chatbot nhớ ngữ cảnh trong phiên hiện tại để trả lời mạch lạc hơn.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-dark" id="resetChatBtn">Làm mới cuộc trò chuyện</button>
                </div>

                <div id="chatMessages" class="chat-scroll p-3">
                    @foreach($history as $item)
                        <div class="chat-row {{ $item['role'] === 'user' ? 'chat-row-user' : 'chat-row-assistant' }}">
                            <div class="chat-bubble {{ $item['role'] === 'user' ? 'chat-bubble-user' : 'chat-bubble-assistant' }}">
                                <div class="chat-text">{!! nl2br(e($item['message'])) !!}</div>

                                @if(!empty($item['data']['books']))
                                    <div class="row g-3 mt-2">
                                        @foreach($item['data']['books'] as $book)
                                            <div class="col-md-6">
                                                <div class="soft-panel p-3 h-100">
                                                    <div class="fw-bold mb-1">{{ $book['title'] }}</div>
                                                    <div class="book-meta mb-1">{{ $book['category'] ?? 'Chưa cập nhật thể loại' }}</div>
                                                    <div class="book-meta mb-2">{{ number_format($book['price'], 0, ',', '.') }} VNĐ</div>
                                                    <a href="{{ $book['detail_url'] }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(!empty($item['data']['quick_actions']))
                                    <div class="d-flex gap-2 flex-wrap mt-3">
                                        @foreach($item['data']['quick_actions'] as $quickAction)
                                            <button type="button" class="btn btn-sm btn-outline-dark quick-prompt" data-message="{{ $quickAction }}">{{ $quickAction }}</button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-top p-3">
                    <form id="chatForm" class="d-flex gap-2 align-items-end">
                        @csrf
                        <div class="flex-grow-1">
                            <label for="chatMessage" class="form-label mb-1">Tin nhắn của bạn</label>
                            <textarea id="chatMessage" class="form-control" rows="2" placeholder="Ví dụ: Gợi ý cho tôi vài sách kinh doanh dưới 100.000đ"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Gửi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-card p-4 mb-4">
                <h2 class="h5 mb-3">Chatbot làm được gì?</h2>
                <ul class="mb-0 ps-3">
                    <li class="mb-2">Gợi ý sách theo thể loại, tác giả, ngân sách.</li>
                    <li class="mb-2">Tìm thông tin sách và link chi tiết.</li>
                    <li class="mb-2">Kiểm tra trạng thái đơn hàng theo mã đơn.</li>
                    <li>Tiếp nhận khiếu nại và hướng dẫn bước tiếp theo.</li>
                </ul>
            </div>

            <div class="section-card p-4">
                <h2 class="h5 mb-3">Mẹo sử dụng</h2>
                <div class="mini-note">
                    <p class="mb-2">- Với tư vấn sách, hãy nói rõ thể loại hoặc tầm giá.</p>
                    <p class="mb-2">- Với đơn hàng, hãy gửi đúng mã đơn như <strong>USR00001-OD00012</strong>.</p>
                    <p class="mb-0">- Nếu bạn đã đăng nhập, chatbot sẽ đọc đúng đơn hàng của tài khoản hiện tại.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chat-scroll {
            max-height: 640px;
            overflow-y: auto;
            background: linear-gradient(180deg, #f9fbff 0%, #f3f7fd 100%);
        }

        .chat-row {
            display: flex;
            margin-bottom: 1rem;
        }

        .chat-row-user {
            justify-content: flex-end;
        }

        .chat-row-assistant {
            justify-content: flex-start;
        }

        .chat-bubble {
            max-width: 82%;
            border-radius: 20px;
            padding: 1rem 1.1rem;
            box-shadow: 0 10px 25px rgba(20, 48, 74, 0.08);
        }

        .chat-bubble-user {
            background: linear-gradient(135deg, #2f6fed 0%, #2259c8 100%);
            color: #fff;
            border-bottom-right-radius: 8px;
        }

        .chat-bubble-assistant {
            background: #fff;
            border: 1px solid #dde7f3;
            color: #1f2a37;
            border-bottom-left-radius: 8px;
        }

        .chat-text {
            white-space: normal;
            line-height: 1.65;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('chatForm');
            const textarea = document.getElementById('chatMessage');
            const messages = document.getElementById('chatMessages');
            const resetButton = document.getElementById('resetChatBtn');

            const csrfToken = document.querySelector('#chatForm input[name="_token"]').value;

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function renderQuickActions(actions) {
                if (!actions || !actions.length) {
                    return '';
                }

                return `
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        ${actions.map(action => `<button type="button" class="btn btn-sm btn-outline-dark quick-prompt" data-message="${escapeHtml(action)}">${escapeHtml(action)}</button>`).join('')}
                    </div>
                `;
            }

            function renderBooks(books) {
                if (!books || !books.length) {
                    return '';
                }

                return `
                    <div class="row g-3 mt-2">
                        ${books.map(book => `
                            <div class="col-md-6">
                                <div class="soft-panel p-3 h-100">
                                    <div class="fw-bold mb-1">${escapeHtml(book.title)}</div>
                                    <div class="book-meta mb-1">${escapeHtml(book.category || 'Chưa cập nhật thể loại')}</div>
                                    <div class="book-meta mb-2">${new Intl.NumberFormat('vi-VN').format(book.price)} VNĐ</div>
                                    <a href="${book.detail_url}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            function appendMessage(role, message, data = {}) {
                const wrapper = document.createElement('div');
                wrapper.className = `chat-row ${role === 'user' ? 'chat-row-user' : 'chat-row-assistant'}`;
                wrapper.innerHTML = `
                    <div class="chat-bubble ${role === 'user' ? 'chat-bubble-user' : 'chat-bubble-assistant'}">
                        <div class="chat-text">${escapeHtml(message).replace(/\n/g, '<br>')}</div>
                        ${renderBooks(data.books)}
                        ${renderQuickActions(data.quick_actions)}
                    </div>
                `;
                messages.appendChild(wrapper);
                messages.scrollTop = messages.scrollHeight;
            }

            async function sendMessage(message) {
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
            }

            function bindQuickPrompts() {
                document.querySelectorAll('.quick-prompt').forEach(button => {
                    button.onclick = function () {
                        const message = this.getAttribute('data-message');
                        textarea.value = message;
                        textarea.focus();
                    };
                });
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const message = textarea.value.trim();
                if (!message) {
                    return;
                }

                textarea.value = '';
                await sendMessage(message);
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
                messages.innerHTML = '';

                payload.history.forEach(item => {
                    appendMessage(item.role, item.message, item.data || {});
                });

                bindQuickPrompts();
            });

            bindQuickPrompts();
            messages.scrollTop = messages.scrollHeight;
        });
    </script>
@endsection
