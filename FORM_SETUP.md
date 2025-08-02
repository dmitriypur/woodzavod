# Настройка формы обратной связи

## Описание
Система обработки форм с отправкой уведомлений на email и в Telegram.

## Роуты
- `POST /submit-form` - AJAX обработка форм с JSON ответом
- `POST /leads` - стандартная обработка форм с редиректом

## Настройка Email

### 1. Настройте переменные в .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@woodzavod.ru
MAIL_FROM_NAME="WoodZavod"

# Email для получения уведомлений
ADMIN_EMAIL=admin@woodzavod.ru
```

### 2. Для Gmail:
1. Включите двухфакторную аутентификацию
2. Создайте пароль приложения в настройках Google
3. Используйте пароль приложения в MAIL_PASSWORD

## Настройка Telegram

### 1. Создайте бота:
1. Напишите @BotFather в Telegram
2. Отправьте `/newbot`
3. Следуйте инструкциям
4. Получите токен бота

### 2. Получите Chat ID:
1. Добавьте бота в группу или напишите ему лично
2. Отправьте сообщение боту
3. Перейдите по ссылке: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Найдите `chat.id` в ответе

### 3. Настройте переменные в .env:
```env
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_CHAT_ID=-1001234567890
```

## Структура данных формы

Форма должна содержать поля:
- `name` (обязательное) - имя клиента
- `phone` (обязательное) - телефон
- `email` (опциональное) - email
- `message` (опциональное) - сообщение
- `house_id` (опциональное) - ID дома из каталога

## Пример использования в JavaScript

```javascript
// Форма должна иметь класс 'modal-form'
<form class="modal-form" method="POST">
    @csrf
    <input type="text" name="name" data-required placeholder="Ваше имя">
    <input type="tel" name="phone" data-required placeholder="Телефон">
    <input type="email" name="email" placeholder="Email">
    <textarea name="message" placeholder="Сообщение"></textarea>
    <input type="hidden" name="house_id" value="1">
    
    <div class="form-loader hidden">Отправка...</div>
    <div class="form-alert hidden"></div>
    
    <button type="submit">Отправить</button>
</form>
```

## Ответы API

### Успешная отправка:
```json
{
    "success": true,
    "message": "Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время."
}
```

### Ошибка валидации:
```json
{
    "success": false,
    "message": "Ошибка валидации: Поле имя обязательно для заполнения."
}
```

### Системная ошибка:
```json
{
    "success": false,
    "message": "Произошла ошибка при отправке заявки. Попробуйте позже."
}
```

## Логирование

Все ошибки логируются в `storage/logs/laravel.log`:
- Ошибки отправки email
- Ошибки отправки в Telegram
- Общие ошибки обработки форм

## Тестирование

1. Убедитесь что сервер запущен: `php artisan serve`
2. Откройте форму на сайте
3. Заполните и отправьте форму
4. Проверьте:
   - Запись в базе данных (таблица `leads`)
   - Email уведомление
   - Сообщение в Telegram
   - Логи в `storage/logs/laravel.log`