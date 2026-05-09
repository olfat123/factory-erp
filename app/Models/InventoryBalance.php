<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryBalance extends Model
{
    protected $fillable = [
        'item_type',
        'item_id',
        'quantity',
        'average_cost',
        'total_value',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'average_cost' => 'decimal:4',
        'total_value' => 'decimal:4',
    ];

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
