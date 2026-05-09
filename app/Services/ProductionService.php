<?php

namespace App\Services;

use App\Enums\ProductionOrderStatus;
use App\Enums\StockMovementType;
use App\Events\ProductionCompleted;
use App\Events\ProductionStarted;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function createOrder(Product $product, float $quantity, int $userId, ?string $plannedDate = null): ProductionOrder
    {
        return DB::transaction(function () use ($product, $quantity, $userId, $plannedDate) {
            $order = ProductionOrder::create([
                'number' => $this->generateOrderNumber(),
                'product_id' => $product->id,
                'created_by' => $userId,
                'status' => ProductionOrderStatus::Draft,
                'quantity' => $quantity,
                'planned_date' => $plannedDate,
            ]);

            // Auto-calculate materials from BOM
            $bom = $product->bom()->with('items.material')->firstOrFail();

            foreach ($bom->items as $bomItem) {
                ProductionOrderItem::create([
                    'production_order_id' => $order->id,
                    'material_id' => $bomItem->material_id,
                    'required_quantity' => $bomItem->quantity * $quantity,
                    'consumed_quantity' => 0,
                    'unit_id' => $bomItem->unit_id,
                ]);
            }

            return $order;
        });
    }

    public function approveOrder(ProductionOrder $order, int $userId): void
    {
        if ($order->status !== ProductionOrderStatus::Draft) {
            throw new \LogicException(__('production.cannot_approve'));
        }

        $order->update([
            'status' => ProductionOrderStatus::Approved,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function startProduction(ProductionOrder $order, int $userId): void
    {
        if ($order->status !== ProductionOrderStatus::Approved) {
            throw new \LogicException(__('production.cannot_start'));
        }

        // Validate stock availability before starting
        $order->load('items.material');
        foreach ($order->items as $item) {
            $balance = $this->inventoryService->getBalance($item->material);
            if ($balance->quantity < $item->required_quantity) {
                throw new InsufficientStockException(
                    "Insufficient stock for material: {$item->material->name}. " .
                    "Required: {$item->required_quantity}, Available: {$balance->quantity}"
                );
            }
        }

        DB::transaction(function () use ($order, $userId) {
            // Consume materials
            foreach ($order->items as $item) {
                $this->inventoryService->recordMovement(
                    item: $item->material,
                    type: StockMovementType::ProductionConsume,
                    quantity: (float) $item->required_quantity,
                    createdBy: $userId,
                    reference: $order,
                );

                $item->update(['consumed_quantity' => $item->required_quantity]);
            }

            $order->update([
                'status' => ProductionOrderStatus::InProduction,
                'started_at' => now()->toDateString(),
            ]);
        });

        event(new ProductionStarted($order));
    }

    public function completeProduction(ProductionOrder $order, float $completedQuantity, int $userId): ProductionBatch
    {
        if ($order->status !== ProductionOrderStatus::InProduction) {
            throw new \LogicException(__('production.cannot_complete'));
        }

        return DB::transaction(function () use ($order, $completedQuantity, $userId) {
            // Calculate unit cost from consumed materials
            $totalMaterialCost = $order->items->sum(function ($item) {
                $balance = $this->inventoryService->getBalance($item->material);
                return $item->consumed_quantity * $balance->average_cost;
            });
            $unitCost = $completedQuantity > 0 ? $totalMaterialCost / $completedQuantity : 0;

            // Create production batch
            $batch = ProductionBatch::create([
                'batch_number' => 'PBATCH-' . $order->number . '-' . now()->format('YmdHis'),
                'production_order_id' => $order->id,
                'product_id' => $order->product_id,
                'quantity' => $completedQuantity,
                'unit_cost' => $unitCost,
                'production_date' => now()->toDateString(),
            ]);

            // Record production output stock movement
            $this->inventoryService->recordMovement(
                item: $order->product,
                type: StockMovementType::ProductionOutput,
                quantity: $completedQuantity,
                createdBy: $userId,
                unitCost: $unitCost,
                reference: $order,
            );

            $order->update([
                'status' => ProductionOrderStatus::Completed,
                'completed_quantity' => $completedQuantity,
                'completed_at' => now()->toDateString(),
            ]);

            event(new ProductionCompleted($order, $batch));

            return $batch;
        });
    }

    private function generateOrderNumber(): string
    {
        $last = ProductionOrder::latest('id')->value('number');
        $next = $last ? (int) substr($last, 3) + 1 : 1;
        return 'PO-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
