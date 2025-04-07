<?php

namespace App\Filament\Resources\ProductImageResource\Pages;

use App\Filament\Resources\ProductImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductImages extends ListRecords
{
    protected static string $resource = ProductImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear una imagen')
                ->modalHeading('Crear nueva imagen')
                ->modalWidth('4xl')
                ->modalSubmitActionLabel('Crear')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }
}
