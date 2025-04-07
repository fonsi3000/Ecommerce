<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImageResource\Pages;
use App\Models\ProductImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;

class ProductImageResource extends Resource
{
    protected static ?string $model = ProductImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Selección del producto por su nombre con búsqueda
            Select::make('product_id')
                ->label('Producto')
                ->relationship('product', 'name')
                ->searchable() // Habilita la búsqueda
                ->preload() // Precarga algunos productos
                ->placeholder('Busca un producto por nombre...')
                ->required(),

            // FileUpload para subir la imagen
            FileUpload::make('image_path')
                ->label('Imagen')
                ->disk('public')
                ->directory('product_images')
                ->visibility('public')
                ->image()
                ->imageEditor()
                ->maxSize(5120)
                ->downloadable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Mostrar el nombre del producto y atributo por relación
            TextColumn::make('product.name')
                ->label('Producto')
                ->searchable()
                ->sortable(),
            // Columna de imagen para mostrar la imagen cargada
            ImageColumn::make('image_path')
                ->label('Imagen')
                ->disk('public')
                ->width(100)
                ->height(80)
                ->extraImgAttributes(['loading' => 'lazy'])
                ->checkFileExistence(false),

            TextColumn::make('created_at')
                ->label('Creado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label('Actualizado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->actions([
                // Acción personalizada para ver la imagen en un modal
                Action::make('verImagen')
                    ->label('Ver Imagen')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Vista previa de imagen')
                    ->modalWidth('sm')
                    ->modalContent(fn($record) => new \Illuminate\Support\HtmlString(
                        '<div class="flex justify-center">
                        <img src="' . Storage::disk('public')->url($record->image_path) . '" alt="Imagen" class="rounded shadow-md" style="max-width:100%;"/>
                     </div>'
                    )),

                // Acción de edición (modal)
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalHeading('Editar Imagen')
                    ->modalWidth('4xl'),

                // Acción de eliminación
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductImages::route('/'),
        ];
    }
}
