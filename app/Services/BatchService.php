<?php

namespace App\Services;

use App\Models\MaterialBatch;
use App\Models\ProductionBatch;

class BatchService
{
    public function getMaterialBatchHistory(int $materialId): \Illuminate\Database\Eloquent\Collection
    {
        return MaterialBatch::where('material_id', $materialId)
            ->with(['goodsReceiptItem.goodsReceipt', 'stockMovements'])
            ->orderByDesc('received_date')
            ->get();
    }

    public function getProductionBatchHistory(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        return ProductionBatch::where('product_id', $productId)
            ->with(['productionOrder'])
            ->orderByDesc('production_date')
            ->get();
    }

    public function generateMaterialBatchNumber(string $materialCode): string
    {
        return 'MB-' . strtoupper($materialCode) . '-' . now()->format('YmdHis');
    }

    public function generateProductionBatchNumber(string $orderNumber): string
    {
        return 'PB-' . strtoupper($orderNumber) . '-' . now()->format('YmdHis');
    }
}
