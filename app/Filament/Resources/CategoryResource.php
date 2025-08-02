<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Каталог';
    protected static ?string $label = 'Категория';
    protected static ?string $pluralLabel = 'Категории';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Основное')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->label('Слаг (URL)')
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(4)
                    ->nullable(),

                Forms\Components\Select::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Название')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('slug')
                ->label('Слаг')
                ->copyable()
                ->toggleable(),

            Tables\Columns\TextColumn::make('parent.name')
                ->label('Родитель')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Создано')
                ->dateTime()
                ->sortable(),
        ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
