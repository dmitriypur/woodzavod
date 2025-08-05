@push('header-scripts')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
@extends('layouts.app')

@section('title', 'Главная')
@section('meta_description', 'Деревянные дома от производителя ДЕРЕВЯННОЕ ДОМОСТРОЕНИЕ — каталог, отзывы, контакты, доставка и оплата.')
@section('meta_keywords', 'деревянные дома, дома из бруса, строительство, отзывы, контакты, доставка, оплата')

@section('content')
    <!-- Hero Section -->
    <section class="h-screen relative flex items-center justify-center text-center text-white-custom overflow-hidden hero-bg" id="home">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <h1 class="font-evolventa text-4xl md:text-6xl font-bold mb-4">ДЕРЕВЯННОЕ ДОМОСТРОЕНИЕ</h1>
            <p class="text-xl md:text-2xl mb-10 opacity-90">Современные дома для комфортной жизни</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button data-modal-target="form1" class="open-modal-btn bg-primary hover:bg-primary-dark text-white-custom px-6 py-3 rounded-lg font-medium transition-all hover:-translate-y-0.5">
                    Рассчитать стоимость
                </button>
                <a href="#projects" class="border-2 border-white-custom text-white-custom hover:bg-white-custom hover:text-secondary px-6 py-3 rounded-lg font-medium transition-all">
                    Посмотреть проекты
                </a>
            </div>
        </div>
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-center text-white-custom opacity-80">
            <span class="block mb-2">Листайте вниз</span>
            <div class="w-0.5 h-8 bg-white-custom mx-auto scroll-arrow"></div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-20 bg-bg-light" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="font-evolventa text-4xl font-semibold text-secondary mb-12 text-center md:text-left">О компании</h2>
                    <p class="text-lg leading-relaxed text-green-dark mb-8">
                        «Деревянное домостроение» — это не просто строительство, это создание тёплого и надёжного пространства для жизни. Натуральные материалы, современные решения, забота о каждой детали. Мы строим дома, в которые хочется возвращаться.
                    </p>
                    <a href="/about" class="border-2 border-secondary text-secondary hover:bg-secondary hover:text-white-custom px-6 py-3 rounded-lg font-medium transition-all inline-block">
                        Подробнее о нас
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="text-center p-8 bg-white-custom rounded-xl shadow-lg">
                        <i class="ri-building-line text-4xl text-primary mb-4"></i>
                        <div class="font-evolventa text-3xl font-bold text-primary mb-2">15</div>
                        <div class="text-green-dark font-medium">лет на рынке</div>
                    </div>
                    <div class="text-center p-8 bg-white-custom rounded-xl shadow-lg">
                        <i class="ri-home-line text-4xl text-primary mb-4"></i>
                        <div class="font-evolventa text-3xl font-bold text-primary mb-2">120+</div>
                        <div class="text-green-dark font-medium">построенных домов</div>
                    </div>
                    <div class="text-center p-8 bg-white-custom rounded-xl shadow-lg">
                        <i class="ri-tools-line text-4xl text-primary mb-4"></i>
                        <div class="font-evolventa text-3xl font-bold text-primary mb-2">100%</div>
                        <div class="text-green-dark font-medium">собственная бригада</div>
                    </div>
                    <div class="text-center p-8 bg-white-custom rounded-xl shadow-lg">
                        <i class="ri-map-pin-line text-4xl text-primary mb-4"></i>
                        <div class="font-evolventa text-3xl font-bold text-primary mb-2">МО</div>
                        <div class="text-green-dark font-medium">работаем по региону</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    @isset($houses)
    <section class="py-20" id="projects">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-evolventa text-4xl font-semibold text-secondary mb-12 text-center">Популярные проекты</h2>

            <div class="relative">
                <div class="swiper projects-swiper !p-4">
                    <div class="swiper-wrapper">
                        @foreach($houses as $item)
                            <div class="swiper-slide !h-auto">
                                <div class="bg-white-custom rounded-xl overflow-hidden shadow-md hover:-translate-y-2 transition-transform">
                                    <div class="h-48 overflow-hidden">
                                        <a href="{{ route('house.show', $item->slug) }}">
                                        @if($item->hasMedia('main'))
                                            <img data-src="{{ $item->getFirstMediaUrl('main') }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-400">Нет изображения</span>
                                            </div>
                                        @endif
                                        </a>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="font-evolventa text-xl text-secondary mb-4"><a href="{{ route('house.show', $item->slug) }}">{{ $item->title }}</a></h3>
                                        <div class="flex justify-between text-sm text-green-dark mb-6">
                                            <span>{{ $item->area_total }} м²</span>
                                            <span>Этажей: {{ $item->floor_count }}</span>
                                            <span>от {{ number_format($item->price, 0, '.', ' ') }} ₽</span>
                                        </div>
                                        <a href="{{ route('house.show', $item->slug) }}" class="bg-primary hover:bg-primary-dark text-white-custom px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Подробнее
                                        </a>
                                    </div>
                        </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                <div class="swiper-projects-pagination flex items-center justify-center mt-4"></div>

                <div class="swiper-projects-next hidden md:block text-4xl text-secondary absolute top-1/2 -right-6 -translate-y-1/2 cursor-pointer">
                    <i class="ri-arrow-right-s-line"></i>
                </div>

                <div class="swiper-projects-prev hidden md:block text-4xl text-secondary absolute top-1/2 -left-6 -translate-y-1/2 cursor-pointer">
                    <i class="ri-arrow-left-s-line"></i>
                </div>
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('catalog') }}" class="bg-primary hover:bg-primary-dark text-white-custom px-8 py-3 rounded-lg font-medium transition-all hover:-translate-y-0.5 inline-block">
                    Перейти в каталог
                </a>
            </div>
        </div>
    </section>
@endisset
    <!-- Process Section -->
    <section id="process" class="py-20 bg-bg-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-evolventa font-bold text-4xl text-secondary mb-4">Как мы работаем</h2>
                <p class="text-lg text-accent">Прозрачный и понятный процесс строительства</p>
            </div>

            <div class="relative">
                <div class="absolute top-16 left-0 right-0 h-1 timeline-line hidden md:block"></div>
                <div class="grid md:grid-cols-5 gap-8 relative z-10">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-phone-line text-2xl text-white"></i>
                        </div>
                        <h3 class="font-evolventa font-semibold text-lg text-secondary mb-2">Заявка</h3>
                        <p class="text-accent text-sm">Оставляете заявку на сайте или звоните нам</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-pencil-ruler-2-line text-2xl text-white"></i>
                        </div>
                        <h3 class="font-evolventa font-semibold text-lg text-secondary mb-2">Проект</h3>
                        <p class="text-accent text-sm">Обсуждаем детали и создаем индивидуальный проект</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-file-text-line text-2xl text-white"></i>
                        </div>
                        <h3 class="font-evolventa font-semibold text-lg text-secondary mb-2">Договор</h3>
                        <p class="text-accent text-sm">Составляем смету и заключаем договор</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-hammer-line text-2xl text-white"></i>
                        </div>
                        <h3 class="font-evolventa font-semibold text-lg text-secondary mb-2">Строительство</h3>
                        <p class="text-accent text-sm">Строим дом с соблюдением всех технологий</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-key-line text-2xl text-white"></i>
                        </div>
                        <h3 class="font-evolventa font-semibold text-lg text-secondary mb-2">Сдача</h3>
                        <p class="text-accent text-sm">Передаем готовый дом и ключи владельцу</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-evolventa font-bold text-4xl text-secondary mb-4">Отзывы клиентов</h2>
                <p class="text-lg text-accent">Что говорят о нас наши клиенты</p>
            </div>

            <div class="relative">
                <div class="swiper reviews-swiper !p-4">
                    <div class="swiper-wrapper">
                        @foreach($reviews as $item)
                            <div class="swiper-slide !h-auto">
                                <div class="bg-white rounded-lg p-8 h-full shadow-md">
                                    <div class="flex items-center mb-6">
                                        @if($item->hasMedia('main'))
                                        <div class="w-16 h-16 bg-cover bg-center rounded-full mr-4" style="background-image: url('{{ $item->getFirstMediaUrl('main') }}');"></div>
                                        @else
                                        <div class="w-16 h-16 bg-cover bg-center rounded-full mr-4" style="background-image: url('{{ asset('images/user.png') }}');"></div>
                                        @endif
                                        <div>
                                            <h4 class="font-semibold text-secondary">{{ $item->author }}</h4>
                                        </div>
                                    </div>
                                    <div class="text-accent leading-relaxed italic">
                                        {{ $item->text }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="swiper-reviews-pagination flex items-center justify-center mt-6"></div>

                <div class="swiper-reviews-next hidden md:block text-4xl text-secondary absolute top-1/2 -right-6 -translate-y-1/2 cursor-pointer">
                    <i class="ri-arrow-right-s-line"></i>
                </div>

                <div class="swiper-reviews-prev hidden md:block text-4xl text-secondary absolute top-1/2 -left-6 -translate-y-1/2 cursor-pointer">
                    <i class="ri-arrow-left-s-line"></i>
                </div>
            </div>

        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-secondary to-blue-900 text-white-custom" id="contact">
        <div class="max-w-4xl mx-auto px-5 text-center">
            <h2 class="font-evolventa text-4xl font-semibold mb-4">Остались вопросы?</h2>
            <p class="text-xl mb-10 opacity-90">Оставьте заявку и мы свяжемся с вами в течение 15 минут</p>
            <form id="modal-form" class="modal-form flex flex-col sm:flex-row gap-4 justify-center mb-12" id="contactForm">
                <input type="text" name="name" placeholder="Ваше имя" data-required class="px-4 py-3 rounded-lg font-montserrat min-w-0 flex-1 max-w-xs text-dark">
                <input type="tel" name="phone" placeholder="Телефон" data-required class="phone-input px-4 py-3 rounded-lg font-montserrat min-w-0 flex-1 max-w-xs text-dark">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white-custom px-6 py-3 rounded-lg font-medium transition-all hover:-translate-y-0.5">
                    Заказать обратный звонок
                </button>
            </form>
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                @if($settings->phone)
                    <a href="tel:{{ App\Helpers\SettingsHelper::phoneDigitsOnly($settings->phone) }}" target="_blank" class="text-center text-2xl border border-white-custom p-2 rounded-full w-10 h-10 flex items-center justify-center text-white-custom hover:bg-primary">
                        <i class="ri-phone-fill"></i>
                    </a>
                @endif
                @if($settings->whatsapp)
                    <a href="https://wa.me/{{ App\Helpers\SettingsHelper::phoneDigitsOnly($settings->whatsapp) }}" target="_blank" class="text-center text-2xl border border-white-custom p-2 rounded-full w-10 h-10 flex items-center justify-center text-white-custom hover:bg-primary">
                        <i class="ri-whatsapp-fill"></i>
                    </a>
                @endif
                @if($settings->telegram)
                    <a href="https://t.me/{{ $settings->telegram }}" target="_blank" class="text-center text-2xl border border-white-custom p-2 rounded-full w-10 h-10 flex items-center justify-center text-white-custom hover:bg-primary">
                        <i class="ri-telegram-fill"></i>
                    </a>
                @endif
                @if($settings->vk)
                    <a href="{{ $settings->vk }}" target="_blank" class="text-center text-2xl border border-white-custom p-2 rounded-full w-10 h-10 flex items-center justify-center text-white-custom hover:bg-primary">
                        <i class="ri-vk-fill"></i>
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection
