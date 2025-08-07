<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\House;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--force : Force regeneration even if files exist}';
    protected $description = 'Generate XML and HTML sitemaps';

    public function handle(): int
    {
        $this->info('Generating sitemaps...');

        try {
            // Проверяем базовые требования
            $this->checkRequirements();
            
            $this->generateXmlSitemap();
            $this->generateHtmlSitemap();
            
            $this->info('Sitemaps generated successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generating sitemaps: ' . $e->getMessage());
            \Log::error('Sitemap generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'app_url' => config('app.url'),
                'public_path' => public_path(),
                'timestamp' => now()
            ]);
            return Command::FAILURE;
        }
    }
    
    private function checkRequirements(): void
    {
        // Проверяем APP_URL
        if (empty(config('app.url'))) {
            throw new \Exception('APP_URL is not set in environment');
        }
        
        // Проверяем права на запись в public
        if (!is_writable(public_path())) {
            throw new \Exception('Public directory is not writable: ' . public_path());
        }
        
        $this->line('✓ Requirements check passed');
    }

    private function generateXmlSitemap(): void
    {
        try {
            $urls = collect();
            $baseUrl = rtrim(config('app.url'), '/');

            // Главная страница
            $urls->push([
                'url' => $baseUrl . '/',
                'lastmod' => Carbon::now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ]);

            // Каталог
            $lastHouseUpdate = House::where('is_published', true)->max('updated_at');
            $urls->push([
                'url' => $baseUrl . '/catalog',
                'lastmod' => $lastHouseUpdate ? Carbon::parse($lastHouseUpdate)->toISOString() : Carbon::now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ]);

            // Страницы
            Page::where('is_published', true)->each(function ($page) use ($urls, $baseUrl) {
                $urls->push([
                    'url' => $baseUrl . '/' . $page->slug,
                    'lastmod' => $page->updated_at->toISOString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ]);
            });

            // Дома
            House::where('is_published', true)->each(function ($house) use ($urls, $baseUrl) {
                $urls->push([
                    'url' => $baseUrl . '/catalog/' . $house->slug,
                    'lastmod' => $house->updated_at->toISOString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7'
                ]);
            });

            $this->line('✓ Collected ' . $urls->count() . ' URLs for XML sitemap');
            
            $xml = view('sitemap.xml', compact('urls'))->render();
            File::put(public_path('sitemap.xml'), $xml);
            
            $this->line('✓ XML sitemap generated: ' . public_path('sitemap.xml'));
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate XML sitemap: ' . $e->getMessage());
        }
    }

    private function generateHtmlSitemap(): void
    {
        try {
            $pages = Page::where('is_published', true)
                ->orderBy('title')
                ->get();

            $houses = House::where('is_published', true)
                ->orderBy('title')
                ->get();

            $categories = Category::orderBy('name')->get();
            
            $this->line('✓ Collected data: ' . $pages->count() . ' pages, ' . $houses->count() . ' houses, ' . $categories->count() . ' categories');

            $html = view('sitemap.html', compact('pages', 'houses', 'categories'))->render();
            File::put(public_path('sitemap.html'), $html);
            
            $this->line('✓ HTML sitemap generated: ' . public_path('sitemap.html'));
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate HTML sitemap: ' . $e->getMessage());
        }
    }
}