<?php

namespace App\Filament\Resources\SubCategoryResource\Pages;

use App\Filament\Resources\SubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubCategories extends ListRecords
{
    protected static string $resource = SubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear una subcategoría')
                ->modalHeading('Crear nueva subcategoría')
                ->modalWidth('4xl')
                ->modalSubmitActionLabel('Crear')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }
}
