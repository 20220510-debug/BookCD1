<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;
use Throwable;

class OpenAiChatbotClient
{
    public function isEnabled(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    public function classifyIntent(string $message): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withToken(config('services.openai.api_key'))
                ->timeout(20)
                ->post(rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/') . '/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Phân loại câu hỏi người dùng vào 1 intent duy nhất: BOOK_RECOMMENDATION, BOOK_SEARCH, ORDER_SUPPORT, COMPLAINT, UNKNOWN. Chỉ trả về JSON {"intent":"...","confidence":0-1}.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $message,
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            if (!is_string($content)) {
                return null;
            }

            $decoded = json_decode(trim($content), true);

            return is_array($decoded) ? $decoded : null;
        } catch (Throwable) {
            return null;
        }
    }
}
