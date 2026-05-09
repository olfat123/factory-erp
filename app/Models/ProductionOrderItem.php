<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrderItem extends Model
{
    protected $fillable = [
        'production_order_id',
        'material_id',
        'required_quantity',
        'consumed_quantity',
        'unit_id',
    ];

    protected $casts = [
        'required_quantity' => 'decimal:4',
        'consumed_quantity' => 'decimal:4',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
