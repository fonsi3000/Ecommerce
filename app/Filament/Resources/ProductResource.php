<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\AttributesRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category_id')
                ->label('Categoría')
                ->relationship('category', 'name')
                ->live()
                ->required(),

            Select::make('sub_category_id')
                ->label('Subcategoría')
                ->relationship('subCategory', 'name')
                ->disabled(fn(callable $get) => !$get('category_id'))
                ->nullable(),

            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Descripción')
                ->columnSpanFull(),

            TextInput::make('price')
                ->label('Precio')
                ->required()
                ->numeric()
                ->prefix('$')
                ->suffix('COP'),

            TextInput::make('stock')
                ->label('Stock')
                ->required()
                ->numeric()
                ->default(0),

            FileUpload::make('image')
                ->label('Imagen principal')
                ->disk('public')
                ->directory('products')
                ->visibility('public')
                ->image()
                ->imageEditor()
                ->maxSize(5120)
                ->downloadable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('category.name')
                ->label('Categoría')
                ->searchable()
                ->sortable(),

            TextColumn::make('subCategory.name')
                ->label('Subcategoría')
                ->searchable()
                ->sortable(),

            TextColumn::make('name')
                ->label('Nombre')
                ->searchable(),

            TextColumn::make('slug')
                ->label('Slug')
                ->searchable(),

            TextColumn::make('price')
                ->label('Precio')
                ->money('COP')
                ->sortable(),

            TextColumn::make('stock')
                ->label('Stock')
                ->numeric()
                ->sortable(),

            TextColumn::make('image')
                ->label('Ruta de imagen')
                ->color('danger')
                ->size('xs'),

            ImageColumn::make('image')
                ->label('Imagen')
                ->disk('public')
                ->width(100)
                ->height(80)
                ->extraImgAttributes(['loading' => 'lazy'])
                ->checkFileExistence(false),
        ])
            ->actions([
                Action::make('verImagen')
                    ->label('Ver Imagen')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Vista previa de imagen')
                    ->modalWidth('sm')
                    ->modalContent(function ($record) {
                        $url = $record->image
                            ? Storage::disk('public')->url($record->image)
                            : 'https://via.placeholder.com/150?text=Sin+Imagen';

                        return new HtmlString(
                            '<div class="flex justify-center p-4">
                            <img src="' . $url . '" alt="Imagen del producto"
                                 class="rounded shadow-md max-h-72 object-contain">
                        </div>'
                        );
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalHeading('Editar Producto')
                    ->modalWidth('4xl'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttributesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
        ];
    }
}
