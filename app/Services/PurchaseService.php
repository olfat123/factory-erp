<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Enums\StockMovementType;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function approvePurchaseOrder(PurchaseOrder $order, int $userId): void
    {
        if ($order->status !== PurchaseOrderStatus::Draft) {
            throw new \LogicException(__('purchase.already_approved'));
        }

        $order->update([
            'status' => PurchaseOrderStatus::Approved,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Receive goods against a purchase order.
     * @param array $items [['purchase_order_item_id', 'quantity', 'unit_cost', 'batch_number', 'expiry_date'], ...]
     */
    public function receiveGoods(PurchaseOrder $order, array $items, int $userId): GoodsReceipt
    {
        return DB::transaction(function () use ($order, $items, $userId) {
            $receipt = GoodsReceipt::create([
                'number' => $this->generateReceiptNumber(),
                'purchase_order_id' => $order->id,
                'received_by' => $userId,
                'received_date' => now()->toDateString(),
            ]);

            foreach ($items as $itemData) {
                $poItem = $order->items()->findOrFail($itemData['purchase_order_item_id']);
                $material = Material::findOrFail($poItem->material_id);

                $receiptItem = GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $poItem->id,
                    'material_id' => $material->id,
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                ]);

                // Create material batch
                $batch = MaterialBatch::create([
                    'batch_number' => $itemData['batch_number'] ?? $this->generateBatchNumber($material),
                    'material_id' => $material->id,
                    'goods_receipt_item_id' => $receiptItem->id,
                    'initial_quantity' => $itemData['quantity'],
                    'current_quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'received_date' => now()->toDateString(),
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                ]);

                // Record stock movement
                $this->inventoryService->recordMovement(
                    item: $material,
                    type: StockMovementType::PurchaseReceive,
                    quantity: (float) $itemData['quantity'],
                    createdBy: $userId,
                    unitCost: (float) $itemData['unit_cost'],
                    batchId: $batch->id,
                    reference: $receipt,
                );

                // Update PO item received quantity
                $poItem->increment('received_quantity', $itemData['quantity']);
            }

            // Recalculate PO total received & update status
            $order->refresh();
            $allReceived = $order->items->every(fn($i) => $i->received_quantity >= $i->quantity);
            $anyReceived = $order->items->some(fn($i) => $i->received_quantity > 0);

            $order->update([
                'status' => $allReceived
                    ? PurchaseOrderStatus::FullyReceived
                    : ($anyReceived ? PurchaseOrderStatus::PartiallyReceived : $order->status),
            ]);

            return $receipt;
        });
    }

    private function generateReceiptNumber(): string
    {
        $last = GoodsReceipt::latest('id')->value('number');
        $next = $last ? (int) substr($last, 3) + 1 : 1;
        return 'GR-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    private function generateBatchNumber(Material $material): string
    {
        return 'BATCH-' . strtoupper($material->code) . '-' . now()->format('YmdHis');
    }
}
