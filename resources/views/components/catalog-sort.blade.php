<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div class="text-gray-700 dark:text-gray-300">
        Найдено: <span class="font-semibold">{{ $houses->total() }}</span> {{ trans_choice('дом|дома|домов', $houses->total()) }}
    </div>
    
    <div class="flex items-center space-x-2">
        <span class="text-gray-700 dark:text-gray-300 text-sm">Сортировать:</span>
        
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" 
               class="px-4 py-2 text-sm font-medium {{ request('sort', 'newest') == 'newest' ? 'text-blue-700 bg-blue-100 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-900 bg-white dark:bg-gray-700 dark:text-white' }} border border-gray-200 dark:border-gray-600 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:hover:bg-gray-600">
                Новые
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" 
               class="px-4 py-2 text-sm font-medium {{ request('sort') == 'price_asc' ? 'text-blue-700 bg-blue-100 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-900 bg-white dark:bg-gray-700 dark:text-white' }} border-t border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:hover:bg-gray-600">
                Дешевле
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" 
               class="px-4 py-2 text-sm font-medium {{ request('sort') == 'price_desc' ? 'text-blue-700 bg-blue-100 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-900 bg-white dark:bg-gray-700 dark:text-white' }} border-t border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:hover:bg-gray-600">
                Дороже
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'area_asc']) }}" 
               class="px-4 py-2 text-sm font-medium {{ request('sort') == 'area_asc' ? 'text-blue-700 bg-blue-100 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-900 bg-white dark:bg-gray-700 dark:text-white' }} border-t border-b border-r border-gray-200 dark:border-gray-600 rounded-r-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:hover:bg-gray-600">
                По площади
            </a>
        </div>
    </div>
</div>