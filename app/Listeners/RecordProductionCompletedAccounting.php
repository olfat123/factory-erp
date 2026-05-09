<?php

namespace App\Listeners;

use App\Events\ProductionCompleted;
use App\Services\AccountingService;

class RecordProductionCompletedAccounting
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    public function handle(ProductionCompleted $event): void
    {
        $unitCost = $event->productionBatch->unit_cost ?? 0;
        $this->accountingService->recordProductionOutput($event->productionOrder, (float) $unitCost);
    }
}
