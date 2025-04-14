<?php

namespace App\Filament\Resources\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    protected static ?string $title = 'Atributos con Stock';

    public function form(Form $form): Form
    {
        return $form->schema([
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
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Atributo'),
                Tables\Columns\TextColumn::make('pivot.stock')->label('Stock'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->form([
                    Forms\Components\TextInput::make('stock')
                        ->label('Stock')
                        ->numeric()
                        ->required()
                        ->minValue(0),
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DetachAction::make()->label('Eliminar'),
            ]);
    }
}
