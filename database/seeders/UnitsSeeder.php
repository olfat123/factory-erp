<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'name_ar' => 'كيلوجرام', 'symbol' => 'KG'],
            ['name' => 'Gram', 'name_ar' => 'جرام', 'symbol' => 'G'],
            ['name' => 'Piece', 'name_ar' => 'قطعة', 'symbol' => 'PCS'],
            ['name' => 'Meter', 'name_ar' => 'متر', 'symbol' => 'M'],
            ['name' => 'Liter', 'name_ar' => 'لتر', 'symbol' => 'L'],
            ['name' => 'Ton', 'name_ar' => 'طن', 'symbol' => 'T'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
