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

    public function isTwoFactorEnabled(): bool
    {
        return (bool) $this->get('two_factor_enabled', false);
    }

    public function getSalaryCurrency(): string
    {
        return (string) $this->get('salary_currency', 'SAR');
    }

    public function getWorkingDaysPerMonth(): int
    {
        return (int) $this->get('working_days_per_month', 22);
    }

    public function getWorkingHoursPerDay(): int
    {
        return (int) $this->get('working_hours_per_day', 8);
    }

    public function getOvertimeRate(): float
    {
        return (float) $this->get('overtime_rate', 1.5);
    }

    public function getSocialInsuranceRate(): float
    {
        return (float) $this->get('social_insurance_rate', 0.0);
    }

    public function getTaxRate(): float
    {
        return (float) $this->get('tax_rate', 0.0);
    }
}
