<?php

namespace App\Events;

use App\Models\GoodsReceipt;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoodsReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly GoodsReceipt $goodsReceipt)
    {
    }
}
