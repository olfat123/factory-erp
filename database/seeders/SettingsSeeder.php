<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'production_approval_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'production'],
            ['key' => 'purchase_approval_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'purchasing'],
            ['key' => 'default_language', 'value' => 'ar', 'type' => 'string', 'group' => 'general'],
            ['key' => 'two_factor_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'security'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
