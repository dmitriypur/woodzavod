@push('header-scripts')
    <meta property="og:description" content="Каталог деревянных домов от производителя Деревянное домостроение"/>
@endpush

@push('schema-org')
    @inject('schemaService', 'App\Services\SchemaOrgService')
    {!! $schemaService->generateItemList($houses, 'Каталог деревянных домов') !!}
    {!! $schemaService->generateBreadcrumbs([
        ['name' => 'Главная', 'url' => url('/')],
        ['name' => 'Каталог', 'url' => route('catalog')]
    ]) !!}
@endpush
@extends('layouts.app')

@section('title', 'Каталог домов')
@section('meta_description', 'Каталог деревянных домов от производителя Деревянное домостроение')
@section('meta_keywords', 'деревянные дома, дома из бруса, каталог домов, купить дом')

@section('content')
<div class="bg-white py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Каталог домов</h1>

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
                            
                            <x-house-card-2 :house="$house" :showSchema="true" />
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
    </div>
</div>
@endsection
