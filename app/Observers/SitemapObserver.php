<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SitemapObserver
{
    /**
     * Регенерация карты сайта после создания записи
     */
    public function created($model): void
    {
        $this->regenerateSitemap($model, 'created');
    }

    /**
     * Регенерация карты сайта после обновления записи
     */
    public function updated($model): void
    {
        $this->regenerateSitemap($model, 'updated');
    }

    /**
     * Регенерация карты сайта после удаления записи
     */
    public function deleted($model): void
    {
        $this->regenerateSitemap($model, 'deleted');
    }

    /**
     * Выполнение регенерации карты сайта
     */
    private function regenerateSitemap($model, string $action): void
    {
        try {
            // Очищаем кеш карт сайта
            Cache::forget('sitemap_' . md5('/sitemap.html'));
            Cache::forget('sitemap_' . md5('/sitemap.xml'));
            Cache::forget('sitemap_' . md5('/robots.txt'));
            
            // Запускаем команду генерации карты сайта в фоне
            Artisan::call('sitemap:generate', ['--force' => true]);
            
            Log::info('Sitemap regenerated and cache cleared', [
                'model' => get_class($model),
                'id' => $model->id ?? null,
                'action' => $action,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to regenerate sitemap', [
                'model' => get_class($model),
                'id' => $model->id ?? null,
                'action' => $action,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }
    }
}