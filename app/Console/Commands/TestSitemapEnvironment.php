<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestSitemapEnvironment extends Command
{
    protected $signature = 'sitemap:test-env';
    protected $description = 'Test environment for sitemap generation';

    public function handle(): int
    {
        $this->info('Testing sitemap environment...');
        
        // Проверка APP_URL
        $appUrl = config('app.url');
        $this->line('APP_URL: ' . ($appUrl ?: 'NOT SET'));
        
        if (empty($appUrl)) {
            $this->error('❌ APP_URL is not set!');
        } else {
            $this->info('✅ APP_URL is set: ' . $appUrl);
        }
        
        // Проверка public директории
        $publicPath = public_path();
        $this->line('Public path: ' . $publicPath);
        
        if (!is_dir($publicPath)) {
            $this->error('❌ Public directory does not exist!');
            return Command::FAILURE;
        } else {
            $this->info('✅ Public directory exists');
        }
        
        // Проверка прав на запись
        if (!is_writable($publicPath)) {
            $this->error('❌ Public directory is not writable!');
            $this->line('Directory permissions: ' . substr(sprintf('%o', fileperms($publicPath)), -4));
        } else {
            $this->info('✅ Public directory is writable');
        }
        
        // Проверка существующих файлов
        $sitemapXml = public_path('sitemap.xml');
        $sitemapHtml = public_path('sitemap.html');
        
        $this->line('Sitemap XML exists: ' . (file_exists($sitemapXml) ? 'YES' : 'NO'));
        $this->line('Sitemap HTML exists: ' . (file_exists($sitemapHtml) ? 'YES' : 'NO'));
        
        // Проверка базы данных
        try {
            $pagesCount = \App\Models\Page::where('is_published', true)->count();
            $housesCount = \App\Models\House::where('is_published', true)->count();
            $categoriesCount = \App\Models\Category::count();
            
            $this->info('✅ Database connection OK');
            $this->line('Published pages: ' . $pagesCount);
            $this->line('Published houses: ' . $housesCount);
            $this->line('Categories: ' . $categoriesCount);
        } catch (\Exception $e) {
            $this->error('❌ Database error: ' . $e->getMessage());
        }
        
        // Проверка view файлов
        $xmlViewPath = resource_path('views/sitemap/xml.blade.php');
        $htmlViewPath = resource_path('views/sitemap/html.blade.php');
        
        $this->line('XML view exists: ' . (file_exists($xmlViewPath) ? 'YES' : 'NO'));
        $this->line('HTML view exists: ' . (file_exists($htmlViewPath) ? 'YES' : 'NO'));
        
        // Тест создания простого файла
        try {
            $testFile = public_path('test_write.txt');
            File::put($testFile, 'test content');
            
            if (file_exists($testFile)) {
                $this->info('✅ File write test successful');
                File::delete($testFile);
            } else {
                $this->error('❌ File write test failed - file not created');
            }
        } catch (\Exception $e) {
            $this->error('❌ File write test failed: ' . $e->getMessage());
        }
        
        $this->info('Environment test completed.');
        return Command::SUCCESS;
    }
}