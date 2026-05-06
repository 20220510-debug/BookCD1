<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Str;

class IntentClassifier
{
    public const BOOK_RECOMMENDATION = 'BOOK_RECOMMENDATION';
    public const BOOK_SEARCH = 'BOOK_SEARCH';
    public const ORDER_SUPPORT = 'ORDER_SUPPORT';
    public const COMPLAINT = 'COMPLAINT';
    public const UNKNOWN = 'UNKNOWN';

    public function classify(string $message, array $context = []): array
    {
        $normalized = Str::lower(trim($message));

        if (($context['expected_input'] ?? null) === 'order_code') {
            return ['intent' => $context['pending_intent'] ?? self::ORDER_SUPPORT, 'confidence' => 0.99];
        }

        if (($context['expected_input'] ?? null) === 'complaint_details') {
            return ['intent' => self::COMPLAINT, 'confidence' => 0.99];
        }

        if (($context['expected_input'] ?? null) === 'recommendation_preferences') {
            return ['intent' => self::BOOK_RECOMMENDATION, 'confidence' => 0.95];
        }

        if (($context['expected_input'] ?? null) === 'search_title') {
            return ['intent' => self::BOOK_SEARCH, 'confidence' => 0.95];
        }

        $complaintKeywords = [
            'khiếu nại', 'khieu nai', 'lỗi', 'loi', 'sai', 'hỏng', 'hong',
            'không nhận được', 'khong nhan duoc', 'chưa nhận được', 'chua nhan duoc',
            'than phiền', 'hoàn tiền', 'hoan tien', 'bị lỗi', 'bi loi',
        ];

        if ($this->containsAny($normalized, $complaintKeywords)) {
            return ['intent' => self::COMPLAINT, 'confidence' => 0.94];
        }

        $orderKeywords = [
            'đơn hàng', 'don hang', 'mã đơn', 'ma don', 'order',
            'chuyển khoản', 'chuyen khoan', 'thanh toán', 'thanh toan',
            'giao hàng', 'giao hang', 'nhận hàng', 'nhan hang',
            'tình trạng đơn', 'tinh trang don',
        ];

        if ($this->containsAny($normalized, $orderKeywords)) {
            return ['intent' => self::ORDER_SUPPORT, 'confidence' => 0.91];
        }

        $searchKeywords = [
            'tìm sách', 'tim sach', 'có sách', 'co sach', 'kiếm sách', 'kiem sach',
            'thông tin sách', 'thong tin sach', 'chi tiết sách', 'chi tiet sach',
            'cuốn', 'cuon', 'quyển', 'quyen',
        ];

        if ($this->containsAny($normalized, $searchKeywords)) {
            return ['intent' => self::BOOK_SEARCH, 'confidence' => 0.88];
        }

        $recommendKeywords = [
            'gợi ý', 'goi y', 'đề xuất', 'de xuat', 'tư vấn', 'tu van',
            'nên đọc', 'nen doc', 'muốn đọc', 'muon doc', 'recommend',
        ];

        if ($this->containsAny($normalized, $recommendKeywords)) {
            return ['intent' => self::BOOK_RECOMMENDATION, 'confidence' => 0.9];
        }

        return ['intent' => self::UNKNOWN, 'confidence' => 0.4];
    }

    protected function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
