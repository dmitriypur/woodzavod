<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Фильтры</h3>
    
    <form action="{{ route('houses.index') }}" method="GET" class="space-y-4">
        @if(request()->has('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        
        <div>
            <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Категория</label>
            <select id="category" name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Все категории</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="price_from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Цена от</label>
                <input type="number" id="price_from" name="price_from" value="{{ request('price_from') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="От">
            </div>
            
            <div>
                <label for="price_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Цена до</label>
                <input type="number" id="price_to" name="price_to" value="{{ request('price_to') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="До">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="area_from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Площадь от</label>
                <input type="number" id="area_from" name="area_from" value="{{ request('area_from') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="От">
            </div>
            
            <div>
                <label for="area_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Площадь до</label>
                <input type="number" id="area_to" name="area_to" value="{{ request('area_to') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="До">
            </div>
        </div>
        
        <div>
            <label for="floors" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Количество этажей</label>
            <select id="floors" name="floors" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Любое количество</option>
                <option value="1" {{ request('floors') == '1' ? 'selected' : '' }}>1 этаж</option>
                <option value="2" {{ request('floors') == '2' ? 'selected' : '' }}>2 этажа</option>
                <option value="3" {{ request('floors') == '3' ? 'selected' : '' }}>3 этажа</option>
            </select>
        </div>
        
        <div class="flex space-x-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Применить</button>
            
            <a href="{{ route('houses.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Сбросить</a>
        </div>
    </form>
</div>