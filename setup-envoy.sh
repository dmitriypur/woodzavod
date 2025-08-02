#!/bin/bash

# Скрипт для настройки Laravel Envoy
# Запустите один раз: ./setup-envoy.sh

echo "Настройка Laravel Envoy..."

# Проверяем установлен ли Envoy
if ! command -v envoy &> /dev/null; then
    echo "Envoy не найден в PATH. Добавляем Composer bin директорию..."
    
    # Определяем shell
    if [[ $SHELL == *"zsh"* ]]; then
        SHELL_RC="$HOME/.zshrc"
    elif [[ $SHELL == *"bash"* ]]; then
        SHELL_RC="$HOME/.bashrc"
    else
        SHELL_RC="$HOME/.profile"
    fi
    
    # Добавляем PATH если его еще нет
    if ! grep -q "composer/vendor/bin" "$SHELL_RC" 2>/dev/null; then
        echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> "$SHELL_RC"
        echo "Добавлен PATH в $SHELL_RC"
        echo "Перезапустите терминал или выполните: source $SHELL_RC"
    else
        echo "PATH уже настроен в $SHELL_RC"
    fi
    
    # Экспортируем для текущей сессии
    export PATH="$HOME/.composer/vendor/bin:$PATH"
fi

# Проверяем версию
if command -v envoy &> /dev/null; then
    echo "✅ Envoy установлен: $(envoy --version)"
    echo "✅ Доступные задачи:"
    envoy tasks
else
    echo "❌ Ошибка: Envoy не найден"
    exit 1
fi

echo ""
echo "🚀 Готово! Теперь вы можете использовать:"
echo "   envoy run deploy --production=user@server.com --commit=HEAD"
echo "   ./deploy.sh production"
echo ""
echo "📖 Подробная документация: DEPLOYMENT.md"