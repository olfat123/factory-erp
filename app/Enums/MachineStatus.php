<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum MachineStatus: string implements HasLabel, HasColor
{
    case Available = 'available';
    case Running = 'running';
    case Maintenance = 'maintenance';
    case OutOfService = 'out_of_service';

    public function getLabel(): ?string
    {
        return __('enums.machine_status.' . $this->value);
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
            self::Available    => 'success',
            self::Running      => 'warning',
            self::Maintenance  => 'info',
            self::OutOfService => 'danger',
        };
    }
}
