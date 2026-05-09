<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PurchaseReceive = 'purchase_receive';
    case ProductionConsume = 'production_consume';
    case ProductionOutput = 'production_output';
    case AdjustmentIncrease = 'adjustment_increase';
    case AdjustmentDecrease = 'adjustment_decrease';
    case Return = 'return';

    public function label(): string
    {
        return match($this) {
            self::PurchaseReceive => __('enums.movement_type.purchase_receive'),
            self::ProductionConsume => __('enums.movement_type.production_consume'),
            self::ProductionOutput => __('enums.movement_type.production_output'),
            self::AdjustmentIncrease => __('enums.movement_type.adjustment_increase'),
            self::AdjustmentDecrease => __('enums.movement_type.adjustment_decrease'),
            self::Return => __('enums.movement_type.return'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PurchaseReceive => 'success',
            self::ProductionConsume => 'warning',
            self::ProductionOutput => 'info',
            self::AdjustmentIncrease => 'success',
            self::AdjustmentDecrease => 'danger',
            self::Return => 'gray',
        };
    }

    public function isIncrease(): bool
    {
        return in_array($this, [
            self::PurchaseReceive,
            self::ProductionOutput,
            self::AdjustmentIncrease,
            self::Return,
        ]);
    }
}
