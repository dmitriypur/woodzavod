<?php

namespace App\Filament\Components;

use App\Helpers\SettingsHelper;
use Filament\Forms\Components\TextInput;

class SettingsTextInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $variables = SettingsHelper::getAvailableVariables();
        $helpText = 'Доступные переменные: ' . implode(', ', array_keys($variables));
        
        $this->helperText($helpText);
        $this->hint('Используйте {{переменная}} для вставки значений из настроек');
    }
    
    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();
        
        return $static;
    }
}