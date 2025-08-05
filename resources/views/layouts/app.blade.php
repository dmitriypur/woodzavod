<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $settings->site_name)</title>

    <!-- Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Деревянные дома от производителя')">
    <meta name="keywords" content="@yield('meta_keywords', 'деревянные дома, дома из бруса, экологичное жилье')">
    <meta property="og:image" content="{{ url('images/logo.png') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="@yield('og_title', 'Деревянные дома от производителя')"/>
    <meta property="og:description" content="@yield('og_description', '✅ Каталог готовых проектов деревянных домов от производителя. Дома из бруса под ключ с гарантией качества. Цены от застройщика без переплат. Бесплатная консультация!')"/>
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:site_name" content="Деревянное домостроение"/>

    <meta name="twitter:title" content="@yield('og_title', 'Деревянные дома от производителя')"/>
    <meta name="twitter:description" content="@yield('og_description', '✅ Каталог готовых проектов деревянных домов от производителя. Дома из бруса под ключ с гарантией качества. Цены от застройщика без переплат. Бесплатная консультация!')"/>

    @if ($settings->favicon)
        <link rel="icon" type="{{ $settings->faviconMimeType() }}"
              href="{{ 'storage/'. $settings->favicon }}">
    @else
        <link rel="icon" type="image/png" sizes="32x32"
              href="{{ asset('icon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16"
              href="{{ asset('icon/favicon-16x16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180"
              href="{{ asset('icon/apple-touch-icon.png') }}">
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Evolventa:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('header-scripts')
</head>
<body class="font-montserrat text-dark bg-white-custom">
<!-- Header -->
<header class="fixed top-0 left-0 right-0 bg-white-custom/95 z-50 py-4 transition-all duration-300" id="header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex items-center justify-between">

            @if(Request::is('/'))
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="Деревянное домостроение" class="h-10 w-auto">
                    <span class="hidden lg:block font-evolventa font-semibold text-secondary text-lg">{{ $settings->site_name }}</span>
                </div>
            @else
                <a href="{{ route('home') }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.svg') }}" alt="Деревянное домостроение" class="h-10 w-auto">
                        <span class="font-evolventa font-semibold text-secondary text-lg">{{ $settings->site_name }}</span>
                    </div>
                </a>
            @endif
            <nav class="hidden md:block absolute md:relative top-full left-0 right-0 bg-white md:bg-transparent">
                <ul class="flex flex-col md:flex-row gap-5 md:gap-8 p-4 md:p-0">
                    <li><a href="{{ route('home') }}" class="text-dark font-medium hover:text-primary transition-colors">Главная</a>
                    </li>
                    <li><a href="/about" class="text-dark font-medium hover:text-primary transition-colors">О нас</a>
                    </li>
                    <li><a href="{{ route('catalog') }}"
                           class="text-dark font-medium hover:text-primary transition-colors">Каталог</a></li>
                    <li><a href="/contact"
                           class="text-dark font-medium hover:text-primary transition-colors">Контакты</a></li>
                </ul>
            </nav>
            <div class="hidden md:block">
                <a href="tel:{{ App\Helpers\SettingsHelper::phoneDigitsOnly($settings->phone) }}" class="text-secondary font-semibold text-lg">{{ $settings->phone }}</a>
            </div>
            <button class="md:hidden flex flex-col gap-1" id="burger">
                <span class="w-6 h-0.5 bg-secondary transition-all"></span>
                <span class="w-6 h-0.5 bg-secondary transition-all"></span>
                <span class="w-6 h-0.5 bg-secondary transition-all"></span>
            </button>
        </div>
    </div>
</header>

@yield('content')

<!-- Footer -->
<footer class="bg-secondary text-white-custom py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 mb-8">
            <div>

                @if(Request::is('/'))
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.svg') }}" alt="Деревянное домостроение" class="h-10 w-auto">
                        <span class="font-evolventa font-semibold text-lg">{{ $settings->site_name }}</span>
                    </div>
                @else
                    <a href="{{ route('home') }}">
                        <div class="flex items-center gap-3 mb-4">
                            <img src="{{ asset('images/logo.svg') }}" alt="Деревянное домостроение" class="h-10 w-auto">
                            <span class="font-evolventa font-semibold text-lg">{{ $settings->site_name }}</span>
                        </div>
                    </a>
                @endif

                <p class="leading-relaxed opacity-80">
                    Строим качественные деревянные дома для комфортной жизни. Натуральные материалы, современные
                    технологии.
                </p>
            </div>
            <div>
                <h4 class="font-evolventa text-primary mb-4">Меню</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}"
                           class="opacity-80 hover:opacity-100 hover:text-primary transition-all">Главная</a></li>
                    <li><a href="/about" class="opacity-80 hover:opacity-100 hover:text-primary transition-all">О
                            нас</a></li>
                    <li><a href="{{ route('catalog') }}" class="opacity-80 hover:opacity-100 hover:text-primary transition-all">Каталог</a>
                    </li>
                    <li><a href="/contact" class="opacity-80 hover:opacity-100 hover:text-primary transition-all">Контакты</a>
                    </li>
                    <li><a href="/policy" class="opacity-80 hover:opacity-100 hover:text-primary transition-all">Политика
                            конфиденциальности</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-evolventa text-primary mb-4">Контакты</h4>
                <div class="space-y-2">
                    <a href="tel:{{ App\Helpers\SettingsHelper::phoneDigitsOnly($settings->phone) }}" class="block opacity-80 hover:opacity-100 transition-opacity">{{ $settings->phone }}</a>
                    <a href="mailto:{{ $settings->email }}" class="block opacity-80 hover:opacity-100 transition-opacity">{{ $settings->email }}</a>
                    <span class="block opacity-80">{{ $settings->address }}</span>
                </div>
            </div>

        </div>
        <div class="text-center pt-8 border-t border-white/10 opacity-60">
            <p>&copy; {{ date('Y') }} {{ $settings->site_name }}. Все права защищены.</p>
        </div>
    </div>
</footer>

<x-form1></x-form1>


<div id="modal" class="fixed inset-0 z-50 bg-black/50 flex items-start justify-center hidden">
    <div class="relative">
        <button id="modal-close" class="absolute top-14 right-8 z-20">✖</button>
        <div id="modal-content" class="px-2"></div>
    </div>
</div>

</body>
</html>
