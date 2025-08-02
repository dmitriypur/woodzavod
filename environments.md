# Конфигурации для различных окружений

## Development (Локальная разработка)

### MAMP/XAMPP конфигурация

```apache
# httpd.conf или .htaccess
<VirtualHost *:80>
    ServerName woodzavod.local
    DocumentRoot "/Applications/MAMP/htdocs/woodzavod/public"
    
    <Directory "/Applications/MAMP/htdocs/woodzavod/public">
        AllowOverride All
        Require all granted
        
        # Laravel pretty URLs
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    
    # Логирование
    ErrorLog "/Applications/MAMP/logs/woodzavod_error.log"
    CustomLog "/Applications/MAMP/logs/woodzavod_access.log" combined
</VirtualHost>
```

### .env для разработки

```env
APP_NAME="Деревянное домостроение"
APP_ENV=local
APP_KEY=base64:GENERATE_KEY_HERE
APP_DEBUG=true
APP_URL=http://woodzavod.local

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=woodzavod
DB_USERNAME=root
DB_PASSWORD=root

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Для тестирования Telegram
TELEGRAM_BOT_TOKEN=test_token
TELEGRAM_CHAT_ID=test_chat_id
```

## Staging (Тестовый сервер)

### Nginx конфигурация для staging

```nginx
server {
    listen 80;
    server_name staging.woodzavod.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name staging.woodzavod.com;
    root /var/www/woodzavod-staging/current/public;
    index index.php;
    
    # SSL конфигурация
    ssl_certificate /etc/letsencrypt/live/staging.woodzavod.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/staging.woodzavod.com/privkey.pem;
    
    # Базовая аутентификация для staging
    auth_basic "Staging Environment";
    auth_basic_user_file /etc/nginx/.htpasswd;
    
    # Логирование
    access_log /var/log/nginx/staging_woodzavod_access.log;
    error_log /var/log/nginx/staging_woodzavod_error.log;
    
    # Laravel конфигурация
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Статические файлы
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Безопасность
    location ~ /\. {
        deny all;
    }
    
    location ~ /(storage|bootstrap/cache) {
        deny all;
    }
}
```

### .env для staging

```env
APP_NAME="Деревянное домостроение (Staging)"
APP_ENV=staging
APP_KEY=base64:STAGING_KEY_HERE
APP_DEBUG=false
APP_URL=https://staging.woodzavod.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=woodzavod_staging
DB_USERNAME=woodzavod_staging
DB_PASSWORD=staging_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=1

MAIL_MAILER=smtp
MAIL_HOST=staging-smtp-host
MAIL_PORT=587
MAIL_USERNAME=staging-email
MAIL_PASSWORD=staging-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=staging@woodzavod.com
MAIL_FROM_NAME="${APP_NAME}"

# Staging Telegram для тестов
TELEGRAM_BOT_TOKEN=staging_bot_token
TELEGRAM_CHAT_ID=staging_chat_id
```

## Production (Боевой сервер)

### Расширенная Nginx конфигурация

```nginx
# Rate limiting zones
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
limit_req_zone $binary_remote_addr zone=admin:10m rate=5r/s;
limit_req_zone $binary_remote_addr zone=general:10m rate=30r/s;

# Upstream для load balancing (если несколько серверов)
upstream php_backend {
    server unix:/var/run/php/php8.2-fpm.sock;
    # server unix:/var/run/php/php8.2-fpm2.sock backup;
}

server {
    listen 80;
    server_name woodzavod.com www.woodzavod.com;
    return 301 https://woodzavod.com$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.woodzavod.com;
    return 301 https://woodzavod.com$request_uri;
}

server {
    listen 443 ssl http2;
    server_name woodzavod.com;
    root /var/www/woodzavod/current/public;
    index index.php;
    
    # SSL конфигурация
    ssl_certificate /etc/letsencrypt/live/woodzavod.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/woodzavod.com/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/woodzavod.com/chain.pem;
    
    # SSL настройки
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    
    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;
    
    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-ancestors 'self';" always;
    
    # Логирование
    access_log /var/log/nginx/woodzavod_access.log combined buffer=16k flush=2m;
    error_log /var/log/nginx/woodzavod_error.log warn;
    
    # Rate limiting
    location / {
        limit_req zone=general burst=50 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location /admin/ {
        limit_req zone=admin burst=10 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
        
        # Дополнительная защита админки
        allow 192.168.1.0/24;  # Офисная сеть
        allow 10.0.0.0/8;      # VPN
        deny all;
    }
    
    # PHP обработка
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php_backend;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # PHP настройки
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }
    
    # Статические файлы с агрессивным кешированием
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|webp|avif)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary "Accept-Encoding";
        
        # Сжатие
        gzip_static on;
        brotli_static on;
    }
    
    # Robots.txt
    location = /robots.txt {
        allow all;
        log_not_found off;
        access_log off;
    }
    
    # Sitemap
    location = /sitemap.xml {
        allow all;
        log_not_found off;
        access_log off;
    }
    
    # Безопасность
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    location ~ /(storage|bootstrap/cache|vendor|node_modules) {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    location ~ \.(env|log|md|txt|yml|yaml|json)$ {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

### .env для production

```env
APP_NAME="Деревянное домостроение"
APP_ENV=production
APP_KEY=base64:PRODUCTION_KEY_HERE
APP_DEBUG=false
APP_URL=https://woodzavod.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
LOG_DAYS=14

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=woodzavod
DB_USERNAME=woodzavod
DB_PASSWORD=very_strong_production_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=redis_password
REDIS_PORT=6379
REDIS_DB=0

MAIL_MAILER=smtp
MAIL_HOST=smtp.yandex.ru
MAIL_PORT=465
MAIL_USERNAME=info@woodzavod.com
MAIL_PASSWORD=email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@woodzavod.com
MAIL_FROM_NAME="${APP_NAME}"

# Production Telegram
TELEGRAM_BOT_TOKEN=production_bot_token
TELEGRAM_CHAT_ID=production_chat_id

# Дополнительные настройки безопасности
SESSION_SAME_SITE=strict
SANCTUM_STATEFUL_DOMAINS=woodzavod.com

# Мониторинг и аналитика
GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
YANDEX_METRICA_ID=YANDEX_COUNTER_ID

# CDN (если используется)
CDN_URL=https://cdn.woodzavod.com

# Backup настройки
BACKUP_DISK=s3
AWS_ACCESS_KEY_ID=backup_access_key
AWS_SECRET_ACCESS_KEY=backup_secret_key
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=woodzavod-backups
```

## Docker конфигурация (альтернатива)

### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: woodzavod_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - woodzavod
    depends_on:
      - db
      - redis

  nginx:
    image: nginx:alpine
    container_name: woodzavod_nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
      - ./docker/ssl:/etc/ssl/certs
    networks:
      - woodzavod
    depends_on:
      - app

  db:
    image: mysql:8.0
    container_name: woodzavod_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: woodzavod
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: woodzavod
      MYSQL_PASSWORD: db_password
    volumes:
      - dbdata:/var/lib/mysql
      - ./docker/mysql/my.cnf:/etc/mysql/my.cnf
    ports:
      - "3306:3306"
    networks:
      - woodzavod

  redis:
    image: redis:alpine
    container_name: woodzavod_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    networks:
      - woodzavod

  queue:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: woodzavod_queue
    restart: unless-stopped
    command: php artisan queue:work --sleep=3 --tries=3
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - woodzavod
    depends_on:
      - db
      - redis

volumes:
  dbdata:
    driver: local

networks:
  woodzavod:
    driver: bridge
```

### Dockerfile

```dockerfile
FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Очистка кеша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Установка Redis расширения
RUN pecl install redis && docker-php-ext-enable redis

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание пользователя
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Копирование приложения
COPY . /var/www
COPY --chown=www:www . /var/www

# Переключение на пользователя
USER www

# Рабочая директория
WORKDIR /var/www

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Права доступа
RUN chmod -R 755 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

## Мониторинг и алерты

### Prometheus + Grafana

```yaml
# docker-compose.monitoring.yml
version: '3.8'

services:
  prometheus:
    image: prom/prometheus
    container_name: prometheus
    ports:
      - "9090:9090"
    volumes:
      - ./monitoring/prometheus.yml:/etc/prometheus/prometheus.yml
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.path=/prometheus'
      - '--web.console.libraries=/etc/prometheus/console_libraries'
      - '--web.console.templates=/etc/prometheus/consoles'

  grafana:
    image: grafana/grafana
    container_name: grafana
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
    volumes:
      - grafana-data:/var/lib/grafana

  node-exporter:
    image: prom/node-exporter
    container_name: node-exporter
    ports:
      - "9100:9100"

volumes:
  grafana-data:
```

### Скрипт для health check

```bash
#!/bin/bash
# health-check.sh

SITE_URL="https://woodzavod.com"
TELEGRAM_BOT_TOKEN="your_bot_token"
TELEGRAM_CHAT_ID="your_chat_id"

# Проверка доступности сайта
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $SITE_URL)

if [ $HTTP_CODE -ne 200 ]; then
    MESSAGE="🚨 ALERT: Сайт $SITE_URL недоступен! HTTP код: $HTTP_CODE"
    curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
        -d chat_id=$TELEGRAM_CHAT_ID \
        -d text="$MESSAGE"
fi

# Проверка использования диска
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')

if [ $DISK_USAGE -gt 80 ]; then
    MESSAGE="⚠️ WARNING: Использование диска: ${DISK_USAGE}%"
    curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
        -d chat_id=$TELEGRAM_CHAT_ID \
        -d text="$MESSAGE"
fi

# Проверка использования памяти
MEM_USAGE=$(free | awk 'NR==2{printf "%.0f", $3*100/$2 }')

if [ $MEM_USAGE -gt 90 ]; then
    MESSAGE="⚠️ WARNING: Использование памяти: ${MEM_USAGE}%"
    curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
        -d chat_id=$TELEGRAM_CHAT_ID \
        -d text="$MESSAGE"
fi
```

## Заключение

Каждое окружение имеет свои особенности:

- **Development**: Максимальное удобство разработки, подробное логирование
- **Staging**: Максимально близко к production, но с дополнительной защитой
- **Production**: Максимальная производительность, безопасность и надежность

Выберите подходящую конфигурацию в зависимости от ваших потребностей и требований проекта.