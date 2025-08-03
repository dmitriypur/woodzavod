# Быстрый деплой на shared хостинг

## Подготовка (один раз)

1. **Настройте .env.production:**
   ```bash
   cp .env.production .env.production.local
   nano .env.production.local
   ```
   
   Измените:
   - `APP_KEY` - сгенерируйте: `php artisan key:generate --show`
   - `APP_URL` - ваш домен
   - `DB_*` - настройки базы данных от хостинга
   - `MAIL_*` - настройки почты от хостинга
   - `SESSION_DOMAIN` - ваш домен

2. **Переименуйте файл:**
   ```bash
   mv .env.production.local .env.production
   ```

## Деплой

1. **Запустите скрипт подготовки:**
   ```bash
   ./prepare-shared-hosting.sh production
   ```

2. **Загрузите архив на хостинг** через cPanel File Manager или FTP

3. **Распакуйте архив** в корень сайта

4. **Настройте структуру файлов:**
   
   **Вариант A (есть доступ к корню):**
   - Переместите содержимое `public/` в `public_html/`
   - Остальные файлы оставьте в корне
   
   **Вариант B (только public_html):**
   - Создайте папку `laravel/` в `public_html/`
   - Переместите все файлы кроме `public/` в `laravel/`
   - Содержимое `public/` переместите в `public_html/`
   - Переименуйте `index.php` → `index-backup.php`
   - Переименуйте `index-shared-hosting.php` → `index.php`

5. **Настройте права доступа:**
   - Папки: 755
   - Файлы: 644
   - `storage/` и `bootstrap/cache/`: 777 (рекурсивно)

6. **Настройте базу данных:**
   - Создайте БД через cPanel
   - Импортируйте дамп или выполните миграции

7. **Создайте администратора:**
   - Откройте `https://yourdomain.com/create-admin.php`
   - Заполните форму
   - **УДАЛИТЕ файл после использования!**

8. **Проверьте работу:**
   - Основной сайт: `https://yourdomain.com`
   - Админка: `https://yourdomain.com/admin`

## Обновление

1. Внесите изменения в код
2. Запустите `./prepare-shared-hosting.sh production`
3. Загрузите новый архив
4. Замените файлы (кроме `.env` и `storage/`)

## Troubleshooting

- **500 ошибка:** Проверьте права на `storage/` (777)
- **Белая страница:** Проверьте пути в `index.php`
- **Ошибки БД:** Проверьте настройки в `.env`
- **Логи:** Смотрите в `storage/logs/laravel.log`

## Полезные команды

```bash
# Генерация ключа приложения
php artisan key:generate --show

# Очистка кешей (локально)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Создание кешей (локально)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

📖 **Подробная инструкция:** `SHARED_HOSTING_DEPLOYMENT.md`