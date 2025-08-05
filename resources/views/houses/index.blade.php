@push('header-scripts')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
@extends('layouts.app')

@section('title', 'Каталог деревянных домов от производителя | Деревянное домостроение')
@section('og_title', 'Каталог деревянных домов от производителя | Деревянное домостроение')
@section('og_description', '✅ Каталог готовых проектов деревянных домов от производителя. Дома из бруса под ключ с гарантией качества. Цены от застройщика без переплат. Бесплатная консультация!')
@section('meta_description', '✅ Каталог готовых проектов деревянных домов от производителя. Дома из бруса под ключ с гарантией качества. Цены от застройщика без переплат. Бесплатная консультация!')
@section('meta_keywords', 'деревянные дома, дома из бруса, каталог домов, купить дом, проекты домов, дома под ключ, строительство домов, цены на дома, готовые проекты, деревянное строительство')

@section('content')
<div class="bg-white py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Каталог деревянных домов от производителя</h1>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Фильтры и категории -->
            <div class="w-full md:w-1/4">
                <div class="bg-gray-50 rounded-lg p-6 shadow-sm">

                    <h2 class="text-xl font-semibold mt-8 mb-4">Сортировка</h2>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                               class="text-gray-700 hover:text-gray-900 {{ request('sort') == 'newest' || !request('sort') ? 'font-semibold' : '' }}">
                                По новизне
                            </a>
                        </li>
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}"
                               class="text-gray-700 hover:text-gray-900 {{ request('sort') == 'price_asc' ? 'font-semibold' : '' }}">
                                По цене (возрастание)
                            </a>
                        </li>
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}"
                               class="text-gray-700 hover:text-gray-900 {{ request('sort') == 'price_desc' ? 'font-semibold' : '' }}">
                                По цене (убывание)
                            </a>
                        </li>
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'area_asc']) }}"
                               class="text-gray-700 hover:text-gray-900 {{ request('sort') == 'area_asc' ? 'font-semibold' : '' }}">
                                По площади (возрастание)
                            </a>
                        </li>
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'area_desc']) }}"
                               class="text-gray-700 hover:text-gray-900 {{ request('sort') == 'area_desc' ? 'font-semibold' : '' }}">
                                По площади (убывание)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Список домов -->
            <div class="w-full md:w-3/4">
                @if($houses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($houses as $house)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1">
                                <a href="{{ route('house.show', $house->slug) }}">
                                    @if($house->hasMedia('main'))
                                        <img src="{{ $house->getFirstMediaUrl('main') }}" alt="{{ $house->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">Нет изображения</span>
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $house->title }}</h3>

                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-gray-600">{{ $house->area_total }} м²</span>
                                            <span class="text-gray-600">{{ $house->floor_count }} этаж{{ $house->floor_count > 1 ? 'а' : '' }}</span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            @if($house->old_price && $house->old_price > $house->price)
                                                <div>
                                                    <span class="text-gray-400 line-through text-sm">{{ number_format($house->old_price, 0, '.', ' ') }} ₽</span>
                                                    <span class="text-gray-900 font-bold block">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                                                </div>
                                            @else
                                                <span class="text-gray-900 font-bold">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                                            @endif

                                            <div class="flex flex-wrap gap-1">
                                                @foreach($house->categories as $category)
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $category->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $houses->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-8 text-center">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Дома не найдены</h3>
                        <p class="text-gray-600">По вашему запросу не найдено ни одного дома. Попробуйте изменить параметры фильтрации.</p>
                    </div>
                @endif
            </div>
        </div>
        <!-- SEO продающий текст -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6 mt-8 border border-green-100">
            <div class="max-w-4xl">
                <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                    <strong class="text-green-700">Выберите идеальный дом из нашего каталога готовых проектов!</strong> 
                    Мы предлагаем качественные деревянные дома из бруса с гарантией от производителя. 
                    Все проекты разработаны с учетом современных требований к комфорту и энергоэффективности.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Цены от застройщика без переплат</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Строительство под ключ за 3-6 месяцев</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Гарантия качества 5 лет</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
