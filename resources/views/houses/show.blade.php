@push('header-scripts')
    @if(isset($house->seo['canonical']) && $house->seo['canonical'] !== '')
        <link rel="canonical"
              href="{{ url($house->seo['canonical']) }}">
    @else
        <link rel="canonical"
              href="{{ url()->current() }}">
    @endif

    @if(isset($house->seo['noindex']) && !!$house->seo['noindex'])
        <meta name="robots" content="noindex">
    @endif
    @if(isset($house->seo['description']) && !!$house->seo['description'])
        <meta property="og:description" content="{{ $description }}"/>
    @endif

@endpush
@extends('layouts.app')

@section('title', \App\Helpers\SettingsHelper::replaceVariables($house->seo['title'] ?? $house->title))
@section('meta_description', Str::limit(strip_tags(\App\Helpers\SettingsHelper::replaceVariables($house->seo['description'] ?? $house->content)), 160))

@section('content')

<div class="bg-white py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Хлебные крошки -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900">
                        Главная
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('catalog') }}" class="text-gray-600 hover:text-gray-900 ml-1 md:ml-2">
                            Каталог
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{!! \App\Helpers\SettingsHelper::replaceVariables($house->title) !!}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Заголовок и категории -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{!! \App\Helpers\SettingsHelper::replaceVariables($house->title) !!}</h1>
            <div class="flex flex-wrap gap-2">
                @foreach($house->categories as $category)
                    <a href="{{ route('catalog', ['category' => $category->slug]) }}" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-200">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Галерея изображений -->
            <div class="lg:col-span-2">
                @if($house->getMedia('gallery')->count() > 2)
                    <!-- Swiper слайдер для галереи -->
                    <div class="swiper house-gallery-swiper mb-6">
                        <div class="swiper-wrapper">
                            @foreach($house->getMedia('gallery') as $media)
                                <div class="swiper-slide">
                                    <div class="bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="{{ $media->getUrl() }}" alt="{{ $house->title }} - изображение {{ $loop->iteration }}" class="w-full h-auto object-cover">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next !text-secondary !bg-white rounded-full !w-10 !h-10 px-2 after:!text-2xl shadow-lg"></div>
                        <div class="swiper-button-prev !text-secondary !bg-white rounded-full !w-10 !h-10 px-2 after:!text-2xl shadow-lg"></div>
                    </div>
                @else
                    <!-- Одно изображение без слайдера -->
                    <div class="bg-gray-100 rounded-lg overflow-hidden mb-6">
                        @if($house->hasMedia('main'))
                            <img src="{{ $house->getFirstMediaUrl('main') }}" alt="{{ $house->title }}" class="w-full h-auto object-cover">
                        @else
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Нет изображения</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Описание -->
                <div class="mt-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Описание</h2>
                    <div class="prose max-w-none">
                        {!! \App\Helpers\SettingsHelper::replaceVariables($house->description) !!}
                    </div>
                </div>
            </div>

            <!-- Информация о доме и форма заявки -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-lg p-6 shadow-sm mb-6">
                    <div class="flex justify-between items-center mb-4">
                        @if($house->old_price && $house->old_price > $house->price)
                            <div>
                                <span class="text-gray-400 line-through text-lg">{{ number_format($house->old_price, 0, '.', ' ') }} ₽</span>
                                <span class="text-gray-900 font-bold text-2xl block">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                            </div>
                        @else
                            <span class="text-gray-900 font-bold text-2xl">{{ number_format($house->price, 0, '.', ' ') }} ₽</span>
                        @endif
                    </div>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Характеристики</h3>
                    <ul class="space-y-2">
                        <li class="flex justify-between">
                            <span class="text-gray-600">Общая площадь:</span>
                            <span class="font-medium">{{ $house->area_total }} м²</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Количество этажей:</span>
                            <span class="font-medium">{{ $house->floor_count }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Объем бруса:</span>
                            <span class="font-medium">{{ $house->timber_volume }} м³</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Спальни:</span>
                            <span class="font-medium">{{ $house->bedroom_count }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Санузлы:</span>
                            <span class="font-medium">{{ $house->bathroom_count }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Форма заявки -->
                <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Оставить заявку</h3>
                    <form id="modal-form" class="modal-form space-y-4">
                        <input type="hidden" name="house_id" value="{{ $house->id }}">

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ваше имя <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" data-required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" data-required class="phone-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Сообщение</label>
                            <textarea name="message" id="message" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div class="form-alert hidden text-sm mt-2"></div>
                        <label class="flex items-start space-x-3 text-[10px] mt-4 leading-snug text-black">
                            <input
                                type="checkbox"
                                class="peer sr-only"
                                name="agree"
                                required
                                checked
                            />
                            <div
                                class="w-5 h-5 border border-black rounded-md flex-shrink-0 relative flex items-center justify-center transition-all duration-200 peer-checked:[&_svg]:opacity-100"
                            >
                                <!-- Галочка -->
                                <svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    class="absolute w-3 h-3 text-black opacity-0  transition-opacity duration-200">
                                    <path d="M1 4.99998L4.53553 8.53551L11.6058 1.46442" stroke="black" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <span>
                            Я даю согласие на обработку персональных данных и соглашаюсь с <a href="/policy" target="_blank" class="underline">политикой конфиденциальности</a>
                        </span>
                        </label>

                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Отправить заявку
                        </button>

                    </form>
                </div>
            </div>
        </div>

        <!-- Отзывы -->
        @if($reviews->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Отзывы</h2>
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $review->author }}</h4>
                                    <span class="text-gray-500 text-sm">{{ $review->created_at->format('d.m.Y') }}</span>
                                </div>
                            </div>
                            <p class="text-gray-700">{{ $review->text }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Похожие дома -->
        @if($similarHouses->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Похожие дома</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($similarHouses as $similarHouse)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1">
                            <a href="{{ route('house.show', $similarHouse->slug) }}">
                                @if($similarHouse->hasMedia('main'))
                                    <img src="{{ $similarHouse->getFirstMediaUrl('main') }}" alt="{{ $similarHouse->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">Нет изображения</span>
                                    </div>
                                @endif

                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $similarHouse->title }}</h3>

                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">{{ $similarHouse->area_total }} м²</span>
                                        <span class="text-gray-600">{{ $similarHouse->floor_count }} этаж{{ $similarHouse->floor_count > 1 ? 'а' : '' }}</span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-900 font-bold">{{ number_format($similarHouse->price, 0, '.', ' ') }} ₽</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Микроразметка Schema.org для товара -->
<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{ $house->title }}",
    "description": "{{ strip_tags($house->description) }}",
    @if($house->hasMedia('main'))
    "image": "{{ $house->getFirstMediaUrl('main') }}",
    @endif
    "offers": {
        "@type": "Offer",
        "priceCurrency": "RUB",
        "price": "{{ $house->price }}",
        "availability": "https://schema.org/InStock"
    },
    "brand": {
        "@type": "Brand",
        "name": "WoodZavod"
    }
}
</script>



@endsection
