<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Характеристики</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($house->area_total)
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Общая площадь: <strong>{{ $house->area_total }} м²</strong></span>
            </div>
        @endif
        
        @if($house->area_living)
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Жилая площадь: <strong>{{ $house->area_living }} м²</strong></span>
            </div>
        @endif
        
        @if($house->floor_count)
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Этажей: <strong>{{ $house->floor_count }}</strong></span>
            </div>
        @endif
        
        @if($house->timber_volume)
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Объем бруса: <strong>{{ $house->timber_volume }} м³</strong></span>
            </div>
        @endif
        
        @if($house->bedroom_count)
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Спален: <strong>{{ $house->bedroom_count }}</strong></span>
            </div>
        @endif
        
        @if($house->bathroom_count)
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Санузлов: <strong>{{ $house->bathroom_count }}</strong></span>
            </div>
        @endif
    </div>
</div>