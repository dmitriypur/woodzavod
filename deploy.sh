#!/bin/bash

# Скрипт для деплоя Laravel приложения через Envoy
# Использование: ./deploy.sh [production|staging] [commit-hash]

set -e

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Функция для вывода сообщений
echo_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

echo_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

echo_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Проверка аргументов
if [ $# -lt 1 ]; then
    echo_error "Использование: $0 [production|staging] [commit-hash]"
    echo "Примеры:"
    echo "  $0 production                    # Деплой последнего коммита на production"
    echo "  $0 production abc123             # Деплой конкретного коммита"
    echo "  $0 staging                       # Деплой на staging"
    exit 1
fi

ENVIRONMENT=$1
COMMIT=${2:-HEAD}

# Проверка окружения
if [ "$ENVIRONMENT" != "production" ] && [ "$ENVIRONMENT" != "staging" ]; then
    echo_error "Неизвестное окружение: $ENVIRONMENT. Используйте 'production' или 'staging'"
    exit 1
fi

# Загрузка переменных окружения
if [ -f .env.deploy ]; then
    source .env.deploy
else
    echo_warning "Файл .env.deploy не найден. Создайте его на основе .env.deploy.example"
fi

# Определение сервера
if [ "$ENVIRONMENT" = "production" ]; then
    SERVER=$PRODUCTION_SERVER
else
    SERVER=$STAGING_SERVER
fi

if [ -z "$SERVER" ]; then
    echo_error "Сервер для окружения $ENVIRONMENT не настроен в .env.deploy"
    exit 1
fi

echo_info "Начинаем деплой на $ENVIRONMENT ($SERVER)"
echo_info "Коммит: $COMMIT"

# Подтверждение для production
if [ "$ENVIRONMENT" = "production" ]; then
    echo_warning "Вы собираетесь выполнить деплой на PRODUCTION сервер!"
    read -p "Продолжить? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo_info "Деплой отменен"
        exit 0
    fi
fi

# Выполнение деплоя
echo_info "Выполняем деплой..."
envoy run deploy --production="$SERVER" --commit="$COMMIT"

if [ $? -eq 0 ]; then
    echo_info "Деплой успешно завершен!"
    echo_info "Не забудьте выполнить очистку старых релизов: envoy run cleanup --production=$SERVER"
else
    echo_error "Деплой завершился с ошибкой!"
    echo_info "Для отката используйте: envoy run rollback --production=$SERVER"
    exit 1
fi