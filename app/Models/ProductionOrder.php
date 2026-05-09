<?php

namespace App\Models;

use App\Enums\ProductionOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ProductionOrder extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'number',
        'product_id',
        'created_by',
        'approved_by',
        'status',
        'quantity',
        'completed_quantity',
        'planned_date',
        'started_at',
        'completed_at',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'status' => ProductionOrderStatus::class,
        'quantity' => 'decimal:4',
        'completed_quantity' => 'decimal:4',
        'planned_date' => 'date',
        'started_at' => 'date',
        'completed_at' => 'date',
        'approved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionOrderItem::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }
}
