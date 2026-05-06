<?php

namespace App\Http\Controllers;

use App\Services\Chatbot\BookstoreChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function __construct(
        protected BookstoreChatbotService $chatbotService
    ) {
    }

    public function index(): View
    {
        return view('chatbot.index', [
            'history' => $this->chatbotService->history(),
        ]);
    }

    public function message(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $result = $this->chatbotService->reply($data['message'], $request->user());

        return response()->json($result);
    }

    public function reset(): JsonResponse
    {
        return response()->json([
            'history' => $this->chatbotService->reset(),
        ]);
    }
}
