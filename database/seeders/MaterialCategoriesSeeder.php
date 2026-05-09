<?php

namespace Database\Seeders;

use App\Models\MaterialCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MaterialCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Plastic', 'name_ar' => 'بلاستيك'],
            ['name' => 'Paper', 'name_ar' => 'ورق'],
            ['name' => 'Metal', 'name_ar' => 'معدن'],
            ['name' => 'Gauze', 'name_ar' => 'شاش'],
            ['name' => 'Packaging', 'name_ar' => 'تعبئة وتغليف'],
        ];

        foreach ($categories as $category) {
            MaterialCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                array_merge($category, ['slug' => Str::slug($category['name'])])
            );
        }
    }
}
