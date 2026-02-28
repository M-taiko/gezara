<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class UdhiyaSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Warehouses ───────────────────────────────────────────────────
        Warehouse::firstOrCreate(['name' => 'المزرعة'],  ['description' => 'المخزن الرئيسي للمزرعة']);
        Warehouse::firstOrCreate(['name' => 'المحل'],    ['description' => 'محل البيع']);

        // ─── Main Categories ──────────────────────────────────────────────
        $cattle = MainCategory::firstOrCreate(['code' => 'BQR'], ['name' => 'عجول']);
        $sheep  = MainCategory::firstOrCreate(['code' => 'GHN'], ['name' => 'خرفان']);
        $goat   = MainCategory::firstOrCreate(['code' => 'JDN'], ['name' => 'جديان']);
        $camel  = MainCategory::firstOrCreate(['code' => 'JML'], ['name' => 'جمال']);

        // ─── Products ─────────────────────────────────────────────────────
        Product::firstOrCreate(['name' => 'عجل عربي'],       ['main_category_id' => $cattle->id, 'is_active' => true]);
        Product::firstOrCreate(['name' => 'عجل بلدي'],       ['main_category_id' => $cattle->id, 'is_active' => true]);
        Product::firstOrCreate(['name' => 'خروف بلدي'],      ['main_category_id' => $sheep->id,  'is_active' => true]);
        Product::firstOrCreate(['name' => 'خروف رومي'],      ['main_category_id' => $sheep->id,  'is_active' => true]);
        Product::firstOrCreate(['name' => 'جدي نعيمي'],      ['main_category_id' => $goat->id,   'is_active' => true]);
        Product::firstOrCreate(['name' => 'جمل عربي أصيل'],  ['main_category_id' => $camel->id,  'is_active' => true]);

        // ─── Chart of Accounts ────────────────────────────────────────────
        $accounts = [
            ['code' => '1000', 'name' => 'الصندوق',                'type' => 'asset',     'is_system' => true, 'balance' => 0],
            ['code' => '1100', 'name' => 'المخزون — الحيوانات',    'type' => 'asset',     'is_system' => true, 'balance' => 0],
            ['code' => '2000', 'name' => 'ذمم دائنة — الموردون',   'type' => 'liability', 'is_system' => true, 'balance' => 0],
            ['code' => '3000', 'name' => 'ذمم مدينة — العملاء',    'type' => 'asset',     'is_system' => true, 'balance' => 0],
            ['code' => '4000', 'name' => 'إيرادات المبيعات',        'type' => 'revenue',   'is_system' => true, 'balance' => 0],
            ['code' => '5000', 'name' => 'تكلفة البضاعة المباعة',  'type' => 'expense',   'is_system' => true, 'balance' => 0],
        ];
        foreach ($accounts as $acc) {
            Account::firstOrCreate(['code' => $acc['code']], $acc);
        }

        $this->command->info('UdhiyaSeeder: Warehouses=2, Categories=4, Products=6, Accounts=6 — no sample data');
    }
}
