#!/bin/bash

# Скрипт подготовки Laravel приложения для деплоя на shared хостинг
# Использование: ./prepare-shared-hosting.sh [production|staging]

set -e

ENV=${1:-production}
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
ARCHIVE_NAME="laravel-app-${ENV}-${TIMESTAMP}.tar.gz"

echo "🚀 Подготовка Laravel приложения для shared хостинга..."
echo "Среда: $ENV"
echo "Архив: $ARCHIVE_NAME"

# Проверяем наличие необходимых файлов
if [ ! -f "composer.json" ]; then
    echo "❌ Ошибка: composer.json не найден. Запустите скрипт из корня Laravel проекта."
    exit 1
fi

if [ ! -f ".env.${ENV}" ]; then
    echo "❌ Ошибка: .env.${ENV} не найден. Создайте файл конфигурации для среды ${ENV}."
    exit 1
fi

echo "📦 Установка зависимостей..."
composer install --optimize-autoloader --no-dev --quiet

echo "🧹 Очистка кешей..."
php artisan config:clear --quiet
php artisan cache:clear --quiet
php artisan view:clear --quiet
php artisan route:clear --quiet

echo "⚡ Создание production кешей..."
cp ".env.${ENV}" .env
php artisan config:cache --quiet
php artisan route:cache --quiet
php artisan view:cache --quiet

echo "🎨 Сборка фронтенд ассетов..."
if [ -f "package.json" ]; then
    npm ci --silent
    npm run build --silent
else
    echo "⚠️  package.json не найден, пропускаем сборку фронтенда"
fi

echo "📁 Создание директории для деплоя..."
mkdir -p "deploy-${ENV}"

echo "📋 Копирование файлов..."
# Копируем все необходимые файлы
cp -r app deploy-${ENV}/
cp -r bootstrap deploy-${ENV}/
cp -r config deploy-${ENV}/
cp -r database deploy-${ENV}/
cp -r public deploy-${ENV}/
cp -r resources deploy-${ENV}/
cp -r routes deploy-${ENV}/
cp -r storage deploy-${ENV}/
cp -r vendor deploy-${ENV}/
cp .env deploy-${ENV}/
cp artisan deploy-${ENV}/
cp composer.json deploy-${ENV}/
cp composer.lock deploy-${ENV}/

# Создаем модифицированный index.php для shared хостинга
cat > "deploy-${ENV}/public/index-shared-hosting.php" << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Пути для shared хостинга (Laravel файлы в подпапке)
if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

# Создаем скрипт создания админа
cat > "deploy-${ENV}/create-admin.php" << 'EOF'
<?php
// Скрипт создания администратора
// УДАЛИТЕ ЭТОТ ФАЙЛ ПОСЛЕ ИСПОЛЬЗОВАНИЯ!

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Admin';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email и пароль обязательны';
    } else {
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $success = 'Администратор создан успешно! Удалите этот файл.';
        } catch (Exception $e) {
            $error = 'Ошибка: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Создание администратора</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Создание администратора</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label>Имя:</label>
                <input type="text" name="name" value="Admin" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit">Создать администратора</button>
        </form>
    <?php endif; ?>
    
    <p><strong>ВАЖНО:</strong> Удалите этот файл после создания администратора!</p>
</body>
</html>
EOF

# Создаем улучшенный .htaccess
cat > "deploy-${ENV}/public/.htaccess" << 'EOF'
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

# Безопасность - блокируем доступ к служебным файлам
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

<Files "artisan">
    Order allow,deny
    Deny from all
</Files>

<Files "*.md">
    Order allow,deny
    Deny from all
</Files>

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
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
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
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>
EOF

# Создаем README для деплоя
cat > "deploy-${ENV}/DEPLOY_README.md" << EOF
# Инструкция по деплою

Этот архив подготовлен для деплоя на shared хостинг.

## Структура файлов

### Вариант 1: Доступ к корневой директории
1. Загрузите все файлы в корень сайта
2. Переместите содержимое папки public/ в public_html/
3. Настройте права доступа (755 для папок, 644 для файлов)
4. Установите права 777 для storage/ и bootstrap/cache/

### Вариант 2: Только public_html
1. Создайте папку laravel/ в public_html/
2. Загрузите все файлы кроме public/ в laravel/
3. Содержимое public/ загрузите в public_html/
4. Переименуйте index.php в index-backup.php
5. Переименуйте index-shared-hosting.php в index.php
6. Отредактируйте пути в index.php если нужно

## После загрузки

1. Создайте базу данных через cPanel
2. Импортируйте структуру БД или выполните миграции
3. Откройте create-admin.php в браузере для создания администратора
4. УДАЛИТЕ create-admin.php после использования
5. Проверьте работу сайта

## Настройки

- Файл .env уже настроен для среды: ${ENV}
- Кеши созданы для production
- Фронтенд собран

## Поддержка

Подробная инструкция в файле SHARED_HOSTING_DEPLOYMENT.md
EOF

echo "📦 Создание архива..."
tar -czf "$ARCHIVE_NAME" \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='tests' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    -C "deploy-${ENV}" .

echo "🧹 Очистка временных файлов..."
rm -rf "deploy-${ENV}"
rm -f .env

echo "✅ Готово!"
echo "📁 Архив создан: $ARCHIVE_NAME"
echo "📤 Загрузите архив на хостинг и следуйте инструкции в DEPLOY_README.md"
echo ""
echo "🔗 Полезные ссылки:"
echo "   - Подробная инструкция: SHARED_HOSTING_DEPLOYMENT.md"
echo "   - Размер архива: $(du -h "$ARCHIVE_NAME" | cut -f1)"
echo ""
echo "⚠️  Не забудьте:"
echo "   1. Настроить базу данных"
echo "   2. Создать администратора через create-admin.php"
echo "   3. Удалить create-admin.php после использования"
echo "   4. Проверить права доступа к storage/"