<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerSalary extends Model
{
    protected $fillable = [
        'worker_id',
        'period',
        'working_days',
        'overtime_hours',
        'bonuses',
        'deductions',
        'gross_salary',
        'social_insurance',
        'tax',
        'net_salary',
        'notes',
    ];

    protected $casts = [
        'period'           => 'date',
        'overtime_hours'   => 'decimal:2',
        'bonuses'          => 'decimal:2',
        'deductions'       => 'decimal:2',
        'gross_salary'     => 'decimal:2',
        'social_insurance' => 'decimal:2',
        'tax'              => 'decimal:2',
        'net_salary'       => 'decimal:2',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }
}
