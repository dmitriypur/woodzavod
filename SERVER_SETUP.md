# Настройка сервера для Laravel приложения

Полное руководство по настройке production сервера для приложения "Деревянное домостроение".

## Быстрый старт

### 1. Подготовка сервера

```bash
# Загрузите файлы на сервер
scp server-setup.sh ssl-setup.sh nginx.conf root@your-server:/root/

# Подключитесь к серверу
ssh root@your-server

# Запустите автоматическую настройку
cd /root
./server-setup.sh
```

### 2. Настройка SSL

```bash
# После настройки DNS записей
./ssl-setup.sh your-domain.com
```

### 3. Настройка базы данных

```bash
# Войдите в MySQL
mysql -u root -p

# Создайте базу данных и пользователя
CREATE DATABASE woodzavod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'woodzavod'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON woodzavod.* TO 'woodzavod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Создание .env файла

```bash
# Создайте production конфигурацию
nano /var/www/woodzavod/.env
```

```env
APP_NAME="Деревянное домостроение"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=woodzavod
DB_USERNAME=woodzavod
DB_PASSWORD=strong_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Telegram Bot (для уведомлений о заявках)
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
```

### 5. Первый деплой

```bash
# На локальной машине
./deploy.sh production
```

## Подробная настройка

### Структура сервера

```
/var/www/woodzavod/
├── current -> releases/20240115123456  # Активная версия
├── releases/                           # Все релизы
│   ├── 20240115123456/
│   └── 20240115134567/
├── storage/                           # Постоянное хранилище
│   ├── app/
│   ├── framework/
│   └── logs/
└── .env                              # Production конфигурация
```

### Компоненты сервера

- **Nginx** - веб-сервер с SSL и безопасностью
- **PHP 8.2-FPM** - обработка PHP с оптимизацией
- **MySQL 8.0** - база данных
- **Redis** - кеш и сессии
- **Supervisor** - управление очередями
- **Certbot** - SSL сертификаты
- **fail2ban** - защита от атак

### Настройки безопасности

#### Nginx Security Headers
- HSTS (HTTP Strict Transport Security)
- X-Frame-Options (защита от clickjacking)
- X-Content-Type-Options (защита от MIME sniffing)
- X-XSS-Protection
- Content Security Policy
- Rate limiting для API и админки

#### fail2ban
- Защита SSH от брутфорса
- Блокировка по логам Nginx
- Автоматическое разблокирование

### Мониторинг и обслуживание

#### Полезные команды

```bash
# Мониторинг системы
/root/monitor.sh

# Проверка SSL
/root/check-ssl.sh

# Логи приложения
tail -f /var/www/woodzavod/storage/logs/laravel.log

# Логи Nginx
tail -f /var/log/nginx/woodzavod_error.log

# Статус очередей
supervisorctl status

# Статус fail2ban
fail2ban-client status
```

#### Регулярное обслуживание

```bash
# Обновление системы (еженедельно)
apt update && apt upgrade -y

# Очистка логов (ежемесячно)
logrotate -f /etc/logrotate.d/woodzavod

# Очистка старых релизов
envoy run cleanup --production=user@server

# Проверка дискового пространства
df -h

# Проверка использования памяти
free -h
```

### Backup стратегия

#### Автоматический backup

```bash
# Создайте скрипт backup
cat > /root/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/woodzavod"

mkdir -p $BACKUP_DIR

# Backup базы данных
mysqldump -u woodzavod -p'password' woodzavod > $BACKUP_DIR/db_$DATE.sql

# Backup файлов
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/woodzavod/storage

# Удаление старых backup (старше 30 дней)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
EOF

chmod +x /root/backup.sh

# Добавьте в cron (ежедневно в 2:00)
echo "0 2 * * * /root/backup.sh" | crontab -
```

### Оптимизация производительности

#### PHP-FPM настройки
```ini
# /etc/php/8.2/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 15
pm.max_requests = 500
```

#### MySQL настройки
```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_type = 1
query_cache_size = 64M
```

#### Redis настройки
```ini
# /etc/redis/redis.conf
maxmemory 512mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### Troubleshooting

#### Частые проблемы

1. **502 Bad Gateway**
   ```bash
   systemctl status php8.2-fpm
   tail -f /var/log/php8.2-fpm.log
   ```

2. **Медленная загрузка**
   ```bash
   # Проверьте использование ресурсов
   htop
   iotop
   ```

3. **Ошибки SSL**
   ```bash
   certbot certificates
   nginx -t
   ```

4. **Проблемы с очередями**
   ```bash
   supervisorctl restart woodzavod-worker:*
   ```

#### Логи для диагностики

- **Nginx**: `/var/log/nginx/woodzavod_error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **Laravel**: `/var/www/woodzavod/storage/logs/laravel.log`
- **MySQL**: `/var/log/mysql/error.log`
- **Redis**: `/var/log/redis/redis-server.log`
- **Supervisor**: `/var/log/supervisor/supervisord.log`

### Масштабирование

#### Горизонтальное масштабирование
- Load balancer (Nginx/HAProxy)
- Отдельный сервер для базы данных
- CDN для статических файлов
- Отдельные серверы для очередей

#### Вертикальное масштабирование
- Увеличение RAM и CPU
- SSD диски
- Оптимизация конфигураций

### Безопасность

#### Чеклист безопасности

- [ ] SSH доступ только по ключам
- [ ] Отключен root login по SSH
- [ ] Настроен fail2ban
- [ ] Установлены все обновления
- [ ] Настроен файрвол (ufw)
- [ ] SSL сертификаты актуальны
- [ ] Регулярные backup
- [ ] Мониторинг логов
- [ ] Сильные пароли для БД
- [ ] Ограничен доступ к админке

#### Дополнительные меры

```bash
# Отключение root login
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
systemctl restart ssh

# Изменение SSH порта
sed -i 's/#Port 22/Port 2222/' /etc/ssh/sshd_config
systemctl restart ssh
ufw allow 2222

# Установка дополнительных инструментов безопасности
apt install -y rkhunter chkrootkit lynis
```

## Заключение

Эта конфигурация обеспечивает:
- **Высокую производительность** через оптимизированные настройки
- **Безопасность** через SSL, заголовки безопасности и fail2ban
- **Надежность** через мониторинг и backup
- **Масштабируемость** через правильную архитектуру

Для production использования рекомендуется дополнительно настроить:
- Внешний мониторинг (Zabbix, Prometheus)
- Централизованное логирование (ELK Stack)
- CDN (CloudFlare, AWS CloudFront)
- Регулярные security аудиты