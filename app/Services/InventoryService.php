<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\InventoryBalance;
use App\Models\Material;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Record a stock movement and update inventory balance.
     * Must be called inside a DB::transaction.
     */
    public function recordMovement(
        Model $item,
        StockMovementType $type,
        float $quantity,
        int $createdBy,
        float $unitCost = 0,
        ?int $batchId = null,
        ?Model $reference = null,
        ?string $notes = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }

        if (!$type->isIncrease()) {
            $this->assertSufficientStock($item, $quantity);
        }

        $movement = StockMovement::create([
            'item_type' => $item->getMorphClass(),
            'item_id' => $item->getKey(),
            'movement_type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'batch_id' => $batchId,
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
            'created_by' => $createdBy,
            'notes' => $notes,
        ]);

        $this->updateBalance($item, $type, $quantity, $unitCost);

        return $movement;
    }

    /**
     * Update the denormalized stock cache on the model.
     */
    private function updateBalance(Model $item, StockMovementType $type, float $quantity, float $unitCost): void
    {
        $balance = InventoryBalance::firstOrCreate(
            ['item_type' => $item->getMorphClass(), 'item_id' => $item->getKey()],
            ['quantity' => 0, 'average_cost' => 0, 'total_value' => 0]
        );

        if ($type->isIncrease()) {
            // Weighted average cost recalculation
            $newTotal = ($balance->quantity * $balance->average_cost) + ($quantity * $unitCost);
            $newQty = $balance->quantity + $quantity;
            $newAvg = $newQty > 0 ? $newTotal / $newQty : $unitCost;

            $balance->update([
                'quantity' => $newQty,
                'average_cost' => $newAvg,
                'total_value' => $newQty * $newAvg,
            ]);
        } else {
            $newQty = $balance->quantity - $quantity;
            $balance->update([
                'quantity' => $newQty,
                'total_value' => $newQty * $balance->average_cost,
            ]);
        }

        // Sync current_stock on the model itself
        $item->update(['current_stock' => $balance->quantity]);
    }

    private function assertSufficientStock(Model $item, float $quantity): void
    {
        $balance = InventoryBalance::where('item_type', $item->getMorphClass())
            ->where('item_id', $item->getKey())
            ->first();

        $currentStock = $balance?->quantity ?? 0;

        if ($currentStock < $quantity) {
            throw new InsufficientStockException(
                "Insufficient stock for {$item->getMorphClass()} #{$item->getKey()}. " .
                "Available: {$currentStock}, Required: {$quantity}"
            );
        }
    }

    public function getBalance(Model $item): InventoryBalance
    {
        return InventoryBalance::firstOrCreate(
            ['item_type' => $item->getMorphClass(), 'item_id' => $item->getKey()],
            ['quantity' => 0, 'average_cost' => 0, 'total_value' => 0]
        );
    }

    public function getLowStockMaterials(): \Illuminate\Database\Eloquent\Collection
    {
        return Material::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->with(['category', 'unit'])
            ->get();
    }
}
