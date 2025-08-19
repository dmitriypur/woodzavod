<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramTestController extends Controller
{
    public function sendTestMessage()
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if (!$botToken || !$chatId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telegram credentials not configured',
                    'bot_token_exists' => !empty($botToken),
                    'chat_id_exists' => !empty($chatId),
                ]);
            }

            $message = "🧪 Тестовое сообщение с продакшена: " . now()->format('d.m.Y H:i:s');

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Тестовое сообщение отправлено',
                    'response_body' => $response->body()
                ]);
            } else {
                Log::error('Telegram API error', [
                    'status' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка Telegram API',
                    'status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Telegram sending exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Исключение при отправке: ' . $e->getMessage()
            ]);
        }
    }
}
