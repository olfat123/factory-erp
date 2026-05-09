<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'item_type',
        'item_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'batch_id',
        'reference_type',
        'reference_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'movement_type' => StockMovementType::class,
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
    ];

    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MaterialBatch::class, 'batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
