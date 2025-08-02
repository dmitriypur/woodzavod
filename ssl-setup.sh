#!/bin/bash

# Скрипт для настройки SSL сертификатов и дополнительной безопасности
# Запуск: sudo ./ssl-setup.sh your-domain.com

set -e

DOMAIN=${1:-woodzavod.ru}
EMAIL=${2:-admin@$DOMAIN}

# Цвета
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
echo_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
echo_error() { echo -e "${RED}[ERROR]${NC} $1"; }

if [[ $EUID -ne 0 ]]; then
   echo_error "Запустите с правами root: sudo $0 domain.com"
   exit 1
fi

if [ -z "$DOMAIN" ]; then
    echo_error "Укажите домен: sudo $0 your-domain.com"
    exit 1
fi

echo_info "Настройка SSL для домена: $DOMAIN"
echo_info "Email для уведомлений: $EMAIL"

# Проверяем что Nginx работает
if ! systemctl is-active --quiet nginx; then
    echo_error "Nginx не запущен. Запустите: systemctl start nginx"
    exit 1
fi

# Получаем SSL сертификат
echo_info "Получение SSL сертификата от Let's Encrypt..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN \
    --non-interactive \
    --agree-tos \
    --email $EMAIL \
    --redirect

if [ $? -eq 0 ]; then
    echo_info "SSL сертификат успешно получен!"
else
    echo_error "Ошибка получения SSL сертификата"
    exit 1
fi

# Настройка автообновления сертификатов
echo_info "Настройка автообновления сертификатов..."
(crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -

# Создание расширенной конфигурации безопасности
echo_info "Создание дополнительных настроек безопасности..."

cat > /etc/nginx/snippets/ssl-params.conf << 'EOF'
# SSL настройки
ssl_protocols TLSv1.2 TLSv1.3;
ssl_prefer_server_ciphers off;
ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;

# OCSP Stapling
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 valid=300s;
resolver_timeout 5s;

# SSL Session
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;
ssl_session_tickets off;

# HSTS
add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;

# Security Headers
add_header X-Frame-Options DENY always;
add_header X-Content-Type-Options nosniff always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none';" always;
EOF

cat > /etc/nginx/snippets/security.conf << 'EOF'
# Скрытие версии Nginx
server_tokens off;

# Защита от clickjacking
add_header X-Frame-Options SAMEORIGIN always;

# Защита от MIME type sniffing
add_header X-Content-Type-Options nosniff always;

# XSS Protection
add_header X-XSS-Protection "1; mode=block" always;

# Referrer Policy
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

# Блокировка доступа к скрытым файлам
location ~ /\. {
    deny all;
    access_log off;
    log_not_found off;
}

# Блокировка доступа к backup файлам
location ~ ~$ {
    deny all;
    access_log off;
    log_not_found off;
}

# Блокировка доступа к конфигурационным файлам
location ~* \.(conf|htaccess|htpasswd|ini|log|sh|sql|tar|gz)$ {
    deny all;
    access_log off;
    log_not_found off;
}
EOF

# Обновляем конфигурацию Nginx для использования новых настроек
echo_info "Обновление конфигурации Nginx..."

# Создаем улучшенную конфигурацию
cat > /etc/nginx/sites-available/woodzavod << EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;
    
    # SSL сертификаты
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;
    
    # Подключаем настройки SSL и безопасности
    include /etc/nginx/snippets/ssl-params.conf;
    include /etc/nginx/snippets/security.conf;
    
    # Корневая директория
    root /var/www/woodzavod/current/public;
    index index.php index.html;
    
    # Логи
    access_log /var/log/nginx/woodzavod_access.log;
    error_log /var/log/nginx/woodzavod_error.log;
    
    # Rate limiting
    limit_req_zone \$binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone \$binary_remote_addr zone=api:10m rate=30r/m;
    
    # Основная обработка запросов Laravel
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # Обработка PHP файлов
    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        # Таймауты
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        
        # Буферизация
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }
    
    # Админка с rate limiting
    location /admin {
        limit_req zone=login burst=5 nodelay;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # API с rate limiting
    location /api {
        limit_req zone=api burst=10 nodelay;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # Статические файлы с кешированием
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt|webp|svg|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
        
        # Сжатие для статических файлов
        gzip_static on;
    }
    
    # Сжатие
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;
    
    # Запрет доступа к служебным файлам Laravel
    location ~ /(storage|bootstrap/cache) {
        deny all;
    }
    
    # Robots.txt и favicon
    location = /robots.txt { access_log off; log_not_found off; }
    location = /favicon.ico { access_log off; log_not_found off; }
    
    # Максимальный размер загружаемых файлов
    client_max_body_size 100M;
    
    # Таймауты
    client_body_timeout 60s;
    client_header_timeout 60s;
    keepalive_timeout 65s;
    send_timeout 60s;
}
EOF

# Тестируем и перезагружаем Nginx
echo_info "Тестирование конфигурации Nginx..."
nginx -t

if [ $? -eq 0 ]; then
    systemctl reload nginx
    echo_info "Nginx перезагружен с новой конфигурацией"
else
    echo_error "Ошибка в конфигурации Nginx"
    exit 1
fi

# Установка fail2ban для дополнительной защиты
echo_info "Установка fail2ban..."
apt install -y fail2ban

# Настройка fail2ban
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5
ignoreip = 127.0.0.1/8 ::1

[sshd]
enabled = true
port = ssh
logpath = /var/log/auth.log
maxretry = 3

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/woodzavod_error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/woodzavod_error.log
maxretry = 10
EOF

systemctl enable fail2ban
systemctl start fail2ban

echo_info "Создание скрипта проверки SSL..."
cat > /root/check-ssl.sh << EOF
#!/bin/bash
echo "=== SSL Certificate Info ==="
certbot certificates

echo "\n=== SSL Test ==="
echo | openssl s_client -servername $DOMAIN -connect $DOMAIN:443 2>/dev/null | openssl x509 -noout -dates

echo "\n=== Security Headers Test ==="
curl -I https://$DOMAIN 2>/dev/null | grep -E '(Strict-Transport|X-Frame|X-Content|X-XSS)'
EOF
chmod +x /root/check-ssl.sh

echo ""
echo_info "🔒 SSL и безопасность настроены!"
echo ""
echo "✅ Выполнено:"
echo "   - SSL сертификат получен и настроен"
echo "   - Автообновление сертификатов настроено"
echo "   - Заголовки безопасности добавлены"
echo "   - Rate limiting настроен"
echo "   - fail2ban установлен и настроен"
echo ""
echo "🔧 Проверка:"
echo "   SSL статус: /root/check-ssl.sh"
echo "   SSL тест: https://www.ssllabs.com/ssltest/analyze.html?d=$DOMAIN"
echo "   Security headers: https://securityheaders.com/?q=$DOMAIN"
echo ""
echo "📋 Рекомендации:"
echo "   - Добавьте домен в HSTS preload list"
echo "   - Настройте мониторинг сертификатов"
echo "   - Регулярно обновляйте систему"
echo "   - Мониторьте логи fail2ban: fail2ban-client status"