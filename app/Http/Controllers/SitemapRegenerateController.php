<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class SitemapRegenerateController extends Controller
{
    /**
     * Ручная регенерация карты сайта
     * Простая версия без токенов
     */
    public function regenerate(Request $request): JsonResponse
    {
        try {
            // Очищаем кеш карт сайта
            Cache::forget('sitemap_' . md5('/sitemap.html'));
            Cache::forget('sitemap_' . md5('/sitemap.xml'));
            Cache::forget('sitemap_' . md5('/robots.txt'));
            
            // Запускаем команду генерации карты сайта
            $exitCode = Artisan::call('sitemap:generate', ['--force' => true]);
            
            if ($exitCode === 0) {
                Log::info('Manual sitemap regeneration successful', [
                    'ip' => $request->ip(),
                    'timestamp' => now()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Sitemap regenerated successfully',
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                throw new \Exception('Artisan command failed with exit code: ' . $exitCode);
            }
            
        } catch (\Exception $e) {
            Log::error('Manual sitemap regeneration failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate sitemap: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Проверка статуса карты сайта
     */
    public function status(Request $request): JsonResponse
    {
        $sitemapXmlPath = public_path('sitemap.xml');
        $sitemapHtmlPath = public_path('sitemap.html');
        
        return response()->json([
            'success' => true,
            'sitemap_xml' => [
                'exists' => file_exists($sitemapXmlPath),
                'last_modified' => file_exists($sitemapXmlPath) ? date('Y-m-d H:i:s', filemtime($sitemapXmlPath)) : null,
                'size' => file_exists($sitemapXmlPath) ? filesize($sitemapXmlPath) : null
            ],
            'sitemap_html' => [
                'exists' => file_exists($sitemapHtmlPath),
                'last_modified' => file_exists($sitemapHtmlPath) ? date('Y-m-d H:i:s', filemtime($sitemapHtmlPath)) : null,
                'size' => file_exists($sitemapHtmlPath) ? filesize($sitemapHtmlPath) : null
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
}