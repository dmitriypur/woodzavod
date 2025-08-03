# Деплой Laravel приложения на обычный хостинг

Это руководство описывает как развернуть Laravel приложение "Деревянное домостроение" на обычном shared хостинге без SSH доступа.

## Требования к хостингу

- PHP 8.2 или выше
- MySQL/MariaDB база данных
- Поддержка .htaccess (Apache)
- Возможность загрузки файлов через FTP/cPanel
- Минимум 512MB RAM для PHP
- Composer (желательно, но не обязательно)

## Подготовка проекта

### 1. Локальная подготовка

```bash
# Установите зависимости
composer install --optimize-autoloader --no-dev

# Очистите кеши
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Создайте production кеши
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Соберите фронтенд ассеты
npm install
npm run build
```

### 2. Создайте .env файл для production

Создайте файл `.env.production`:

```env
APP_NAME="Деревянное домостроение"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Europe/Moscow
APP_URL=https://yourdomain.com

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru

LOG_CHANNEL=single
LOG_LEVEL=error

# Настройки базы данных от хостинг провайдера
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.yourdomain.com

CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=public

# Настройки почты от хостинг провайдера
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Деревянное домостроение"
```

## Структура файлов на хостинге

### Вариант 1: Если есть доступ к корневой директории

```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── public/ (содержимое переместить в public_html)
```

### Вариант 2: Только доступ к public_html

```
public_html/
├── laravel/ (все файлы Laravel кроме public)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   └── composer.json
├── index.php (модифицированный)
├── .htaccess
└── остальные файлы из папки public/
```

## Пошаговая инструкция

### Шаг 1: Загрузка файлов

1. **Создайте архив проекта:**
   ```bash
   # Исключите ненужные файлы
   tar -czf laravel-app.tar.gz \
     --exclude='node_modules' \
     --exclude='.git' \
     --exclude='tests' \
     --exclude='.env' \
     --exclude='storage/logs/*' \
     --exclude='storage/framework/cache/*' \
     --exclude='storage/framework/sessions/*' \
     --exclude='storage/framework/views/*' \
     .
   ```

2. **Загрузите через FTP/cPanel File Manager**

### Шаг 2: Настройка структуры (Вариант 2)

Если у вас только доступ к public_html, модифицируйте `public/index.php`:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Измените пути на относительные
if (file_exists($maintenance = __DIR__.'/laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/laravel/vendor/autoload.php';

$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### Шаг 3: Настройка базы данных

1. **Создайте базу данных через cPanel**
2. **Импортируйте структуру:**
   ```sql
   -- Экспортируйте локально
   php artisan schema:dump
   
   -- Или создайте SQL файл с миграциями
   ```

3. **Выполните миграции (если есть SSH):**
   ```bash
   php artisan migrate --force
   ```

### Шаг 4: Настройка прав доступа

Через cPanel File Manager установите права:
- Папки: 755
- Файлы: 644
- storage/ и bootstrap/cache/: 777 (рекурсивно)

### Шаг 5: Настройка .htaccess

В корне public_html создайте/обновите `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Безопасность
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>
```

### Шаг 6: Настройка Filament Admin

Создайте пользователя админа:

```php
// Создайте файл create_admin.php в корне
<?php
require_once 'laravel/vendor/autoload.php';

$app = require_once 'laravel/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@yourdomain.com',
    'password' => Hash::make('your-secure-password'),
    'email_verified_at' => now(),
]);

echo "Admin user created successfully!\n";
```

Запустите через браузер: `https://yourdomain.com/create_admin.php`

**Удалите файл после создания пользователя!**

## Обновление приложения

### Автоматическое обновление (если есть SSH)

Создайте скрипт `update.php`:

```php
<?php
// Простой скрипт обновления
if ($_GET['token'] !== 'your-secret-token') {
    die('Unauthorized');
}

// Выполните команды обновления
exec('cd laravel && git pull origin main');
exec('cd laravel && composer install --no-dev --optimize-autoloader');
exec('cd laravel && php artisan migrate --force');
exec('cd laravel && php artisan config:cache');
exec('cd laravel && php artisan route:cache');
exec('cd laravel && php artisan view:cache');

echo 'Updated successfully!';
```

### Ручное обновление

1. Создайте новый архив с обновлениями
2. Загрузите и замените файлы
3. Выполните миграции через веб-интерфейс

## Оптимизация производительности

### 1. Кеширование

```bash
# Локально перед загрузкой
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Оптимизация Composer

```bash
composer install --optimize-autoloader --no-dev
composer dump-autoload --optimize
```

### 3. Настройка .htaccess для кеширования

```apache
# Кеширование статических файлов
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
</IfModule>

# Сжатие
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## Troubleshooting

### Частые проблемы:

1. **500 Internal Server Error:**
   - Проверьте права доступа к storage/
   - Проверьте .env файл
   - Проверьте логи в storage/logs/

2. **Ошибка "Class not found":**
   - Выполните `composer dump-autoload`
   - Проверьте пути в index.php

3. **Ошибки базы данных:**
   - Проверьте настройки DB в .env
   - Убедитесь что миграции выполнены

4. **Проблемы с Filament:**
   - Очистите кеш: `php artisan filament:clear-cached-components`
   - Проверьте права доступа к storage/

### Логирование

Для отладки добавьте в .env:
```env
LOG_CHANNEL=single
LOG_LEVEL=debug
```

Проверяйте логи в `storage/logs/laravel.log`

## Безопасность

1. **Скройте Laravel файлы:**
   - Переместите все файлы кроме public/ в недоступную директорию
   - Настройте .htaccess для блокировки доступа к служебным файлам

2. **Обновляйте регулярно:**
   - Laravel framework
   - PHP версию
   - Зависимости Composer

3. **Мониторинг:**
   - Настройте уведомления об ошибках
   - Регулярно проверяйте логи
   - Используйте HTTPS

## Заключение

Деплой на shared хостинг требует больше ручной работы, но вполне возможен. Главное - правильно настроить структуру файлов и права доступа.

Для production рекомендуется:
- Использовать VPS или dedicated сервер
- Настроить автоматический деплой
- Использовать CDN для статических файлов
- Настроить мониторинг и бэкапы