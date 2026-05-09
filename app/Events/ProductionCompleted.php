<?php

namespace App\Events;

use App\Models\ProductionBatch;
use App\Models\ProductionOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ProductionOrder $productionOrder,
        public readonly ProductionBatch $productionBatch
    ) {
    }
}
