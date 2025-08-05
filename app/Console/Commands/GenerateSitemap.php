<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\House;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--force : Force regeneration even if files exist}';
    protected $description = 'Generate XML and HTML sitemaps';

    public function handle(): int
    {
        $this->info('Generating sitemaps...');

        try {
            $this->generateXmlSitemap();
            $this->generateHtmlSitemap();
            
            $this->info('Sitemaps generated successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generating sitemaps: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function generateXmlSitemap(): void
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

        $xml = view('sitemap.xml', compact('urls'))->render();
        File::put(public_path('sitemap.xml'), $xml);
        
        $this->line('✓ XML sitemap generated: ' . public_path('sitemap.xml'));
    }

    private function generateHtmlSitemap(): void
    {
        $pages = Page::where('is_published', true)
            ->orderBy('title')
            ->get();

        $houses = House::where('is_published', true)
            ->orderBy('title')
            ->get();

        $categories = Category::orderBy('name')->get();

        $html = view('sitemap.html', compact('pages', 'houses', 'categories'))->render();
        File::put(public_path('sitemap.html'), $html);
        
        $this->line('✓ HTML sitemap generated: ' . public_path('sitemap.html'));
    }
}