<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Material extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'category_id',
        'unit_id',
        'minimum_stock',
        'current_stock',
        'average_cost',
        'is_active',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:4',
        'current_stock' => 'decimal:4',
        'average_cost' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    protected function translatedName(): Attribute
    {
        return Attribute::make(
            get: fn () => app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name) : $this->name,
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(MaterialBatch::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'item');
    }

    public function inventoryBalance(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(InventoryBalance::class, 'item');
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
