<?php

namespace App\Contracts;

use App\Models\GoodsReceipt;
use App\Models\ProductionOrder;
use App\Models\StockMovement;

interface AccountingServiceInterface
{
    public function recordPurchaseReceiving(GoodsReceipt $goodsReceipt): void;

    public function recordMaterialConsumption(ProductionOrder $productionOrder): void;

    public function recordProductionOutput(ProductionOrder $productionOrder, float $unitCost): void;

    public function recordStockAdjustment(StockMovement $movement): void;
}
