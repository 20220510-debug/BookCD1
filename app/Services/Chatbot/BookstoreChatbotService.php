<?php

namespace App\Services\Chatbot;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BookstoreChatbotService
{
    protected const HISTORY_KEY = 'chatbot.history';
    protected const CONTEXT_KEY = 'chatbot.context';
    protected const HISTORY_LIMIT = 14;

    public function __construct(
        protected IntentClassifier $classifier,
        protected OpenAiChatbotClient $openAiClient
    ) {
    }

    public function history(): array
    {
        return Session::get(self::HISTORY_KEY, [$this->welcomeMessage()]);
    }

    public function reset(): array
    {
        Session::forget([self::HISTORY_KEY, self::CONTEXT_KEY]);

        return $this->history();
    }

    public function reply(string $message, ?User $user = null): array
    {
        $history = $this->history();
        $context = Session::get(self::CONTEXT_KEY, []);

        $history[] = $this->makeHistoryItem('user', trim($message), 'user_message');

        $intentResult = $this->classifyIntent($message, $context);
        $assistant = match ($intentResult['intent']) {
            IntentClassifier::BOOK_RECOMMENDATION => $this->handleRecommendation($message, $context),
            IntentClassifier::BOOK_SEARCH => $this->handleBookSearch($message, $context),
            IntentClassifier::ORDER_SUPPORT => $this->handleOrderSupport($message, $context, $user),
            IntentClassifier::COMPLAINT => $this->handleComplaint($message, $context, $user),
            default => $this->fallbackResponse(),
        };

        $context = array_merge($context, $assistant['context_updates'] ?? []);

        if (($assistant['clear_expected'] ?? false) === true) {
            unset($context['expected_input'], $context['pending_intent']);
        }

        if (($assistant['reset_context'] ?? false) === true) {
            $context = [];
        }

        unset($assistant['context_updates'], $assistant['clear_expected'], $assistant['reset_context']);

        $history[] = $this->makeHistoryItem(
            'assistant',
            $assistant['message'],
            $assistant['type'],
            $assistant['data'] ?? []
        );

        $history = array_slice($history, -self::HISTORY_LIMIT);

        Session::put(self::HISTORY_KEY, $history);
        Session::put(self::CONTEXT_KEY, $context);

        return [
            'assistant' => $assistant,
            'history' => $history,
            'intent' => $intentResult,
        ];
    }

    protected function classifyIntent(string $message, array $context): array
    {
        $openAiResult = $this->openAiClient->classifyIntent($message);

        if (is_array($openAiResult) && !empty($openAiResult['intent'])) {
            return [
                'intent' => $openAiResult['intent'],
                'confidence' => (float) ($openAiResult['confidence'] ?? 0.7),
            ];
        }

        return $this->classifier->classify($message, $context);
    }

    protected function handleRecommendation(string $message, array $context): array
    {
        $preferences = array_merge($context['recommendation'] ?? [], $this->extractBookPreferences($message));

        if (!$this->hasEnoughRecommendationData($preferences)) {
            $categories = Category::query()->limit(6)->pluck('category_name')->filter()->implode(', ');

            return [
                'type' => 'question',
                'message' => "📚 Tôi có thể gợi ý sách cho bạn.\n\nBạn thích thể loại nào, tác giả nào hoặc tầm giá bao nhiêu?\nVí dụ: {$categories}.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'recommendation_preferences',
                    'pending_intent' => IntentClassifier::BOOK_RECOMMENDATION,
                    'recommendation' => $preferences,
                ],
            ];
        }

        $books = $this->queryBooks($preferences)->take(3)->get();

        if ($books->isEmpty()) {
            return [
                'type' => 'recommendation',
                'message' => "📚 Tôi chưa tìm thấy sách phù hợp hoàn toàn.\n\nBạn thử đổi thể loại, tăng ngân sách hoặc nói rõ tác giả bạn muốn nhé.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'recommendation_preferences',
                    'pending_intent' => IntentClassifier::BOOK_RECOMMENDATION,
                    'recommendation' => $preferences,
                ],
            ];
        }

        $messageText = "📚 Gợi ý cho bạn:\n\n";

        foreach ($books as $index => $book) {
            $messageText .= ($index + 1) . ". {$book->title}\n";
            $messageText .= "- Thể loại: " . ($book->category?->category_name ?? 'Chưa cập nhật') . "\n";
            $messageText .= "- Giá: " . number_format((float) $book->price, 0, ',', '.') . " VNĐ\n";
            $messageText .= "- Vì sao phù hợp: " . $this->buildRecommendationReason($book, $preferences) . "\n\n";
        }

        $messageText .= "Bạn muốn xem chi tiết cuốn nào không?";

        return [
            'type' => 'recommendation',
            'message' => trim($messageText),
            'data' => [
                'books' => $this->formatBooks($books),
            ],
            'clear_expected' => true,
            'context_updates' => [
                'recommendation' => $preferences,
                'last_suggested_books' => $books->pluck('id')->all(),
            ],
        ];
    }

    protected function handleBookSearch(string $message, array $context): array
    {
        $searchText = $this->extractSearchText($message);

        if (!$searchText) {
            return [
                'type' => 'question',
                'message' => "📖 Bạn hãy nhập tên sách hoặc một phần tên sách để tôi tìm giúp bạn.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'search_title',
                    'pending_intent' => IntentClassifier::BOOK_SEARCH,
                ],
            ];
        }

        $book = Book::with(['category', 'publisher', 'authors'])
            ->where('title', 'like', '%' . $searchText . '%')
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', [$searchText . '%'])
            ->orderBy('title')
            ->first();

        if (!$book) {
            return [
                'type' => 'search',
                'message' => "📖 Tôi chưa tìm thấy sách phù hợp với từ khóa \"{$searchText}\".\n\nBạn thử nhập tên gần đúng hơn hoặc nói thể loại bạn muốn nhé.",
                'data' => [],
                'clear_expected' => true,
            ];
        }

        $messageText = "📖 Thông tin sách:\n\n";
        $messageText .= "- Tên: {$book->title}\n";
        $messageText .= "- Giá: " . number_format((float) $book->price, 0, ',', '.') . " VNĐ\n";
        $messageText .= "- Tình trạng: " . $this->bookAvailabilityLabel($book) . "\n";
        $messageText .= "- Link: " . route('books.show', $book->id);

        $data = ['book' => $this->formatBook($book)];

        if (!$book->isOnline() && (int) $book->stock <= 0) {
            $similar = Book::with(['category', 'publisher', 'authors'])
                ->where('category_id', $book->category_id)
                ->where('id', '!=', $book->id)
                ->latest()
                ->take(2)
                ->get();

            if ($similar->isNotEmpty()) {
                $messageText .= "\n\n👉 Gợi ý 2 sách tương tự:";
                foreach ($similar as $item) {
                    $messageText .= "\n- {$item->title}";
                }
                $data['similar_books'] = $this->formatBooks($similar);
            }
        }

        return [
            'type' => 'search',
            'message' => $messageText,
            'data' => $data,
            'clear_expected' => true,
            'context_updates' => [
                'last_search' => $searchText,
            ],
        ];
    }

    protected function handleOrderSupport(string $message, array $context, ?User $user): array
    {
        if (!$user) {
            return [
                'type' => 'order_support',
                'message' => "🔍 Tôi có thể hỗ trợ kiểm tra đơn hàng.\n\nBạn vui lòng đăng nhập trước, sau đó gửi mã đơn hàng để tôi kiểm tra ngay.",
                'data' => [],
                'clear_expected' => true,
            ];
        }

        $orderCode = $this->extractOrderCode($message) ?: ($context['order_code'] ?? null);

        if (!$orderCode) {
            return [
                'type' => 'question',
                'message' => "🔍 Kiểm tra đơn hàng:\n\nVui lòng cung cấp mã đơn hàng.\nSau đó tôi sẽ kiểm tra ngay cho bạn.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'order_code',
                    'pending_intent' => IntentClassifier::ORDER_SUPPORT,
                ],
            ];
        }

        $order = Order::with(['payment', 'orderItems.book'])
            ->where('user_id', $user->id)
            ->where('order_code', $orderCode)
            ->first();

        if (!$order) {
            return [
                'type' => 'order_support',
                'message' => "🔍 Tôi chưa tìm thấy đơn hàng có mã {$orderCode} trong tài khoản của bạn.\n\nBạn kiểm tra lại mã đơn hàng giúp tôi nhé.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'order_code',
                    'pending_intent' => IntentClassifier::ORDER_SUPPORT,
                ],
            ];
        }

        $messageText = "🔍 Thông tin đơn hàng:\n\n";
        $messageText .= "- Mã đơn: {$order->order_code}\n";
        $messageText .= "- Loại đơn: " . ($order->order_type === 'online' ? 'Sách online' : 'Sách offline') . "\n";
        $messageText .= "- Trạng thái: " . $this->humanizeOrderStatus($order->status) . "\n";
        $messageText .= "- Thanh toán: " . $this->humanizePaymentMethod(optional($order->payment)->payment_method) . "\n";
        $messageText .= "- Tổng tiền: " . number_format((float) $order->total_amount, 0, ',', '.') . " VNĐ";

        return [
            'type' => 'order_support',
            'message' => $messageText,
            'data' => [
                'order' => [
                    'order_code' => $order->order_code,
                    'status' => $order->status,
                    'status_label' => $this->humanizeOrderStatus($order->status),
                    'order_type' => $order->order_type,
                    'payment_method' => optional($order->payment)->payment_method,
                    'payment_method_label' => $this->humanizePaymentMethod(optional($order->payment)->payment_method),
                    'total_amount' => (float) $order->total_amount,
                ],
            ],
            'clear_expected' => true,
            'context_updates' => [
                'order_code' => $orderCode,
            ],
        ];
    }

    protected function handleComplaint(string $message, array $context, ?User $user): array
    {
        $orderCode = $this->extractOrderCode($message) ?: ($context['complaint_order_code'] ?? null);

        if (!$user) {
            return [
                'type' => 'complaint',
                'message' => "😔 Rất xin lỗi bạn về trải nghiệm này.\n\n👉 Để hỗ trợ nhanh nhất, bạn vui lòng đăng nhập rồi cung cấp:\n- Mã đơn hàng\n- Mô tả vấn đề ngắn gọn\n\nChúng tôi sẽ hỗ trợ bạn ngay.",
                'data' => [],
                'clear_expected' => true,
            ];
        }

        if (!$orderCode) {
            return [
                'type' => 'complaint',
                'message' => "😔 Rất xin lỗi bạn về trải nghiệm này.\n\n👉 Để hỗ trợ nhanh nhất, bạn vui lòng cung cấp:\n- Mã đơn hàng\n- Mô tả vấn đề ngắn gọn",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'order_code',
                    'pending_intent' => IntentClassifier::COMPLAINT,
                ],
            ];
        }

        $order = Order::where('user_id', $user->id)
            ->where('order_code', $orderCode)
            ->first();

        if (!$order) {
            return [
                'type' => 'complaint',
                'message' => "😔 Tôi chưa tìm thấy đơn hàng {$orderCode} trong tài khoản của bạn.\n\nBạn gửi lại đúng mã đơn để tôi hỗ trợ tiếp nhé.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'order_code',
                    'pending_intent' => IntentClassifier::COMPLAINT,
                ],
            ];
        }

        $issueText = trim(str_ireplace($orderCode, '', $message));
        $issueText = trim(str_replace(['mã đơn', 'ma don', 'đơn hàng', 'don hang'], '', $issueText));

        if (mb_strlen($issueText) < 5 && ($context['expected_input'] ?? null) !== 'complaint_details') {
            return [
                'type' => 'complaint',
                'message' => "😔 Tôi đã ghi nhận mã đơn {$orderCode}.\n\nBạn vui lòng mô tả ngắn gọn vấn đề để tôi hỗ trợ đúng hơn nhé.",
                'data' => [],
                'context_updates' => [
                    'expected_input' => 'complaint_details',
                    'pending_intent' => IntentClassifier::COMPLAINT,
                    'complaint_order_code' => $orderCode,
                ],
            ];
        }

        if (($context['expected_input'] ?? null) === 'complaint_details' && empty($issueText)) {
            $issueText = trim($message);
        }

        return [
            'type' => 'complaint',
            'message' => "😔 Rất xin lỗi bạn về trải nghiệm này.\n\nTôi đã ghi nhận khiếu nại cho đơn {$orderCode} với nội dung: {$issueText}.\n\n⏱ Bộ phận CSKH sẽ liên hệ với bạn sớm để xử lý triệt để vấn đề này.",
            'data' => [
                'order_code' => $orderCode,
                'issue' => $issueText,
            ],
            'clear_expected' => true,
            'context_updates' => [
                'complaint_order_code' => $orderCode,
            ],
        ];
    }

    protected function fallbackResponse(): array
    {
        return [
            'type' => 'fallback',
            'message' => "🤖 Xin lỗi, tôi chưa hiểu rõ yêu cầu của bạn.\n\n👉 Bạn có thể:\n- Viết lại câu hỏi\n- Hoặc hỏi theo các nhóm: tư vấn sách, tìm sách, hỗ trợ đơn hàng, khiếu nại",
            'data' => [
                'quick_actions' => [
                    'Gợi ý sách kinh doanh dưới 100.000đ',
                    'Tìm sách Đắc Nhân Tâm',
                    'Kiểm tra đơn hàng',
                    'Tôi muốn khiếu nại đơn hàng',
                ],
            ],
        ];
    }

    protected function extractBookPreferences(string $message): array
    {
        $normalized = Str::lower($message);
        $preferences = [];

        $category = Category::all()->first(function (Category $item) use ($normalized) {
            return filled($item->category_name) && Str::contains($normalized, Str::lower($item->category_name));
        });
        if ($category) {
            $preferences['category_id'] = $category->id;
        }

        $author = Author::all()->first(function (Author $item) use ($normalized) {
            return filled($item->author_name) && Str::contains($normalized, Str::lower($item->author_name));
        });
        if ($author) {
            $preferences['author_id'] = $author->id;
        }

        if (Str::contains($normalized, ['online', 'ebook', 'pdf'])) {
            $preferences['book_type'] = 'online';
        }

        if (Str::contains($normalized, ['offline', 'sách giấy', 'sach giay'])) {
            $preferences['book_type'] = 'offline';
        }

        if (preg_match('/(\d+)\s*k/', $normalized, $matches)) {
            $preferences['price_max'] = ((int) $matches[1]) * 1000;
        } elseif (preg_match('/(\d{2,7})/', preg_replace('/[^\d]/', ' ', $normalized), $matches)) {
            $number = (int) $matches[1];
            if ($number >= 10000) {
                $preferences['price_max'] = $number;
            }
        }

        return $preferences;
    }

    protected function hasEnoughRecommendationData(array $preferences): bool
    {
        return filled($preferences['category_id'] ?? null)
            || filled($preferences['author_id'] ?? null)
            || filled($preferences['book_type'] ?? null)
            || filled($preferences['price_max'] ?? null);
    }

    protected function queryBooks(array $preferences)
    {
        return Book::with(['category', 'publisher', 'authors'])
            ->when(isset($preferences['category_id']), fn ($query) => $query->where('category_id', $preferences['category_id']))
            ->when(isset($preferences['author_id']), fn ($query) => $query->whereHas('authors', function ($subQuery) use ($preferences) {
                $subQuery->where('authors.id', $preferences['author_id']);
            }))
            ->when(isset($preferences['book_type']), fn ($query) => $query->where('book_type', $preferences['book_type']))
            ->when(isset($preferences['price_max']), fn ($query) => $query->where('price', '<=', $preferences['price_max']))
            ->where(function ($query) {
                $query->where('book_type', 'online')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('book_type', 'offline')
                            ->where('stock', '>', 0);
                    });
            })
            ->latest();
    }

    protected function extractSearchText(string $message): ?string
    {
        $text = trim($message);
        $patterns = [
            '/^(tìm sách|tim sach|kiếm sách|kiem sach|thông tin sách|thong tin sach|chi tiết sách|chi tiet sach)\s*/iu',
            '/^(cho tôi|cho toi|giúp tôi|giup toi|muốn|muon)\s*/iu',
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text) ?? $text;
        }

        $text = trim($text, " \t\n\r\0\x0B\"'.,:;!?");

        return mb_strlen($text) >= 2 ? $text : null;
    }

    protected function extractOrderCode(string $message): ?string
    {
        if (preg_match('/[A-Z]{3}\d{5}-OD\d{5}/i', $message, $matches)) {
            return strtoupper($matches[0]);
        }

        return null;
    }

    protected function bookAvailabilityLabel(Book $book): string
    {
        if ($book->isOnline()) {
            return 'Mua online';
        }

        return (int) $book->stock > 0 ? 'Còn hàng' : 'Hết hàng';
    }

    protected function buildRecommendationReason(Book $book, array $preferences): string
    {
        $reasons = [];

        if (($preferences['category_id'] ?? null) === $book->category_id) {
            $reasons[] = 'đúng thể loại bạn đang quan tâm';
        }

        if (isset($preferences['price_max']) && (float) $book->price <= (float) $preferences['price_max']) {
            $reasons[] = 'nằm trong ngân sách của bạn';
        }

        if (($preferences['book_type'] ?? null) === $book->book_type) {
            $reasons[] = $book->isOnline() ? 'có thể đọc online ngay' : 'phù hợp nếu bạn muốn sách giấy';
        }

        return $reasons ? ucfirst(implode(', ', $reasons)) . '.' : 'Đây là cuốn đang được nhiều người quan tâm.';
    }

    protected function formatBooks(Collection $books): array
    {
        return $books->map(fn (Book $book) => $this->formatBook($book))->all();
    }

    protected function formatBook(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'price' => (float) $book->price,
            'book_type' => $book->book_type,
            'image' => $book->image,
            'detail_url' => route('books.show', $book->id),
            'category' => $book->category?->category_name,
            'publisher' => $book->publisher?->publisher_name,
            'authors' => $book->authors->pluck('author_name')->all(),
            'availability' => $this->bookAvailabilityLabel($book),
        ];
    }

    protected function humanizeOrderStatus(?string $status): string
    {
        return match ($status) {
            Order::STATUS_DANG_GIAO_DICH => 'Đang giao dịch',
            Order::STATUS_DA_GIAO_DICH_THANH_CONG => 'Đã giao dịch thành công',
            Order::STATUS_DA_XAC_NHAN_DA_THANH_TOAN => 'Đã xác nhận đã thanh toán',
            Order::STATUS_DA_XAC_NHAN_GIAO_DICH => 'Đã xác nhận giao dịch',
            Order::STATUS_DANG_GIAO_HANG => 'Đang giao hàng',
            Order::STATUS_DA_NHAN_HANG => 'Đã nhận hàng',
            Order::STATUS_DON_HANG_DA_BI_HUY => 'Đơn hàng đã bị hủy',
            default => 'Chưa cập nhật',
        };
    }

    protected function humanizePaymentMethod(?string $method): string
    {
        return match ($method) {
            'bank_transfer' => 'Chuyển khoản',
            'cod' => 'Thanh toán khi nhận hàng',
            default => 'Chưa cập nhật',
        };
    }

    protected function welcomeMessage(): array
    {
        return $this->makeHistoryItem(
            'assistant',
            "Xin chào, tôi là trợ lý sách của Book Store.\n\nTôi có thể hỗ trợ bạn:\n- Tư vấn sách\n- Tìm kiếm sách cụ thể\n- Hỗ trợ đơn hàng\n- Tiếp nhận khiếu nại",
            'welcome',
            [
                'quick_actions' => [
                    'Gợi ý sách kỹ năng',
                    'Tìm sách Đắc Nhân Tâm',
                    'Kiểm tra đơn hàng',
                    'Tôi muốn khiếu nại đơn hàng',
                ],
            ]
        );
    }

    protected function makeHistoryItem(string $role, string $message, string $type, array $data = []): array
    {
        return [
            'role' => $role,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
