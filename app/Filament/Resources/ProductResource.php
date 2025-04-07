<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;
use App\Models\Product;
use App\Models\Category;
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

    // Cambio de icono a bolsa de compras
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        // Obtener el ID de la categoría "Maquillaje"
        $maquillajeId = Category::where('name', 'Maquillaje')->first()?->id;

        return $form->schema([
            Select::make('category_id')
                ->label('Categoría')
                ->relationship('category', 'name')
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, callable $set) use ($maquillajeId) {
                    // Si no es la categoría maquillaje, limpiamos la subcategoría
                    if ($state != $maquillajeId) {
                        $set('sub_category_id', null);
                    }
                }),

            Select::make('sub_category_id')
                ->label('Subcategoría')
                ->relationship('subCategory', 'name')
                // Solo habilitado para categoría maquillaje
                ->disabled(fn(callable $get) => $get('category_id') != $maquillajeId)
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

            TextColumn::make('variants_sum_stock')
                ->label('Stock Total')
                ->formatStateUsing(fn($record) => $record->variants->sum('stock'))
                ->sortable(false),

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
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
