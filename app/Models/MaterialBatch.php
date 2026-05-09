<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialBatch extends Model
{
    protected $fillable = [
        'batch_number',
        'material_id',
        'goods_receipt_item_id',
        'initial_quantity',
        'current_quantity',
        'unit_cost',
        'received_date',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:4',
        'current_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'received_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'batch_id');
    }
}
