<?php

namespace App\Filament\Resources\MaterialCategoryResource\Pages;

use App\Filament\Resources\MaterialCategoryResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditMaterialCategory extends EditRecord
{
    protected static string $resource = MaterialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
