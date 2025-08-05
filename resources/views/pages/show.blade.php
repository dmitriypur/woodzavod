@push('header-scripts')
    @if(isset($page->seo['canonical']) && $page->seo['canonical'] !== '')
        <link rel="canonical"
              href="{{ url($page->seo['canonical']) }}">
    @else
        <link rel="canonical"
              href="{{ url()->current() }}">
    @endif

    @if(isset($page->seo['noindex']) && !!$page->seo['noindex'])
        <meta name="robots" content="noindex">
    @endif
@endpush
@extends('layouts.app')

@section('title', \App\Helpers\SettingsHelper::replaceVariables($page->seo['title'] ?? $page->title))
@section('meta_description', Str::limit(strip_tags(\App\Helpers\SettingsHelper::replaceVariables($page->seo['description'] ?? $page->content)), 160))
@section('og_title', \App\Helpers\SettingsHelper::replaceVariables($page->seo['title'] ?? $page->title))
@section('og_description', \App\Helpers\SettingsHelper::replaceVariables($page->seo['description'] ?? ''))

@section('content')
<div class="bg-white pt-40 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Хлебные крошки -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900">
                        Главная
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{!! \App\Helpers\SettingsHelper::replaceVariables($page->title) !!}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Заголовок страницы -->
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{!! \App\Helpers\SettingsHelper::replaceVariables($page->title) !!}</h1>

        <!-- Содержимое страницы -->
        <div class="prose max-w-none space-y-4 [&_h2]:text-xl [&_h2]:text-gray-800 [&_h3]:text-gray-800 [&_h3]:text-lg [&_h2]:font-semibold [&_h3]:font-semibold text-gray-600 [&_ul]:list-disc [&_ul]:pl-4">
            {!! \App\Helpers\SettingsHelper::replaceVariables($page->content) !!}
            @if($page->slug === 'contact')
            <div class="w-full mt-10">
                <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Ad003ce172ea60d49c68d00756840f1bc0476ef950b8fb3c7f5f83965d44614d6&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=false"></script>
            </div>
            @endif
        </div>

        <!-- Если это страница контактов, добавляем форму обратной связи -->
        @if($page->slug === 'contacts')
            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Связаться с нами</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Контактная информация</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <span class="block font-medium text-gray-900">Адрес</span>
                                        <span class="text-gray-600">г. Москва, ул. Примерная, д. 123</span>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <div>
                                        <span class="block font-medium text-gray-900">Телефон</span>
                                        <a href="tel:+7XXXXXXXXXX" class="text-gray-600 hover:text-gray-900">+7 (XXX) XXX-XX-XX</a>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <span class="block font-medium text-gray-900">Email</span>
                                        <a href="mailto:info@woodzavod.ru" class="text-gray-600 hover:text-gray-900">info@woodzavod.ru</a>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <span class="block font-medium text-gray-900">Режим работы</span>
                                        <span class="text-gray-600">Пн-Пт: 9:00 - 18:00<br>Сб-Вс: выходной</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Отправить сообщение</h3>
                            <form action="{{ route('leads.store') }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ваше имя</label>
                                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                                    <input type="tel" name="phone" id="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Сообщение</label>
                                    <textarea name="message" id="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Отправить сообщение
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
