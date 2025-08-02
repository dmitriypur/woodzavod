<?php

namespace App\Helpers;

use App\Settings\GeneralSettings;

class SettingsHelper
{
    /**
     * Заменяет переменные настроек в тексте
     * Использование: {{site_name}}, {{phone}}, {{email}} и т.д.
     */
    public static function replaceVariables(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        $settings = app(GeneralSettings::class);
        
        $variables = [
            '{{site_name}}' => $settings->site_name,
            '{{phone}}' => $settings->phone,
            '{{email}}' => $settings->email,
            '{{vk}}' => $settings->vk,
            '{{telegram}}' => $settings->telegram,
            '{{youtube}}' => $settings->youtube,
            '{{rutube}}' => $settings->rutube,
            '{{city}}' => $settings->city,
            '{{postal_code}}' => $settings->postal_code,
            '{{address}}' => $settings->address,
            '{{coordinates}}' => $settings->coordinates,
            '{{schedule}}' => $settings->schedule,
        ];

        return str_replace(array_keys($variables), array_values($variables), $text);
    }

    /**
     * Извлекает только цифры из номера телефона
     */
    public static function phoneDigitsOnly(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Получает список доступных переменных для подсказки
     */
    public static function getAvailableVariables(): array
    {
        return [
            '{{site_name}}' => 'Название сайта',
            '{{phone}}' => 'Телефон',
            '{{email}}' => 'Email',
            '{{vk}}' => 'Ссылка на VK',
            '{{telegram}}' => 'Ссылка на Telegram',
            '{{youtube}}' => 'Ссылка на YouTube',
            '{{rutube}}' => 'Ссылка на RuTube',
            '{{city}}' => 'Город',
            '{{postal_code}}' => 'Индекс',
            '{{address}}' => 'Адрес',
            '{{coordinates}}' => 'Координаты',
            '{{schedule}}' => 'Расписание работы',
        ];
    }
}