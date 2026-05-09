<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Product extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'sku',
        'name',
        'name_ar',
        'description',
        'production_time',
        'current_stock',
        'is_active',
    ];

    protected $casts = [
        'production_time' => 'integer',
        'current_stock' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function bom(): HasOne
    {
        return $this->hasOne(BillOfMaterial::class);
    }

    public function bomItems(): HasManyThrough
    {
        return $this->hasManyThrough(BillOfMaterialItem::class, BillOfMaterial::class);
    }

    public function productionOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function productionBatches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'item');
    }

    public function inventoryBalance(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(InventoryBalance::class, 'item');
    }
}
