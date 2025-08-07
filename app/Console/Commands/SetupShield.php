<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SetupShield extends Command
{
    protected $signature = 'shield:setup {--user-id=1 : ID пользователя для назначения роли super_admin}';
    protected $description = 'Настройка Shield разрешений и создание супер-администратора';

    public function handle(): int
    {
        $this->info('🛡️  Настройка Shield...');
        
        try {
            // Генерируем разрешения для всех ресурсов и страниц
            $this->info('📋 Генерация разрешений...');
            Artisan::call('shield:generate', ['--all' => true]);
            $this->line('✅ Разрешения сгенерированы');
            
            // Создаем супер-администратора
            $userId = $this->option('user-id');
            $this->info('👑 Создание супер-администратора...');
            Artisan::call('shield:super-admin', ['user' => $userId]);
            $this->line('✅ Супер-администратор создан для пользователя ID: ' . $userId);
            
            // Очищаем кеш
            $this->info('🧹 Очистка кеша...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            $this->line('✅ Кеш очищен');
            
            $this->info('🎉 Shield успешно настроен!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Ошибка настройки Shield: ' . $e->getMessage());
            Log::error('Shield setup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $this->option('user-id'),
                'timestamp' => now()
            ]);
            return Command::FAILURE;
        }
    }
}