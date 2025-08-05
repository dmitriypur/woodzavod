<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheSitemap
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cacheKey = 'sitemap_' . md5($request->getPathInfo());
        
        // Проверяем кеш
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            return response($cachedResponse['content'])
                ->header('Content-Type', $cachedResponse['content_type'])
                ->header('Cache-Control', 'public, max-age=86400'); // 24 часа
        }
        
        $response = $next($request);
        
        // Кешируем ответ на 24 часа
        if ($response->getStatusCode() === 200) {
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'content_type' => $response->headers->get('Content-Type')
            ], now()->addDay());
            
            $response->header('Cache-Control', 'public, max-age=86400');
        }
        
        return $response;
    }
}