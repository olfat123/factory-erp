<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Worker extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'name_ar',
        'job_title',
        'base_salary',
        'hire_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'hire_date'   => 'date',
        'is_active'   => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(WorkerSalary::class);
    }
}
