<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HouseResource\Pages;
use App\Filament\Components\SettingsRichEditor;
use App\Filament\Components\SettingsTextInput;
use App\Filament\Components\SettingsTextarea;
use App\Models\House;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class HouseResource extends Resource
{
    protected static ?string $model = House::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')->schema([
                    TextInput::make('title')
                        ->label('Название дома')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('subtitle')
                        ->label('Подзаголовок')
                        ->maxLength(255),

                    SettingsRichEditor::make('description')
                        ->label('Описание')
                        ->columnSpanFull(),

                    Select::make('categories')
                        ->label('Категории')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Category::all()
                                ->mapWithKeys(function ($category) {
                                    $prefix = $category->parent ? $category->parent->name . ' / ' : '';
                                    return [$category->id => $prefix . $category->name];
                                });
                        }),
                    TextInput::make('price')
                        ->label('Цена'),

                    TextInput::make('old_price')
                        ->label('Старая цена'),

                    Toggle::make('is_published')
                        ->label('Опубликовано')
                        ->default(true),
                ]),

                Section::make('Характеристики')->schema([
                    TextInput::make('area_total')
                        ->label('Общая площадь (м²)')
                        ->numeric()
                        ->step(0.1),

                    TextInput::make('floor_count')
                        ->label('Этажей')
                        ->numeric(),

                    TextInput::make('brus_volume')
                        ->label('Объем бруса (м³)')
                        ->numeric()
                        ->step(0.1),

                    TextInput::make('bedroom_count')
                        ->label('Количество спален')
                        ->numeric(),

                    TextInput::make('bathroom_count')
                        ->label('Количество санузлов')
                        ->numeric(),
                ]),

                Section::make('Изображения')->schema([
                    SpatieMediaLibraryFileUpload::make('main_image')
                        ->label('Главное изображение')
                        ->directory('main')
                        ->collection('main')
                        ->image(),

                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->label('Галерея')
                        ->collection('gallery')
                        ->directory('gallery')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->downloadable(),
                ]),
                Section::make('SEO')->schema([
                    SettingsTextInput::make('seo.title'),

                    TextInput::make('slug')
                        ->label('Слаг (URL)')
                        ->disabled()
                        ->dehydrated()
                        ->unique(ignoreRecord: true),

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
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Категории')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_published')
                    ->label('Статус публикации')
                    ->options([
                        '1' => 'Опубликованные',
                        '0' => 'Черновики',
                    ]),
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Категория')
                    ->relationship('categories', 'name')
                    ->multiple(),
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_from')
                                    ->label('Цена от')
                                    ->numeric(),
                                Forms\Components\TextInput::make('price_to')
                                    ->label('Цена до')
                                    ->numeric(),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('clone')
                    ->label('Клонировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Клонировать дом')
                    ->modalDescription('Вы уверены, что хотите клонировать этот дом?')
                    ->modalSubmitActionLabel('Клонировать')
                    ->action(function (House $record) {
                        $clonedData = $record->toArray();
                        unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);

                        // Добавляем суффикс к названию
                        $clonedData['title'] = $record->title . ' (копия)';

                        // Создаем клон
                        $clonedHouse = House::create($clonedData);

                        // Копируем связи с категориями
                        $clonedHouse->categories()->sync($record->categories->pluck('id'));

                        // Копируем медиафайлы
                        if ($record->hasMedia('main')) {
                            $record->getFirstMedia('main')->copy($clonedHouse, 'main');
                        }

                        foreach ($record->getMedia('gallery') as $media) {
                            $media->copy($clonedHouse, 'gallery');
                        }

                        Notification::make()
                            ->title('Дом успешно клонирован')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('clone_bulk')
                        ->label('Клонировать выбранные')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Клонировать дома')
                        ->modalDescription('Вы уверены, что хотите клонировать выбранные дома?')
                        ->modalSubmitActionLabel('Клонировать')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $clonedData = $record->toArray();
                                unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);

                                $clonedData['title'] = $record->title . ' (копия)';

                                $clonedHouse = House::create($clonedData);

                                // Копируем связи с категориями
                                $clonedHouse->categories()->sync($record->categories->pluck('id'));

                                // Копируем медиафайлы
                                if ($record->hasMedia('main')) {
                                    $record->getFirstMedia('main')->copy($clonedHouse, 'main');
                                }

                                foreach ($record->getMedia('gallery') as $media) {
                                    $media->copy($clonedHouse, 'gallery');
                                }
                            }

                            Notification::make()
                                ->title('Дома успешно клонированы')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListHouses::route('/'),
            'create' => Pages\CreateHouse::route('/create'),
            'edit' => Pages\EditHouse::route('/{record}/edit'),
        ];
    }
}
