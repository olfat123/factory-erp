<?php

namespace App\Models;

use App\Enums\MachineStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Machine extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => MachineStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function productMachines(): HasMany
    {
        return $this->hasMany(ProductMachine::class);
    }
}
