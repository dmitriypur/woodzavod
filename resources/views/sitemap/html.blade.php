@extends('layouts.app')

@section('title', 'Карта сайта')
@section('meta_description', 'Карта сайта Деревянное домостроение - все страницы, каталог домов и категории')

@push('schema-org-footer')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Деревянное домостроение",
    "url": "{{ url('/') }}",
    "description": "Карта сайта Деревянное домостроение - все страницы, каталог домов и категории",
    "mainEntity": {
        "@type": "SiteNavigationElement",
        "name": "Карта сайта"
    }
}
</script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8" itemscope itemtype="https://schema.org/SiteNavigationElement">
    <h1 class="text-3xl font-bold text-gray-900 mb-8" itemprop="name">Карта сайта</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Основные страницы -->
        <div class="bg-white rounded-lg shadow-md p-6" itemscope itemtype="https://schema.org/ItemList">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2" itemprop="name">
                <i class="fas fa-home mr-2 text-blue-600"></i>
                Основные страницы
            </h2>
            <ul class="space-y-2">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <meta itemprop="position" content="1">
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors" itemprop="item">
                        <span itemprop="name">Главная страница</span>
                    </a>
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <meta itemprop="position" content="2">
                    <a href="{{ route('catalog') }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors" itemprop="item">
                        <span itemprop="name">Каталог домов</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Статические страницы -->
        @if($pages->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6" itemscope itemtype="https://schema.org/ItemList">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2" itemprop="name">
                <i class="fas fa-file-alt mr-2 text-green-600"></i>
                Информационные страницы
            </h2>
            <ul class="space-y-2">
                @foreach($pages as $index => $page)
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <meta itemprop="position" content="{{ $index + 1 }}">
                    <a href="{{ route('page.show', $page->slug) }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors" itemprop="item">
                        <span itemprop="name">{{ $page->title }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Категории -->
        @if($categories->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-tags mr-2 text-purple-600"></i>
                Категории
            </h2>
            <ul class="space-y-2">
                @foreach($categories as $category)
                <li class="flex items-center">
                    @if($category->parent_id)
                        <span class="text-gray-400 mr-2">└─</span>
                    @endif
                    <span class="text-gray-700">{{ $category->name }}</span>
                    @if($category->houses->count() > 0)
                        <span class="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">
                            {{ $category->houses->count() }}
                        </span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Каталог домов -->
    @if($houses->count() > 0)
    <div class="mt-8 bg-white rounded-lg shadow-md p-6" itemscope itemtype="https://schema.org/ItemList">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2" itemprop="name">
            <i class="fas fa-building mr-2 text-orange-600"></i>
            Каталог домов ({{ $houses->count() }} проектов)
        </h2>
        <meta itemprop="numberOfItems" content="{{ $houses->count() }}">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($houses as $index => $house)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <meta itemprop="position" content="{{ $index + 1 }}">
                <a href="{{ route('house.show', $house->slug) }}" class="block" itemprop="item" itemscope itemtype="https://schema.org/Product">
                    <h3 class="font-medium text-gray-900 hover:text-blue-600 transition-colors" itemprop="name">
                        {{ $house->title }}
                    </h3>
                    <meta itemprop="url" content="{{ route('house.show', $house->slug) }}">
                    @if($house->subtitle)
                        <p class="text-sm text-gray-600 mt-1">{{ $house->subtitle }}</p>
                    @endif
                    <div class="mt-2 text-xs text-gray-500">
                        @if($house->area_total)
                            <span class="mr-3">{{ $house->area_total }} м²</span>
                        @endif
                        @if($house->price)
                            <span class="font-medium text-green-600">{{ number_format($house->price, 0, ',', ' ') }} ₽</span>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Дополнительная информация -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-3">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
            О карте сайта
        </h3>
        <p class="text-gray-600 text-sm leading-relaxed">
            Данная карта сайта содержит все доступные страницы нашего сайта. 
            Карта автоматически обновляется при добавлении новых страниц, домов или категорий.
            Последнее обновление: {{ now()->format('d.m.Y H:i') }}
        </p>
        <div class="mt-4">
            <a href="{{ route('sitemap.xml') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Скачать XML карту сайта
            </a>
        </div>
    </div>
</div>
@endsection