# Деплой Laravel приложения через Envoy

Это руководство описывает настройку и использование Laravel Envoy для автоматического деплоя приложения "Деревянное домостроение".

## Предварительные требования

php vendor/bin/envoy run deploy

1. **Laravel Envoy установлен глобально:**
   ```bash
   composer global require laravel/envoy
   ```

2. **SSH доступ к серверу** с настроенными ключами
3. **Git репозиторий** с вашим кодом
4. **Настроенный веб-сервер** (Nginx/Apache) на целевом сервере

## Настройка сервера

### Структура директорий на сервере:
```
/var/www/derevyannoe-domostroenie.ru/
├── current -> releases/20240115123456  # Символическая ссылка на текущий релиз
├── releases/                           # Директория с релизами
│   ├── 20240115123456/
│   ├── 20240115134567/
│   └── ...
├── storage/                           # Постоянное хранилище
│   ├── app/
│   ├── framework/
│   └── logs/
└── .env                              # Конфигурация production
```

### Подготовка сервера:

1. **Создайте структуру директорий:**
   ```bash
   sudo mkdir -p /var/www/derevyannoe-domostroenie.ru/{releases,storage/{app,framework,logs}}
   sudo chown -R www-data:www-data /var/www/derevyannoe-domostroenie.ru
   ```

2. **Создайте .env файл для production:**
   ```bash
   sudo nano /var/www/derevyannoe-domostroenie.ru/.env
   ```

3. **Настройте веб-сервер** чтобы он указывал на `/var/www/derevyannoe-domostroenie.ru/current/public`

## Настройка проекта

### 1. Настройте переменные деплоя

Скопируйте и отредактируйте файл конфигурации:
```bash
cp .env.deploy .env.deploy.local
```

Отредактируйте `.env.deploy.local`:
```bash
# Ваш production сервер
PRODUCTION_SERVER=user@your-server.com

# Ваш Git репозиторий
REPOSITORY=git@github.com:username/derevyannoe-domostroenie.ru.git

# Пути на сервере (измените если нужно)
APP_DIR=/var/www/derevyannoe-domostroenie.ru
RELEASES_DIR=/var/www/derevyannoe-domostroenie.ru/releases
```

### 2. Настройте SSH ключи

Убедитесь что у вас есть SSH доступ к серверу:
```bash
ssh user@your-server.com
```

### 3. Настройте Git доступ на сервере

На сервере добавьте SSH ключ для доступа к Git репозиторию:
```bash
ssh-keygen -t rsa -b 4096 -C "deploy@your-server.com"
cat ~/.ssh/id_rsa.pub
```

Добавьте публичный ключ в настройки репозитория (GitHub/GitLab).

## Использование

### Быстрый деплой

Используйте готовый скрипт:
```bash
# Деплой последнего коммита на production
./deploy.sh production

# Деплой конкретного коммита
./deploy.sh production abc123

# Деплой на staging (если настроен)
./deploy.sh staging
```

### Ручные команды Envoy

```bash
# Полный деплой
envoy run deploy --production=user@server.com --commit=HEAD

# Откат к предыдущей версии
envoy run rollback --production=user@server.com

# Очистка старых релизов (оставляет последние 5)
envoy run cleanup --production=user@server.com

# Выполнение отдельных задач
envoy run migrate_database --production=user@server.com
envoy run restart_services --production=user@server.com
```

## Доступные задачи

- **deploy** - Полный деплой (основная команда)
- **clone_repository** - Клонирование репозитория
- **run_composer** - Установка зависимостей
- **update_symlinks** - Обновление символических ссылок
- **optimize_application** - Кеширование и сборка фронтенда
- **migrate_database** - Выполнение миграций
- **restart_services** - Перезапуск сервисов
- **rollback** - Откат к предыдущей версии
- **cleanup** - Очистка старых релизов

## Настройка уведомлений

### Slack уведомления

1. Создайте Slack webhook в вашем workspace
2. Добавьте URL в `.env.deploy.local`:
   ```bash
   SLACK_WEBHOOK=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK
   ```
3. Обновите Envoy.blade.php с правильным webhook URL

## Troubleshooting

### Частые проблемы:

1. **Ошибка прав доступа:**
   ```bash
   sudo chown -R www-data:www-data /var/www/derevyannoe-domostroenie.ru
   sudo chmod -R 755 /var/www/derevyannoe-domostroenie.ru
   ```

2. **Ошибка composer:**
   Убедитесь что composer установлен на сервере:
   ```bash
   which composer
   ```

3. **Ошибка npm/node:**
   Установите Node.js на сервере:
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt-get install -y nodejs
   ```

4. **Ошибка миграций:**
   Проверьте настройки базы данных в `.env` на сервере

### Логи деплоя

Логи Envoy сохраняются в:
- Локально: вывод команды
- На сервере: `/var/log/nginx/error.log`, `/var/log/php8.2-fpm.log`

## Безопасность

1. **Никогда не коммитьте** `.env.deploy.local` в репозиторий
2. **Используйте SSH ключи** вместо паролей
3. **Ограничьте доступ** к серверу только необходимым IP
4. **Регулярно обновляйте** зависимости и систему

## Дополнительные возможности

### Zero-downtime deployment

Текущая конфигурация обеспечивает zero-downtime deployment через символические ссылки.

### Мониторинг

Добавьте мониторинг деплоев через:
- Slack уведомления
- Email уведомления
- Логирование в файл

### Автоматический деплой

Настройте автоматический деплой через:
- GitHub Actions
- GitLab CI/CD
- Jenkins

Пример GitHub Action:
```yaml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Deploy
        run: |
          envoy run deploy --production=${{ secrets.PRODUCTION_SERVER }}
```
