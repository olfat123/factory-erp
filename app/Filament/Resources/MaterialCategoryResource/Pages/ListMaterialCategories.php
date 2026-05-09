<?php

namespace App\Filament\Resources\MaterialCategoryResource\Pages;

use App\Filament\Resources\MaterialCategoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListMaterialCategories extends ListRecords
{
    protected static string $resource = MaterialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
