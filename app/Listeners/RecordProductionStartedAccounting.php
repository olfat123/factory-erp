<?php

namespace App\Listeners;

use App\Events\ProductionStarted;
use App\Services\AccountingService;

class RecordProductionStartedAccounting
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    public function handle(ProductionStarted $event): void
    {
        $event->productionOrder->load('items.material');
        $this->accountingService->recordMaterialConsumption($event->productionOrder);
    }
}
