# Система переменных настроек

Эта система позволяет использовать переменные из настроек сайта в текстовых полях Filament и выводить их на сайте.

## Доступные переменные

- `{{site_name}}` - Название сайта
- `{{phone}}` - Телефон
- `{{email}}` - Email
- `{{vk}}` - Ссылка на VK
- `{{telegram}}` - Ссылка на Telegram
- `{{youtube}}` - Ссылка на YouTube
- `{{rutube}}` - Ссылка на RuTube
- `{{city}}` - Город
- `{{postal_code}}` - Индекс
- `{{address}}` - Адрес
- `{{coordinates}}` - Координаты
- `{{schedule}}` - Расписание работы

## Использование в Filament

### Текстовые поля с поддержкой переменных

```php
use App\Filament\Components\SettingsTextInput;
use App\Filament\Components\SettingsTextarea;

// Вместо обычного TextInput
SettingsTextInput::make('title')
    ->label('Заголовок')
    ->placeholder('Например: {{site_name}} - лучшие дома');

// Вместо обычного Textarea
SettingsTextarea::make('description')
    ->label('Описание')
    ->placeholder('Компания {{site_name}} по адресу {{address}}');
```

### Автоматические подсказки

Компоненты `SettingsTextInput` и `SettingsTextarea` автоматически показывают:
- Список доступных переменных в helperText
- Подсказку о формате использования в hint

## Использование на сайте

### Blade директива @settings

```blade
{{-- Простое использование --}}
@settings('Добро пожаловать на {{site_name}}!')

{{-- В любом месте шаблона --}}
<p>@settings('Звоните: {{phone}}')</p>
<p>@settings('Пишите: {{email}}')</p>
```

### Прямое использование helper'а

```php
use App\Helpers\SettingsHelper;

$text = 'Компания {{site_name}} работает по адресу {{address}}';
$processedText = SettingsHelper::replaceVariables($text);
```

### В контроллерах

```php
use App\Helpers\SettingsHelper;

public function show($id)
{
    $page = Page::find($id);
    
    // Обработка переменных в контенте
    $page->processed_content = SettingsHelper::replaceVariables($page->content);
    
    return view('page', compact('page'));
}
```

## Примеры использования

### SEO мета-теги

```php
// В Filament форме
SettingsTextInput::make('seo.title')
    ->placeholder('{{site_name}} - качественные деревянные дома');

SettingsTextarea::make('seo.description')
    ->placeholder('Компания {{site_name}} строит дома. Телефон: {{phone}}');
```

### Контент страниц

```blade
{{-- В шаблоне --}}
<h1>@settings('Добро пожаловать в {{site_name}}')</h1>
<p>@settings('Мы находимся по адресу {{address}} и работаем {{schedule}}')</p>
<p>@settings('Связаться с нами: {{phone}} или {{email}}')</p>
```

### Футер сайта

```blade
<footer>
    <div class="contact-info">
        <h3>@settings('{{site_name}}')</h3>
        <p>@settings('Адрес: {{address}}')</p>
        <p>@settings('Телефон: {{phone}}')</p>
        <p>@settings('Email: {{email}}')</p>
        <p>@settings('Режим работы: {{schedule}}')</p>
    </div>
</footer>
```

## Тестирование

Для проверки работы системы переменных:

1. Перейдите на `/test-variables`
2. Убедитесь, что переменные корректно заменяются
3. Проверьте работу в админ-панели Filament

## Расширение системы

### Добавление новых переменных

1. Добавьте поле в `GeneralSettings`
2. Обновите массив в `SettingsHelper::replaceVariables()`
3. Обновите список в `SettingsHelper::getAvailableVariables()`

### Создание кастомных компонентов

```php
use App\Filament\Components\SettingsTextInput;

class CustomSettingsInput extends SettingsTextInput
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Дополнительная настройка
        $this->maxLength(500);
    }
}
```