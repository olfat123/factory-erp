<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1100', 'name' => 'Raw Materials Inventory',   'name_ar' => 'مخزون المواد الخام',        'type' => 'asset'],
            ['code' => '1200', 'name' => 'Work In Progress',          'name_ar' => 'إنتاج تحت التشغيل',         'type' => 'asset'],
            ['code' => '1300', 'name' => 'Finished Goods Inventory',  'name_ar' => 'مخزون المنتجات التامة',     'type' => 'asset'],
            ['code' => '2100', 'name' => 'Accounts Payable',          'name_ar' => 'الدائنون',                   'type' => 'liability'],
            ['code' => '5000', 'name' => 'Cost of Goods Sold',        'name_ar' => 'تكلفة البضاعة المباعة',     'type' => 'expense'],
            ['code' => '5100', 'name' => 'Inventory Adjustment',      'name_ar' => 'تسوية المخزون',             'type' => 'expense'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
