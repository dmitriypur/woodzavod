# Настройка Filament Shield при деплое

Этот документ описывает автоматическую настройку системы разрешений Filament Shield при деплое на production сервер.

## Автоматическая настройка

### Команда shield:setup

Создана команда `php artisan shield:setup` которая автоматически:

1. **Генерирует разрешения** для всех ресурсов и страниц (`shield:generate --all`)
2. **Создает супер-администратора** (`shield:super-admin --user=1`)
3. **Очищает кеш** для применения изменений

### Интеграция с Envoy

В `Envoy.blade.php` обновлена задача `migrateDatabase` с механизмом однократного выполнения:

```bash
@task('migrateDatabase', ['on' => 'remote'])
{{ logMessage("🙈  Migrating database and setting up permissions...") }}
cd {{ $newReleaseDir }}
php artisan migrate --force

# Check if shield:setup has already been run
if [ ! -f {{ $baseDir }}/storage/.shield-setup-done ]; then
    php artisan shield:setup
    touch {{ $baseDir }}/storage/.shield-setup-done
    echo "Shield setup completed and marked as done"
else
    echo "Shield setup already completed, skipping..."
fi
@endtask
```

**Механизм однократного выполнения:**
- Команда `shield:setup` выполняется только при первом деплое
- Создается флаг-файл `/var/www/derevyannoe-domostroenie.ru/storage/.shield-setup-done`
- При последующих деплоях команда пропускается
- Для принудительного повторного выполнения удалите флаг-файл

Теперь при каждом деплое автоматически:
- Выполняются миграции
- Настраиваются разрешения Shield (только при первом деплое)
- Создается/обновляется супер-администратор (только при первом деплое)

## Ручная настройка

### На production сервере

```bash
# Подключение к серверу
ssh root@77.222.42.47

# Переход в директорию приложения
cd /var/www/derevyannoe-domostroenie.ru/current

# Настройка Shield
php artisan shield:setup

# Или по отдельности:
php artisan shield:generate --all
php artisan shield:super-admin --user=1
php artisan cache:clear
```

### Параметры команды

```bash
# Создать супер-админа для другого пользователя
php artisan shield:setup --user-id=5

# Справка по команде
php artisan shield:setup --help
```

### Принудительное повторное выполнение

Если нужно повторно запустить настройку Shield:

```bash
# Удалить флаг-файл
rm /var/www/derevyannoe-domostroenie.ru/storage/.shield-setup-done

# Запустить деплой или выполнить команду вручную
php artisan shield:setup
```

**Когда это может понадобиться:**
- Добавлены новые ресурсы или страницы
- Изменились политики доступа
- Нужно пересоздать разрешения после изменений в коде

## Проверка после деплоя

### 1. Таблицы в базе данных

Должны быть созданы таблицы:
- `permissions`
- `roles`
- `model_has_permissions`
- `model_has_roles` 
- `role_has_permissions`

### 2. Роли и разрешения

```sql
-- Проверить созданные роли
SELECT * FROM roles;

-- Проверить разрешения
SELECT * FROM permissions;

-- Проверить назначение ролей пользователям
SELECT u.email, r.name as role 
FROM users u 
JOIN model_has_roles mhr ON u.id = mhr.model_id 
JOIN roles r ON mhr.role_id = r.id;
```

### 3. Доступ к админ-панели

- Войти в админ-панель: `https://derevyannoe-domostroenie.ru/admin`
- Проверить доступ ко всем разделам
- Убедиться что пользователь имеет роль `super_admin`

## Созданные разрешения

### Ресурсы
- **Categories**: `view_category`, `view_any_category`, `create_category`, `update_category`, `delete_category`, `delete_any_category`
- **Houses**: `view_house`, `view_any_house`, `create_house`, `update_house`, `delete_house`, `delete_any_house`
- **Leads**: `view_lead`, `view_any_lead`, `create_lead`, `update_lead`, `delete_lead`, `delete_any_lead`
- **Pages**: `view_page`, `view_any_page`, `create_page`, `update_page`, `delete_page`, `delete_any_page`
- **Reviews**: `view_review`, `view_any_review`, `create_review`, `update_review`, `delete_review`, `delete_any_review`
- **Roles**: `view_role`, `view_any_role`, `create_role`, `update_role`, `delete_role`, `delete_any_role`
- **Users**: `view_user`, `view_any_user`, `create_user`, `update_user`, `delete_user`, `delete_any_user`

### Страницы
- **ManageGeneral**: `view_manage::general`
- **ManageSitemap**: `view_manage::sitemap`

## Troubleshooting

### Ошибка "Permission denied"

```bash
# Проверить права на файлы
sudo chown -R www-data:www-data /var/www/derevyannoe-domostroenie.ru
sudo chmod -R 755 /var/www/derevyannoe-domostroenie.ru
```

### Ошибка "User not found"

```bash
# Создать пользователя если не существует
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@admin.ru', 'password' => bcrypt('password')]);
>>> exit

# Затем настроить Shield
php artisan shield:setup --user-id=1
```

### Ошибка "Class not found"

```bash
# Очистить кеш и перегенерировать автозагрузку
composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Логи ошибок

Проверить логи в:
- `/var/www/derevyannoe-domostroenie.ru/current/storage/logs/laravel.log`
- `/var/log/nginx/error.log`
- `/var/log/php8.3-fpm.log`

## Безопасность

1. **Смените пароль** супер-администратора после первого входа
2. **Создайте отдельные роли** для разных типов пользователей
3. **Ограничьте разрешения** согласно принципу минимальных привилегий
4. **Регулярно проверяйте** назначенные роли и разрешения

## Дополнительные команды

```bash
# Показать все разрешения
php artisan shield:show

# Создать новую роль
php artisan shield:create-role

# Назначить роль пользователю
php artisan shield:assign-role

# Удалить роль у пользователя
php artisan shield:remove-role
```