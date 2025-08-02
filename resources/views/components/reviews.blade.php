@if(count($reviews) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Отзывы клиентов</h3>
        
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $review->author }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('d.m.Y') }}</div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300">{{ $review->text }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endif