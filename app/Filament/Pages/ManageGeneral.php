<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\TextInput;

class ManageGeneral extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $slug = 'settings/general';

    protected static ?string $navigationGroup = 'Настройки';

    protected static ?string $navigationLabel = 'Основные настройки';

    protected static ?string $title = 'Основные настройки';

    protected static ?int $navigationSort = 7;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')->schema([
                    TextInput::make('site_name')->label('Название сайта')->nullable(),
                    TextInput::make('phone')->label('Телефон')->nullable(),
                    TextInput::make('whatsapp')->label('WhatsApp')->nullable(),
                    TextInput::make('email')->label('Email')->nullable(),
                    TextInput::make('vk')->label('Ссылка на VK')->nullable(),
                    TextInput::make('telegram')->label('Ссылка на Telegram')->nullable(),
                    TextInput::make('youtube')->label('Ссылка на YouTube')->nullable(),
                    TextInput::make('rutube')->label('Ссылка на RuTube')->nullable(),
                    TextInput::make('city')->label('Город')->nullable(),
                    TextInput::make('postal_code')->label('Индекс')->nullable(),
                    TextInput::make('address')->label('Адрес')->nullable(),
                    TextInput::make('coordinates')->label('Координаты')->nullable(),
                    TextInput::make('schedule')->label('Режим работы')->nullable(),
                ]),
                Forms\Components\Section::make('Favicon')->schema([
                    Forms\Components\FileUpload::make('favicon')
                        ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('120')
                        ->imageResizeTargetHeight('120')
                        ->label('favicon')
                        ->nullable(),
                ]),
            ]);
    }
}
