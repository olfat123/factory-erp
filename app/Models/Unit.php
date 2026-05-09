<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function translatedName(): Attribute
    {
        return Attribute::make(
            get: fn () => app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name) : $this->name,
        );
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
