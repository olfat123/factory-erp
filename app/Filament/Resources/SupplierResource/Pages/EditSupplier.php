<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
