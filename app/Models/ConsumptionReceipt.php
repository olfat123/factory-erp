<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ConsumptionReceipt extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'type',
        'description',
        'amount',
        'receipt_date',
        'period_month',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'receipt_date'  => 'date',
        'period_month'  => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public static function types(): array
    {
        return [
            'electricity'         => __('resources.consumption_receipt.types.electricity'),
            'telephone'           => __('resources.consumption_receipt.types.telephone'),
            'internet'            => __('resources.consumption_receipt.types.internet'),
            'machine_maintenance' => __('resources.consumption_receipt.types.machine_maintenance'),
            'rent'                => __('resources.consumption_receipt.types.rent'),
            'other'               => __('resources.consumption_receipt.types.other'),
        ];
    }
}
