# Исправление проблем с SSL в Laravel после настройки HTTPS

## Проблема
После настройки SSL на nginx, Laravel приложение перестало работать, хотя до этого работало на HTTP.

## Причины
1. **Отсутствие HTTPS заголовков в nginx** - Laravel не понимает, что запрос пришел через HTTPS
2. **Неправильная настройка trusted proxies** - Laravel не доверяет nginx как прокси серверу
3. **Отсутствие принудительного HTTPS** - Laravel генерирует HTTP ссылки вместо HTTPS

## Исправления

### 1. Обновить nginx конфигурацию
В секции `location ~ \.php$` добавить HTTPS заголовки:

```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    
    # HTTPS заголовки для Laravel
    fastcgi_param HTTPS on;
    fastcgi_param HTTP_X_FORWARDED_PROTO https;
    fastcgi_param HTTP_X_FORWARDED_FOR $proxy_add_x_forwarded_for;
    fastcgi_param HTTP_X_FORWARDED_HOST $server_name;
    fastcgi_param SERVER_PORT 443;
    
    fastcgi_hide_header X-Powered-By;
    # остальные настройки...
}
```

### 2. Обновить AppServiceProvider
В `app/Providers/AppServiceProvider.php` добавить:

```php
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    // Принудительное использование HTTPS в production
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
    
    // Доверие прокси серверам (nginx)
    $this->app['request']->setTrustedProxies(
        ['127.0.0.1', '::1'], // localhost
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
    );
    
    // остальной код...
}
```

### 3. Проверить .env файл
Убедиться что в production .env файле:

```env
APP_ENV=production
APP_URL=https://your-domain.com
APP_DEBUG=false
```

## Команды для применения

1. Обновить nginx конфигурацию на сервере
2. Перезагрузить nginx: `sudo systemctl reload nginx`
3. Очистить кеш Laravel: `php artisan config:clear && php artisan cache:clear`
4. Перезапустить PHP-FPM: `sudo systemctl restart php8.2-fpm`

## Дополнительные проверки

- Проверить логи nginx: `/var/log/nginx/error.log`
- Проверить логи Laravel: `storage/logs/laravel.log`
- Убедиться что SSL сертификат валиден: `openssl s_client -connect your-domain.com:443`

## Альтернативное решение
Если проблемы продолжаются, можно использовать middleware `TrustProxies`:

```bash
php artisan make:middleware TrustProxies
```

И настроить его в `app/Http/Kernel.php`.