<?php

namespace App\Listeners;

use App\Events\GoodsReceived;
use App\Services\AccountingService;

class RecordGoodsReceivedAccounting
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    public function handle(GoodsReceived $event): void
    {
        $this->accountingService->recordPurchaseReceiving($event->goodsReceipt);
    }
}
