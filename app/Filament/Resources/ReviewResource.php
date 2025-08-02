<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Отзывы';

    protected static ?string $pluralModelLabel = 'Отзывы';

    protected static ?string $modelLabel = 'Отзыв';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('house_id')
                    ->label('Дом')
                    ->relationship('house', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('author')
                    ->label('Автор')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('text')
                    ->label('Текст отзыва')
                    ->required()
                    ->rows(5),

                SpatieMediaLibraryFileUpload::make('avatar')
                    ->label('Аватар')
                    ->collection('main')
                    ->image(),

                Forms\Components\Toggle::make('is_published')
                    ->label('Опубликован')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('house.title')
                    ->label('Дом')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('text')
                    ->label('Текст')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликован')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_published')
                    ->label('Статус публикации')
                    ->options([
                        '1' => 'Опубликованные',
                        '0' => 'Неопубликованные',
                    ]),

                Tables\Filters\SelectFilter::make('house_id')
                    ->label('Дом')
                    ->relationship('house', 'title'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('clone')
                    ->label('Клонировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Клонировать отзыв')
                    ->modalDescription('Вы уверены, что хотите клонировать этот отзыв?')
                    ->modalSubmitActionLabel('Клонировать')
                    ->action(function (Review $record) {
                        $clonedData = $record->toArray();
                        unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);

                        // Добавляем суффикс к автору
                        $clonedData['author'] = $record->author . ' (копия)';

                        Review::create($clonedData);

                        Notification::make()
                            ->title('Отзыв успешно клонирован')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Опубликовать')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => true])),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Снять с публикации')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => false])),
                    Tables\Actions\BulkAction::make('clone_bulk')
                        ->label('Клонировать выбранные')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Клонировать отзывы')
                        ->modalDescription('Вы уверены, что хотите клонировать выбранные отзывы?')
                        ->modalSubmitActionLabel('Клонировать')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $clonedData = $record->toArray();
                                unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);

                                $clonedData['author'] = $record->author . ' (копия)';

                                Review::create($clonedData);
                            }

                            Notification::make()
                                ->title('Отзывы успешно клонированы')
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
