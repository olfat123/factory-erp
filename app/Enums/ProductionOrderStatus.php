<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum ProductionOrderStatus: string implements HasLabel, HasColor
{
    case Draft = 'draft';
    case Approved = 'approved';
    case InProduction = 'in_production';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return __('enums.production_status.' . $this->value);
    }

    public function label(): string
    {
        return $this->getLabel();
    }

    public function getColor(): string|array|null
    {
        return $this->color();
    }

    public function color(): string
    {
        return match($this) {
            self::Draft       => 'gray',
            self::Approved    => 'info',
            self::InProduction => 'warning',
            self::Completed   => 'success',
            self::Cancelled   => 'danger',
        };
    }
}
