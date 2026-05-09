<?php

namespace App\Events;

use App\Models\ProductionOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ProductionOrder $productionOrder)
    {
    }
}
