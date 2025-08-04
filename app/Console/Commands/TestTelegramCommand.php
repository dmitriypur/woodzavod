<?php

namespace App\Console\Commands;

use App\Http\Controllers\LeadController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestTelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram bot connection and send test message';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Telegram connection...');
        
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        
        $this->info('Environment variables:');
        $this->line('TELEGRAM_BOT_TOKEN: ' . ($botToken ? 'Set (' . strlen($botToken) . ' chars)' : 'Not set'));
        $this->line('TELEGRAM_CHAT_ID: ' . ($chatId ?: 'Not set'));
        
        if (!$botToken || !$chatId) {
            $this->error('Telegram credentials not configured!');
            return 1;
        }

        try {
            // Проверяем бота
            $this->info('\nChecking bot info...');
            $botResponse = Http::timeout(10)->get("https://api.telegram.org/bot{$botToken}/getMe");
            
            if ($botResponse->successful()) {
                $botInfo = $botResponse->json();
                $this->info('Bot info: ' . $botInfo['result']['first_name'] . ' (@' . $botInfo['result']['username'] . ')');
            } else {
                $this->error('Bot token invalid: ' . $botResponse->body());
                return 1;
            }

            // Отправляем тестовое сообщение
            $this->info('\nSending test message...');
            $testResponse = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => '🧪 Тест соединения из консоли - ' . now()->format('d.m.Y H:i:s')
            ]);

            if ($testResponse->successful()) {
                $this->info('✅ Test message sent successfully!');
                $this->line('Response: ' . $testResponse->body());
            } else {
                $this->error('❌ Failed to send test message');
                $this->error('Status: ' . $testResponse->status());
                $this->error('Response: ' . $testResponse->body());
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
            Log::error('Telegram test command error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->info('\n✅ Telegram connection test completed successfully!');
        return 0;
    }
}