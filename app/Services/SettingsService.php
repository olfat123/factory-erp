<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->typed_value : $default;
        });
    }

    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting:{$key}");
    }

    public function isProductionApprovalEnabled(): bool
    {
        return (bool) $this->get('production_approval_enabled', true);
    }

    public function isPurchaseApprovalEnabled(): bool
    {
        return (bool) $this->get('purchase_approval_enabled', true);
    }

    public function getDefaultLanguage(): string
    {
        return (string) $this->get('default_language', 'en');
    }
}
