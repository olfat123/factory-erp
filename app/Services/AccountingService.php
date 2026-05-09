<?php

namespace App\Services;

use App\Contracts\AccountingServiceInterface;
use App\Models\Account;
use App\Models\GoodsReceipt;
use App\Models\JournalEntry;
use App\Models\ProductionOrder;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService implements AccountingServiceInterface
{
    const RAW_MATERIALS  = '1100';
    const WIP            = '1200';
    const FINISHED_GOODS = '1300';
    const ACCOUNTS_PAY   = '2100';
    const COGS           = '5000';
    const ADJ_ACCOUNT    = '5100';

    public function recordPurchaseReceiving(GoodsReceipt $goodsReceipt): void
    {
        $amount = $goodsReceipt->items->sum(fn ($i) => $i->quantity * $i->unit_cost);
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($goodsReceipt, $amount) {
            $entry = $this->createEntry(
                type: 'goods_received',
                reference: $goodsReceipt,
                amount: $amount,
                description: "Goods Receipt #{$goodsReceipt->id} — PO #{$goodsReceipt->purchase_order_id}",
            );
            $this->addLine($entry, self::RAW_MATERIALS, debit: $amount);
            $this->addLine($entry, self::ACCOUNTS_PAY,  credit: $amount);
        });
    }

    public function recordMaterialConsumption(ProductionOrder $productionOrder): void
    {
        $amount = $productionOrder->items->sum(
            fn ($i) => $i->consumed_quantity * ($i->material->average_cost ?? 0)
        );
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($productionOrder, $amount) {
            $entry = $this->createEntry(
                type: 'production_consume',
                reference: $productionOrder,
                amount: $amount,
                description: "Production Order #{$productionOrder->id} — material consumption",
            );
            $this->addLine($entry, self::WIP,           debit: $amount);
            $this->addLine($entry, self::RAW_MATERIALS, credit: $amount);
        });
    }

    public function recordProductionOutput(ProductionOrder $productionOrder, float $unitCost): void
    {
        $qty    = $productionOrder->completed_quantity ?? 0;
        $amount = $qty * $unitCost;
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($productionOrder, $amount) {
            $entry = $this->createEntry(
                type: 'production_output',
                reference: $productionOrder,
                amount: $amount,
                description: "Production Order #{$productionOrder->id} — output to finished goods",
            );
            $this->addLine($entry, self::FINISHED_GOODS, debit: $amount);
            $this->addLine($entry, self::WIP,             credit: $amount);
        });
    }

    public function recordStockAdjustment(StockMovement $movement): void
    {
        $unitCost   = $movement->unit_cost ?? 0;
        $amount     = abs($movement->quantity * $unitCost);
        if ($amount <= 0) {
            return;
        }

        $typeValue  = is_object($movement->movement_type) ? $movement->movement_type->value : $movement->movement_type;
        $isIncrease = str_ends_with(strtoupper($typeValue), 'INCREASE');
        $type       = $isIncrease ? 'adjustment_increase' : 'adjustment_decrease';

        DB::transaction(function () use ($movement, $amount, $type, $isIncrease) {
            $entry = $this->createEntry(
                type: $type,
                reference: $movement,
                amount: $amount,
                description: "Stock adjustment #{$movement->id}",
            );

            if ($isIncrease) {
                $this->addLine($entry, self::RAW_MATERIALS, debit: $amount);
                $this->addLine($entry, self::ADJ_ACCOUNT,   credit: $amount);
            } else {
                $this->addLine($entry, self::ADJ_ACCOUNT,   debit: $amount);
                $this->addLine($entry, self::RAW_MATERIALS,  credit: $amount);
            }
        });
    }

    private function createEntry(string $type, mixed $reference, float $amount, string $description): JournalEntry
    {
        return JournalEntry::create([
            'reference_number' => $this->nextReferenceNumber(),
            'type'             => $type,
            'reference_type'   => $reference::class,
            'reference_id'     => $reference->id,
            'description'      => $description,
            'total_amount'     => $amount,
            'posted_at'        => now(),
            'created_by'       => Auth::id() ?? 1,
        ]);
    }

    private function addLine(JournalEntry $entry, string $accountCode, float $debit = 0, float $credit = 0): void
    {
        $account = Account::where('code', $accountCode)->firstOrFail();

        $entry->lines()->create([
            'account_id'  => $account->id,
            'debit'       => $debit,
            'credit'      => $credit,
            'description' => $account->name,
        ]);
    }

    private function nextReferenceNumber(): string
    {
        $last = JournalEntry::max('id') ?? 0;
        return 'JE-' . str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    }
}

}
