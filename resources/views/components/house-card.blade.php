<div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover group">
    <div class="relative overflow-hidden">
        @if($house->getFirstMedia('main'))
            <img src="{{ $house->getFirstMedia('main')->getUrl() }}" alt="{{ $house->title }}" class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-56 bg-gray-200 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
        @endif
        <div class="absolute top-4 left-4">
            <span class="bg-emerald-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                Новинка
            </span>
        </div>
        <div class="absolute top-4 right-4">
            <button class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors group">
                <svg class="w-5 h-5 text-gray-600 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>
        </div>
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent p-4">
            <div class="text-white text-right">
                <span class="text-2xl font-bold">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                <p class="text-sm opacity-90">под ключ</p>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($house->categories as $category)
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full border border-emerald-200">{{ $category->name }}</span>
            @endforeach
        </div>
        
        <a href="{{ route('houses.show', $house->slug) }}" class="block">
            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors">{{ $house->title }}</h3>
        </a>
        
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ $house->area_total }} м²</p>
                <p class="text-xs text-gray-500">площадь</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ $house->floor_count }}</p>
                <p class="text-xs text-gray-500">этажей</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ $house->timber_volume }} м³</p>
                <p class="text-xs text-gray-500">древесины</p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('houses.show', $house->slug) }}" class="flex-1 btn-primary text-center">
                Подробнее
            </a>
            <button class="btn-secondary px-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>