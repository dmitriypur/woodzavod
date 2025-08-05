<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\House;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /**
     * Генерация HTML карты сайта
     */
    public function html(): Response
    {
        $pages = Page::where('is_published', true)
            ->orderBy('title')
            ->get();

        $houses = House::where('is_published', true)
            ->orderBy('title')
            ->get();

        $categories = Category::orderBy('name')->get();

        return response()->view('sitemap.html', compact('pages', 'houses', 'categories'))
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Генерация XML карты сайта
     */
    public function xml(): Response
    {
        $urls = collect();

        // Главная страница
        $urls->push([
            'url' => route('home'),
            'lastmod' => Carbon::now()->toISOString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ]);

        // Каталог
        $lastHouseUpdate = House::where('is_published', true)->max('updated_at');
        $urls->push([
            'url' => route('catalog'),
            'lastmod' => $lastHouseUpdate ? Carbon::parse($lastHouseUpdate)->toISOString() : Carbon::now()->toISOString(),
            'changefreq' => 'daily',
            'priority' => '0.9'
        ]);

        // Страницы
        Page::where('is_published', true)->each(function ($page) use ($urls) {
            $urls->push([
                'url' => route('page.show', $page->slug),
                'lastmod' => $page->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ]);
        });

        // Дома
        House::where('is_published', true)->each(function ($house) use ($urls) {
            $urls->push([
                'url' => route('house.show', $house->slug),
                'lastmod' => $house->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ]);
        });

        return response()->view('sitemap.xml', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Генерация robots.txt с указанием на карту сайта
     */
    public function robots(): Response
    {
        $baseUrl = request()->getSchemeAndHttpHost();
        
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        
        // Блокируем служебные директории Laravel
        $content .= "# Блокируем служебные директории Laravel\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /storage/\n";
        $content .= "Disallow: /vendor/\n";
        $content .= "Disallow: /bootstrap/\n";
        $content .= "Disallow: /config/\n";
        $content .= "Disallow: /database/\n";
        $content .= "Disallow: /resources/\n";
        $content .= "Disallow: /routes/\n";
        $content .= "Disallow: /tests/\n";
        $content .= "Disallow: /app/\n";
        $content .= "Disallow: /.env\n";
        $content .= "Disallow: /composer.json\n";
        $content .= "Disallow: /composer.lock\n";
        $content .= "Disallow: /package.json\n";
        $content .= "Disallow: /package-lock.json\n";
        $content .= "Disallow: /artisan\n";
        $content .= "Disallow: /phpunit.xml\n";
        $content .= "Disallow: /webpack.mix.js\n";
        $content .= "Disallow: /vite.config.js\n";
        $content .= "Disallow: /tailwind.config.js\n";
        $content .= "Disallow: /postcss.config.js\n\n";
        
        // Блокируем временные и служебные файлы
        $content .= "# Блокируем временные и служебные файлы\n";
        $content .= "Disallow: /*.log\n";
        $content .= "Disallow: /*.tmp\n";
        $content .= "Disallow: /*~\n";
        $content .= "Disallow: /*.bak\n\n";
        
        // Блокируем поисковые параметры
        $content .= "# Блокируем поисковые параметры\n";
        $content .= "Disallow: /*?\n";
        $content .= "Disallow: /*&\n";
        $content .= "Disallow: /*=\n\n";
        
        // Разрешаем доступ к статическим ресурсам
        $content .= "# Разрешаем доступ к статическим ресурсам\n";
        $content .= "Allow: /css/\n";
        $content .= "Allow: /js/\n";
        $content .= "Allow: /images/\n";
        $content .= "Allow: /favicon.ico\n";
        $content .= "Allow: /robots.txt\n";
        $content .= "Allow: /sitemap.xml\n";
        $content .= "Allow: /sitemap.html\n\n";
        
        // Карта сайта
        $content .= "# Карта сайта\n";
        $content .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        return response($content)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}