<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getTranslatedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->name_ar
            ? $this->name_ar
            : $this->name;
    }
}
