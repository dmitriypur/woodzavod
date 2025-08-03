@extends('layouts.app')

@section('title', 'Страница не найдена')
@section('meta_description', 'Запрашиваемая страница не найдена')

@section('content')
<main class="pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center py-16">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-primary mb-4">404</h1>
                <h2 class="text-3xl font-evolventa font-semibold text-secondary mb-4">Страница не найдена</h2>
                <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                    К сожалению, запрашиваемая страница не существует или была перемещена.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('home') }}" 
                   class="bg-primary text-white px-8 py-3 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                    Вернуться на главную
                </a>
                <a href="{{ route('catalog') }}" 
                   class="border border-secondary text-secondary px-8 py-3 rounded-lg font-medium hover:bg-secondary hover:text-white transition-colors">
                    Посмотреть каталог
                </a>
            </div>
            
            <div class="mt-12">
                <p class="text-gray-500 mb-4">Возможно, вас заинтересует:</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="/about" class="text-primary hover:underline">О нас</a>
                    <a href="/contact" class="text-primary hover:underline">Контакты</a>
                    <a href="{{ route('catalog') }}" class="text-primary hover:underline">Каталог домов</a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection