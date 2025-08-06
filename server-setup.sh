#!/bin/bash

# Скрипт автоматической настройки сервера для Laravel приложения
# Запуск: sudo ./server-setup.sh

set -e

# Цвета
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
echo_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
echo_error() { echo -e "${RED}[ERROR]${NC} $1"; }
echo_step() { echo -e "${BLUE}[STEP]${NC} $1"; }

# Проверка root прав
if [[ $EUID -ne 0 ]]; then
   echo_error "Этот скрипт должен запускаться с правами root (sudo)"
   exit 1
fi

echo_step "Обновление системы..."
apt update && apt upgrade -y

echo_step "Установка базовых пакетов..."
apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

echo_step "Добавление репозиториев..."
# PHP репозиторий
add-apt-repository ppa:ondrej/php -y

# Node.js репозиторий
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -

apt update

echo_step "Установка Nginx..."
apt install -y nginx
systemctl enable nginx
systemctl start nginx

echo_step "Установка PHP 8.2 и расширений..."
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-pgsql php8.2-sqlite3 \
    php8.2-redis php8.2-xml php8.2-zip php8.2-curl php8.2-gd php8.2-mbstring \
    php8.2-bcmath php8.2-intl php8.2-soap php8.2-xsl php8.2-opcache \
    php8.2-readline php8.2-common php8.2-cli

systemctl enable php8.2-fpm
systemctl start php8.2-fpm

echo_step "Установка Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

echo_step "Установка Node.js и npm..."
apt install -y nodejs
npm install -g npm@latest

echo_step "Установка MySQL..."
apt install -y mysql-server
systemctl enable mysql
systemctl start mysql

echo_step "Установка Redis..."
apt install -y redis-server
systemctl enable redis-server
systemctl start redis-server

echo_step "Установка Supervisor..."
apt install -y supervisor
systemctl enable supervisor
systemctl start supervisor

echo_step "Настройка PHP..."
# Настройка PHP-FPM
cp /etc/php/8.2/fpm/php.ini /etc/php/8.2/fpm/php.ini.backup

# Основные настройки PHP
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_input_time = .*/max_input_time = 300/' /etc/php/8.2/fpm/php.ini
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
sed -i 's/;opcache.enable=.*/opcache.enable=1/' /etc/php/8.2/fpm/php.ini
sed -i 's/;opcache.memory_consumption=.*/opcache.memory_consumption=256/' /etc/php/8.2/fpm/php.ini

# Настройка PHP-FPM pool
cp /etc/php/8.2/fpm/pool.d/www.conf /etc/php/8.2/fpm/pool.d/www.conf.backup
sed -i 's/pm.max_children = .*/pm.max_children = 50/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/pm.start_servers = .*/pm.start_servers = 10/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/pm.min_spare_servers = .*/pm.min_spare_servers = 5/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/pm.max_spare_servers = .*/pm.max_spare_servers = 15/' /etc/php/8.2/fpm/pool.d/www.conf

systemctl restart php8.2-fpm

echo_step "Создание структуры директорий..."
mkdir -p /var/www/Деревянное домостроение/{releases,storage/{app,framework,logs}}
chown -R www-data:www-data /var/www/Деревянное домостроение
chmod -R 755 /var/www/Деревянное домостроение

echo_step "Настройка Nginx..."
# Удаляем дефолтный сайт
rm -f /etc/nginx/sites-enabled/default

# Копируем конфигурацию (предполагается что nginx.conf уже создан)
if [ -f "nginx.conf" ]; then
    cp nginx.conf /etc/nginx/sites-available/Деревянное домостроение
    ln -sf /etc/nginx/sites-available/Деревянное домостроение /etc/nginx/sites-enabled/
    echo_info "Конфигурация Nginx скопирована"
else
    echo_warning "Файл nginx.conf не найден. Создайте его вручную."
fi

# Тестируем конфигурацию
nginx -t && systemctl reload nginx

echo_step "Настройка файрвола..."
ufw --force enable
ufw allow ssh
ufw allow 'Nginx Full'
ufw allow 3306  # MySQL

echo_step "Установка Certbot для SSL..."
snap install core; snap refresh core
snap install --classic certbot
ln -sf /snap/bin/certbot /usr/bin/certbot

echo_step "Создание пользователя для деплоя..."
if ! id "deploy" &>/dev/null; then
    useradd -m -s /bin/bash deploy
    usermod -aG www-data deploy
    echo_info "Пользователь 'deploy' создан"
else
    echo_info "Пользователь 'deploy' уже существует"
fi

# Создаем директорию для SSH ключей
mkdir -p /home/deploy/.ssh
chown deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh

echo_step "Настройка Supervisor для очередей Laravel..."
cat > /etc/supervisor/conf.d/Деревянное домостроение-worker.conf << 'EOF'
[program:Деревянное домостроение-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/Деревянное домостроение/current/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/Деревянное домостроение/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update

echo_step "Настройка логротации..."
cat > /etc/logrotate.d/Деревянное домостроение << 'EOF'
/var/www/Деревянное домостроение/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
EOF

echo_step "Настройка cron для Laravel Scheduler..."
(crontab -u www-data -l 2>/dev/null; echo "* * * * * cd /var/www/Деревянное домостроение/current && php artisan schedule:run >> /dev/null 2>&1") | crontab -u www-data -

echo_step "Оптимизация системы..."
# Настройка swap если его нет
if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
    echo_info "Swap файл создан (2GB)"
fi

# Настройка лимитов
echo 'www-data soft nofile 65536' >> /etc/security/limits.conf
echo 'www-data hard nofile 65536' >> /etc/security/limits.conf

echo_step "Создание скрипта для получения SSL сертификата..."
cat > /root/setup-ssl.sh << 'EOF'
#!/bin/bash
# Получение SSL сертификата
# Замените Деревянное домостроение.ru на ваш домен
certbot --nginx -d Деревянное домостроение.ru -d www.Деревянное домостроение.ru --non-interactive --agree-tos --email admin@Деревянное домостроение.ru
EOF
chmod +x /root/setup-ssl.sh

echo_step "Создание скрипта мониторинга..."
cat > /root/monitor.sh << 'EOF'
#!/bin/bash
# Простой мониторинг сервисов
echo "=== Статус сервисов ==="
systemctl is-active nginx php8.2-fpm mysql redis-server supervisor

echo "\n=== Использование диска ==="
df -h /

echo "\n=== Использование памяти ==="
free -h

echo "\n=== Логи ошибок Nginx ==="
tail -n 5 /var/log/nginx/Деревянное домостроение_error.log 2>/dev/null || echo "Логи не найдены"

echo "\n=== Процессы PHP-FPM ==="
ps aux | grep php-fpm | grep -v grep | wc -l
EOF
chmod +x /root/monitor.sh

echo ""
echo_info "🎉 Сервер настроен!"
echo ""
echo "📋 Следующие шаги:"
echo "1. Настройте DNS записи для вашего домена"
echo "2. Добавьте SSH ключ для пользователя deploy:"
echo "   sudo -u deploy ssh-keygen -t rsa -b 4096"
echo "   cat /home/deploy/.ssh/id_rsa.pub"
echo "3. Получите SSL сертификат: /root/setup-ssl.sh"
echo "4. Создайте .env файл: /var/www/Деревянное домостроение/.env"
echo "5. Настройте базу данных MySQL"
echo "6. Выполните первый деплой"
echo ""
echo "🔧 Полезные команды:"
echo "   Мониторинг: /root/monitor.sh"
echo "   Логи Nginx: tail -f /var/log/nginx/Деревянное домостроение_error.log"
echo "   Логи PHP: tail -f /var/log/php8.2-fpm.log"
echo "   Перезапуск: systemctl restart nginx php8.2-fpm"
echo ""
echo "🔐 Безопасность:"
echo "   - Настройте SSH ключи и отключите парольную аутентификацию"
echo "   - Смените пароль root: passwd"
echo "   - Настройте fail2ban: apt install fail2ban"
echo "   - Обновляйте систему: apt update && apt upgrade"