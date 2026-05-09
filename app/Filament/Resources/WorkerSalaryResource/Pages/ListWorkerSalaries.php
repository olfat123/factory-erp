<?php

namespace App\Filament\Resources\WorkerSalaryResource\Pages;

use App\Filament\Resources\WorkerSalaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkerSalaries extends ListRecords
{
    protected static string $resource = WorkerSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
