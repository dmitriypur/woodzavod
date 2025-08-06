<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Страница О компании
        Page::create([
            'slug' => 'about',
            'title' => 'О компании',
            'content' => '<div class="space-y-6">
                <p>Компания «Деревянное домостроение» — это современное производство деревянных домов из бруса, основанное в 2010 году. За это время мы построили более 500 домов по всей России.</p>
                
                <h2 class="text-2xl font-semibold">Наши преимущества</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold mb-3">Собственное производство</h3>
                        <p>Мы контролируем весь процесс от заготовки леса до строительства дома, что гарантирует высокое качество.</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold mb-3">Экологичность</h3>
                        <p>Используем только натуральные материалы, безопасные для здоровья и окружающей среды.</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold mb-3">Индивидуальный подход</h3>
                        <p>Разрабатываем проекты с учетом всех пожеланий заказчика и особенностей участка.</p>
                    </div>
                </div>
                
                <h2 class="text-2xl font-semibold">Наша команда</h2>
                
                <p>В нашей компании работают опытные специалисты: архитекторы, инженеры, строители с многолетним стажем работы в сфере деревянного домостроения.</p>
                
                <h2 class="text-2xl font-semibold">Наша миссия</h2>
                
                <p>Мы стремимся сделать экологичное жилье доступным для каждой семьи, создавая уютные и долговечные деревянные дома, в которых будет комфортно жить многим поколениям.</p>
            </div>',
        ]);

        // Страница Контакты
        Page::create([
            'slug' => 'contacts',
            'title' => 'Контакты',
            'content' => '<div class="space-y-6">
                <p>Мы всегда рады ответить на ваши вопросы и помочь с выбором проекта деревянного дома.</p>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h2 class="text-2xl font-semibold mb-4">Офис продаж</h2>
                    
                    <p><strong>Адрес:</strong> г. Москва, ул. Примерная, д. 123</p>
                    <p><strong>Телефон:</strong> +7 (XXX) XXX-XX-XX</p>
                    <p><strong>Email:</strong> info@Деревянное домостроение.ru</p>
                    <p><strong>Режим работы:</strong> Пн-Пт с 9:00 до 18:00, Сб-Вс выходной</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h2 class="text-2xl font-semibold mb-4">Производство</h2>
                    
                    <p><strong>Адрес:</strong> Московская область, г. Примерный, ул. Заводская, д. 45</p>
                    <p><strong>Телефон:</strong> +7 (XXX) XXX-XX-XX</p>
                    <p><strong>Режим работы:</strong> Пн-Пт с 8:00 до 17:00, Сб-Вс выходной</p>
                </div>
                
                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-4">Как добраться</h2>
                    
                    <!-- Здесь можно добавить карту или схему проезда -->
                    <div class="bg-gray-200 h-96 flex items-center justify-center">
                        <p class="text-gray-600">Карта проезда</p>
                    </div>
                </div>
            </div>',
        ]);

        // Страница Доставка и оплата
        Page::create([
            'slug' => 'delivery-and-payment',
            'title' => 'Доставка и оплата',
            'content' => '<div class="space-y-6">
                <h2 class="text-2xl font-semibold">Доставка</h2>
                
                <p>Мы осуществляем доставку домокомплектов по всей России. Стоимость доставки рассчитывается индивидуально в зависимости от удаленности объекта и объема материалов.</p>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-3">Сроки доставки</h3>
                    
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Москва и Московская область — 1-3 дня</li>
                        <li>Центральный федеральный округ — 3-7 дней</li>
                        <li>Другие регионы России — от 7 до 14 дней</li>
                    </ul>
                </div>
                
                <h2 class="text-2xl font-semibold mt-8">Оплата</h2>
                
                <p>Мы предлагаем несколько вариантов оплаты для вашего удобства:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold mb-3">Наличный расчет</h3>
                        <p>Вы можете оплатить заказ наличными в нашем офисе или при получении домокомплекта.</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold mb-3">Безналичный расчет</h3>
                        <p>Оплата по счету для физических и юридических лиц.</p>
                    </div>
                </div>
                
                <h2 class="text-2xl font-semibold mt-8">Этапы оплаты</h2>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <ol class="list-decimal pl-6 space-y-4">
                        <li>
                            <strong>Предоплата 30%</strong>
                            <p>После подписания договора вносится предоплата в размере 30% от стоимости домокомплекта.</p>
                        </li>
                        <li>
                            <strong>Оплата 60%</strong>
                            <p>Перед отгрузкой домокомплекта с производства оплачивается 60% от стоимости.</p>
                        </li>
                        <li>
                            <strong>Окончательный расчет 10%</strong>
                            <p>После доставки и проверки комплектности производится окончательный расчет.</p>
                        </li>
                    </ol>
                </div>
            </div>',
        ]);
    }
}