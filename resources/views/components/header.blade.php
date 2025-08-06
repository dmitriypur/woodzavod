<header class="bg-white dark:bg-gray-800 shadow-md">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="self-center text-2xl font-semibold whitespace-nowrap text-gray-900 dark:text-white">Деревянное домостроение</span>
                </a>
            </div>
            
            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8">
                <nav class="flex space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                        Главная
                    </a>
                    <a href="{{ route('houses.index') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                        Каталог
                    </a>
                    <a href="{{ route('pages.show', 'about') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                        О компании
                    </a>
                    <a href="{{ route('pages.show', 'delivery-and-payment') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                        Доставка и оплата
                    </a>
                    <a href="{{ route('pages.show', 'contacts') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                        Контакты
                    </a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <a href="tel:+79991234567" class="flex items-center text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        +7 (999) 123-45-67
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>