<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'default_language',            'value' => 'ar',    'type' => 'string',  'group' => 'general'],

            // Security
            ['key' => 'two_factor_enabled',          'value' => 'false', 'type' => 'boolean', 'group' => 'security'],

            // Approvals
            ['key' => 'production_approval_enabled', 'value' => 'true',  'type' => 'boolean', 'group' => 'production'],
            ['key' => 'purchase_approval_enabled',   'value' => 'true',  'type' => 'boolean', 'group' => 'purchasing'],

            // Salaries
            ['key' => 'salary_currency',             'value' => 'SAR',   'type' => 'string',  'group' => 'salaries'],
            ['key' => 'working_days_per_month',      'value' => '22',    'type' => 'integer', 'group' => 'salaries'],
            ['key' => 'working_hours_per_day',       'value' => '8',     'type' => 'integer', 'group' => 'salaries'],
            ['key' => 'overtime_rate',               'value' => '1.5',   'type' => 'string',  'group' => 'salaries'],
            ['key' => 'social_insurance_rate',       'value' => '0',     'type' => 'string',  'group' => 'salaries'],
            ['key' => 'tax_rate',                    'value' => '0',     'type' => 'string',  'group' => 'salaries'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
