<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variantes del Producto';

    public function form(Form $form): Form
    {
        return $form->schema([
            // Selector del tipo de atributo
            Forms\Components\Select::make('atributo_tipo')
                ->label('Tipo de Atributo')
                ->options([
                    'color' => 'Color',
                    'tono' => 'Tono',
                    'ninguno' => 'Sin atributo',
                ])
                ->default('ninguno')
                ->required()
                ->reactive(),

            // Campos de COLOR
            Forms\Components\TextInput::make('nombre_color')
                ->label('Nombre del color')
                ->maxLength(50)
                ->requiredIf('atributo_tipo', 'color')
                ->visible(fn(callable $get) => $get('atributo_tipo') === 'color'),

            Forms\Components\ColorPicker::make('color')
                ->label('Color')
                ->requiredIf('atributo_tipo', 'color')
                ->visible(fn(callable $get) => $get('atributo_tipo') === 'color'),

            // Campos de TONO
            Forms\Components\TextInput::make('nombre_tono')
                ->label('Nombre del tono')
                ->maxLength(50)
                ->requiredIf('atributo_tipo', 'tono')
                ->visible(fn(callable $get) => $get('atributo_tipo') === 'tono'),

            Forms\Components\ColorPicker::make('tono')
                ->label('Tono')
                ->requiredIf('atributo_tipo', 'tono')
                ->visible(fn(callable $get) => $get('atributo_tipo') === 'tono'),

            // Stock
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
                // Tipo de atributo
                Tables\Columns\BadgeColumn::make('atributo_tipo')
                    ->label('Tipo')
                    ->colors([
                        'color' => 'info',
                        'tono' => 'warning',
                        'ninguno' => 'gray',
                    ])
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'color' => 'Color',
                        'tono' => 'Tono',
                        'ninguno' => 'Sin atributo',
                        default => $state,
                    }),

                // Color
                Tables\Columns\TextColumn::make('nombre_color')
                    ->label('Nombre color')
                    ->default('-'),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),

                // Tono
                Tables\Columns\TextColumn::make('nombre_tono')
                    ->label('Nombre tono')
                    ->default('-'),

                Tables\Columns\ColorColumn::make('tono')
                    ->label('Tono'),

                // Stock
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state) => $state > 0 ? 'success' : 'danger'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Variante')
                    ->modalHeading('Nueva Variante')
                    ->modalWidth('md'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar Variante')
                    ->modalHeading('Editar Variante')
                    ->modalWidth('md'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
