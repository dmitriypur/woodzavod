# WoodZavod - Сайт производителя деревянных домов

## О проекте

WoodZavod - это веб-сайт для компании, занимающейся производством и продажей деревянных домов. Проект разработан на Laravel с использованием Filament для административной панели.

## Функциональность

- Каталог домов с фильтрацией по категориям и сортировкой
- Детальные страницы домов с характеристиками и галереей изображений
- Отзывы клиентов
- Форма обратной связи для заявок
- Статические страницы (О компании, Контакты и т.д.)
- Административная панель на Filament для управления всем контентом

## Технологии

- Laravel 10
- Filament 3 (админ-панель)
- Spatie Media Library (для управления изображениями)
- Tailwind CSS (для стилизации)

## Требования

- PHP 8.1 или выше
- Composer
- MySQL 5.7 или выше
- Node.js и NPM (для сборки фронтенда)

## Установка

1. Клонировать репозиторий:
   ```
   git clone https://github.com/your-username/woodzavod.git
   cd woodzavod
   ```

2. Установить зависимости PHP:
   ```
   composer install
   ```

3. Установить зависимости JavaScript:
   ```
   npm install
   ```

4. Скопировать файл .env.example в .env и настроить подключение к базе данных:
   ```
   cp .env.example .env
   ```

5. Сгенерировать ключ приложения:
   ```
   php artisan key:generate
   ```

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
