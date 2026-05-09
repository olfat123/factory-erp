<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['bomItems'] = $this->record->bom
            ? $this->record->bom->items()->get(['material_id', 'quantity', 'unit_id'])->toArray()
            : [];
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->bomItems = $data['bomItems'] ?? [];
        unset($data['bomItems']);
        return $data;
    }

    protected array $bomItems = [];

    protected function afterSave(): void
    {
        $bom = $this->record->bom()->firstOrCreate([]);
        $bom->items()->delete();
        foreach ($this->bomItems as $item) {
            $bom->items()->create($item);
        }
    }
}
