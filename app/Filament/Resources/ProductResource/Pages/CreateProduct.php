<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected array $bomItems = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->bomItems = $data['bomItems'] ?? [];
        unset($data['bomItems']);
        return $data;
    }

    protected function afterCreate(): void
    {
        if (empty($this->bomItems)) {
            return;
        }
        $bom = $this->record->bom()->firstOrCreate([]);
        foreach ($this->bomItems as $item) {
            $bom->items()->create($item);
        }
    }
}
