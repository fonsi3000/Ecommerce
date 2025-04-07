<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    // Este valor debe coincidir con el método variants() en el modelo Product.
    protected static string $relationship = 'variants';

    // Título opcional para la pestaña o sección.
    protected static ?string $title = 'Variantes del Producto';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('color')
                ->label('Color')
                ->required(),
            Forms\Components\TextInput::make('tono')
                ->label('Tono')
                ->nullable(),
            Forms\Components\TextInput::make('stock')
                ->label('Stock')
                ->numeric()
                ->required()
                ->minValue(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('color')->label('Color'),
                Tables\Columns\TextColumn::make('tono')->label('Tono'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Agregar Variante'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
