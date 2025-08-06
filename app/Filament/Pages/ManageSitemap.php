<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ManageSitemap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    
    protected static string $view = 'filament.pages.manage-sitemap';
    
    protected static ?string $navigationGroup = 'Настройки';
    
    protected static ?string $navigationLabel = 'Карта сайта';
    
    protected static ?string $title = 'Управление картой сайта';
    
    protected static ?int $navigationSort = 8;
    
    public function getSitemapStatus(): array
    {
        $sitemapXmlPath = public_path('sitemap.xml');
        $sitemapHtmlPath = public_path('sitemap.html');
        
        return [
            'xml' => [
                'exists' => file_exists($sitemapXmlPath),
                'last_modified' => file_exists($sitemapXmlPath) ? date('d.m.Y H:i:s', filemtime($sitemapXmlPath)) : null,
                'size' => file_exists($sitemapXmlPath) ? round(filesize($sitemapXmlPath) / 1024, 1) : null
            ],
            'html' => [
                'exists' => file_exists($sitemapHtmlPath),
                'last_modified' => file_exists($sitemapHtmlPath) ? date('d.m.Y H:i:s', filemtime($sitemapHtmlPath)) : null,
                'size' => file_exists($sitemapHtmlPath) ? round(filesize($sitemapHtmlPath) / 1024, 1) : null
            ]
        ];
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerate')
                ->label('🔄 Регенерировать карту сайта')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Регенерация карты сайта')
                ->modalDescription('Вы уверены, что хотите регенерировать карту сайта? Это может занять несколько секунд.')
                ->modalSubmitActionLabel('Да, регенерировать')
                ->action(function () {
                    try {
                        // Очищаем кеш карт сайта
                        Cache::forget('sitemap_' . md5('/sitemap.html'));
                        Cache::forget('sitemap_' . md5('/sitemap.xml'));
                        Cache::forget('sitemap_' . md5('/robots.txt'));
                        
                        // Запускаем команду генерации карты сайта
                        $exitCode = Artisan::call('sitemap:generate', ['--force' => true]);
                        
                        if ($exitCode === 0) {
                            Log::info('Manual sitemap regeneration successful from Filament admin', [
                                'user_id' => auth()->id(),
                                'timestamp' => now()
                            ]);
                            
                            Notification::make()
                                ->title('Успешно!')
                                ->body('Карта сайта успешно регенерирована')
                                ->success()
                                ->send();
                        } else {
                            throw new \Exception('Artisan command failed with exit code: ' . $exitCode);
                        }
                        
                    } catch (\Exception $e) {
                        Log::error('Manual sitemap regeneration failed from Filament admin', [
                            'error' => $e->getMessage(),
                            'user_id' => auth()->id(),
                            'timestamp' => now()
                        ]);
                        
                        Notification::make()
                            ->title('Ошибка!')
                            ->body('Не удалось регенерировать карту сайта: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('clear_cache')
                ->label('🗑️ Очистить кеш')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Очистка кеша карты сайта')
                ->modalDescription('Вы уверены, что хотите очистить кеш карты сайта?')
                ->modalSubmitActionLabel('Да, очистить')
                ->action(function () {
                    try {
                        Cache::forget('sitemap_' . md5('/sitemap.html'));
                        Cache::forget('sitemap_' . md5('/sitemap.xml'));
                        Cache::forget('sitemap_' . md5('/robots.txt'));
                        
                        Log::info('Sitemap cache cleared from Filament admin', [
                            'user_id' => auth()->id(),
                            'timestamp' => now()
                        ]);
                        
                        Notification::make()
                            ->title('Успешно!')
                            ->body('Кеш карты сайта очищен')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Log::error('Failed to clear sitemap cache from Filament admin', [
                            'error' => $e->getMessage(),
                            'user_id' => auth()->id(),
                            'timestamp' => now()
                        ]);
                        
                        Notification::make()
                            ->title('Ошибка!')
                            ->body('Не удалось очистить кеш: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
        ];
    }
}