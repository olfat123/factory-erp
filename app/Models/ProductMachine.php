<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMachine extends Model
{
    protected $fillable = [
        'bill_of_material_id',
        'machine_id',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
    ];

    public function billOfMaterial(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
