<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Информация о статусе карты сайта -->
        <x-filament::section>
            <x-slot name="heading">
                📊 Статус файлов карты сайта
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $status = $this->getSitemapStatus();
                @endphp
                
                <!-- XML Sitemap -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-3 h-3 rounded-full mr-3 {{ $status['xml']['exists'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <h3 class="text-lg font-semibold">sitemap.xml</h3>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Статус:</span>
                            <span class="{{ $status['xml']['exists'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $status['xml']['exists'] ? '✅ Существует' : '❌ Не найден' }}
                            </span>
                        </div>
                        
                        @if($status['xml']['exists'])
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Изменен:</span>
                                <span>{{ $status['xml']['last_modified'] }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Размер:</span>
                                <span>{{ $status['xml']['size'] }} KB</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($status['xml']['exists'])
                        <div class="mt-4">
                            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-primary-600 hover:text-primary-500 text-sm">
                                🔗 Открыть файл
                            </a>
                        </div>
                    @endif
                </div>
                
                <!-- HTML Sitemap -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-3 h-3 rounded-full mr-3 {{ $status['html']['exists'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <h3 class="text-lg font-semibold">sitemap.html</h3>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Статус:</span>
                            <span class="{{ $status['html']['exists'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $status['html']['exists'] ? '✅ Существует' : '❌ Не найден' }}
                            </span>
                        </div>
                        
                        @if($status['html']['exists'])
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Изменен:</span>
                                <span>{{ $status['html']['last_modified'] }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Размер:</span>
                                <span>{{ $status['html']['size'] }} KB</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($status['html']['exists'])
                        <div class="mt-4">
                            <a href="{{ url('/sitemap.html') }}" target="_blank" class="text-primary-600 hover:text-primary-500 text-sm">
                                🔗 Открыть файл
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::section>
        
        <!-- Информация об автоматическом обновлении -->
        <x-filament::section>
            <x-slot name="heading">
                ⚙️ Автоматическое обновление
            </x-slot>
            
            <div class="prose dark:prose-invert max-w-none">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Карта сайта автоматически обновляется при:
                </p>
                
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>• Создании, редактировании или удалении домов</li>
                    <li>• Создании, редактировании или удалении страниц</li>
                    <li>• Создании, редактировании или удалении категорий</li>
                </ul>
                
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        💡 <strong>Совет:</strong> Если карта сайта не обновляется автоматически, используйте кнопку "Регенерировать карту сайта" выше.
                    </p>
                </div>
            </div>
        </x-filament::section>
        
        <!-- Консольные команды -->
        <x-filament::section>
            <x-slot name="heading">
                🖥️ Консольные команды
            </x-slot>
            
            <div class="space-y-4">
                <div class="bg-gray-900 rounded-lg p-4">
                    <p class="text-sm text-gray-400 mb-2">Ручная регенерация через консоль:</p>
                    <code class="text-green-400 text-sm">php artisan sitemap:generate --force</code>
                </div>
                
                <div class="bg-gray-900 rounded-lg p-4">
                    <p class="text-sm text-gray-400 mb-2">Просмотр логов:</p>
                    <code class="text-green-400 text-sm">tail -f storage/logs/laravel.log | grep sitemap</code>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>