<?php

namespace App\Filament\Resources\ProductionOrderResource\Pages;

use App\Filament\Resources\ProductionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditProductionOrder extends EditRecord
{
    protected static string $resource = ProductionOrderResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
