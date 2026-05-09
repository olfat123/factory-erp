<?php

namespace App\Filament\Resources\ConsumptionReceiptResource\Pages;

use App\Filament\Resources\ConsumptionReceiptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsumptionReceipts extends ListRecords
{
    protected static string $resource = ConsumptionReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
