<?php

namespace App\Filament\Resources;

use App\Filament\Components\SettingsRichEditor;
use App\Filament\Components\SettingsTextInput;
use App\Filament\Components\SettingsTextarea;
use App\Filament\Components\SettingsTiptapEditor;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $label = 'Страница';
    protected static ?string $pluralLabel = 'Страницы';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                        if ($context === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('Слаг')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash'])
                    ->helperText('Используется в URL страницы'),

                // SettingsRichEditor::make('content')
                //     ->label('Содержимое')
                //     ->columnSpanFull(),

               SettingsTiptapEditor::make('content')
                    ->label('Текст')
                   ->columnSpanFull(),

                Forms\Components\Toggle::make('is_published')
                    ->label('Опубликовано'),

                Section::make('SEO')->schema([
                    SettingsTextInput::make('seo.title')
                        ->label('SEO Title'),

                    Forms\Components\TextInput::make('seo.canonical')
                        ->label('Канонический URL')
                        ->prefix(rtrim(config('app.url'), '/')),

                    SettingsTextarea::make('seo.description')
                        ->helperText(function (?string $state): string {
                            return (string)Str::of(strlen($state))
                                ->append(' / ')
                                ->append(160 . ' ')
                                ->append('символов');
                        })
                        ->reactive(),

                    Forms\Components\Checkbox::make('seo.noindex')
                        ->label('Запретить поисковикам индексировать эту страницу'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Слаг')
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('clone')
                    ->label('Клонировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('success')
                    ->action(function (Page $record) {
                        $clonedPage = $record->replicate();
                        $clonedPage->title = $record->title . ' (копия)';
                        $clonedPage->slug = Page::generateUniqueSlug($clonedPage->title);
                        $clonedPage->save();

                        Notification::make()
                            ->title('Страница успешно клонирована')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Клонировать страницу')
                    ->modalDescription('Вы уверены, что хотите создать копию этой страницы?')
                    ->modalSubmitActionLabel('Клонировать'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('clone_bulk')
                        ->label('Клонировать выбранные')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $clonedCount = 0;
                            foreach ($records as $record) {
                                $clonedPage = $record->replicate();
                                $clonedPage->title = $record->title . ' (копия)';
                                $clonedPage->slug = Page::generateUniqueSlug($clonedPage->title);
                                $clonedPage->save();
                                $clonedCount++;
                            }

                            Notification::make()
                                ->title("Клонировано страниц: {$clonedCount}")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Клонировать выбранные страницы')
                        ->modalDescription('Вы уверены, что хотите создать копии выбранных страниц?')
                        ->modalSubmitActionLabel('Клонировать'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
